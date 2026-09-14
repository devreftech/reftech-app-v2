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
        if (!Schema::hasTable('bank_reconciliations')) {
            Schema::create('bank_reconciliations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_bank');
                $table->integer('period_month');
                $table->integer('period_year');
                $table->date('statement_date');
                $table->double('statement_balance')->default(0);
                $table->double('book_balance')->default(0);
                $table->double('difference')->default(0);
                $table->enum('status', ['draft', 'reconciled'])->default('draft');
                $table->json('reconciled_items')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->index(['id_bank', 'period_year', 'period_month']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
    }
};
