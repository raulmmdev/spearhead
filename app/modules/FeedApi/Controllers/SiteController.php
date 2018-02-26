<?php

namespace Modules\FeedApi\Controllers;

use \Symfony\Component\HttpFoundation\Response;
use App\Business\Api\Request\ApiRequest;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\Error\ErrorCode;
use App\Business\Message\MessageManager;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApiRequestResource;
use Illuminate\Http\Request;

class SiteController extends Controller
{

	private $messageManager;
	private $apiResponseManager;

	public function __construct(
		MessageManager $messageManager,
		ApiResponseManager $apiResponseManager
	) {
		$this->messageManager = $messageManager;
		$this->apiResponseManager = $apiResponseManager;
	}

    /**
     * Create a new site from a Request.
     *
     * @param Request $request
     * @return SiteResource
     */
    public function createSite(Request $request)
	{
		$apiRequest = new ApiRequest($request, $this->messageManager);
		$result = $apiRequest->resolve(ApiRequest::MSG_CREATE_SITE);

		if (!$result) {
			return $this
				->apiResponseManager
				->createErrorResponse(
					Response::HTTP_INTERNAL_SERVER_ERROR,
					ErrorCode::ERROR_SAVE_MESSAGE
				);
		}

		return $this->apiResponseManager->createResponse(Response::HTTP_CREATED, ApiRequest::MSG_DESCRIPTIONS[ApiRequest::MSG_CREATE_SITE]);
    }
}
