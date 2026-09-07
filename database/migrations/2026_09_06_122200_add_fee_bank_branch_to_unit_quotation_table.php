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
        if (Schema::hasTable('unit_quotation')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                if (!Schema::hasColumn('unit_quotation', 'fee_bank_branch')) {
                    $table->string('fee_bank_branch', 100)->nullable()->after('fee_bank_holder');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('unit_quotation')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                if (Schema::hasColumn('unit_quotation', 'fee_bank_branch')) {
                    $table->dropColumn('fee_bank_branch');
                }
            });
        }
    }
};
