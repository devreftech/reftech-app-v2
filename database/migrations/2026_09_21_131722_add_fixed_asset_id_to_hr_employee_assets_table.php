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
        Schema::table('hr_employee_assets', function (Blueprint $table) {
            $table->unsignedBigInteger('fixed_asset_id')->nullable()->after('employee_id');
            $table->foreign('fixed_asset_id')->references('id')->on('fixed_asset')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hr_employee_assets', function (Blueprint $table) {
            $table->dropForeign(['fixed_asset_id']);
            $table->dropColumn('fixed_asset_id');
        });
    }
};
