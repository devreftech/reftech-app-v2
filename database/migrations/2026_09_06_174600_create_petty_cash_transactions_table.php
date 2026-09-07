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
        if (!Schema::hasTable('petty_cash_transactions')) {
            Schema::create('petty_cash_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_bank');
                $table->string('voucher_number', 50)->index();
                $table->enum('type', ['disbursement', 'topup'])->default('disbursement'); // disbursement = uang keluar (BKK), topup = uang masuk/isi ulang (BKM)
                $table->date('date');
                $table->string('category', 100)->nullable(); // ATK, Transport & Bensin, Konsumsi & Pantry, Ekspedisi, Perbaikan, dll.
                $table->string('recipient', 150)->nullable(); // Nama pemohon / penerima uang
                $table->decimal('amount', 15, 2)->default(0);
                $table->text('description')->nullable();
                $table->string('proof_attachment', 255)->nullable();
                $table->unsignedBigInteger('id_source_bank')->nullable(); // Bank kantor asal dana jika topup
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('id_bank')->references('id')->on('bank')->onDelete('cascade');
                $table->foreign('id_source_bank')->references('id')->on('bank')->onDelete('set null');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cash_transactions');
    }
};
