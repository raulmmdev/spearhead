<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rule as ExtendedRule;

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
        $validLocales = config('qwindo.locales');

        foreach ($value as $locale => $data) {
            $label = $this->parentAttribute .'['. $locale .']';

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate the locale

            $this->validator = Validator::make([
                'locale' => $locale,
            ], [
                'locale' => ['string', ExtendedRule::in($validLocales)],
            ], [
                'locale.string' => 'The '. $label .' must be a string.',
                'locale.in' => 'The '. $label .' is not a valid locale code.',
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
