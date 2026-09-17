<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Additive only: `qty` defaults to 1 so every existing component row
     * (previously implicitly "1 pcs per set") stays correct with zero backfill.
     */
    public function up(): void
    {
        Schema::table('item_product_set', function (Blueprint $table) {
            if (!Schema::hasColumn('item_product_set', 'qty')) {
                $table->unsignedInteger('qty')->default(1)->after('id_replacement');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_product_set', function (Blueprint $table) {
            if (Schema::hasColumn('item_product_set', 'qty')) {
                $table->dropColumn('qty');
            }
        });
    }
};
