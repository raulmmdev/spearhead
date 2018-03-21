<?php

namespace App\Http\Requests\Qwindo;

use App\Business\Api\Interfaces\ResolvableInterface;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\Message\MessageManager;
use App\Http\Requests\ApiRequest;
use App\Model\Entity\SiteProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * DeleteSiteProductRequest
 */
class DeleteSiteProductRequest extends ApiRequest implements ResolvableInterface
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
        $product = $this->all();

        return ['product' => $product];
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
        $site = Auth::guard('api')->user()->site;
        $data = $this->validationData();

        return [
            'product' => ['required', 'array', 'min:1'],
            'product.id' => ['required', 'integer', Rule::exists('site_product', 'source_id')->where(function($query) use ($site, $data) {
                $query->where('site_id', $site->id);
                //$query->where('status', SiteProduct::STATUS_ENABLED);
            })],
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
