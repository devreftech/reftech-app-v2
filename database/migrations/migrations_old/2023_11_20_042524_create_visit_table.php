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
        Schema::create('visit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_client');
            $table->string('status', 20);
            $table->string('compressor_data');
            $table->integer('running_hour');
            $table->date('date');
            $table->string('prospect');
            $table->string('map_url');
            $table->string('note');
            $table->string('recomendation');
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
        Schema::dropIfExists('visit');
    }
};
