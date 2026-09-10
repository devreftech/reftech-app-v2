<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prospect_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_prospect');
            $table->unsignedBigInteger('id_user'); // penerima notifikasi (Sales Manager / Admin)
            $table->string('type')->default('prospect_created');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('id_prospect')->references('id')->on('prospect')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');

            // Idempotensi: satu notifikasi per (prospect, penerima, tipe) — aman dari double-submit form.
            $table->unique(['id_prospect', 'id_user', 'type'], 'prospect_notif_unique');
            $table->index(['id_user', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prospect_notifications');
    }
};
