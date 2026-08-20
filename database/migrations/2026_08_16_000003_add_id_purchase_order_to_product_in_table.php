<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // product_in yang lahir dari Goods Receipt sebelumnya cuma nyebut PO-nya lewat
    // teks bebas di kolom note — nggak ada link terstruktur balik ke purchase_order,
    // jadi nggak bisa dipakai buat referensi harga/HPP pas admin ngisi invoicing.
    public function up(): void
    {
        Schema::table('product_in', function (Blueprint $table) {
            $table->unsignedBigInteger('id_purchase_order')->nullable()->after('id_supplier');
        });

        // Backfill baris lama yang lahir dari Goods Receipt PO (sebelum kolom ini ada),
        // no PO-nya cuma ketulis di teks note, cocokkan balik ke purchase_order.no_po.
        $rows = \Illuminate\Support\Facades\DB::table('product_in')
            ->where('note', 'like', 'Otomatis dibuat via Goods Receipt PO %')
            ->get(['id', 'note']);

        foreach ($rows as $row) {
            $noPo = trim(str_replace('Otomatis dibuat via Goods Receipt PO ', '', $row->note));
            $po = \Illuminate\Support\Facades\DB::table('purchase_order')->where('no_po', $noPo)->first();
            if ($po) {
                \Illuminate\Support\Facades\DB::table('product_in')->where('id', $row->id)->update(['id_purchase_order' => $po->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('product_in', function (Blueprint $table) {
            $table->dropColumn('id_purchase_order');
        });
    }
};
