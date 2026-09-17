<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Jenis Cuti
        Schema::create('hr_leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique();
            $table->unsignedSmallInteger('default_days')->default(12);
            $table->boolean('is_paid')->default(true);
            $table->boolean('requires_attachment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Kuota Cuti Karyawan per Tahun
        Schema::create('hr_leave_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedSmallInteger('year');
            $table->unsignedSmallInteger('total_quota')->default(12);
            $table->unsignedSmallInteger('used_quota')->default(0);
            $table->unsignedSmallInteger('remaining_quota')->default(12);
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->unique(['employee_id', 'year']);
        });

        // 3. Pengajuan Cuti & Izin
        Schema::create('hr_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('total_days')->default(1);
            $table->text('reason');
            $table->string('attachment')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->text('rejection_note')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('hr_leave_types')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_leave_requests');
        Schema::dropIfExists('hr_leave_balances');
        Schema::dropIfExists('hr_leave_types');
    }
};
