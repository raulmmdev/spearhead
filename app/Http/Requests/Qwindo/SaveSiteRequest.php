<?php

namespace App\Http\Requests\Qwindo;

use App\Business\Api\Interfaces\ResolvableInterface;
use App\Business\Message\MessageManager;
use Illuminate\Foundation\Http\FormRequest;

class SaveSiteRequest extends FormRequest implements ResolvableInterface
{
    const QUEUE = 'site';

    protected $messageManager;

    public function __construct(MessageManager $messageManager)
    {
        $this->messageManager = $messageManager;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
        ];
    }

    public function resolve()
    {
        return $this->messageManager->produceJobMessage(self::QUEUE, $this->all());
    }
}
