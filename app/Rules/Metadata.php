<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Validation\Rule as ExtendedRule;

class Metadata implements Rule
{
    private $validator;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        $validMetadatas = config('qwindo.metadatas');

        foreach ($value as $key => $data) {
            $label = $attribute .'['. $key .']';

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate the keys

            $this->validator = Validator::make([
                'meta' => $key,
            ], [
                'meta' => ['string', 'min:1', ExtendedRule::in($validMetadatas)],
            ], [
                'meta.string' => 'The '. $label .' must be a string.',
                'meta.min'  => 'The '. $label .' must be at least :min.',
                'meta.in' => 'The '. $label .' is not a valid meta tag.',
            ]);

            if ($this->validator->fails()) {
                return false;
            }

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate the data

            $this->validator = Validator::make([
                'meta' => $data,
            ], [
                'meta' => ['required', new Locale($label, 'required|string|between:3,200')],
            ], [
                'meta.required' => 'The '. $label .' requires a localized key.',
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
