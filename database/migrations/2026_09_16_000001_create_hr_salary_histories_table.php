<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hr_salary_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('effective_date');
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('previous_basic_salary', 15, 2)->default(0);
            $table->decimal('transport_allowance', 15, 2)->default(0);
            $table->decimal('meal_allowance', 15, 2)->default(0);
            $table->decimal('position_allowance', 15, 2)->default(0);
            $table->decimal('other_allowance', 15, 2)->default(0);
            $table->decimal('bpjs_kesehatan', 15, 2)->default(0);
            $table->decimal('bpjs_ketenagakerjaan', 15, 2)->default(0);
            $table->decimal('total_gross', 15, 2)->default(0);
            $table->decimal('previous_total_gross', 15, 2)->default(0);
            $table->decimal('increment_amount', 15, 2)->default(0);
            $table->decimal('increment_percentage', 6, 2)->default(0);
            $table->string('change_type', 100)->default('Kenaikan Tahunan');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_salary_histories');
    }
};
