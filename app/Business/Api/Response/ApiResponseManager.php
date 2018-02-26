<?php
namespace App\Business\Api\Response;

use App\Business\Error\ErrorCode;
use Illuminate\Http\JsonResponse;

class ApiResponseManager
{
	/**
     * Create a new error response.
     *
     * @return bool
     */
    public function createErrorResponse(int $httpStatusCode, string $errorCode): JsonResponse
    {
        $body = [
            'errors' => [
                'code' => $errorCode,
                'status' => $httpStatusCode,
                'title' => ErrorCode::getMessage($errorCode)
            ],
        ];

    	return new JsonResponse($body, $httpStatusCode);
    }
}