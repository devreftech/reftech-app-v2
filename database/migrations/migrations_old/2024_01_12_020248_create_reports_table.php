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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_technician');
            $table->foreignId('id_pic');
            $table->foreignId('id_machine');
            $table->string('no_service');
            $table->enum('type',['Visit', 'Service','General']);
            $table->integer('running');
            $table->integer('load');
            $table->string('jobdesc');
            $table->date('date');
            $table->longText('desc');
            $table->longText('recomendation');
            $table->string('sign_client')->nullable();
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
        Schema::dropIfExists('reports');
    }
};
