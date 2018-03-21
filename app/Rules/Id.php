<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;

class Id implements Rule
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
        foreach ($value as $i => $entry) {
            $label = $attribute .'['. $i .']';

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate data

            $this->validator = Validator::make([
                'id' => $entry
            ], [
                'id' => 'required|integer|min:1',
            ], [
                'id.required' => 'The '. $label .' is required.',
                'id.integer' => 'The '. $label .' must be an integer.',
                'id.min' => 'The '. $label .' must be at least :min.',
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
