<?php

namespace App\Http\Requests\Qwindo;

use App\Business\Api\Interfaces\ResolvableInterface;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\Message\MessageManager;
use App\Http\Requests\ApiRequest;
use App\Rules\Product\DeleteProductRequest as DeleteRuleset;

/**
 * DeleteProductRequest
 */
class DeleteProductRequest extends ApiRequest implements ResolvableInterface
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Message Manager
     *
     * @access protected
     * @var $messageManager
     */
    protected $messageManager;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Object constructor
     *
     * @access public
     * @param MessageManager     $messageManager
     * @param ApiResponseManager $apiResponseManager
     */
    public function __construct(
        MessageManager $messageManager,
        ApiResponseManager $apiResponseManager
    ) {
        parent::__construct($apiResponseManager);
        $this->messageManager = $messageManager;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Determine if the user is authorized to make this request.
     *
     * @access public
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Override validationData() to reindex the input data for validation purposes
     *
     * @return array
     */
    protected function validationData() : array
    {
        $data = $this->all();

        return ['product' => $data];
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Get the validation rules that apply to the request.
     *
     * @access public
     * @return array
     */
    public function rules(): array
    {
        return [
            'product' => ['required', 'array', 'min:1', new DeleteRuleset],
        ];
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Resolve current request
     *
     * @access public
     * @return bool
     */
    public function resolve() :? string
    {
        return $this->messageManager->produceJobMessage(
            ApiRequest::QUEUE_PRODUCT,
            ApiRequest::ACTION_DELETE,
            $this->validationData()
        );
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
