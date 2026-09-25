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
            if (!Schema::hasColumn('fixed_asset', 'pic_construction_ids')) {
                $table->json('pic_construction_ids')->nullable()->after('nomor_dokumen_legalitas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fixed_asset', function (Blueprint $table) {
            if (!Schema::hasColumn('fixed_asset', 'pic_construction_ids')) {
                $table->dropColumn('pic_construction_ids');
            }
        });
    }
};
