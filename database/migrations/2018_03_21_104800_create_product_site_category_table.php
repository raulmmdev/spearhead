<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSiteCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_site_category', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned()->comment('Product Id');
            $table->integer('site_category_id')->unsigned()->comment('Site Category Id');

            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            $table->foreign('site_category_id')->references('id')->on('site_category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_site_category');
    }
}
