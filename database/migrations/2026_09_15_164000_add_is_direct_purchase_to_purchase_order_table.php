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
        Schema::table('purchase_order', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order', 'is_direct_purchase')) {
                $table->tinyInteger('is_direct_purchase')->default(0)->after('id_purchase_request');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_order', 'is_direct_purchase')) {
                $table->dropColumn('is_direct_purchase');
            }
        });
    }
};
