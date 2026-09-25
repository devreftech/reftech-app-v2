<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Batch Header Bonus Semesteran
        Schema::create('hr_semester_bonuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. BNS-SEM1-2026
            $table->string('title'); // e.g. Bonus Penjualan & Kinerja Semester 1 2026
            $table->tinyInteger('semester'); // 1 or 2
            $table->year('year');
            $table->date('payment_date')->nullable();
            $table->enum('status', ['Draft', 'Confirmed', 'Approved', 'Paid'])->default('Draft');
            $table->decimal('total_addition', 15, 2)->default(0);
            $table->decimal('total_deduction', 15, 2)->default(0);
            $table->decimal('total_net_amount', 15, 2)->default(0);
            $table->integer('total_recipients_count')->default(0);
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->foreignId('expense_id')->nullable()->constrained('expense')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Karyawan Penerima Bonus Semesteran
        Schema::create('hr_semester_bonus_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bonus_id')->constrained('hr_semester_bonuses')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('total_addition', 15, 2)->default(0);
            $table->decimal('total_deduction', 15, 2)->default(0);
            $table->decimal('net_bonus', 15, 2)->default(0);
            $table->enum('payment_status', ['Pending', 'Paid'])->default('Pending');
            $table->dateTime('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['bonus_id', 'employee_id']);
        });

        // 3. Item Komponen Dinamis Fleksibel per Karyawan
        Schema::create('hr_semester_bonus_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipient_id')->constrained('hr_semester_bonus_recipients')->cascadeOnDelete();
            $table->string('name'); // e.g. Bonus Penjualan Unit, Evaluasi Kinerja, Insentif Khusus, etc.
            $table->enum('type', ['addition', 'deduction'])->default('addition');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('notes')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_semester_bonus_items');
        Schema::dropIfExists('hr_semester_bonus_recipients');
        Schema::dropIfExists('hr_semester_bonuses');
    }
};
