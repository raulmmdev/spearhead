<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;

class Tax implements Rule
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
        $label = $attribute .'[:attribute]';

        $this->validator = Validator::make($value, [
            'id' => 'required|integer',
            'name' => 'required|string',
            'rules' => ['required', 'array', 'min:1', new TaxLine($label)],
        ], [
            'id.required' => 'The '. $label .' field is required.',
            'name.required' => 'The '. $label .' field is required.',
            'rules.required' => 'The '. $label .' field is required.',

            'id.integer' => 'The '. $label .' must be an integer.',
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
        return $this->validator->messages()->first();
    }
}
