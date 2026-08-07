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
        Schema::create('detail_delivery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_delivery');
            $table->foreignId('id_pn');
            $table->longText('desc');
            $table->integer('qty');
            $table->string('info_qty');
            $table->enum('view', ['0', '1']);
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
        Schema::dropIfExists('detail_delivery');
    }
};
