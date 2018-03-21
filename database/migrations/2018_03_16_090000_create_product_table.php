<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->nullable(false)->unsigned()->comment('Site id');
            $table->integer('source_id')->unsigned()->nullable(true)->comment('Shop/Source product id');

            // Product level
            $table->string('sku_number')->nullable(true)->comment('SKU number');
            $table->string('gtin')->nullable(true)->comment('GTIN');
            $table->string('name')->nullable(false)->comment('Product name');
            $table->jsonb('images')->nullable(true)->comment('Image URLs');
            $table->float('sale_price')->nullable(false)->default(0)->comment('Sale price');
            $table->float('retail_price')->nullable(false)->default(0)->comment('Sale price');
            $table->integer('stock')->nullable(false)->unsigned()->default(0)->comment('Stock level');
            $table->integer('cashback')->nullable(true)->default(10)->comment('Cashback applied to this product');
            $table->enum('status', ['ENABLED', 'DISABLED'])->default('ENABLED')->comment('Enable/Disable');
            $table->jsonb('weight')->nullable(true)->comment('Weight information as JSON document with [value, unit] information');
            $table->jsonb('custom_attributes')->nullable(true)->comment('Attributes');

            // Product Attributes level
            //$table->jsonb('tax')->nullable(true)->comment('Tax rules');
            //$table->string('brand')->nullable(true)->comment('Brand name');
            //$table->jsonb('short_description')->nullable(false)->comment('Short description as JSON document with locale as key and text as value');
            //$table->jsonb('long_description')->nullable(false)->comment('Short description as JSON document with locale as key and text as value');
            //$table->jsonb('metadata')->nullable(false)->comment('Metadata as JSON document with locale as key and text as value');
            //$table->boolean('is_downloadable')->nullable(false)->default(false)->comment('True/False');

            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('site')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product');
    }
}
