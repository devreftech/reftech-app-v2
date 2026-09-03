<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('project_expenses', 'payment_info')) {
                $table->string('payment_info')->nullable()->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_expenses', function (Blueprint $table) {
            if (Schema::hasColumn('project_expenses', 'payment_info')) {
                $table->dropColumn('payment_info');
            }
        });
    }
};
