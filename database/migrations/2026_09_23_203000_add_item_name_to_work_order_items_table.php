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
        Schema::table('work_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('work_order_items', 'item_name')) {
                $table->string('item_name', 255)->nullable()->after('id_work_order');
            }
            if (!Schema::hasColumn('work_order_items', 'unit')) {
                $table->string('unit', 50)->default('Pcs')->after('qty_requested');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('work_order_items', 'item_name')) {
                $table->dropColumn('item_name');
            }
            if (Schema::hasColumn('work_order_items', 'unit')) {
                $table->dropColumn('unit');
            }
        });
    }
};
