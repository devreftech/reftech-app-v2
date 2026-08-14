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
        Schema::create('purchase_request_detail_allocation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_purchase_request_detail');
            $table->unsignedBigInteger('id_purchase_order');
            $table->integer('qty');
            $table->timestamps();

            $table->foreign('id_purchase_request_detail', 'pr_detail_alloc_detail_fk')
                ->references('id')->on('purchase_request_detail')->onDelete('cascade');
            $table->foreign('id_purchase_order', 'pr_detail_alloc_po_fk')
                ->references('id')->on('purchase_order')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_request_detail_allocation');
    }
};
