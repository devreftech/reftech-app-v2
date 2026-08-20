<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Info GR (qty_received/gr_status/gr_note/no_do/gr_date/warehouse) sebelumnya
    // nempel di purchase_request_detail (per item PR), padahal satu item bisa
    // split qty ke beberapa PO — jadi penerimaan barangnya juga per alokasi
    // (per item, per PO), bukan per item PR secara keseluruhan.
    public function up(): void
    {
        Schema::table('purchase_request_detail_allocation', function (Blueprint $table) {
            $table->integer('qty_received')->nullable();
            $table->string('gr_status')->nullable();
            $table->text('gr_note')->nullable();
            $table->string('no_do')->nullable();
            $table->date('gr_date')->nullable();
            $table->string('warehouse')->nullable();
        });

        Schema::table('purchase_request_detail', function (Blueprint $table) {
            $table->dropColumn(['qty_received', 'gr_status', 'gr_note', 'no_do', 'gr_date', 'warehouse']);
        });
    }

    public function down(): void
    {
        Schema::table('purchase_request_detail', function (Blueprint $table) {
            $table->integer('qty_received')->nullable();
            $table->string('gr_status')->nullable();
            $table->text('gr_note')->nullable();
            $table->string('no_do')->nullable();
            $table->date('gr_date')->nullable();
            $table->string('warehouse')->nullable();
        });

        Schema::table('purchase_request_detail_allocation', function (Blueprint $table) {
            $table->dropColumn(['qty_received', 'gr_status', 'gr_note', 'no_do', 'gr_date', 'warehouse']);
        });
    }
};
