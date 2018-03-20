<?php

namespace App\Business\Product;

use App\Business\Job\CreateProductJob;
use App\Business\Job\DeleteProductJob;
use App\Model\Entity\Repository\ApiFeatureRepository;
use App\Model\Entity\Site;
use App\Model\Entity\Product;
use App\Model\Entity\ProductVariant;

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

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    public function __construct(ApiFeatureRepository $apiFeatureRepository)
    {
        $this->apiFeatureRepository = $apiFeatureRepository;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * Create the product from a job.
     *
     * @access public
     * @param CreateProductJob $job
     * @return CreateProductJob | null
     */
    public function createFromJob(CreateProductJob $job) : ?CreateProductJob
    {
        try {
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Get the site

            $site = $this->apiFeatureRepository->find($job->data['user']['id'])->site;

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Process product

            $product = new Product;
            $product->source_id = $job->data['product']['product_id'];
            $product->sku_number = $job->data['product']['sku_number'];
            $product->name = $job->data['product']['product_name'];
            $product->images = json_encode($job->data['product']['product_image_urls']);
            $product->sale_price = $job->data['product']['sale_price'];
            $product->retail_price = $job->data['product']['retail_price'];
            $product->stock = $job->data['product']['stock'];
            $product->cashback = $job->data['product']['cashback'] ?? config('qwindo.cashbak.product');
            $product->status = $job->data['product']['status'] ?? Product::STATUS_ENABLED;
            $product->gtin = $job->data['product']['gtin'];
            $product->attributes = json_encode($job->data['product']['attributes']);
            $product->tax = json_encode($job->data['product']['tax']);
            $product->brand = $job->data['product']['brand'];
            $product->short_description = json_encode($job->data['product']['short_product_description']);
            $product->long_description = json_encode($job->data['product']['long_product_description']);
            $product->metadata = json_encode($job->data['product']['metadata']);
            $product->is_downloadable = (boolean) $job->data['product']['downloadable'];
            $product->weight = json_encode([
                'value' => $job->data['product']['weight'],
                'unit' => $job->data['product']['weight_unit'],
            ]);
            $product->site()->associate($site);
            $product->save();

            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            // Process variants

            if (isset($job->data['product']['variants']) && is_array($job->data['product']['variants']) && count($job->data['product']['variants'])) {
                foreach ($job->data['product']['variants'] as $entry) {
                    $variant = new ProductVariant;
                    $variant->source_id = $entry['product_id'];
                    $variant->sku_number = $entry['sku_number'];
                    $variant->name = sprintf('%s (%s)', $job->data['product']['product_name'], $entry['sku_number']);
                    $variant->images = json_encode($entry['product_image_urls']);
                    $variant->sale_price = $entry['sale_price'];
                    $variant->retail_price = $entry['retail_price'];
                    $variant->stock = $entry['stock'];
                    $variant->cashback = $entry['cashback'] ?? config('qwindo.cashbak.product');
                    $variant->status = $entry['status'] ?? Product::STATUS_ENABLED;
                    $variant->gtin = $entry['gtin'];
                    $variant->attributes = json_encode($entry['attributes']);
                    $variant->weight = json_encode([
                        'value' => $entry['weight'],
                        'unit' => $entry['weight_unit'],
                    ]);
                    $variant->site()->associate($site);
                    $variant->product()->associate($product);
                    $variant->save();
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
            // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

            $product = Product::where('source_id', $job->data['product']['product_id'])->first();

            if ($product !== null) {
                $product->update([
                    'status' => Product::STATUS_DISABLED,
                ]);
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
