<?php

namespace Modules\FeedApi\Controllers;

use \Symfony\Component\HttpFoundation\Response;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\BusinessLog\BusinessLogManager;
use App\Business\Error\ErrorCode;
use App\Business\Message\MessageManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Qwindo\SaveSiteRequest;
use App\Model\Document\BusinessLog;
use Illuminate\Http\Request;

/**
 * SiteController
 */
class SiteController extends Controller
{
    const RESPONSE_TYPES = [
        'createSite' => 'create_site_request',
    ];

    /**
     * $messageManager
     * @access private
     * @var MessageManager
     */
    private $messageManager;

    /**
     * $apiResponseManager
     * @access private
     * @var ApiResponseManager
     */
    private $apiResponseManager;

    /**
     * $businessLogManager
     * @access private
     * @var BusinessLogManager
     */
    private $businessLogManager;

    /**
     * @access public
     * @param MessageManager
     * @param ApiResponseManager
     * @param BusinessLogManager
     */
    public function __construct(
        MessageManager $messageManager,
        ApiResponseManager $apiResponseManager,
        BusinessLogManager $businessLogManager
    ) {
        $this->messageManager = $messageManager;
        $this->apiResponseManager = $apiResponseManager;
        $this->businessLogManager = $businessLogManager;
    }

    /**
     * Create a new site job from a Request.
     *
     * @access public
     * @param SaveSiteRequest $request
     * @return JsonResponse
     */
    public function createSite(SaveSiteRequest $request): \Illuminate\Http\JsonResponse
    {
        $result = $request->resolve();

        if (!$result) {
            return $this
                ->apiResponseManager
                ->createErrorResponse(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ErrorCode::ERROR_CODE_SAVE_MESSAGE
                );
        }

        $this->businessLogManager->info(
            BusinessLog::USER_TYPE_MERCHANT,
            BusinessLog::ELEMENT_TYPE_SITE,
            'Site creation request '.$result.' has been received.',
            json_encode($request->all()),
            BusinessLog::HTTP_TYPE_PUSH
        );

        return $this
            ->apiResponseManager
            ->createResponse(
                Response::HTTP_CREATED,
                self::RESPONSE_TYPES[__FUNCTION__]
            );
    }
}
