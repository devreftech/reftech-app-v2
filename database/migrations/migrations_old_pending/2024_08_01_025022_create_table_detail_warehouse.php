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
        Schema::create('detail_warehouse', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_warehouse');
            $table->foreignId('id_replacement');
            $table->integer('qty');
            $table->enum('warehouse1',['BDG', 'BKS']);
            $table->enum('warehouse2',['BDG', 'BKS']);
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
        Schema::dropIfExists('table_detail_warehouse');
    }
};
