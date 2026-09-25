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
        Schema::table('fixed_asset_construction_costs', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_id')->nullable()->after('fixed_asset_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fixed_asset_construction_costs', function (Blueprint $table) {
            $table->dropColumn('purchase_order_id');
        });
    }
};
