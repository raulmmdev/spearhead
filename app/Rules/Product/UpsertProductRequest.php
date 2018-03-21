<?php

namespace App\Rules\Product;

use App\Model\Entity\Product;
use App\Rules\Attribute;
use App\Rules\Id;
use App\Rules\Image;
use App\Rules\Locale;
use App\Rules\Metadata;
use App\Rules\Tax;
use App\Rules\ProductVariant\UpsertProductVariant;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule as ExtendedRule;
use Validator;

class UpsertProductRequest implements Rule
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
        $validStatuses = [Product::STATUS_ENABLED, Product::STATUS_DISABLED];

        $rules = [
            'sku_number' => 'required|string|between:3,100',
            'gtin' => 'nullable|string|between:1,100',
            'brand' => 'nullable|string|between:2,100',
            'product_name' => 'required|string|between:3,200',
            'product_image_urls' => ['required', 'array', 'min:1', new Image],
            'sale_price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'tax' => ['required', 'array', 'min:1', new Tax],
            'stock' => 'integer|min:0',
            'cashback' => 'integer|min:0',
            'status' => ['string', ExtendedRule::in($validStatuses)],
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'required|string',
            'attributes' => ['required', 'array', 'min:1', new Attribute],
            'metadata' => ['required', 'array', 'min:3', new Metadata],
            'short_product_description' => ['required', 'array', 'min:1', new Locale],
            'long_product_description' => ['required', 'array', 'min:1', new Locale],
            'downloadable' => 'required|boolean',
            'category_ids' => ['required', 'array', 'min:1', new Id],
            'variants' => ['array', new UpsertProductVariant],
        ];

        $exists = Product::where('site_id', $this->site->id)->where('source_id', (int) $value['product_id'])->exists();

        if ($exists) {
            $rules['product_id'] = ['required', 'integer', ExtendedRule::exists('product', 'source_id')->where(function ($query) {
                $query->where('site_id', $this->site->id);
            })];
        } else {
            $rules['product_id'] = ['required', 'integer', ExtendedRule::unique('product', 'source_id')->where(function ($query) {
                $query->where('site_id', $this->site->id);
            })];
        }

        $this->validator = Validator::make($value, $rules, [
            'product_id.required' => 'The :attribute field is required.',
            'product_id.integer' => 'The :attribute must be an integer.',
            'product_id.exists' => 'The selected :attribute is invalid. (Does not exists in DB)',
            'product_id.unique' => 'The :attribute has already been taken.',
        ]);

        if ($this->validator->fails()) {
            return false;
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
