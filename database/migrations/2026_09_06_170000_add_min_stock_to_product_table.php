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
        if (Schema::hasTable('product') && !Schema::hasColumn('product', 'min_stock')) {
            Schema::table('product', function (Blueprint $table) {
                $table->integer('min_stock')->default(5)->after('pending_stock');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product') && Schema::hasColumn('product', 'min_stock')) {
            Schema::table('product', function (Blueprint $table) {
                $table->dropColumn('min_stock');
            });
        }
    }
};
