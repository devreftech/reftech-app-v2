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
        Schema::table('bank', function (Blueprint $table) {
            if (!Schema::hasColumn('bank', 'is_petty_cash')) {
                $table->boolean('is_petty_cash')->default(false)->after('entity');
            }
            if (!Schema::hasColumn('bank', 'pic_id')) {
                $table->unsignedBigInteger('pic_id')->nullable()->after('is_petty_cash');
            }
            if (!Schema::hasColumn('bank', 'plafond')) {
                $table->decimal('plafond', 15, 2)->default(0)->after('pic_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank', function (Blueprint $table) {
            if (Schema::hasColumn('bank', 'plafond')) {
                $table->dropColumn('plafond');
            }
            if (Schema::hasColumn('bank', 'pic_id')) {
                $table->dropColumn('pic_id');
            }
            if (Schema::hasColumn('bank', 'is_petty_cash')) {
                $table->dropColumn('is_petty_cash');
            }
        });
    }
};
