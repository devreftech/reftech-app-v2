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
        Schema::create('rev_quote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_quotation');
            $table->string('no_pr');
            $table->string('rev_no_quote');
            $table->integer('discount');
            $table->integer('total_after_disc');
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
        Schema::dropIfExists('rev_quote');
    }
};
