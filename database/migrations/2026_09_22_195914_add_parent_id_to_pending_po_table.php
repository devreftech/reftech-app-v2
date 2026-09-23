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
        Schema::table('pending_po', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id_unit_quotation')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pending_po', function (Blueprint $table) {
            $table->dropColumn('parent_id');
        });
    }
};
