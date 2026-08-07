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
        Schema::create('product_in', function (Blueprint $table) {
            $table->id();
            $table->string('no_do')->nullable();
            $table->string('invoice')->nullable();
            $table->string('supplier')->nullable();
            $table->string('note')->nullable();
            $table->date('date');
            $table->date('date_invoice')->nullable();
            $table->integer('subtotal')->nullable();
            $table->integer('total_no_tax')->nullable();
            $table->integer('shipping')->nullable();
            $table->integer('tax')->nullable();
            $table->integer('total')->nullable();
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
        Schema::dropIfExists('product_in');
    }
};
