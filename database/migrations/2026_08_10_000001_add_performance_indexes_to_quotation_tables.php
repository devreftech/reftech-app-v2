<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add performance indexes.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotation', function (Blueprint $table) {
            if (Schema::hasColumn('quotation', 'id_sales')) {
                $table->index('id_sales', 'idx_quotation_id_sales');
            }
            if (Schema::hasColumn('quotation', 'id_pic')) {
                $table->index('id_pic', 'idx_quotation_id_pic');
            }
            if (Schema::hasColumn('quotation', 'status')) {
                $table->index('status', 'idx_quotation_status');
            }
            if (Schema::hasColumn('quotation', 'primary_id')) {
                $table->index('primary_id', 'idx_quotation_primary_id');
            }
            if (Schema::hasColumn('quotation', 'is_primary')) {
                $table->index('is_primary', 'idx_quotation_is_primary');
            }
        });

        Schema::table('unit_quotation', function (Blueprint $table) {
            if (Schema::hasColumn('unit_quotation', 'id_sales')) {
                $table->index('id_sales', 'idx_unit_quotation_id_sales');
            }
            if (Schema::hasColumn('unit_quotation', 'id_client')) {
                $table->index('id_client', 'idx_unit_quotation_id_client');
            }
            if (Schema::hasColumn('unit_quotation', 'status')) {
                $table->index('status', 'idx_unit_quotation_status');
            }
            if (Schema::hasColumn('unit_quotation', 'is_latest')) {
                $table->index('is_latest', 'idx_unit_quotation_is_latest');
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
        Schema::table('quotation', function (Blueprint $table) {
            $table->dropIndex('idx_quotation_id_sales');
            $table->dropIndex('idx_quotation_id_pic');
            $table->dropIndex('idx_quotation_status');
            $table->dropIndex('idx_quotation_primary_id');
            $table->dropIndex('idx_quotation_is_primary');
        });

        Schema::table('unit_quotation', function (Blueprint $table) {
            $table->dropIndex('idx_unit_quotation_id_sales');
            $table->dropIndex('idx_unit_quotation_id_client');
            $table->dropIndex('idx_unit_quotation_status');
            $table->dropIndex('idx_unit_quotation_is_latest');
        });
    }
};
