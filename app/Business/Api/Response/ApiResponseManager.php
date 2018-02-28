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
	public function createResponse(int $httpStatusCode, string $type, array $attributes = []) : JsonResponse
	{
		$response = [
			'type' => $type,
			'attributes' => $attributes,
		];

		return new JsonResponse($response, $httpStatusCode);
	}

	/**
	 * Create a new error response.
	 *
	 * @return bool
	 */
	public function createErrorResponse(int $httpStatusCode, string $errorCode) : JsonResponse
	{
		$response = [
			'errors' => [
				'code' => $errorCode,
				'status' => $httpStatusCode,
				'title' => ErrorCode::ERROR_MESSAGES[$errorCode]
			],
		];

		return new JsonResponse($response, $httpStatusCode);
	}
}
