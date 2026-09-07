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
        if (Schema::hasTable('unit_quotation') && !Schema::hasColumn('unit_quotation', 'id_source_bank')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                $table->unsignedBigInteger('id_source_bank')->nullable()->after('fee_paid_by');
                $table->foreign('id_source_bank')->references('id')->on('bank')->onDelete('set null');
            });
        }

        if (Schema::hasTable('manual_management_fees') && !Schema::hasColumn('manual_management_fees', 'id_source_bank')) {
            Schema::table('manual_management_fees', function (Blueprint $table) {
                $table->unsignedBigInteger('id_source_bank')->nullable()->after('fee_paid_by');
                $table->foreign('id_source_bank')->references('id')->on('bank')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('unit_quotation') && Schema::hasColumn('unit_quotation', 'id_source_bank')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                $table->dropForeign(['id_source_bank']);
                $table->dropColumn('id_source_bank');
            });
        }

        if (Schema::hasTable('manual_management_fees') && Schema::hasColumn('manual_management_fees', 'id_source_bank')) {
            Schema::table('manual_management_fees', function (Blueprint $table) {
                $table->dropForeign(['id_source_bank']);
                $table->dropColumn('id_source_bank');
            });
        }
    }
};
