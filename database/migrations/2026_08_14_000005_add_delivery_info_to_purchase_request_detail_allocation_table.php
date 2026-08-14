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
        Schema::table('purchase_request_detail_allocation', function (Blueprint $table) {
            $table->string('purchase_type')->nullable()->after('qty');
            $table->string('cargo')->nullable()->after('purchase_type');
            $table->string('no_resi')->nullable()->after('cargo');
            $table->date('purchase_date')->nullable()->after('no_resi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_request_detail_allocation', function (Blueprint $table) {
            $table->dropColumn(['purchase_type', 'cargo', 'no_resi', 'purchase_date']);
        });
    }
};
