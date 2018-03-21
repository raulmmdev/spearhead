<?php

namespace App\Rules\Category;

use App\Model\Entity\SiteCategory;
use App\Rules\Locale;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ExtendedRule;
use Validator;

class UpsertRuleset implements Rule
{
    private $validator;
    private $site;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->site = Auth::guard('api')->user()->site;
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

        $rules = [
            'title' => ['required', 'array', 'min:1', new Locale(null, 'required|string|between:3,200')],
            'cashback' => 'integer|min:1',
            'children' => ['array', new UpsertRuleset],
        ];

        $exists = SiteCategory::where('site_id', $this->site->id)->where('source_id', $tree[0]['id'])->exists();

        if ($exists) {
            $rules['id'] = ['required', 'integer', ExtendedRule::exists('site_category', 'source_id')->where(function ($query) {
                $query->where('site_id', $this->site->id);
            })];
        } else {
            $rules['id'] = ['required', 'integer', ExtendedRule::unique('site_category', 'source_id')->where(function ($query) {
                $query->where('site_id', $this->site->id);
            })];
        }

        foreach ($tree as $category) {
            $this->validator = Validator::make($category, $rules);

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
