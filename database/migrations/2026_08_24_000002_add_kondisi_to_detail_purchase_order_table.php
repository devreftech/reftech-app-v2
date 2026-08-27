<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_purchase_order', function (Blueprint $table) {
            // Cuma relevan buat baris category='Unit' — nentuin GR-nya (lihat
            // UnitProductInController::storeGoodsReceipt) masuk ke unit_inventory
            // (Baru, siap jual) atau fixed_asset (Second, QC dulu sebelum dipakai/dijual).
            $table->string('kondisi')->nullable()->after('id_unit');
        });
    }

    public function down(): void
    {
        Schema::table('detail_purchase_order', function (Blueprint $table) {
            $table->dropColumn('kondisi');
        });
    }
};
