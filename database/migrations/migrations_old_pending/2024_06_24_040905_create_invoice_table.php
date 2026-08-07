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
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->foreignId("id_quotation");
            $table->longText("term")->nullable();
            $table->string("no_po");
            $table->enum("type",['CT', 'DP', 'BP']);
            $table->enum("flag", ['Reftech', 'Kojisha']);
            $table->string("no_invoice")->nullable();
            $table->string("sign")->nullable();
            $table->enum('invoiceTo', ['1','2'])->nullable();
            $table->float('pph');
            // $table->enum('doTo', ['1','2'])->nullable();
            // $table->date('dateDo');
            $table->date('date');
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
        Schema::dropIfExists('invoice');
    }
};
