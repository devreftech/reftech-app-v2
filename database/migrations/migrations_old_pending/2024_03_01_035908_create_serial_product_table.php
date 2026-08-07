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
        Schema::create('serial_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_product');
            $table->string('fxp_parts');
            $table->string('brand');
            $table->string('pn');
            $table->string('image');
            $table->longText('detail')->nullable();
            $table->integer('price');
            $table->enum("rental", ['0','1'])->nullable();
            $table->enum("second", ['0','1'])->nullable();
            $table->enum("new", ['0','1'])->nullable();
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
        Schema::dropIfExists('serial_product');
    }
};
