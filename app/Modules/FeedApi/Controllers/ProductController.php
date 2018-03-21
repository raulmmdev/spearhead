<?php

namespace Modules\FeedApi\Controllers;

use \Symfony\Component\HttpFoundation\Response;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\BusinessLog\BusinessLogManager;
use App\Business\Error\ErrorCode;
use App\Business\Message\MessageManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Qwindo\DeleteProductRequest;
use App\Http\Requests\Qwindo\UpsertProductRequest;
use App\Model\Document\BusinessLog;
use Illuminate\Http\Request;

/**
 * ProductController
 */
class ProductController extends Controller
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIEs
    //------------------------------------------------------------------------------------------------------------------

    const MESSAGE_PATTERNS = [
        'delete' => 'Request [ %s ] related with PRODUCT DELETE has been received.',
        'upsert' => 'Request [ %s ] related with PRODUCT UPSERT has been received.',
    ];

    const RESPONSE_TYPES = [
        'delete' => 'delete_product_request',
        'upsert' => 'upsert_product_request',
    ];

    const RESPONSE_HTTP_STATUS = [
        'delete' => Response::HTTP_OK,
        'upsert' => Response::HTTP_OK,
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
     * Upsert job from a Request.
     *
     * @access public
     * @param UpsertProductRequest $request
     * @return JsonResponse
     */
    public function upsert(UpsertProductRequest $request): \Illuminate\Http\JsonResponse
    {
        return $this->processRequest(__FUNCTION__, $request);
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Delete job from a Request.
     *
     * @access public
     * @param DeleteProductRequest $request
     * @return JsonResponse
     */
    public function delete(DeleteProductRequest $request): \Illuminate\Http\JsonResponse
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
