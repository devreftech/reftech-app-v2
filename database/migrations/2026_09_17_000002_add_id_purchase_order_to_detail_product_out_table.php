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
        if (Schema::hasTable('detail_product_out') && !Schema::hasColumn('detail_product_out', 'id_purchase_order')) {
            Schema::table('detail_product_out', function (Blueprint $table) {
                $table->unsignedBigInteger('id_purchase_order')->nullable()->after('id_product_out')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('detail_product_out') && Schema::hasColumn('detail_product_out', 'id_purchase_order')) {
            Schema::table('detail_product_out', function (Blueprint $table) {
                $table->dropIndex(['id_purchase_order']);
                $table->dropColumn('id_purchase_order');
            });
        }
    }
};
