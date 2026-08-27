<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        $hasIndex = function ($table, $name) {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$name}'");
            return count($indexes) > 0;
        };

        if (!$hasIndex('detail_product_out', 'idx_detail_product_out_id_po')) {
            Schema::table('detail_product_out', function (Blueprint $table) {
                $table->index('id_product_out', 'idx_detail_product_out_id_po');
            });
        }
        if (!$hasIndex('detail_product_out', 'idx_detail_product_out_id_sp')) {
            Schema::table('detail_product_out', function (Blueprint $table) {
                $table->index('id_serial_product', 'idx_detail_product_out_id_sp');
            });
        }
        if (!$hasIndex('detail_product_out', 'idx_detail_product_out_id_dp')) {
            Schema::table('detail_product_out', function (Blueprint $table) {
                $table->index('id_detail_product', 'idx_detail_product_out_id_dp');
            });
        }
        if (!$hasIndex('product', 'idx_product_category')) {
            Schema::table('product', function (Blueprint $table) {
                $table->index('category', 'idx_product_category');
            });
        }
    }

    public function down()
    {
        Schema::table('detail_product_out', function (Blueprint $table) {
            $table->dropIndex('idx_detail_product_out_id_po');
            $table->dropIndex('idx_detail_product_out_id_sp');
            $table->dropIndex('idx_detail_product_out_id_dp');
        });

        Schema::table('product', function (Blueprint $table) {
            $table->dropIndex('idx_product_category');
        });
    }
};
