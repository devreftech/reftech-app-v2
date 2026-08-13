<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_request_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_purchase_request');
            $table->unsignedBigInteger('id_equivalent');
            $table->integer('qty');
            $table->longText('note')->nullable();
            $table->bigInteger('price')->nullable();
            $table->bigInteger('amount')->nullable();
            $table->string('purchase_type')->nullable();
            $table->string('cargo')->nullable();
            $table->string('no_resi')->nullable();
            $table->date('purchase_date')->nullable();
            $table->integer('qty_received')->nullable();
            $table->string('gr_status')->nullable();
            $table->text('gr_note')->nullable();
            $table->string('no_do')->nullable();
            $table->date('gr_date')->nullable();
            $table->string('warehouse')->nullable();
            $table->timestamps();

            $table->foreign('id_purchase_request')->references('id')->on('purchase_request')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_request_detail');
    }
};
