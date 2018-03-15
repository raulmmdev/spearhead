<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteCategoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_category', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned()->comment('Site id');
            $table->integer('parent_id')->unsigned()->nullable(true)->comment('Parent site category id');
            $table->integer('source_id')->unsigned()->nullable(true)->comment('Shop/Source category id');
            $table->integer('cashback')->nullable(true)->default(10)->comment('Cashback applied to this category');
            $table->jsonb('title')->nullable(false)->comment('Category title as JSON document with locale as key and text as value');
            $table->enum('status', ['ENABLED', 'DISABLED'])->default('ENABLED')->comment('Enable/Disable');
            $table->timestamps();

            $table->foreign('site_id')->references('id')->on('site')->onDelete('cascade');

            $table->index(['site_id', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_category');
    }
}
