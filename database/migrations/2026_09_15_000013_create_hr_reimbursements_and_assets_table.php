<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Klaim Reimbursement Biaya Operasional
        Schema::create('hr_reimbursements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('claim_number', 50)->unique();
            $table->enum('claim_type', ['BBM / Bensin', 'Tol / Parkir', 'Akomodasi / Hotel', 'Konsumsi Lapangan', 'Medis / Pengobatan', 'Lainnya'])->default('Lainnya');
            $table->decimal('amount', 15, 2);
            $table->date('event_date');
            $table->text('description');
            $table->string('receipt_image')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Paid'])->default('Pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['employee_id', 'status']);
        });

        // 2. Inventaris Alat Kerja Karyawan
        Schema::create('hr_employee_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('asset_name', 150);
            $table->string('asset_code', 50)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->enum('condition', ['Baik', 'Normal', 'Rusak Ringan', 'Perlu Servis'])->default('Baik');
            $table->date('handover_date');
            $table->date('returned_date')->nullable();
            $table->enum('status', ['Digunakan', 'Dikembalikan', 'Hilang/Rusak'])->default('Digunakan');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index(['employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_employee_assets');
        Schema::dropIfExists('hr_reimbursements');
    }
};
