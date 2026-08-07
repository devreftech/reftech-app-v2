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
        Schema::create('detail_service_quotation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_subtitle');
            $table->string('product');
            $table->longText('detail');
            $table->integer('price');
            $table->integer('amount');
            $table->integer('qty');
            $table->integer('disc');
            $table->string('info_qty');
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
        Schema::dropIfExists('detail_service_quotation');
    }
};
