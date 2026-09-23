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
        Schema::table('sales_target_histories', function (Blueprint $table) {
            $table->boolean('is_active_roster')->default(true)->after('target_annual');
            $table->string('sales_type')->default('field')->after('is_active_roster'); // 'field', 'crm', 'ecommerce'
            $table->string('status')->default('active')->after('sales_type'); // 'active', 'resigned', 'cuti', 'transferred'
            $table->date('join_date')->nullable()->after('status');
            $table->date('resign_date')->nullable()->after('join_date');
            $table->json('kpi_config')->nullable()->after('resign_date');
            $table->text('notes')->nullable()->after('kpi_config');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_target_histories', function (Blueprint $table) {
            $table->dropColumn([
                'is_active_roster',
                'sales_type',
                'status',
                'join_date',
                'resign_date',
                'kpi_config',
                'notes'
            ]);
        });
    }
};
