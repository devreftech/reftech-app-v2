<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_out', function (Blueprint $table) {
            if (!Schema::hasColumn('product_out', 'id_pending')) {
                $table->unsignedBigInteger('id_pending')->nullable()->after('id_user')->index();
            }
        });

        // Backfill existing 1-to-1 relationships from pending_po.id_product_out
        try {
            DB::statement("
                UPDATE product_out po
                INNER JOIN pending_po p ON p.id_product_out = po.id
                SET po.id_pending = p.id
                WHERE po.id_pending IS NULL AND p.id_product_out IS NOT NULL
            ");
        } catch (\Throwable $e) {
            // Log or ignore if table is empty or structure varies
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_out', function (Blueprint $table) {
            if (Schema::hasColumn('product_out', 'id_pending')) {
                $table->dropColumn('id_pending');
            }
        });
    }
};
