<?php

namespace App\Rules\Site;

use Illuminate\Contracts\Validation\Rule;
use Validator;

class CreateRuleset implements Rule
{
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
        $this->validator = Validator::make($value, [
            /*'site' => ['required', 'array', 'min:9', new SiteRuleset],*/
            'merchant' => ['required', 'array', 'min:5', new MerchantRuleset],
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
        return $this->validator->errors()->first();
    }
}
