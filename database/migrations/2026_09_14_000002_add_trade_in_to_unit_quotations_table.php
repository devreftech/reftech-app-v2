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
                if (!Schema::hasColumn('unit_quotation', 'has_trade_in')) {
                    $table->boolean('has_trade_in')->default(false)->after('total');
                }
                if (!Schema::hasColumn('unit_quotation', 'trade_in_brand')) {
                    $table->string('trade_in_brand')->nullable()->after('has_trade_in');
                }
                if (!Schema::hasColumn('unit_quotation', 'trade_in_model')) {
                    $table->string('trade_in_model')->nullable()->after('trade_in_brand');
                }
                if (!Schema::hasColumn('unit_quotation', 'trade_in_power')) {
                    $table->string('trade_in_power')->nullable()->after('trade_in_model');
                }
                if (!Schema::hasColumn('unit_quotation', 'trade_in_sn')) {
                    $table->string('trade_in_sn')->nullable()->after('trade_in_power');
                }
                if (!Schema::hasColumn('unit_quotation', 'trade_in_price')) {
                    $table->decimal('trade_in_price', 15, 2)->default(0)->after('trade_in_sn');
                }
                if (!Schema::hasColumn('unit_quotation', 'trade_in_notes')) {
                    $table->text('trade_in_notes')->nullable()->after('trade_in_price');
                }
            });
        }

        if (Schema::hasTable('unit_quotation_options')) {
            Schema::table('unit_quotation_options', function (Blueprint $table) {
                if (!Schema::hasColumn('unit_quotation_options', 'has_trade_in')) {
                    $table->boolean('has_trade_in')->default(false)->after('total');
                }
                if (!Schema::hasColumn('unit_quotation_options', 'trade_in_brand')) {
                    $table->string('trade_in_brand')->nullable()->after('has_trade_in');
                }
                if (!Schema::hasColumn('unit_quotation_options', 'trade_in_model')) {
                    $table->string('trade_in_model')->nullable()->after('trade_in_brand');
                }
                if (!Schema::hasColumn('unit_quotation_options', 'trade_in_power')) {
                    $table->string('trade_in_power')->nullable()->after('trade_in_model');
                }
                if (!Schema::hasColumn('unit_quotation_options', 'trade_in_sn')) {
                    $table->string('trade_in_sn')->nullable()->after('trade_in_power');
                }
                if (!Schema::hasColumn('unit_quotation_options', 'trade_in_price')) {
                    $table->decimal('trade_in_price', 15, 2)->default(0)->after('trade_in_sn');
                }
                if (!Schema::hasColumn('unit_quotation_options', 'trade_in_notes')) {
                    $table->text('trade_in_notes')->nullable()->after('trade_in_price');
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
                $columns = ['has_trade_in', 'trade_in_brand', 'trade_in_model', 'trade_in_power', 'trade_in_sn', 'trade_in_price', 'trade_in_notes'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('unit_quotation', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('unit_quotation_options')) {
            Schema::table('unit_quotation_options', function (Blueprint $table) {
                $columns = ['has_trade_in', 'trade_in_brand', 'trade_in_model', 'trade_in_power', 'trade_in_sn', 'trade_in_price', 'trade_in_notes'];
                foreach ($columns as $col) {
                    if (Schema::hasColumn('unit_quotation_options', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
