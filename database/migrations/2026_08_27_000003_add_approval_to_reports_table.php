<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Alur baru: service report yang dibuat teknisi masuk status 'pending'
            // dulu, harus di-approve role ServiceM sebelum kelihatan (badge) di
            // sisi Sales. Ditolak => 'rejected' + reject_note, balik ke teknisi
            // buat dibenerin (edit => otomatis balik 'pending').
            $table->string('approval_status', 20)->default('pending')->after('sign_client');
            $table->unsignedBigInteger('approved_by')->nullable()->after('approval_status');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('reject_note')->nullable()->after('approved_at');
        });

        // Semua report yang sudah ada sebelum fitur ini dianggap sudah disetujui,
        // supaya antrian approval ServiceM tidak kebanjiran data lama.
        DB::table('reports')->update(['approval_status' => 'approved']);
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'approved_by', 'approved_at', 'reject_note']);
        });
    }
};
