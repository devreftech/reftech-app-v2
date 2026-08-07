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
        
        // Schema::create('compressor', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('compressor_brand', 50);
        //     $table->string('series', 25);
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
        Schema::dropIfExists('compressor');
    }
};
