<?php

namespace App\Rules\Site;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rule as ExtendedRule;
use Validator;

class SiteRuleset implements Rule
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
            'portal_payment_methods' => ['required', 'min:1', 'array'],
            'portal_payment_methods.*' => ['string', ExtendedRule::in(config('qwindo.system')['valid_payment_methods'])],
            'portal_url' => ['required', 'string', 'url'],
            'site_apikey' => ['required', 'string'],
            'portal_description' => ['required', 'string'],
            'site_id' => ['required', 'integer'],
            'supportemail' => ['nullable', 'email'],
            'ca_code' => ['required', 'string'],
            'support_phone' => ['string'],
            'mcc' => ['required', 'string'],
            'site_status' => ['required', ExtendedRule::in($validStatuses)]
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
