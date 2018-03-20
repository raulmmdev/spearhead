<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;

class Image implements Rule
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
        $imageUrls = array_values($value);

        foreach ($imageUrls as $i => $entry) {
            $label = $attribute .'['. $i .']';

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate data

            $this->validator = Validator::make($entry, [
                'url' => 'required|url',
                'main' => 'required|boolean'
            ], [
                'url.url' => 'The '. $label .'[:attribute] format is invalid.',
                'main.boolean' => 'The '. $label .'[:attribute] field must be true or false.',
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
