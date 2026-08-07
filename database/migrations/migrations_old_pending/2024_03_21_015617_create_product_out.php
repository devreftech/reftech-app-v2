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
        Schema::create('product_out', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user');
            $table->foreignId('id_invoice');
            $table->string('detail_client');
            $table->string('invoice');
            $table->string('po');
            $table->enum('no_type',['1', '2']);
            $table->date('date');
            $table->string('note');
            $table->enum('vers',['Offline', 'Online']);
            $table->integer('shipping');
            $table->integer('total');
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
        Schema::dropIfExists('product_out');
    }
};
