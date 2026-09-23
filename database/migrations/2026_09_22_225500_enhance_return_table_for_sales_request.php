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
        Schema::table('return', function (Blueprint $table) {
            if (!Schema::hasColumn('return', 'id_quotation')) {
                $table->unsignedBigInteger('id_quotation')->nullable()->after('id_pending');
            }
            if (!Schema::hasColumn('return', 'id_unit_quotation')) {
                $table->unsignedBigInteger('id_unit_quotation')->nullable()->after('id_quotation');
            }
            if (!Schema::hasColumn('return', 'id_sales')) {
                $table->unsignedBigInteger('id_sales')->nullable()->after('id_unit_quotation');
            }
            if (!Schema::hasColumn('return', 'reason_category')) {
                $table->string('reason_category', 100)->nullable()->after('status');
            }
            if (!Schema::hasColumn('return', 'reason_note')) {
                $table->text('reason_note')->nullable()->after('reason_category');
            }
            if (!Schema::hasColumn('return', 'resolution')) {
                $table->string('resolution', 50)->nullable()->after('reason_note'); // replacement, refund, deposit
            }
            if (!Schema::hasColumn('return', 'bank_name')) {
                $table->string('bank_name', 100)->nullable()->after('resolution');
            }
            if (!Schema::hasColumn('return', 'bank_account')) {
                $table->string('bank_account', 100)->nullable()->after('bank_name');
            }
            if (!Schema::hasColumn('return', 'bank_holder')) {
                $table->string('bank_holder', 150)->nullable()->after('bank_account');
            }
            if (!Schema::hasColumn('return', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('bank_holder');
            }
        });

        Schema::table('detail_return', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_return', 'id_detail_quotation')) {
                $table->unsignedBigInteger('id_detail_quotation')->nullable()->after('id_replacement');
            }
            if (!Schema::hasColumn('detail_return', 'id_unit_quotation_detail')) {
                $table->unsignedBigInteger('id_unit_quotation_detail')->nullable()->after('id_detail_quotation');
            }
            if (!Schema::hasColumn('detail_return', 'item_name')) {
                $table->string('item_name', 255)->nullable()->after('id_unit_quotation_detail');
            }
            if (!Schema::hasColumn('detail_return', 'price')) {
                $table->decimal('price', 15, 2)->default(0)->after('qty');
            }
            if (!Schema::hasColumn('detail_return', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0)->after('price');
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
        Schema::table('return', function (Blueprint $table) {
            $table->dropColumn([
                'id_quotation',
                'id_unit_quotation',
                'id_sales',
                'reason_category',
                'reason_note',
                'resolution',
                'bank_name',
                'bank_account',
                'bank_holder',
                'total_amount',
            ]);
        });

        Schema::table('detail_return', function (Blueprint $table) {
            $table->dropColumn([
                'id_detail_quotation',
                'id_unit_quotation_detail',
                'item_name',
                'price',
                'amount',
            ]);
        });
    }
};
