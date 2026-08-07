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
        Schema::create('quotation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pic');
            $table->foreignId('id_sales');
            $table->foreignId('id_admin')->nullable();
            $table->foreignId('id_service')->nullable();
            $table->foreignId('id_support')->nullable();
            $table->integer('primary_id');
            $table->enum('is_primary',['0', '1']);
            $table->integer('num_rev');
            $table->string('no_pr')->nullable();
            $table->enum('destination',['1', '2']);
            $table->string('title');
            $table->integer('status');
            $table->string('note');
            $table->longText('comment')->nullable();
            $table->longText('commentAdmin')->nullable();
            $table->enum('flag',['Reftech','Kojisha']);
            $table->enum('type',['Sparepart','Service']);

            //Quote Date
            $table->date('status_date');
            $table->date('estimated_date');
            $table->date('expired_date');
            $table->date('po_date')->nullable();
            $table->date('upload_date')->nullable();

            $table->string('po_file')->nullable();
            $table->enum('level',['0', '1']);
            //price 
            $table->integer('tax');
            $table->integer('shipping');
            $table->string('no_quote');
            $table->integer('diskon');
            $table->integer('fee');
            $table->integer('nett');
            $table->integer('subtotal');
            $table->integer('total_no_tax');
            $table->integer('harga_total');
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
        Schema::dropIfExists('quotation');
    }
};
