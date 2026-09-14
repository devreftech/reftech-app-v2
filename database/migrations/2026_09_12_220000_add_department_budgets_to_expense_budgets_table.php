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
        if (Schema::hasTable('expense_budgets') && !Schema::hasColumn('expense_budgets', 'department_budgets')) {
            Schema::table('expense_budgets', function (Blueprint $table) {
                $table->json('department_budgets')->nullable()->after('monthly_budget');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('expense_budgets') && Schema::hasColumn('expense_budgets', 'department_budgets')) {
            Schema::table('expense_budgets', function (Blueprint $table) {
                $table->dropColumn('department_budgets');
            });
        }
    }
};
