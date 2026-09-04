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
            if (!Schema::hasColumn('unit', 'speed_type')) {
                $table->string('speed_type', 50)->nullable()->after('type_unit');
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
            if (Schema::hasColumn('unit', 'speed_type')) {
                $table->dropColumn('speed_type');
            }
        });
    }
};
