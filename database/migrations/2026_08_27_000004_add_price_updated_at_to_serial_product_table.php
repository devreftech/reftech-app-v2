<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('serial_product', function (Blueprint $table) {
            // Nyimpen kapan TERAKHIR kolom `price` berubah. Beda sama `updated_at`
            // yang ikut ke-bump tiap edit brand/pn/image. Di-maintain otomatis
            // lewat model event SerialProduct::updating (cek isDirty('price')).
            $table->timestamp('price_updated_at')->nullable()->after('price');
        });

        // Data lama: belum ada history perubahan harga, jadi pakai updated_at
        // sebagai perkiraan terakhir harga disentuh.
        DB::statement('UPDATE serial_product SET price_updated_at = updated_at');
    }

    public function down(): void
    {
        Schema::table('serial_product', function (Blueprint $table) {
            $table->dropColumn('price_updated_at');
        });
    }
};
