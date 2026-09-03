<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('unit_quotation')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                if (!Schema::hasColumn('unit_quotation', 'fee')) {
                    $table->decimal('fee', 15, 2)->default(0)->nullable()->after('total');
                }
                if (!Schema::hasColumn('unit_quotation', 'fee_note')) {
                    $table->text('fee_note')->nullable()->after('fee');
                }
            });
        }

        if (Schema::hasTable('unit_quotation_detail')) {
            Schema::table('unit_quotation_detail', function (Blueprint $table) {
                if (!Schema::hasColumn('unit_quotation_detail', 'fee')) {
                    $table->decimal('fee', 15, 2)->default(0)->nullable()->after('amount');
                }
            });
        }

        if (Schema::hasTable('unit_quotation_options')) {
            Schema::table('unit_quotation_options', function (Blueprint $table) {
                if (!Schema::hasColumn('unit_quotation_options', 'fee')) {
                    $table->decimal('fee', 15, 2)->default(0)->nullable()->after('total');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('unit_quotation')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                if (Schema::hasColumn('unit_quotation', 'fee_note')) {
                    $table->dropColumn('fee_note');
                }
                if (Schema::hasColumn('unit_quotation', 'fee')) {
                    $table->dropColumn('fee');
                }
            });
        }

        if (Schema::hasTable('unit_quotation_detail')) {
            Schema::table('unit_quotation_detail', function (Blueprint $table) {
                if (Schema::hasColumn('unit_quotation_detail', 'fee')) {
                    $table->dropColumn('fee');
                }
            });
        }

        if (Schema::hasTable('unit_quotation_options')) {
            Schema::table('unit_quotation_options', function (Blueprint $table) {
                if (Schema::hasColumn('unit_quotation_options', 'fee')) {
                    $table->dropColumn('fee');
                }
            });
        }
    }
};
