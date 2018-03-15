<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;

class Locale implements Rule
{
    private $validator;
    private $validationRules;

    /**
     * Create a new rule instance.
     *
     * @param  string $validationRules
     * @return void
     */
    public function __construct(string $validationRules = null)
    {
        $this->validationRules = $validationRules;
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
        $validLocales = array_keys(config('qwindo.locales'));

        foreach ($value as $locale => $string)
        {
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate the locale

            $this->validator = Validator::make([
                $attribute => $locale,
                'validLocales' => ['nl_NL', 'en_GB'],
            ], [
                $attribute => 'in_array:validLocales.*'
            ], [
                $attribute .'.in_array' => 'The :attribute is not a valid locale.',
            ]);

            if ($this->validator->fails()) {
                return false;
            }

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate the string

            $this->validator = \Validator::make([
                $attribute => $string,
            ], [
                $attribute => $this->validationRules ?? 'required|string',
            ], [
                $attribute .'.required' => 'The :attribute.'. $locale .' field is required.',
                $attribute .'.string'   => 'The :attribute.'. $locale .' must be a string.',
                $attribute .'.between'  => 'The :attribute.'. $locale .' must be between :min and :max characters.',
            ]);

            if ($this->validator->fails()) {
                return false;
            }

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
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
