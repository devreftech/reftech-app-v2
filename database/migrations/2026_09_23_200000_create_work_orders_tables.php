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
        if (!Schema::hasTable('work_orders')) {
            Schema::create('work_orders', function (Blueprint $table) {
                $table->id();
                $table->string('no_wo')->unique();
                $table->unsignedBigInteger('id_fixed_asset')->nullable()->index();
                $table->unsignedBigInteger('id_machine')->nullable()->index();
                $table->unsignedBigInteger('id_user_created')->index();
                $table->unsignedBigInteger('id_user_technician')->nullable()->index();
                $table->unsignedBigInteger('id_user_warehouse')->nullable()->index();
                $table->unsignedBigInteger('id_user_accounting')->nullable()->index();
                $table->date('date');
                $table->date('target_date')->nullable();
                $table->string('status', 50)->default('pending_warehouse')->index();
                $table->string('accounting_treatment', 30)->nullable(); // expense | capitalize
                $table->unsignedBigInteger('id_purchase_request')->nullable()->index();
                $table->unsignedBigInteger('id_product_out')->nullable()->index();
                $table->text('description')->nullable();
                $table->text('warehouse_note')->nullable();
                $table->text('accounting_note')->nullable();
                $table->text('rejected_reason')->nullable();
                $table->decimal('total_cost', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('work_order_items')) {
            Schema::create('work_order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_work_order')->index();
                $table->unsignedBigInteger('id_product')->nullable()->index();
                $table->unsignedBigInteger('id_detail_product')->nullable()->index();
                $table->unsignedBigInteger('id_equivalent')->nullable()->index();
                $table->string('warehouse', 20)->default('BDG');
                $table->decimal('qty_requested', 10, 2)->default(1);
                $table->decimal('qty_approved', 10, 2)->default(1);
                $table->decimal('qty_issued', 10, 2)->default(0);
                $table->decimal('stock_at_check', 10, 2)->default(0);
                $table->decimal('unit_price', 15, 2)->default(0);
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->boolean('needs_pr')->default(false);
                $table->string('note', 255)->nullable();
                $table->timestamps();

                $table->foreign('id_work_order')->references('id')->on('work_orders')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_order_items');
        Schema::dropIfExists('work_orders');
    }
};
