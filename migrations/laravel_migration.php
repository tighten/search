<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSearchIdtoAccountId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('searches', function ($table) {
            $table->increments('id')->unsigned();
            $table->integer('account_id')->nullable()->unsigned();
            $table->integer('order')->nullable();
            $table->string('title')->nullable();
            $table->string('content_type')->nullable();
            $table->text('criteria')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('searches');
    }
}
