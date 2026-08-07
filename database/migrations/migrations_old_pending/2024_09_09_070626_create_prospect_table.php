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
        Schema::create('prospect', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_support');
            $table->foreignId('id_sales')->nullable();
            $table->foreignId('id_quotation')->nullable();
            $table->foreignId('id_pic');
            $table->longText('kebutuhan');
            $table->date('date');
            $table->enum('provide', ['0','1'])->nullable();
            $table->enum('level', ['0','1'])->nullable();
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
        Schema::dropIfExists('prospect');
    }
};
