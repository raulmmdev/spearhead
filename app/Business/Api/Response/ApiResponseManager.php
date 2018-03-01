<?php

namespace App\Business\Api\Response;

use App\Business\Error\ErrorCode;
use Illuminate\Http\JsonResponse;

/**
 * ApiResponseManager
 */
class ApiResponseManager
{
    /**
     * createResponse
     *
     * @param  int    $httpStatusCode
     * @param  string $type
     * @param  array  $attributes
     * @return JsonResponse
     */
    public function createResponse(
        int $httpStatusCode,
        string $type,
        array $attributes = []
    ): JsonResponse {
        $response = [
            'type' => $type,
            'attributes' => $attributes,
        ];

        return new JsonResponse($response, $httpStatusCode);
    }

    /**
     * createErrorResponse
     *
     * @param  int    $httpStatusCode
     * @param  string $errorCode
     * @return JsonResponse
     */
    public function createErrorResponse(int $httpStatusCode, string $errorCode): JsonResponse
    {
        $response = [
            'errors' => [
                'code' => $errorCode,
                'status' => $httpStatusCode,
                'title' => ErrorCode::ERROR_MESSAGES[$errorCode],
            ],
        ];

        return new JsonResponse($response, $httpStatusCode);
    }
}
