<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteAttribute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_attribute', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('site_id')->unsigned();
            $table->enum('name', ['PAYMENT_METHODS', 'SUPPORT_EMAIL', 'SUPPORT_PHONE', 'CA_CODE', 'MCC_CODE']);
            $table->string('value')->nullable(true);
            $table->timestamps();

            $table
                ->foreign('site_id')
                ->references('id')
                ->on('site')
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
        Schema::dropIfExists('site_attribute');
    }
}
