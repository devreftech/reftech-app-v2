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
        Schema::create('bearing_kit_prices', function (Blueprint $table) {
            $table->id();
            $table->string('power')->unique();
            $table->unsignedBigInteger('price_bearing_kit')->default(0);
            $table->unsignedBigInteger('price_bearing_kit_main_motor')->default(0);
            $table->unsignedBigInteger('price_bearing_kit_fan_motor')->default(0);
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
        Schema::dropIfExists('bearing_kit_prices');
    }
};
