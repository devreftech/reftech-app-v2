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
        Schema::table('unit', function (Blueprint $table) {
            if (!Schema::hasColumn('unit', 'cooling_capacity')) {
                $table->string('cooling_capacity', 255)->nullable()->after('power');
            }
            if (!Schema::hasColumn('unit', 'power_input')) {
                $table->string('power_input', 255)->nullable()->after('cooling_capacity');
            }
            if (!Schema::hasColumn('unit', 'evaporator')) {
                $table->string('evaporator', 255)->nullable()->after('refrigerant_type');
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
        Schema::table('unit', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('unit', 'cooling_capacity')) $cols[] = 'cooling_capacity';
            if (Schema::hasColumn('unit', 'power_input')) $cols[] = 'power_input';
            if (Schema::hasColumn('unit', 'evaporator')) $cols[] = 'evaporator';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
