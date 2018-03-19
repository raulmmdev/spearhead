<?php

namespace Modules\FeedApi\Controllers;

use \Symfony\Component\HttpFoundation\Response;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\BusinessLog\BusinessLogManager;
use App\Business\Error\ErrorCode;
use App\Business\Message\MessageManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Qwindo\CreateSiteProductRequest;
use App\Http\Requests\Qwindo\UpdateSiteProductRequest;
use App\Http\Requests\Qwindo\DeleteSiteProductRequest;
use App\Model\Document\BusinessLog;
use Illuminate\Http\Request;

/**
 * SiteProductController
 */
class SiteProductController extends Controller
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIEs
    //------------------------------------------------------------------------------------------------------------------

    const MESSAGE_PATTERNS = [
        'create' => 'Request [ %s ] related with PRODUCT CREATE has been received.',
        'update' => 'Request [ %s ] related with PRODUCT UPDATE has been received.',
        'delete' => 'Request [ %s ] related with PRODUCT DELETE has been received.',
    ];

    const RESPONSE_TYPES = [
        'create' => 'create_product_request',
        'update' => 'update_product_request',
        'delete' => 'delete_product_request',
    ];

    const RESPONSE_HTTP_STATUS = [
        'create' => Response::HTTP_CREATED,
        'update' => Response::HTTP_OK,
        'delete' => Response::HTTP_OK,
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
     * Create job from a Request.
     *
     * @access public
     * @param CreateSiteProductRequest $request
     * @return JsonResponse
     */
    public function create(CreateSiteProductRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->processRequest(__FUNCTION__, $request);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Update job from a Request.
     *
     * @access public
     * @param UpdateSiteProductRequest $request
     * @return JsonResponse
     */
    public function update(CreateSiteProductRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->processRequest(__FUNCTION__, $request);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Delete job from a Request.
     *
     * @access public
     * @param DeleteSiteProductRequest $request
     * @return JsonResponse
     */
    public function delete(DeleteSiteProductRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->processRequest(__FUNCTION__, $request);
    }

    //------------------------------------------------------------------------------------------------------------------
    // PRIVATED METHOD
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Process generic request
     *
     * @access private
     * @param string $method
     * @param $request
     * @return JsonResponse
     */
    private function processRequest(string $method, $request): \Illuminate\Http\JsonResponse
    {
        $result = $request->resolve();

        if ($result === false) {
            return $this
                ->apiResponseManager
                ->createErrorResponse(
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ErrorCode::ERROR_CODE_SAVE_MESSAGE
                );
        }

        $this->businessLogManager->info(
            BusinessLog::USER_TYPE_MERCHANT,
            BusinessLog::ELEMENT_TYPE_PRODUCT,
            sprintf(self::MESSAGE_PATTERNS[$method], $result),
            json_encode($request->all()),
            BusinessLog::HTTP_TYPE_PUSH
        );

        return $this
            ->apiResponseManager
            ->createResponse(self::RESPONSE_HTTP_STATUS[$method], self::RESPONSE_TYPES[$method]);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
