<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApiFeatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_feature', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned();
            $table->string('login')->unique();
            $table->string('key');
            $table->enum('status', ['ENABLED', 'DISABLED', 'BLOCKED'])->default('ENABLED');
            $table->timestamps();

            //constraints
            $table
                ->foreign('site_id')
                ->references('id')->on('site')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_feature');
    }
}
