<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;

class AttributeLocale implements Rule
{
    private $validator;
    private $parentAttribute;
    private $validationRules;

    /**
     * Create a new rule instance.
     *
     * @param  string $parentAttribute
     * @return void
     */
    public function __construct(string $parentAttribute, string $validationRules = null)
    {
        $this->parentAttribute = $parentAttribute;
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

        foreach ($value as $locale => $data)
        {
            $label = $this->parentAttribute .'['. $locale .']';

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

            $this->validator = \Validator::make($data, [
                'label' => 'required',
                'value' => 'required',
            ], [
                'label.required' => 'The '. $label .'[label] is required.',
                'value.required' => 'The '. $label .'[value] is required.',
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
