<?php

namespace App\Business\Product;

use App\Business\Job\DeleteProductJob;
use App\Business\Job\UpsertProductJob;
use App\Business\Product\Attribute\ProductAttributeManager;
use App\Business\ProductVariant\ProductVariantManager;
use App\Model\Entity\Product;
use App\Model\Entity\ProductAttribute;
use App\Model\Entity\ProductVariant;
use App\Model\Entity\Repository\ApiFeatureRepository;
use App\Model\Entity\Repository\ProductRepository;
use App\Model\Entity\Repository\ProductVariantRepository;

/**
 * ProductManager
 */
class ProductManager
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * ApiFeatureRepository container
     *
     * @access protected
     * @var ApiFeatureRepository
     */
    protected $apiFeatureRepository;

    /**
     * ProductAttributeManager container
     *
     * @access protected
     * @var ProductAttributeManager
     */
    protected $productAttributeManager;

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    public function __construct(
        ApiFeatureRepository $apiFeatureRepository,
        ProductRepository $productRepository,
        ProductVariantRepository $productVariantRepository,
        ProductAttributeManager $productAttributeManager,
        ProductVariantManager $productVariantManager
    ) {
        $this->apiFeatureRepository = $apiFeatureRepository;
        $this->productRepository = $productRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->productAttributeManager = $productAttributeManager;
        $this->productVariantManager = $productVariantManager;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Upsert the product from a job.
     *
     * @access public
     * @param UpsertProductJob $job
     * @return UpsertProductJob | null
     */
    public function upsertFromJob(UpsertProductJob $job) : ?UpsertProductJob
    {
        try {
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Get the site

            $site = $this->apiFeatureRepository->find($job->data['user']['id'])->site;

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Exist current product?

            $product = $this->productRepository->findWhere([
                'site_id' => $site->id,
                'source_id' => $job->data['product']['product_id'],
            ])->first();

            if ($product === null) {
                $product = new Product;
                $product->site()->associate($site);
                $product->setSourceId($job->data['product']['product_id']);
            }

            $product->setSkuNumber($job->data['product']['sku_number']);
            $product->setGtin($job->data['product']['gtin']);
            $product->setName($job->data['product']['product_name']);
            $product->setImages(json_encode($job->data['product']['product_image_urls']));
            $product->setSalePrice($job->data['product']['sale_price']);
            $product->setRetailPrice($job->data['product']['retail_price']);
            $product->setStock($job->data['product']['stock']);
            $product->setCashback($job->data['product']['cashback'] ?? config('qwindo.cashbak.product'));
            $product->setStatus($job->data['product']['status'] ?? Product::STATUS_ENABLED);
            $product->setWeight(json_encode([
                'value' => $job->data['product']['weight'],
                'unit' => $job->data['product']['weight_unit'],
            ]));
            $product->setCustomAttributes(json_encode($job->data['product']['attributes']));
            $product->save();

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Product Attributes

            $this->productAttributeManager->setAttribute(
                $product,
                ProductAttribute::ATTRIBUTE_TAX,
                json_encode($job->data['product']['tax'])
            );

            $this->productAttributeManager->setAttribute(
                $product,
                ProductAttribute::ATTRIBUTE_BRAND,
                $job->data['product']['brand']
            );

            $this->productAttributeManager->setAttribute(
                $product,
                ProductAttribute::ATTRIBUTE_SHORT_DESCRIPTION,
                json_encode($job->data['product']['short_product_description'])
            );

            $this->productAttributeManager->setAttribute(
                $product,
                ProductAttribute::ATTRIBUTE_LONG_DESCRIPTION,
                json_encode($job->data['product']['long_product_description'])
            );

            $this->productAttributeManager->setAttribute(
                $product,
                ProductAttribute::ATTRIBUTE_METADATA,
                json_encode($job->data['product']['metadata'])
            );

            $this->productAttributeManager->setAttribute(
                $product,
                ProductAttribute::ATTRIBUTE_IS_DOWNLOADABLE,
                (boolean) $job->data['product']['downloadable'] ? 1 : 0
            );

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Process variants

            if (array_has($job->data['product'], 'variants')) {
                foreach ($job->data['product']['variants'] as $entry) {
                    $this->productVariantManager->setVariant($site, $product, $entry);
                }
            }

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $job->setObject($product);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());

            $job->setErrors([
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $job;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Delete the product from a job.
     *
     * @access public
     * @param DeleteProductJob $job
     * @return DeleteProductJob | null
     */
    public function deleteFromJob(DeleteProductJob $job) : ?DeleteProductJob
    {
        try {
            $site = $this->apiFeatureRepository->find($job->data['user']['id'])->site;

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $product = $this->productRepository->with('variants')->findWhere([
                'site_id' => $site->id,
                'source_id' => $job->data['product']['product_id'],
            ])->first();

            if ($product !== null) {
                // Disable the product
                $this->productRepository->update([
                    'status' => Product::STATUS_DISABLED,
                ], $product->getId());

                // Disable the product variants
                if ($product->variants) {
                    foreach ($product->variants as $variant) {
                        $this->productVariantRepository->update([
                            'status' => ProductVariant::STATUS_DISABLED,
                        ], $variant->getId());
                    }
                }
            }

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $job->setObject($product);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());

            $job->setErrors([
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return $job;
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
