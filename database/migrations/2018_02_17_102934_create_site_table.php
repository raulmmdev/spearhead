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
            $table->string('name')->nullable(false);
            $table->enum('status', ['ENABLED', 'DISABLED', 'BLOCKED'])->default('ENABLED');
            $table->timestamps();
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
