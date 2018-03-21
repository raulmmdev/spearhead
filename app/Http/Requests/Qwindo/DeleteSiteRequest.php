<?php

namespace App\Http\Requests\Qwindo;

use App\Business\Api\Interfaces\ResolvableInterface;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\Job\JobFactory;
use App\Http\Requests\ApiRequest;
use App\Rules\Site\SiteRuleset;
use App\Rules\Site\MerchantRuleset;

/**
 * DeleteSiteRequest
 */
class DeleteSiteRequest extends ApiRequest implements ResolvableInterface
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Message Manager
     *
     * @access protected
     * @var $jobFactory
     */
    protected $jobFactory;

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
        JobFactory $jobFactory,
        ApiResponseManager $apiResponseManager
    ) {
        parent::__construct($apiResponseManager);
        $this->jobFactory = $jobFactory;
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
        $id = $this->route('siteId');
        return true;
    }

    /**
     * prepareForValidation
     * @access public
     * @return array
     */
    public function prepareForValidation()
    {
        $attributes = parent::all();

        // you can add fields
        $attributes['site_id'] = $this->route('siteId');

        $this->replace($attributes);
        return parent::all();
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
            'site_id' => 'required|numeric|exists:site,native_id',
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
        $data = $this->all();

        $data['crud_operation'] = ApiRequest::ACTION_DELETE;
        $job = $this->jobFactory->create(ApiRequest::QUEUE_SITE, $data);
        $job->resolve();

        $response = [
            'object' => $job->getObject(),
            'errors' => $job->getErrors(),
        ];

        return json_encode($response);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
