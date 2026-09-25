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
        Schema::table('fixed_asset', function (Blueprint $table) {
            if (!Schema::hasColumn('fixed_asset', 'lokasi_bangunan')) {
                $table->text('lokasi_bangunan')->nullable()->after('desc');
            }
            if (!Schema::hasColumn('fixed_asset', 'luas_bangunan')) {
                $table->decimal('luas_bangunan', 10, 2)->nullable()->after('lokasi_bangunan');
            }
            if (!Schema::hasColumn('fixed_asset', 'luas_tanah')) {
                $table->decimal('luas_tanah', 10, 2)->nullable()->after('luas_bangunan');
            }
            if (!Schema::hasColumn('fixed_asset', 'status_bangunan')) {
                $table->string('status_bangunan', 50)->nullable()->default('operational')->after('luas_tanah');
            }
            if (!Schema::hasColumn('fixed_asset', 'tipe_pengadaan')) {
                $table->string('tipe_pengadaan', 50)->nullable()->default('beli_jadi')->after('status_bangunan');
            }
            if (!Schema::hasColumn('fixed_asset', 'nomor_dokumen_legalitas')) {
                $table->string('nomor_dokumen_legalitas', 255)->nullable()->after('tipe_pengadaan');
            }
        });

        if (!Schema::hasTable('fixed_asset_construction_costs')) {
            Schema::create('fixed_asset_construction_costs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fixed_asset_id')->index();
                $table->date('tanggal');
                $table->string('kategori_biaya', 50)->comment('material, jasa, operasional');
                $table->string('nama_item');
                $table->unsignedBigInteger('supplier_id')->nullable();
                $table->string('payee')->nullable();
                $table->decimal('qty', 10, 2)->default(1);
                $table->string('satuan', 50)->nullable();
                $table->decimal('harga_satuan', 15, 2)->default(0);
                $table->decimal('total_biaya', 15, 2)->default(0);
                $table->string('no_bukti')->nullable();
                $table->string('foto_bukti')->nullable();
                $table->unsignedBigInteger('id_pengeluaran')->nullable()->comment('Akun Kas / Bank');
                $table->unsignedBigInteger('id_beban')->nullable()->comment('Akun Beban untuk kategori jasa/operasional');
                $table->unsignedBigInteger('expense_id')->nullable()->index()->comment('Relasi ke tabel expense jika jasa');
                $table->text('catatan')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('fixed_asset_id')->references('id')->on('fixed_asset')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_asset_construction_costs');

        Schema::table('fixed_asset', function (Blueprint $table) {
            $table->dropColumn([
                'lokasi_bangunan',
                'luas_bangunan',
                'luas_tanah',
                'status_bangunan',
                'tipe_pengadaan',
                'nomor_dokumen_legalitas',
            ]);
        });
    }
};
