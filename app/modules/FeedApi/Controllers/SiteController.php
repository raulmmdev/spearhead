<?php

namespace Modules\FeedApi\Controllers;

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
		//create the site
		$apiRequest = new ApiRequest($request, $this->messageManager);
		$result = $apiRequest->resolve(ApiRequest::MSG_CREATE_SITE);

		if (!$result) {
			//something went wrong?
			return $this->apiResponseManager->createErrorResponse(500, ErrorCode::ERROR_SAVE_MESSAGE);
		}

		//response
		ApiRequestResource::withoutWrapping(); //remove the top data element wrapper
        return new ApiRequestResource($result);
    }
}
	
