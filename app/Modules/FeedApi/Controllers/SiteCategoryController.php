<?php

namespace Modules\FeedApi\Controllers;

use \Symfony\Component\HttpFoundation\Response;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\BusinessLog\BusinessLogManager;
use App\Business\Error\ErrorCode;
use App\Business\Message\MessageManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Qwindo\UpsertSiteCategoryRequest;
use App\Model\Document\BusinessLog;
use Illuminate\Http\Request;

/**
 * SiteCategoryController
 */
class SiteCategoryController extends Controller
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIEs
    //------------------------------------------------------------------------------------------------------------------

    const RESPONSE_TYPES = [
        'upsertSiteCategory' => 'upsert_category_request',
    ];

    const MESSAGE_PATTERNS = [
        'upsertSiteCategory' => 'Request [ %s ] related with CATEGORY UPSERT has been received.',
    ];

    /**
     * $messageManager
     *
     * @access private
     * @var MessageManager
     */
    private $messageManager;

    /**
     * $apiResponseManager
     *
     * @access private
     * @var ApiResponseManager
     */
    private $apiResponseManager;

    /**
     * $businessLogManager
     *
     * @access private
     * @var BusinessLogManager
     */
    private $businessLogManager;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Object constructor
     *
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

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Update/Insert job from a Request.
     *
     * @access public
     * @param UpsertSiteCategoryRequest $request
     * @return JsonResponse
     */
    public function upsertSiteCategory(UpsertSiteCategoryRequest $request): \Illuminate\Http\JsonResponse
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
            BusinessLog::ELEMENT_TYPE_CATEGORY,
            sprintf(self::MESSAGE_PATTERNS[__FUNCTION__], $result),
            json_encode($request->all()),
            BusinessLog::HTTP_TYPE_PUSH
        );

        return $this
            ->apiResponseManager
            ->createResponse(Response::HTTP_OK, self::RESPONSE_TYPES[__FUNCTION__]);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
