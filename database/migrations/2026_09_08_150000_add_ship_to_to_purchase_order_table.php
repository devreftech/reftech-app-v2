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
        Schema::table('purchase_order', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order', 'ship_to')) {
                $table->text('ship_to')->nullable()->after('delivery');
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
        Schema::table('purchase_order', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_order', 'ship_to')) {
                $table->dropColumn('ship_to');
            }
        });
    }
};
