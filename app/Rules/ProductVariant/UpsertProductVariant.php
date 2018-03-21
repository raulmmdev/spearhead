<?php

namespace App\Rules\ProductVariant;

use App\Model\Entity\ProductVariant;
use App\Rules\Attribute;
use App\Rules\Image;
use App\Rules\Locale;
use App\Rules\Tax;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ExtendedRule;
use Validator;

class UpsertProductVariant implements Rule
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
        $label = $attribute .'[:attribute]';

        foreach ($value as $entry) {
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Validate each entry

            $rules = [
                'product_id' => ['required', 'integer'],
                'sku_number' => 'required|string|between:3,50',
                'gtin' => 'nullable|string|between:3,50',
                'product_image_urls' => ['required', 'array', 'min:1', new Image],
                'sale_price' => 'required|numeric|min:0',
                'retail_price' => 'required|numeric|min:0',
                'stock' => 'integer|min:0',
                'cashback' => 'integer|min:0',
                'weight' => 'nullable|numeric|min:0',
                'weight_unit' => 'required|string',
                'attributes' => ['required', 'array', 'min:1', new Attribute],
            ];

            $exists = ProductVariant::where('site_id', $this->site->id)->where('source_id', (int) $entry['product_id'])->exists();

            if ($exists) {
                array_push($rules['product_id'], ExtendedRule::exists('product_variant', 'source_id')->where(function ($query) {
                    $query->where('site_id', $this->site->id);
                }));
            } else {
                array_push($rules['product_id'], ExtendedRule::unique('product_variant', 'source_id')->where(function ($query) {
                    $query->where('site_id', $this->site->id);
                }));
            }

            $this->validator = Validator::make($entry, $rules, [
                'product_id.required' => 'The '. $label .' field is required.',
                'product_id.integer' => 'The '. $label .' must be an integer.',
                'product_id.exists' => 'The '. $label .' is invalid. (Does not exists in DB)',
                'product_id.unique' => 'The '. $label .' has already been taken.',
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
