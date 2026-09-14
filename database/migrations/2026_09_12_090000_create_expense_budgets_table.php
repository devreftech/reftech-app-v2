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
        Schema::create('expense_budgets', function (Blueprint $table) {
            $table->id();
            $table->integer('year')->index();
            $table->string('entity', 50)->default('all')->index(); // 'reftech', 'kojisha', 'all'
            $table->double('annual_budget')->default(0);
            $table->double('monthly_budget')->default(0);
            $table->double('m1')->nullable();
            $table->double('m2')->nullable();
            $table->double('m3')->nullable();
            $table->double('m4')->nullable();
            $table->double('m5')->nullable();
            $table->double('m6')->nullable();
            $table->double('m7')->nullable();
            $table->double('m8')->nullable();
            $table->double('m9')->nullable();
            $table->double('m10')->nullable();
            $table->double('m11')->nullable();
            $table->double('m12')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique(['year', 'entity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_budgets');
    }
};
