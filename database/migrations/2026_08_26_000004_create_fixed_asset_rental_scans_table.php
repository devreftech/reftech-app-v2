<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Log tiap kali unit Fixed Asset (Mesin) di-scan barcode buat keluar rental
        // (action=out) atau diterima kembali (action=in). Ini yang jadi sumber
        // "riwayat rental" di halaman detail unit — durasi rental tinggal dihitung
        // dari selisih waktu antara baris out & baris in berikutnya.
        Schema::create('fixed_asset_rental_scans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_fixed_asset');
            $table->enum('action', ['out', 'in']);
            // id_client & id_pic_internal diisi pas action=out (dipilih dari form scan).
            // Baris action=in nyalin nilai yang sama dari baris out terakhir, biar
            // riwayatnya tetap jelas "unit ini balik dari sewa yang mana" tanpa perlu
            // join balik ke baris out-nya.
            $table->unsignedBigInteger('id_client')->nullable();
            $table->unsignedBigInteger('id_pic_internal')->nullable();
            $table->unsignedBigInteger('scanned_by')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('id_fixed_asset')->references('id')->on('fixed_asset')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixed_asset_rental_scans');
    }
};
