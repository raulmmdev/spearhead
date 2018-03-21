<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;

class Locale implements Rule
{
    private $validator;
    private $validationRules;
    private $preffix;

    /**
     * Create a new rule instance.
     *
     * @param  string $validationRules
     * @return void
     */
    public function __construct(string $preffix = null, string $validationRules = null)
    {
        $this->preffix = $preffix;
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
        $this->preffix = $this->preffix ?? $attribute;

        $validLocales = array_keys(config('qwindo.locales'));

        foreach ($value as $locale => $string)
        {
            $label = $this->preffix .'['. $locale .']';

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate the locale

            $this->validator = Validator::make([
                'locale' => $locale,
                'validLocales' => $validLocales,
            ], [
                'locale' => 'in_array:validLocales.*'
            ], [
                'locale.in_array' => 'The '. $label .' is not a valid locale.',
            ]);

            if ($this->validator->fails()) {
                return false;
            }

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate the string

            $this->validator = \Validator::make([
                'text' => $string,
            ], [
                'text' => $this->validationRules ?? 'required|string',
            ], [
                'text.required' => 'The '. $label .' is required.',
                'text.string'   => 'The '. $label .' must be a string.',
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
