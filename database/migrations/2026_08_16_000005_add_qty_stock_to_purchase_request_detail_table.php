<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // qty_stock = qty tambahan yang diminta Logistic/Admin buat buffer stok gudang,
    // terpisah dari `qty` yang murni kebutuhan SO/customer (turun dari quotation) —
    // biar keduanya tetap bisa dibedakan/di-audit belakangan, nggak tercampur.
    public function up(): void
    {
        Schema::table('purchase_request_detail', function (Blueprint $table) {
            $table->integer('qty_stock')->nullable()->default(0)->after('qty');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_request_detail', function (Blueprint $table) {
            $table->dropColumn('qty_stock');
        });
    }
};
