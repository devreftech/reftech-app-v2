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
        if (!Schema::hasTable('purchase_payments')) {
            Schema::create('purchase_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_product_in')->nullable()->index();
                $table->unsignedBigInteger('id_project_expense')->nullable()->index();
                $table->unsignedBigInteger('id_supplier')->nullable()->index();
                $table->unsignedBigInteger('id_bank')->nullable()->index();
                $table->string('payment_number')->nullable();
                $table->date('date')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->string('payment_method')->default('Transfer Bank');
                $table->string('proof_file')->nullable();
                $table->text('note')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('purchase_payments');
    }
};
