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
        if (!Schema::hasTable('marketplace_settlements')) {
            Schema::create('marketplace_settlements', function (Blueprint $table) {
                $table->id();
                $table->string('settlement_number')->unique();
                $table->unsignedBigInteger('id_marketplace');
                $table->unsignedBigInteger('id_bank');
                $table->date('settlement_date');
                $table->string('reference_no')->nullable();
                $table->double('gross_amount')->default(0);
                $table->double('fee_amount')->default(0);
                $table->double('net_amount')->default(0);
                $table->string('proof_file')->nullable();
                $table->enum('status', ['pending', 'reconciled'])->default('pending');
                $table->text('note')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('id_marketplace')->references('id')->on('marketplaces');
                $table->foreign('id_bank')->references('id')->on('bank');
                $table->index(['id_marketplace', 'settlement_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_settlements');
    }
};
