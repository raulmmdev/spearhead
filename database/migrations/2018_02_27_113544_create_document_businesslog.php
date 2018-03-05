<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Jenssegers\Mongodb\Schema\Blueprint;

class CreateDocumentBusinesslog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::connection('mongodb')->hasTable('business_log')) {
            Schema::connection('mongodb')->create('business_log', function(Blueprint $collection) {
                $collection->bigIncrements('id')->unique();
                $collection->bigInteger('site_id');
                $collection->bigInteger('feed_id');
                $collection->enum('http_type', ['PUSH', 'PULL']);
                $collection->enum('level_type', ['INFO', 'ERROR', 'EXCEPTION']);
                $collection->enum('user_type', ['MERCHANT', 'ADMIN']);
                $collection->enum('element_type', ['SITE', 'PRODUCT', 'CATEGORY', 'IMAGE']);
                $collection->string('title', 256);
                $collection->mediumText('message');
                $collection->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mongodb')->drop('business_log');
    }
}
