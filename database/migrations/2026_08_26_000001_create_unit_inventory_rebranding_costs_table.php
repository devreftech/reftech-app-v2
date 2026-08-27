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
        // Rincian biaya rebranding per unit fisik (unit_inventory) — diisi belakangan
        // setelah GR, bisa lebih dari satu baris (cat, stiker, ongkos kerja, dst).
        // unit_inventory.biaya_rebranding/total_modal di-sync ulang tiap kali baris
        // di sini ditambah/dihapus, lihat UnitProductInController.
        Schema::create('unit_inventory_rebranding_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_unit_inventory');
            $table->string('item');
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('note')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('id_unit_inventory')->references('id')->on('unit_inventory')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unit_inventory_rebranding_costs');
    }
};
