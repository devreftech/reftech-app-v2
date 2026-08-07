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
        
        // Schema::create('service', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('id_client');
        //     $table->foreignId('id_technician');
        //     $table->foreignId('id_quotation');
        //     $table->integer('no_pruchase_order');
        //     $table->string('job_description', 25);
        //     $table->date('date');
        //     $table->string('compressor_type', 25);
        //     $table->string('area', 20);
        //     $table->string('recommendation', 20);
        //     $table->string('status',15);
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
        Schema::dropIfExists('service');
    }
};
