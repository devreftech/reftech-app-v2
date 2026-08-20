<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_in_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_product_in');
            $table->string('label');
            $table->integer('amount');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index('id_product_in');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_in_costs');
    }
};
