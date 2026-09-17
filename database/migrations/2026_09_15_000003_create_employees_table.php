<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            // Nullable: karyawan tanpa akun login (mis. buruh harian) tetap bisa punya data HR.
            // Role 'Client' di tabel users sengaja tidak dibuatkan employee record.
            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->unsignedBigInteger('id_department')->nullable();
            $table->unsignedBigInteger('id_position')->nullable();

            $table->string('nik', 30)->nullable(); // Nomor Induk Karyawan
            $table->date('join_date')->nullable();
            $table->date('birthday')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();

            // 'Perlu Verifikasi': dipakai backfill untuk data lama yang users.active=0
            // tapi tidak ada tanggal resign yang bisa dipercaya — jangan tebak jadi 'Resign'.
            $table->enum('employment_status', ['Tetap', 'Kontrak', 'Probation', 'Resign', 'Perlu Verifikasi'])->default('Kontrak');
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->date('resign_date')->nullable();

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('id_department')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('id_position')->references('id')->on('positions')->onDelete('set null');

            $table->index('employment_status');
            $table->index(['id_department', 'id_position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
