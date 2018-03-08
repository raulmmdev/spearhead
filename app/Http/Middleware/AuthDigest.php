<?php

namespace App\Http\Middleware;

use \Symfony\Component\HttpFoundation\Response;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\Error\ErrorCode;
use App\Business\Wsse\WsseMerchantToken;
use App\Model\Entity\ApiFeature;
use Closure;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthDigest
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
        if ($request->headers->has('x-wsse')) {
            $wsseRegex = '/AuthToken MerchantAPILogin="(?<MerchantAPILogin>[^"]+)", PasswordDigest="(?<PasswordDigest>[^"]+)", Nonce="(?<Nonce>[^"]+)", Created="(?<Created>[^"]+)"/';

            if (preg_match($wsseRegex, $request->headers->get('x-wsse'), $matches)) {
                $token = new WsseMerchantToken();
                $token->userLogin = $matches['MerchantAPILogin'];
                $token->digest = $matches['PasswordDigest'];
                $token->nonce = $matches['Nonce'];
                $token->created = $matches['Created'];
                $token->ip = $request->getClientIp();

                $this->validate($token);

                return $next($request);
            } else {
                $response = $this
                ->apiResponseManager
                ->createErrorResponse(
                    Response::HTTP_FORBIDDEN,
                    ErrorCode::ERROR_CODE_INVALID_WSSE
                );

                throw new HttpResponseException($response);
            }
        } else {
            $response = $this
                ->apiResponseManager
                ->createErrorResponse(
                    Response::HTTP_FORBIDDEN,
                    ErrorCode::ERROR_CODE_MISSING_WSSE
                );

            throw new HttpResponseException($response);
        }
    }

    /**
     * validate
     * @param  WsseMerchantToken $token
     * @return void | Exception
     */
    public function validate(WsseMerchantToken $token): bool
    {
        $user = ApiFeature::where('login', $token->userLogin)->first();
        if ($user === null) {
            $this->logFailure(sprintf('User [%s] not found', $token->userLogin));
            $response = $this
                ->apiResponseManager
                ->createErrorResponse(
                    Response::HTTP_FORBIDDEN,
                    ErrorCode::ERROR_CODE_WRONG_CREDENTIALS
                );

            throw new HttpResponseException($response);
        }

        if (false === $user->isEnabled()) {
            $this->logFailure(sprintf('User [%s] is disabled', $token->userLogin));
            $response = $this
                ->apiResponseManager
                ->createErrorResponse(
                    Response::HTTP_FORBIDDEN,
                    ErrorCode::ERROR_CODE_WRONG_CREDENTIALS
                );

            throw new HttpResponseException($response);
        }

        $validDigest = $this->validateDigest($token->digest, $token->nonce, $token->created, $user->key);

        if ($validDigest === false) {
            $response = $this
            ->apiResponseManager
            ->createErrorResponse(
                Response::HTTP_FORBIDDEN,
                ErrorCode::ERROR_CODE_WRONG_CREDENTIALS
            );

            throw new HttpResponseException($response);
        }

        return true;
    }


    private function validateDigest(string $digest, string $nonce, string $created, string $secret): bool
    {
        //$expire is valid timestamp?
        if (!preg_match("/^[1-9]{1}[0-9]{9,10}$/", $created)) {
            $this->logFailure(sprintf('Invalid created value: %s', $created));

            return false;
        }

        //timestamp expires after some time
        if (time() - $created > 6000) {
            $this->logFailure(sprintf('Expired "created" value: %s received but current time is %s', $created, time()));

            return false;
        }

        //validate the secret
        $expected = base64_encode(sha1(base64_decode($nonce).$created.$secret, true));

        if ($digest !== $expected) {
            $this->logFailure(sprintf('Invalid digest, received [%s] but expected [%s]', $digest, $expected));
        }

        return $digest === $expected;
    }


    /**
     * logFailure
     * @param  string $reason
     * @return void
     */
    private function logFailure(string $reason): void
    {
        \Log::debug("[Failed authentication] ".$reason);
    }
}
