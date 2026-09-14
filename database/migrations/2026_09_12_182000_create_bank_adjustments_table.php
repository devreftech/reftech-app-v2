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
        Schema::create('bank_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number', 50)->unique();
            $table->unsignedBigInteger('id_bank');
            $table->double('previous_balance')->default(0);
            $table->double('adjusted_balance')->default(0);
            $table->double('difference')->default(0);
            $table->enum('type', ['in', 'out', 'neutral'])->default('neutral');
            $table->date('date');
            $table->text('reason');
            $table->string('proof_file')->nullable();
            $table->boolean('is_cutoff_initial')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('id_bank');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_adjustments');
    }
};
