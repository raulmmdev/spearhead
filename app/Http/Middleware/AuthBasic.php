<?php

namespace App\Http\Middleware;

use \Symfony\Component\HttpFoundation\Response;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\Error\ErrorCode;
use App\Model\Entity\ApiFeature;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthBasic
{
    /**
     * @access protected
     * @var $apiResponseManager
     */
    protected $apiResponseManager;

    public function __construct(ApiResponseManager $apiResponseManager)
    {
        $this->apiResponseManager = $apiResponseManager;
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
        $json = json_encode(json_decode($request->getContent()));

        $url  = $request->url();
        $url .= ($request->getQueryString())
            ? '?'. $request->getQueryString()
            : '';

        if ($request->headers->has('Auth')) {
            $auth = base64_decode($headers->get('Auth'));

            list($hash_id, $timestamp, $token) = explode(':', $auth);
            $timestamp = (float) $timestamp;

            if (!$hash_id || !$timestamp || !$token) {
                $response = $this->createErrorResponse(
                    Response::HTTP_FORBIDDEN,
                    ErrorCode::ERROR_CODE_MALFORMED_AUTH
                );

                throw new HttpResponseException($response);
            }

            $user = ApiFeature::where('login', $hash_id)->first();

            if ($user === null) {
                $this->logFailure(sprintf('User [%s] not found', $hash_id));
                $response = $this->createErrorResponse(
                    Response::HTTP_FORBIDDEN,
                    ErrorCode::ERROR_CODE_WRONG_CREDENTIALS
                );

                throw new HttpResponseException($response);
            }

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

            if ($diff > 10) {
                $response = $this->createErrorResponse(
                    Response::HTTP_REQUEST_TIMEOUT,
                    ErrorCode::ERROR_CODE_REQUEST_TIMEOUT
                );

                throw new HttpResponseException($response);
            }

            $expectedToken = hash_hmac('sha512', $url.$timestamp.$json, $user->key);

            if ($expectedToken === $token) {
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
        } else {
            $response = $this->createErrorResponse(
                Response::HTTP_FORBIDDEN,
                ErrorCode::ERROR_CODE_MISSING_AUTH
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
        \Log::debug("[Failed authentication] ".$reason);
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
