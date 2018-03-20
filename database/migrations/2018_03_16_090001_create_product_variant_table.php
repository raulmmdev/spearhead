<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductVariantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variant', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->nullable(false)->unsigned()->comment('Site id');
            $table->integer('product_id')->nullable(false)->unsigned()->comment('Product id');
            $table->integer('source_id')->unsigned()->nullable(true)->comment('Shop/Source variant id');
            $table->string('sku_number')->nullable(true)->comment('SKU number');
            $table->string('gtin')->nullable(true)->comment('GTIN');
            $table->string('brand')->nullable(true)->comment('Brand name');
            $table->string('name')->nullable(false)->comment('Variant name');
            $table->jsonb('images')->nullable(true)->comment('Image URLs');
            $table->float('sale_price')->nullable(false)->default(0)->comment('Sale price');
            $table->float('retail_price')->nullable(false)->default(0)->comment('Sale price');
            $table->jsonb('tax')->nullable(true)->comment('Tax rules');
            $table->integer('stock')->nullable(false)->unsigned()->default(0)->comment('Stock level');
            $table->integer('cashback')->nullable(true)->default(10)->comment('Cashback applied to this variant');
            $table->enum('status', ['ENABLED', 'DISABLED'])->default('ENABLED')->comment('Enable/Disable');
            $table->jsonb('weight')->nullable(true)->comment('Weight information as JSON document with [value, unit] information');
            $table->jsonb('attributes')->nullable(true)->comment('Attributes');

            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('site')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variant');
    }
}
