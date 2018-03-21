<?php

namespace App\Rules\Site;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rule as ExtendedRule;
use Validator;

class MerchantRuleset implements Rule
{
    const STATUS_ACTIVE = 'active';

    /**
     * @access private
     * $validator
     * @var Validator
     */
    private $validator;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validStatuses = [self::STATUS_ACTIVE];

        $this->validator = Validator::make($value, [
            'country' => ['required', 'regex:/(^('.config('qwindo.system')['valid_countries'].')$)/u'],
            'email_address' => ['required', 'email'],
            'name' => ['required', 'string'],
            'merchant_id' => ['required', 'integer'],
            'merchant_status' => ['required', ExtendedRule::in($validStatuses)]
        ]);

        if ($this->validator->fails()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->validator->errors();
    }
}