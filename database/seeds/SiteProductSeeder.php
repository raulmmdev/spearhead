<?php

use App\Model\Entity\Site;
use App\Model\Entity\SiteProduct;
use App\Model\Entity\SiteProductVariant;
use Illuminate\Database\Seeder;

/**
 * SiteProductSeeder
 */
class SiteProductSeeder extends Seeder
{
    //------------------------------------------------------------------------------------------------------------------
    // PROPERTIES
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Path where JSON file will be placed
     *
     * @var string
     */
    const JSON = 'seeds/json/vinq-6205-541/products-57.json';

    //------------------------------------------------------------------------------------------------------------------
    // PUBLIC METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $site = Site::find(Site::pluck('id')[0]);

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Clean up DB

        SiteProduct::where('site_id', $site->id)->delete();

        // - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
        // Process products

        $products = $this->loadJson();

        if (is_array($products) && count($products)) {
            foreach ($products as $prod) {
                //  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -
                // Product

                $product = new SiteProduct();
                $product->source_id = $prod['product_id'];
                $product->sku_number = $prod['sku_number'];
                $product->name = $prod['product_name'];
                $product->images = isset($prod['product_image_urls']) ? json_encode($prod['product_image_urls']) : null;
                $product->sale_price = $prod['sale_price'];
                $product->retail_price = $prod['retail_price'];
                $product->stock = $prod['stock'];
                $product->cashback = $prod['cashback'] ?? config('qwindo.cashback.product');
                $product->status = $prod['status'] ?? SiteProduct::STATUS_ENABLED;

                $product->gtin = $prod['gtin'];
                $product->attributes = json_encode($prod['attributes']);
                $product->weight = json_encode([
                    'value' => $prod['weight'],
                    'unit' => $prod['weight_unit'],
                ]);

                $product->tax = json_encode($prod['tax']);
                $product->brand = $prod['brand'];
                $product->short_description = json_encode($prod['short_product_description']);
                $product->long_description = json_encode($prod['long_product_description']);
                $product->metadata = json_encode($prod['metadata']);
                $product->is_downloadable = (bool) $prod['downloadable'];

                $product->site()->associate($site);
                $product->save();

                //  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -  -
                // Variants

                if (isset($prod['variants']) && is_array($prod['variants']) && count($prod['variants'])) {
                    foreach ($prod['variants'] as $var) {
                        $variant = new SiteProductVariant();
                        $variant->source_id = $var['product_id'];
                        $variant->sku_number = $var['sku_number'];
                        $variant->name = sprintf('%s (#%s)', $product->name, $var['sku_number']);
                        $variant->images = isset($var['product_image_urls']) ? json_encode($var['product_image_urls']) : null;
                        $variant->sale_price = $var['sale_price'];
                        $variant->retail_price = $var['retail_price'];
                        $variant->stock = $var['stock'];
                        $variant->cashback = $var['cashback'] ?? config('qwindo.cashback.product');
                        $variant->status = $var['status'] ?? SiteProduct::STATUS_ENABLED;

                        $variant->gtin = $var['gtin'];
                        $variant->attributes = json_encode($var['attributes']);
                        $variant->weight = json_encode([
                            'value' => $var['weight'],
                            'unit' => $var['weight_unit'],
                        ]);

                        $variant->site()->associate($site);
                        $variant->product()->associate($product);
                        $variant->save();
                    }
                }
            }
        }
    }

    //------------------------------------------------------------------------------------------------------------------
    // PRIVATED METHODS
    //------------------------------------------------------------------------------------------------------------------

    /**
     * Loads the categories.json
     *
     * @access private
     * @return array
     */
    private function loadJson() : array
    {
        return json_decode(file_get_contents(database_path(self::JSON)), true);
    }

    //------------------------------------------------------------------------------------------------------------------
    //------------------------------------------------------------------------------------------------------------------
}
