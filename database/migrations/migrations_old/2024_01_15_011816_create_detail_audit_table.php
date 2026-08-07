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
        Schema::create('detail_audit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_audit');
            $table->string('tools');
            $table->integer('qty');
            $table->string('desc');
            $table->enum('assesment',['Ada', 'Tidak Lengkap', 'Hilang']);
            $table->string('note');
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
        Schema::dropIfExists('detail_audit');
    }
};
