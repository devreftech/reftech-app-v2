<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Kolom id_rental_accessory di detail_purchase_order
        if (Schema::hasTable('detail_purchase_order') && !Schema::hasColumn('detail_purchase_order', 'id_rental_accessory')) {
            Schema::table('detail_purchase_order', function (Blueprint $table) {
                $table->unsignedBigInteger('id_rental_accessory')->nullable()->after('id_unit');
            });
        }

        // 2. Kolom id_purchase_order di rental_accessories
        if (Schema::hasTable('rental_accessories') && !Schema::hasColumn('rental_accessories', 'id_purchase_order')) {
            Schema::table('rental_accessories', function (Blueprint $table) {
                $table->unsignedBigInteger('id_purchase_order')->nullable()->after('code');
            });
        }

        // 3. Pastikan tipe PO 'Accessories' terdaftar di purchase_order_types
        if (Schema::hasTable('purchase_order_types')) {
            $exists = DB::table('purchase_order_types')->where('name', 'Accessories')->exists();
            if (!$exists) {
                DB::table('purchase_order_types')->insert([
                    'name' => 'Accessories',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('detail_purchase_order') && Schema::hasColumn('detail_purchase_order', 'id_rental_accessory')) {
            Schema::table('detail_purchase_order', function (Blueprint $table) {
                $table->dropColumn('id_rental_accessory');
            });
        }

        if (Schema::hasTable('rental_accessories') && Schema::hasColumn('rental_accessories', 'id_purchase_order')) {
            Schema::table('rental_accessories', function (Blueprint $table) {
                $table->dropColumn('id_purchase_order');
            });
        }
    }
};
