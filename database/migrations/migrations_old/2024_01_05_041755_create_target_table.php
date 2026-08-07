<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('target', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sales');
            $table->string('dc');
            $table->enum('statDc', ['0', '1']);
            $table->string('crm');
            $table->enum('statCrm', ['0', '1']);
            $table->string('visit');
            $table->enum('statVisit', ['0', '1']);
            $table->string('quote');
            $table->enum('statQuote', ['0', '1']);
            $table->string('po');
            $table->enum('statPo', ['0', '1']);
            $table->string('total');
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
        Schema::dropIfExists('target');
    }
};
