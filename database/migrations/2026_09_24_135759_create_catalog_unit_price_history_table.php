<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini awalnya dibuat manual (tanpa migration) dan sudah ada di
     * production & staging. Migration ini hanya membuat tabel bila belum ada
     * (mis. database baru / Docker), dengan struktur identik production.
     */
    public function up()
    {
        if (Schema::hasTable('catalog_unit_price_history')) {
            return;
        }

        Schema::create('catalog_unit_price_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_catalog_unit')->constrained('catalog_unit')->cascadeOnDelete();
            $table->bigInteger('price_idr')->default(0);
            $table->decimal('price_usd', 15, 2)->default(0);
            $table->foreignId('changed_by')->constrained('users');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down()
    {
        // Sengaja kosong: tabel berisi data yang sudah ada sebelum migration
        // ini dibuat, sehingga rollback tidak boleh menghapusnya.
    }
};