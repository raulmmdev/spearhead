<?php

namespace App\Http\Requests\Qwindo;

use App\Business\Api\Interfaces\ResolvableInterface;
use App\Business\Api\Response\ApiResponseManager;
use App\Business\Message\MessageManager;
use App\Http\Requests\ApiRequest;

/**
 * SaveSiteRequest
 */
class SaveSiteRequest extends ApiRequest implements ResolvableInterface
{
    /**
     * @access protected
     * @var $messageManager
     */
    protected $messageManager;

    public function __construct(
        MessageManager $messageManager,
        ApiResponseManager $apiResponseManager
    ) {
        parent::__construct($apiResponseManager);
        $this->messageManager = $messageManager;
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
        return [
            'name' => 'required|max:255',
        ];
    }

    /**
     * @access public
     * @return bool
     */
    public function resolve() :? string
    {
        return $this->messageManager->produceJobMessage(
            ApiRequest::QUEUE_SITE,
            ApiRequest::ACTION_CREATE,
            $this->all()
        );
    }
}
