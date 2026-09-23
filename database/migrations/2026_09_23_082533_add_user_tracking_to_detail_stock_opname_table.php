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
        Schema::table('detail_stock_opname', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_stock_opname', 'id_user_bdg')) {
                $table->unsignedBigInteger('id_user_bdg')->nullable()->after('stock_bdg');
            }
            if (!Schema::hasColumn('detail_stock_opname', 'id_user_bks')) {
                $table->unsignedBigInteger('id_user_bks')->nullable()->after('stock_bks');
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
        Schema::table('detail_stock_opname', function (Blueprint $table) {
            if (Schema::hasColumn('detail_stock_opname', 'id_user_bdg')) {
                $table->dropColumn('id_user_bdg');
            }
            if (Schema::hasColumn('detail_stock_opname', 'id_user_bks')) {
                $table->dropColumn('id_user_bks');
            }
        });
    }
};
