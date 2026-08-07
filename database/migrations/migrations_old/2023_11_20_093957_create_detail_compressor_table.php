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
        // Dibuka setelah masuk finale leads project
        
        // Schema::create('detail_compressor', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('id_compressor');
        //     $table->string('compressor_type', 25);
        //     $table->integer('hp');
        //     $table->integer('bar');
        //     $table->integer('fad');
        //     $table->integer('start_comissioning');
        //     $table->string('serial_number', 25);
        //     $table->integer('waranty');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detail_compressor');
    }
};
