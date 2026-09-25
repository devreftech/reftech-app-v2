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
                if (!Schema::hasColumn('unit_quotation', 'merge_terms')) {
                    $table->boolean('merge_terms')->default(false)->after('rental_terms');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('unit_quotation')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                if (Schema::hasColumn('unit_quotation', 'merge_terms')) {
                    $table->dropColumn('merge_terms');
                }
            });
        }
    }
};
