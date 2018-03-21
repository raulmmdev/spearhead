<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_attribute', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned()->comment('Product Id');
            $table->enum('name', ['BRAND', 'TAX', 'SHORT_DESCRIPTION', 'LONG_DESCRIPTION', 'METADATA', 'IS_DOWNLOADABLE'])->comment('Product attribute name');
            $table->mediumText('value')->nullable(true)->comment('Product attribute value');
            $table->timestamps();

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
        Schema::dropIfExists('product_attribute');
    }
}
