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
        Schema::table('fixed_asset', function (Blueprint $table) {
            if (!Schema::hasColumn('fixed_asset', 'harga_rental_hari')) {
                $table->unsignedBigInteger('harga_rental_hari')->nullable()->after('harga_jual');
            }
            if (!Schema::hasColumn('fixed_asset', 'harga_rental_bulan')) {
                $table->unsignedBigInteger('harga_rental_bulan')->nullable()->after('harga_rental_hari');
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
        Schema::table('fixed_asset', function (Blueprint $table) {
            if (Schema::hasColumn('fixed_asset', 'harga_rental_bulan')) {
                $table->dropColumn('harga_rental_bulan');
            }
            if (Schema::hasColumn('fixed_asset', 'harga_rental_hari')) {
                $table->dropColumn('harga_rental_hari');
            }
        });
    }
};
