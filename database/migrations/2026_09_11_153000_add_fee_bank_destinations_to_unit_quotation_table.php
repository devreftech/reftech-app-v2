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
        if (Schema::hasTable('unit_quotation')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                if (!Schema::hasColumn('unit_quotation', 'fee_bank_destinations')) {
                    $table->json('fee_bank_destinations')->nullable()->after('fee_bank_branch');
                }
            });
        }

        if (Schema::hasTable('manual_management_fees')) {
            Schema::table('manual_management_fees', function (Blueprint $table) {
                if (!Schema::hasColumn('manual_management_fees', 'fee_bank_destinations')) {
                    $table->json('fee_bank_destinations')->nullable()->after('fee_bank_branch');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('unit_quotation')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                if (Schema::hasColumn('unit_quotation', 'fee_bank_destinations')) {
                    $table->dropColumn('fee_bank_destinations');
                }
            });
        }

        if (Schema::hasTable('manual_management_fees')) {
            Schema::table('manual_management_fees', function (Blueprint $table) {
                if (Schema::hasColumn('manual_management_fees', 'fee_bank_destinations')) {
                    $table->dropColumn('fee_bank_destinations');
                }
            });
        }
    }
};
