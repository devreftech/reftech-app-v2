<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_quotation_payment_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_payment');
            $table->unsignedBigInteger('id_unit_quotation');
            $table->unsignedBigInteger('id_user');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('id_payment')->references('id')->on('payment')->onDelete('cascade');
            $table->foreign('id_unit_quotation')->references('id')->on('unit_quotation')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->index(['id_user', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_quotation_payment_notifications');
    }
};
