<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;

class Attribute implements Rule
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
        foreach ($value as $key => $data) {
            $label = $attribute .'['. $key .']';

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate the keys

            $this->validator = Validator::make([
                'attribute' => $key,
            ], [
                'attribute' => 'required|string',
            ], [
                'attribute.required' => 'The '. $attribute .' field contains an empty key.',
            ]);

            if ($this->validator->fails()) {
                return false;
            }

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate the data

            $this->validator = Validator::make([
                $key => $data,
            ], [
                $key => ['required', new AttributeLocale($attribute .'['. $key .']', 'string')],
            ], [
                $key .'.required' => 'The '. $label .' requires a localized key.',
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
