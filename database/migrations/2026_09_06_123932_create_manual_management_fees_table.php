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
        Schema::create('manual_management_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->string('custom_company_name', 255)->nullable();
            $table->date('date');
            $table->string('title', 255);
            $table->string('reference_no', 100)->nullable();
            $table->decimal('gross_fee', 15, 2)->default(0);
            $table->string('fee_bank_name', 100)->nullable();
            $table->string('fee_bank_branch', 100)->nullable();
            $table->string('fee_bank_account', 100)->nullable();
            $table->string('fee_bank_holder', 150)->nullable();
            $table->string('fee_payment_status', 50)->default('unpaid');
            $table->dateTime('fee_transfer_date')->nullable();
            $table->string('fee_transfer_proof', 255)->nullable();
            $table->text('fee_transfer_note')->nullable();
            $table->unsignedBigInteger('fee_paid_by')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_management_fees');
    }
};
