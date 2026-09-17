<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('evaluator_id')->nullable();
            $table->enum('evaluation_type', ['Probation 3 Bulan', 'Tahunan (Annual)', 'Kenaikan Jabatan', 'Evaluasi Khusus'])->default('Probation 3 Bulan');
            $table->date('evaluation_date');
            $table->unsignedTinyInteger('score')->default(80); // 0 - 100
            $table->text('strengths')->nullable();
            $table->text('improvements')->nullable();
            $table->enum('recommendation', ['Lolos Pegawai Tetap', 'Perpanjang Kontrak', 'Peringatan / Evaluasi Ulang', 'Tidak Dilanjutkan / PHK'])->default('Lolos Pegawai Tetap');
            $table->enum('status', ['Draft', 'Final'])->default('Final');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('evaluator_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['employee_id', 'evaluation_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_evaluations');
    }
};
