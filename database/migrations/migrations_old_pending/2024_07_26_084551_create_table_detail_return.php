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
        Schema::create('detail_return', function (Blueprint $table) {
            $table->id();
            $table->foreignId("id_return");
            $table->foreignId("id_pn");
            $table->longText('detail_product');
            $table->integer('qty');
            $table->string('info_qty');
            $table->integer('price');
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
        Schema::dropIfExists('table_detail_return');
    }
};
