<?php

namespace App\Http\Requests\Qwindo;

use App\Business\Api\Interfaces\ResolvableInterface;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\Job\JobFactory;
use App\Http\Requests\ApiRequest;

/**
 * CreateSiteRequest
 */
class CreateSiteRequest extends ApiRequest implements ResolvableInterface
{
    /**
     * @access protected
     * @var $jobFactory
     */
    protected $jobFactory;

    public function __construct(
        JobFactory $jobFactory,
        ApiResponseManager $apiResponseManager
    ) {
        parent::__construct($apiResponseManager);
        $this->jobFactory = $jobFactory;
    }

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

    /**
     * Get the validation rules that apply to the request.
     *
     * @access public
     * @return array
     */
    public function rules(): array
    {
        return [];
        /*return [
            'site.name' => 'required|max:255',
        ];*/
    }

    /**
     * @access public
     * @return bool
     */
    public function resolve() :? string
    {
        $data = $this->all();
        $data['crud_operation'] = ApiRequest::ACTION_CREATE;
        $job = $this->jobFactory->create(ApiRequest::QUEUE_SITE, $data);
        $job->resolve();

        $response = [
            'object' => $job->getObject(),
            'errors' => $job->getErrors(),
        ];

        return json_encode($response);
    }
}
