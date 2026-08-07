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
        Schema::create('termncon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_quotation');
            $table->string('validity');
            $table->string('pricing');
            $table->string('delivery_process');
            $table->string('payment');
            $table->string('warranty')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('termncon');
    }
};
