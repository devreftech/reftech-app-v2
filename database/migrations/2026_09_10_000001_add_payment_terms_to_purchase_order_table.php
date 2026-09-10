<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menstruktur-kan termin pembayaran PO supaya alur Account Payable bisa
     * menentukan jatuh tempo dengan benar:
     *   - payment_type: cash | transfer | tempo
     *   - top_days: termin hari (hanya relevan saat 'tempo')
     *   - due_date_estimate: estimasi jatuh tempo yang diisi saat PO dibuat
     * Due date final tetap dihitung ulang saat invoice supplier diupload
     * (POController::uploadInvoice), berbasis tanggal invoice + top_days.
     */
    public function up()
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->string('payment_type', 20)->default('cash')->after('payment');
            $table->unsignedSmallInteger('top_days')->nullable()->after('payment_type');
            $table->date('due_date_estimate')->nullable()->after('top_days');
            // Tanggal invoice supplier — diisi saat uploadInvoice, dipakai sebagai
            // basis perhitungan jatuh tempo AP (termasuk kalau invoice diupload
            // sebelum Goods Receipt dibuat).
            $table->date('invoice_date')->nullable()->after('no_invoice_supplier');
        });

        // Backfill dari kolom teks 'payment' lama (non-destruktif).
        DB::table('purchase_order')->orderBy('id')->chunkById(500, function ($rows) {
            foreach ($rows as $row) {
                $payment = (string) ($row->payment ?? '');

                if (preg_match('/(\d+)\s*days?\s*after\s*invoice/i', $payment, $m)) {
                    DB::table('purchase_order')->where('id', $row->id)->update([
                        'payment_type' => 'tempo',
                        'top_days'     => (int) $m[1],
                    ]);
                } elseif (preg_match('/\btempo\b|\bcredit\b|\bn\/?30\b|\bn\/?60\b/i', $payment)) {
                    DB::table('purchase_order')->where('id', $row->id)->update([
                        'payment_type' => 'tempo',
                        'top_days'     => 30,
                    ]);
                } else {
                    // Cash Before Delivery / DP xx% & BP xx% / kosong / lainnya.
                    DB::table('purchase_order')->where('id', $row->id)->update([
                        'payment_type' => 'cash',
                    ]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'top_days', 'due_date_estimate', 'invoice_date']);
        });
    }
};
