<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('unit_quotation', 'is_draft')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                $table->boolean('is_draft')->default(false)->after('status')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('unit_quotation', 'is_draft')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                $table->dropColumn('is_draft');
            });
        }
    }
};
