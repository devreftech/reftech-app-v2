<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('unit_inventory_rebranding_costs', function (Blueprint $table) {
            $table->date('date')->nullable()->after('id_unit_inventory');
        });
    }

    public function down()
    {
        Schema::table('unit_inventory_rebranding_costs', function (Blueprint $table) {
            $table->dropColumn('date');
        });
    }
};
