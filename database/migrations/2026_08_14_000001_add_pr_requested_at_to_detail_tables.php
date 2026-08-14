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
        Schema::table('detail_quotation', function (Blueprint $table) {
            $table->timestamp('pr_requested_at')->nullable()->after('status');
            $table->integer('pr_qty_needed')->nullable()->after('pr_requested_at');
        });
        Schema::table('detail_pending_po', function (Blueprint $table) {
            $table->timestamp('pr_requested_at')->nullable()->after('status');
            $table->integer('pr_qty_needed')->nullable()->after('pr_requested_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('detail_quotation', function (Blueprint $table) {
            $table->dropColumn(['pr_requested_at', 'pr_qty_needed']);
        });
        Schema::table('detail_pending_po', function (Blueprint $table) {
            $table->dropColumn(['pr_requested_at', 'pr_qty_needed']);
        });
    }
};
