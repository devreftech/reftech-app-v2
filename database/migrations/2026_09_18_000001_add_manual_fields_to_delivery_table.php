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
        Schema::table('delivery', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery', 'no_do')) {
                $table->string('no_do')->nullable()->after('id');
            }
            if (!Schema::hasColumn('delivery', 'entity')) {
                $table->string('entity', 50)->nullable()->default('Reftech')->after('no_do');
            }
            if (!Schema::hasColumn('delivery', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('id_unit_quotation');
            }
            if (!Schema::hasColumn('delivery', 'address')) {
                $table->text('address')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('delivery', 'po_number')) {
                $table->string('po_number')->nullable()->after('address');
            }
            if (!Schema::hasColumn('delivery', 'driver_name')) {
                $table->string('driver_name')->nullable()->after('destination');
            }
            if (!Schema::hasColumn('delivery', 'vehicle_no')) {
                $table->string('vehicle_no')->nullable()->after('driver_name');
            }
            if (!Schema::hasColumn('delivery', 'note')) {
                $table->text('note')->nullable()->after('vehicle_no');
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
        Schema::table('delivery', function (Blueprint $table) {
            $columns = [
                'no_do',
                'entity',
                'customer_name',
                'address',
                'po_number',
                'driver_name',
                'vehicle_no',
                'note',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('delivery', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
