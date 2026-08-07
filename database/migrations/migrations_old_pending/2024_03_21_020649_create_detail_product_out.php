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
        Schema::create('detail_product_out', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_product_out');
            $table->foreignId('id_detail_product');
            $table->foreignId('id_serial_product');
            $table->integer('qty');
            $table->enum('warehouse',['BDG','BKS']);
            $table->integer('price');
            $table->integer('amount');
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
        Schema::dropIfExists('detail_product_out');
    }
};
