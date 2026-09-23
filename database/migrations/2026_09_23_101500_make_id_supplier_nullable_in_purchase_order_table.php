<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('purchase_order') && Schema::hasColumn('purchase_order', 'id_supplier')) {
            try {
                // Gunakan ALTER TABLE statement langsung untuk kompatibilitas multi-driver
                DB::statement("ALTER TABLE `purchase_order` MODIFY `id_supplier` BIGINT UNSIGNED NULL");
            } catch (\Throwable $e) {
                // Fallback jika tipe kolom int biasa
                try {
                    DB::statement("ALTER TABLE `purchase_order` MODIFY `id_supplier` INT(11) NULL");
                } catch (\Throwable $ex) {
                    // Ignore if already nullable
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op to prevent breaking existing data with NULL suppliers
    }
};
