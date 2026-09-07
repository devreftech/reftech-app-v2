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
        if (Schema::hasTable('payment') && !Schema::hasColumn('payment', 'id_bank')) {
            Schema::table('payment', function (Blueprint $table) {
                $table->unsignedBigInteger('id_bank')->nullable()->after('id_unit_quotation');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('payment') && Schema::hasColumn('payment', 'id_bank')) {
            Schema::table('payment', function (Blueprint $table) {
                $table->dropColumn('id_bank');
            });
        }
    }
};
