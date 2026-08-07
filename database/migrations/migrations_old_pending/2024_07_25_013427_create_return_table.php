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
        Schema::create('return', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_quotation');
            $table->string('no_return');
            $table->integer('subtotal');
            $table->integer('tax');
            $table->integer('total');
            $table->date('date');
            $table->enum('lvl',['0','1']);
            $table->longText('note');
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
        Schema::dropIfExists('return');
    }
};
