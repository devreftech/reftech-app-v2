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
        Schema::create('req_visit', function (Blueprint $table) {
            $table->id();
            $table->foreignId("id_machine");
            $table->foreignId("id_service")->nullable();
            $table->date("date")->nullable();
            $table->date("req_date");
            $table->date("visit_date")->nullable();
            $table->longText("note");
            $table->longText("visit_note")->nullable();
            $table->longText("desc")->nullable();
            $table->enum('status',['Waiting', 'Pending', 'On Process', 'Finish']);
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
        Schema::dropIfExists('req_visit');
    }
};
