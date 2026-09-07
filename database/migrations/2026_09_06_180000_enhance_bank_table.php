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
        Schema::table('bank', function (Blueprint $table) {
            if (!Schema::hasColumn('bank', 'initial_balance')) {
                $table->decimal('initial_balance', 18, 2)->default(0)->after('bank');
            }
            if (!Schema::hasColumn('bank', 'atas_nama')) {
                $table->string('atas_nama')->nullable()->after('no_rek');
            }
            if (!Schema::hasColumn('bank', 'branch')) {
                $table->string('branch')->nullable()->after('atas_nama');
            }
            if (!Schema::hasColumn('bank', 'is_active')) {
                $table->boolean('is_active')->default(1)->after('saldo');
            }
            if (!Schema::hasColumn('bank', 'description')) {
                $table->text('description')->nullable()->after('is_active');
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
        Schema::table('bank', function (Blueprint $table) {
            $table->dropColumn(['initial_balance', 'atas_nama', 'branch', 'is_active', 'description']);
        });
    }
};
