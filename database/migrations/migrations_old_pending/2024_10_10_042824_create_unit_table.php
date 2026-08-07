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
        Schema::create('unit', function (Blueprint $table) {
            $table->id();
            $table->string("sku");
            // $table->enum("rental", ['0','1']);
            // $table->enum("second", ['0','1']);
            // $table->enum("new", ['0','1']);
            $table->integer('first_stock');
            $table->integer('warehouse_stock');
            $table->integer('stock');
            $table->string("status");
            $table->string("sn");
            $table->string("bar");
            $table->string("power");
            $table->string("air_cap");
            $table->string("connect");
            $table->string("dimension");
            $table->string("weight");
            $table->string("note");
            $table->string("desc");
            $table->date("date");
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
        Schema::dropIfExists('unit');
    }
};
