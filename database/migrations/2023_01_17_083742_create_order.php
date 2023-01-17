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
        Schema::create('order', function (Blueprint $table) {
            $table->bigIncrements('order_id');
            $table->timestamp('order_date');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('table_id');
            $table->string('customer_name', 100);
            $table->enum('status', ['pending','paid']);
            $table->timestamps();

            $table->foreign('user_id')->references('user_id')->on('user');
            $table->foreign('table_id')->references('table_id')->on('table');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order');
    }
};
