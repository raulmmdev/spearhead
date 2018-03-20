<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;

class TaxLine implements Rule
{
    private $validator;
    private $preffix;

    /**
     * Create a new rule instance.
     *
     * @param  string $parentAttribute
     * @return void
     */
    public function __construct(string $preffix = null)
    {
        $this->preffix = $preffix;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->preffix = $this->preffix ?? $attribute;

        foreach ($value as $countryCode => $tax) {
            $label = $this->preffix .'['. $countryCode .']';

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate countries

            $this->validator = Validator::make([
                $attribute => $countryCode,
                'validCountryCodes' => ['US', 'NL'],
            ], [
                $attribute => 'in_array:validCountryCodes.*'
            ], [
                $attribute .'.in_array' => 'The '. $label .' is not a valid country code.',
            ]);

            if ($this->validator->fails()) {
                return false;
            }

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate tax

            $this->validator = Validator::make([
                $attribute => $tax,
            ], [
                $attribute => 'numeric|min:0'
            ], [
                $attribute .'.numeric' => 'The '. $label .' must be a number.'
            ]);

            if ($this->validator->fails()) {
                return false;
            }
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
        return $this->validator->messages()->first();
    }
}
