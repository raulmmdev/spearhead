<?php

namespace App\Http\Requests;

use \Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ApiRequest extends FormRequest
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    const QUEUE_SITE = 'site';
    const QUEUE_CATEGORY = 'category';
    const QUEUE_PRODUCT = 'product';
    const QUEUE_IMAGE = 'image';

    const ACTION_UPSERT = 'UPSERT';

    const ACTION_CREATE = 'CREATE';
    const ACTION_UPDATE = 'UPDATE';
    const ACTION_DELETE = 'DELETE';

    /**
     * @access protected
     * @var $apiResponseManager
     */
    protected $apiResponseManager;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    public function __construct(
        \App\Business\Api\Response\ApiResponseManager $apiResponseManager
    ) {
        $this->apiResponseManager = $apiResponseManager;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Handle a failed validation attempt.
     *
     * @access protected
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = $this
            ->apiResponseManager
            ->formatValidationErrors(
                $validator->errors()
            );

        throw new HttpResponseException(response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY));
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
