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
            if (!Schema::hasColumn('purchase_order', 'no_reference')) {
                $table->string('no_reference')->nullable()->after('no_po');
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
            if (Schema::hasColumn('purchase_order', 'no_reference')) {
                $table->dropColumn('no_reference');
            }
        });
    }
};
