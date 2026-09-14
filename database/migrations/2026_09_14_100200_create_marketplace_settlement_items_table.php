<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('marketplace_settlement_items')) {
            Schema::create('marketplace_settlement_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_marketplace_settlement');
                $table->unsignedBigInteger('id_payment');
                $table->double('amount')->default(0);
                $table->timestamps();

                $table->foreign('id_marketplace_settlement', 'mkt_settlement_items_settlement_fk')
                    ->references('id')->on('marketplace_settlements')->cascadeOnDelete();
                $table->foreign('id_payment', 'mkt_settlement_items_payment_fk')
                    ->references('id')->on('payment');
                $table->unique(['id_marketplace_settlement', 'id_payment'], 'mkt_settlement_items_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_settlement_items');
    }
};
