<?php

namespace App\Model\Entity\Repository;

use App\Model\Entity\Product;
use App\Model\Entity\ProductVariant;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * ProductRepository
 */
class ProductRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @access public
     * @return string
     */
    public function model()
    {
        return 'App\Model\Entity\Product';
    }

    /**
     * Disable product
     */
    public function disableProduct(Product $product)
    {
        Product::where([
            'site_id' => $product->site_id,
            'id' => $product->id,
        ])->update([
            'status' => ProductVariant::STATUS_DISABLED,
        ]);
    }

    /**
     * Disable product variants
     */
    public function disableProductVariants(Product $product)
    {
        ProductVariant::where([
            'site_id' => $product->site_id,
            'product_id' => $product->id,
        ])->update([
            'status' => ProductVariant::STATUS_DISABLED,
        ]);
    }
}
