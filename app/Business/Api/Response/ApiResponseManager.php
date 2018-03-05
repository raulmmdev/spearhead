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
     * @access public
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
     * @access public
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

    /**
     * create a json api standard validation error array
     *
     * @access public
     * @param  array  $errors
     * @return string
     */
    public function formatValidationErrors(array $errors): array
    {
        $formattedErrors= [];

        foreach ($errors as $source => $errorList) {
            $errorSource = [ "pointer" => "/data/attributes/".$source];
            foreach ($errorList as $errorMessage) {
                $formattedErrors[] = [
                    "source" => $errorSource,
                    "title" => "Invalid Attribute",
                    "details" => $errorMessage,
                ];
            }
        }

        $response = [
            "errors" => $formattedErrors,
        ];

        return $response;
    }
}
