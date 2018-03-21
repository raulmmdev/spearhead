<?php

namespace App\Business\ProductVariant;

use App\Model\Entity\Site;
use App\Model\Entity\Product;
use App\Model\Entity\ProductVariant;
use App\Model\Entity\Repository\ApiFeatureRepository;
use App\Model\Entity\Repository\ProductVariantRepository;

/**
 * ProductVariantManager
 */
class ProductVariantManager
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * ProductVariantRepository container
     *
     * @access protected
     * @var ProductVariantRepository
     */
    protected $productVariantRepository;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    public function __construct(ProductVariantRepository $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Set variant
     *
     * @access public
     * @param Site $site
     * @param Product $product
     * @param array $entry
     * @return void
     */
    public function setVariant(Site $site, Product $product, array $entry) : void
    {
        $variant = $this->productVariantRepository->findWhere([
            'site_id' => $site->getId(),
            'product_id' => $product->getId(),
            'source_id' => $entry['product_id'],
        ])->first();

        if ($variant === null) {
            $variant = new ProductVariant;
            $variant->site()->associate($site);
            $variant->product()->associate($product);
        }

        $variant->setSourceId($entry['product_id']);
        $variant->setSkuNumber($entry['sku_number']);
        $variant->setGtin($entry['gtin']);
        $variant->setName(sprintf('%s (%s)', $product->getName(), $entry['sku_number']));
        $variant->setImages(json_encode($entry['product_image_urls']));
        $variant->setSalePrice($entry['sale_price']);
        $variant->setRetailPrice($entry['retail_price']);
        $variant->setStock($entry['stock']);
        $variant->setCashback($entry['cashback'] ?? config('qwindo.cashbak.product'));
        $variant->setStatus($entry['status'] ?? Product::STATUS_ENABLED);
        $variant->setWeight(json_encode([
            'value' => $entry['weight'],
            'unit' => $entry['weight_unit'],
        ]));
        $variant->setCustomAttributes(json_encode($entry['attributes']));
        $variant->save();
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
