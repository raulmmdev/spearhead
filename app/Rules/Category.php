<?php

namespace App\Rules;

use Validator;
use Illuminate\Contracts\Validation\Rule;
use App\Rules\Locale;

class Category implements Rule
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
        $tree = array_values($value);

        foreach ($tree as $category) {
            $this->validator = Validator::make($category, [
                'id' => 'required|integer',
                'title' => ['required', 'array', new Locale('required|string|between:3,200')],
                'cashback' => 'integer|min:1',
                'children' => ['array', new Category],
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
        return $this->validator->errors()->first();
    }
}
