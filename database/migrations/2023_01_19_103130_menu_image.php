<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_image', function (Blueprint $table) {
            $table->bigIncrements('menu_image_id');
            $table->unsignedBigInteger('menu_id');
            $table->String('menu_image_name');
            $table->timestamps();

            $table->foreign('menu_id')->references('menu_id')->on('menu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu');
    }
};
