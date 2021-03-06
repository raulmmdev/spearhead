<?php

namespace App\Rules\Product;

use App\Model\Entity\Product;
use App\Rules\Attribute;
use App\Rules\Image;
use App\Rules\Locale;
use App\Rules\Tax;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ExtendedRule;
use Validator;

class DeleteProductRequest implements Rule
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    private $validator;
    private $site;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->site = Auth::guard('api')->user()->site;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->validator = Validator::make($value, [
            'product_id' => ['required', 'integer', ExtendedRule::exists('product', 'source_id')->where(function ($query) {
                $query->where('site_id', $this->site->id);
            })],
        ], [
            'product_id.exists' => 'The selected :attribute is invalid or does not exists in DB.',
        ]);

        if ($this->validator->fails()) {
            return false;
        }

        return true;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->validator->errors()->first();
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
