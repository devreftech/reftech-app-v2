<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Item kebutuhan di dalam 1 follow-up. Misal 1x follow-up minta 5 item, yang
// bisa di-provide cuma 1 — tiap item disimpan barisnya sendiri dengan status
// masing-masing, bukan digabung jadi satu teks.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_lead_follow_up_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_follow_up')->index();
            $table->string('item_name', 255);
            $table->string('qty', 50)->nullable();
            $table->enum('status', ['pending', 'provided', 'not_provided'])->default('pending')->index();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('id_follow_up')->references('id')->on('online_lead_follow_ups')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_lead_follow_up_items');
    }
};
