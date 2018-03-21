<?php

namespace App\Rules\SiteCategory;

use App\Model\Entity\SiteCategory;
use App\Rules\Locale;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ExtendedRule;
use Validator;

class UpsertSiteCategoryRequest implements Rule
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    private $validator;
    private $site;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->site = Auth::guard('api')->user()->site;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $rules = [
            'title' => ['required', 'array', 'min:1', new Locale(null, 'required|string|between:3,200')],
            'cashback' => 'integer|min:0',
            'children' => ['array', 'min:1', new UpsertSiteCategoryRequest],
        ];

        foreach ($value as $category) {
            $exists = SiteCategory::where('site_id', $this->site->id)->where('source_id', (int) $category['id'])->exists();

            if ($exists) {
                $rules['id'] = ['required', 'integer', ExtendedRule::exists('site_category', 'source_id')->where(function ($query) {
                    $query->where('site_id', $this->site->id);
                })];
            } else {
                $rules['id'] = ['required', 'integer', ExtendedRule::unique('site_category', 'source_id')->where(function ($query) {
                    $query->where('site_id', $this->site->id);
                })];
            }

            $this->validator = Validator::make($category, $rules, [
                'id.required' => 'The '. $attribute .'[:attribute] is required.',
                'id.integer' => 'The '. $attribute .'[:attribute] must be an integer.',
                'title.required' => 'The '. $attribute .'[:attribute] is required.',
                'title.array' => 'The '. $attribute .'[:attribute] is required.',
                'title.min' => 'The '. $attribute .'[:attribute] must be at least :min characters.',
                'cashback.integer' => 'The '. $attribute .'[:attribute] must be an integer.',
                'cashback.min' => 'The '. $attribute .'[:attribute] must be at least :min.',
                'children.array' => 'The '. $attribute .'[:attribute] must be an array.',
                'children.min' => 'The '. $attribute .'[:attribute] must be at least :min characters.',
            ]);

            if ($this->validator->fails()) {
                return false;
            }
        }

        return true;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->validator->errors()->first();
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
