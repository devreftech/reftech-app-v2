<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_leave_balances') && !Schema::hasColumn('hr_leave_balances', 'is_active')) {
            Schema::table('hr_leave_balances', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('remaining_quota');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hr_leave_balances') && Schema::hasColumn('hr_leave_balances', 'is_active')) {
            Schema::table('hr_leave_balances', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
