<?php

namespace App\Http\Middleware;

use \Symfony\Component\HttpFoundation\Response;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\Error\ErrorCode;
use App\Model\Entity\ApiFeature;
use App\Model\Entity\Repository\ApiFeatureRepository;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;

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
        ApiFeatureRepository $apiFeatureRepo
    ) {
        $this->apiResponseManager = $apiResponseManager;
        $this->apiFeatureRepo = $apiFeatureRepo;
        $this->requestTimeout = env('API_DIGEST_TIMEOUT', 15);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $headers = $request->headers;
        $content = $request->getContent();

        $url  = $request->url();
        $url .= ($request->getQueryString())
            ? '?'. $request->getQueryString()
            : '';

        //header?
        $authorizedHeaders = $this->getValidHeaders($request);
        if (!$request->headers->has('Auth')) {
            $response = $this->createErrorResponse(
                Response::HTTP_FORBIDDEN,
                ErrorCode::ERROR_CODE_MISSING_AUTH
            );

            throw new HttpResponseException($response);
        }

        $auth = base64_decode($headers->get('Auth'));

        list($hash_id, $timestamp, $token) = explode(':', $auth);
        $timestamp = (float) $timestamp;

        //missing components?
        if (!$hash_id || !$timestamp || !$token) {
            $response = $this->createErrorResponse(
                Response::HTTP_FORBIDDEN,
                ErrorCode::ERROR_CODE_MALFORMED_AUTH
            );

            throw new HttpResponseException($response);
        }

        $user = $this->apiFeatureRepo->findByField('login', $hash_id)->first();

        //no user detected?
        if ($user === null) {
            $this->logFailure(sprintf('User [%s] not found', $hash_id));
            $response = $this->createErrorResponse(
                Response::HTTP_FORBIDDEN,
                ErrorCode::ERROR_CODE_WRONG_CREDENTIALS
            );

            throw new HttpResponseException($response);
        }

        //user is disabled?
        if (false === $user->isEnabled()) {
            $this->logFailure(sprintf('User [%s] is disabled', $hash_id));
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
            $request->merge(['user' => $user]);
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
}
