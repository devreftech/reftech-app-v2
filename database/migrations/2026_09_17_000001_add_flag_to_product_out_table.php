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
        if (Schema::hasTable('product_out') && !Schema::hasColumn('product_out', 'flag')) {
            Schema::table('product_out', function (Blueprint $table) {
                $table->string('flag', 50)->default('Reftech')->nullable()->after('vers');
                $table->index('flag', 'idx_product_out_flag');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_out') && Schema::hasColumn('product_out', 'flag')) {
            Schema::table('product_out', function (Blueprint $table) {
                $table->dropIndex('idx_product_out_flag');
                $table->dropColumn('flag');
            });
        }
    }
};
