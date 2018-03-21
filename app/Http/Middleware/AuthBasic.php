<?php

namespace App\Http\Middleware;

use \Symfony\Component\HttpFoundation\Response;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\Error\ErrorCode;
use App\Business\User\Interfaces\UserInterface;
use App\Model\Entity\ApiFeature;
use App\Model\Entity\Repository\ApiFeatureRepository;
use App\Model\Entity\Repository\SiteProviderFeatureRepository;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthBasic
{
    /**
     * @access protected
     * @var $apiResponseManager
     */
    protected $apiResponseManager;

    /**
     * @access protected
     * @var $featureRepo
     */
    protected $featureRepo;

    public function __construct(
        ApiResponseManager $apiResponseManager,
        ApiFeatureRepository $apiFeatureRepo,
        SiteProviderFeatureRepository $siteProviderFeatureRepo
    ) {
        $this->apiResponseManager = $apiResponseManager;
        $this->apiFeatureRepo = $apiFeatureRepo;
        $this->siteProviderFeatureRepo = $siteProviderFeatureRepo;
        $this->requestTimeout = env('API_DIGEST_TIMEOUT', 15);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $headers = $request->headers;
        $content = $request->getContent();

        $url  = $request->url();
        $url .= ($request->getQueryString())
            ? '?'. $request->getQueryString()
            : '';

        //header? ('Auth', 'SiteProvider')
        $supportedHeaders = isset(config('qwindo.authconfigurations')[\Route::getCurrentRoute()->getName()])
            ? config('qwindo.authconfigurations')[\Route::getCurrentRoute()->getName()]
            : null;

        $authHeader = $this->getAuthHeader($request, $supportedHeaders);
        if (is_null($authHeader)) {
            $response = $this->createErrorResponse(
                Response::HTTP_FORBIDDEN,
                ErrorCode::ERROR_CODE_MISSING_AUTH
            );

            throw new HttpResponseException($response);
        }

        $auth = base64_decode($authHeader);
        list($hashId, $timestamp, $token) = explode(':', $auth);
        $timestamp = (float) $timestamp;

        //missing components?
        if (!$hashId || !$timestamp || !$token) {
            $response = $this->createErrorResponse(
                Response::HTTP_FORBIDDEN,
                ErrorCode::ERROR_CODE_MALFORMED_AUTH
            );

            throw new HttpResponseException($response);
        }

        $user = $this->getUser($request, $hashId);

        //no user detected?
        if ($user === null) {
            $this->logFailure(sprintf('User [%s] not found', $hashId));
            $response = $this->createErrorResponse(
                Response::HTTP_FORBIDDEN,
                ErrorCode::ERROR_CODE_WRONG_CREDENTIALS
            );

            throw new HttpResponseException($response);
        }

        //user is disabled?
        if (false === $user->isEnabled()) {
            $this->logFailure(sprintf('User [%s] is disabled', $hashId));
            $response = $this->createErrorResponse(
                Response::HTTP_FORBIDDEN,
                ErrorCode::ERROR_CODE_WRONG_CREDENTIALS
            );

            throw new HttpResponseException($response);
        }

        $currentTime = microtime(true);
        $diff = round($currentTime - $timestamp);

        //timeout?
        if ($diff > $this->requestTimeout) {
            $response = $this->createErrorResponse(
                Response::HTTP_REQUEST_TIMEOUT,
                ErrorCode::ERROR_CODE_REQUEST_TIMEOUT
            );

            throw new HttpResponseException($response);
        }

        $expectedToken = hash_hmac('sha512', $url.$timestamp.$content, $user->key);

        //token matches?
        if ($expectedToken === $token) {
            $guard = Auth::guard('api')->setUser($user);
            return $next($request);
        } else {
            $this->logFailure(
                sprintf('Invalid digest, received [%s] but expected [%s]', $token, $expectedToken)
            );
            $response = $this->createErrorResponse(
                Response::HTTP_FORBIDDEN,
                ErrorCode::ERROR_CODE_WRONG_CREDENTIALS
            );

            throw new HttpResponseException($response);
        }
    }

    /**
     * logFailure
     *
     * @access private
     * @param  string $reason
     * @return void
     */
    private function logFailure(string $reason): void
    {
        \Log::debug('[Failed authentication] '.$reason);
    }

    /**
     * createErrorResponse
     *
     * @access private
     * @param  string $httpStatus
     * @param  string $code
     * @return
     */
    private function createErrorResponse(
        string $httpStatus,
        string $code
    ): \Illuminate\Http\JsonResponse {
        return $this
            ->apiResponseManager
            ->createErrorResponse(
                $httpStatus,
                $code
            );
    }

    /**
     * getAuthHeader
     *
     * @access private
     * @param  Request $request
     * @param  array   $availableAuthHeaders
     * @return bool
     */
    private function getAuthHeader(Request $request, array $availableAuthHeaders):? string
    {
        if (in_array('Auth', $availableAuthHeaders) && $request->headers->has('Auth')) {
            return $request->headers->get('Auth');
        } elseif (in_array('SiteProvider', $availableAuthHeaders) && $request->headers->has('SiteProvider')) {
            return $request->headers->get('SiteProvider');
        }

        return null;
    }

    /**
     * getUser
     *
     * @access private
     * @param  Request $request
     * @param  string  $login
     * @return UserInterface|null
     */
    private function getUser(Request $request, string $login):? UserInterface
    {
        if ($request->headers->has('Auth')) {
            return $this->apiFeatureRepo->findByField('login', $login)->first();
        } elseif ($request->headers->has('SiteProvider')) {
            return $this->siteProviderFeatureRepo->findByField('login', $login)->first();
        }

        return null;
    }
}
