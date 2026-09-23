<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 1 Lead bisa di-follow-up berkali-kali (kebutuhan baru muncul lagi di kemudian
// hari) — tiap follow-up dicatat sebagai baris sendiri (bukan nimpa field
// product_interest/notes di online_leads), jadi histori kebutuhan dari waktu
// ke waktu tetap utuh. Item-item di dalam 1 follow-up ada di tabel terpisah
// (online_lead_follow_up_items) karena 1x follow-up bisa berisi beberapa item
// kebutuhan yang statusnya provided/not_provided beda-beda.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_lead_follow_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lead')->index();
            $table->unsignedBigInteger('id_user')->nullable()->index();
            $table->date('date')->index();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('id_lead')->references('id')->on('online_leads')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_lead_follow_ups');
    }
};
