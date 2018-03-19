<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        echo('Creating site table '.PHP_EOL);
        Schema::create('site', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('name')->nullable(false);
            $table->string('url');
            $table->string('api_key');
            $table->string('native_id')->unique();
            $table->enum('status', ['ENABLED', 'DISABLED', 'BLOCKED'])->default('ENABLED');
            $table->timestamps();

            //constraints
            $table
                ->foreign('user_id')
                ->references('id')->on('user')
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
        Schema::dropIfExists('site');
    }
}
