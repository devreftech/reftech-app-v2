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
        Schema::create('detail_quotation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_quotation');
            $table->foreignId('id_equivalent');
            // $table->string('product', 25); // detail
            $table->longText('detail_product'); // detail
            $table->integer('qty'); // detail
            $table->string('info_qty')->nullable(); // detail
            $table->integer('disc');
            $table->integer('fee');
            $table->integer('price'); // detail
            $table->integer('amount'); // detail
            $table->integer('pph');
            $table->enum('view',['0','1']);
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
        Schema::dropIfExists('detail_quotation');
    }
};
