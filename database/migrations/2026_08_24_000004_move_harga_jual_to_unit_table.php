<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit', function (Blueprint $table) {
            // Harga jual/listing sekarang satu per MODEL unit (dipakai sama rata buat
            // semua serial number-nya), bukan per unit fisik lagi — lihat
            // UnitProductInController::updateHargaJualUnit().
            $table->decimal('harga_jual', 15, 2)->nullable()->after('stock');
        });

        // Backfill dari unit_inventory yang udah sempat di-set manual sebelumnya —
        // ambil nilai non-null pertama per id_unit.
        $existing = DB::table('unit_inventory')
            ->whereNotNull('harga_jual')
            ->orderBy('id')
            ->get(['id_unit', 'harga_jual']);

        $seen = [];
        foreach ($existing as $row) {
            if (isset($seen[$row->id_unit])) {
                continue;
            }
            $seen[$row->id_unit] = true;
            DB::table('unit')->where('id', $row->id_unit)->update(['harga_jual' => $row->harga_jual]);
        }

        Schema::table('unit_inventory', function (Blueprint $table) {
            $table->dropColumn('harga_jual');
        });
    }

    public function down(): void
    {
        Schema::table('unit_inventory', function (Blueprint $table) {
            $table->decimal('harga_jual', 15, 2)->nullable()->after('total_modal');
        });

        Schema::table('unit', function (Blueprint $table) {
            $table->dropColumn('harga_jual');
        });
    }
};
