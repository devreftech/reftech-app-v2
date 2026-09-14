<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Purely additive: both columns are nullable / have a safe default, so every
     * existing row in `payment` (including the 348 legacy Escrow rows without an
     * escrow_channel) stays valid with zero backfill required. The existing
     * `method` and `escrow_channel` columns are left untouched.
     */
    public function up(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            if (!Schema::hasColumn('payment', 'id_marketplace')) {
                $table->unsignedBigInteger('id_marketplace')->nullable()->after('escrow_channel');
                $table->foreign('id_marketplace')->references('id')->on('marketplaces')->nullOnDelete();
            }
            if (!Schema::hasColumn('payment', 'disbursement_status')) {
                $table->string('disbursement_status', 20)->default('held')->after('id_marketplace');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment', function (Blueprint $table) {
            if (Schema::hasColumn('payment', 'id_marketplace')) {
                $table->dropForeign(['id_marketplace']);
                $table->dropColumn('id_marketplace');
            }
            if (Schema::hasColumn('payment', 'disbursement_status')) {
                $table->dropColumn('disbursement_status');
            }
        });
    }
};
