<?php

namespace App\Http\Requests\Qwindo;

use App\Business\Api\Interfaces\ResolvableInterface;
use App\Business\Message\MessageManager;
use Illuminate\Foundation\Http\FormRequest;

/**
 * SaveSiteRequest
 */
class SaveSiteRequest extends FormRequest implements ResolvableInterface
{
    const QUEUE = 'site';

    /**
     * @access protected
     * @var $messageManager
     */
    protected $messageManager;

    /**
     * @param MessageManager
     */
    public function __construct(MessageManager $messageManager)
    {
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
    public function resolve(): bool
    {
        return $this->messageManager->produceJobMessage(self::QUEUE, $this->all());
    }
}
