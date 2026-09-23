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
        Schema::table('detail_product', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_product', 'is_opname')) {
                $table->boolean('is_opname')->default(true)->after('stock');
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
        Schema::table('detail_product', function (Blueprint $table) {
            if (Schema::hasColumn('detail_product', 'is_opname')) {
                $table->dropColumn('is_opname');
            }
        });
    }
};
