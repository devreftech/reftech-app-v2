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
        Schema::create('online_leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sales')->index();
            $table->unsignedBigInteger('id_client')->nullable()->index();
            $table->unsignedBigInteger('id_quotation')->nullable()->index();
            $table->unsignedBigInteger('id_unit_quotation')->nullable()->index();
            $table->string('channel', 80)->index();
            $table->enum('customer_type', ['User', 'Reseller'])->default('User')->index();
            $table->string('name', 255);
            $table->string('phone', 50);
            $table->string('company', 255)->nullable();
            $table->text('product_interest')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['new', 'contacted', 'quoted', 'deal', 'lost'])->default('new')->index();
            $table->date('date')->index();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            // Foreign key constraints where appropriate
            $table->foreign('id_sales')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('online_leads');
    }
};
