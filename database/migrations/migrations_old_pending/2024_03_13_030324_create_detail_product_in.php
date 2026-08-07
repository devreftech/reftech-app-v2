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
        Schema::create('detail_product_in', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_product_in');
            $table->foreignId('id_detail_product');
            $table->integer('qty');
            $table->enum('warehouse',['BDG','BKS']);
            $table->integer('modal')->nullable();
            $table->integer('amount')->nullable();
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
        Schema::dropIfExists('detail_product_in');
    }
};
