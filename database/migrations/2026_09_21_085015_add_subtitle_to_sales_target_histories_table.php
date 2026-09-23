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
        Schema::table('sales_target_histories', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('sales_type');
            $table->string('subtitle')->nullable()->after('display_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales_target_histories', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'subtitle']);
        });
    }
};
