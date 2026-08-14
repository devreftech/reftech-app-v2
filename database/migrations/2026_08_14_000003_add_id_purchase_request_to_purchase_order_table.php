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
            $table->unsignedBigInteger('id_purchase_request')->nullable()->after('id_supplier');
            $table->foreign('id_purchase_request')->references('id')->on('purchase_request')->nullOnDelete();
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
            $table->dropForeign(['id_purchase_request']);
            $table->dropColumn('id_purchase_request');
        });
    }
};
