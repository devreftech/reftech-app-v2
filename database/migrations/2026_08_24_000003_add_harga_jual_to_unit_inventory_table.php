<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_inventory', function (Blueprint $table) {
            // Harga tawar/listing buat unit ini selagi status Available — beda dari
            // total_modal (harga pokok/beli). Diisi manual, gak otomatis ke-hitung.
            $table->decimal('harga_jual', 15, 2)->nullable()->after('total_modal');
        });
    }

    public function down(): void
    {
        Schema::table('unit_inventory', function (Blueprint $table) {
            $table->dropColumn('harga_jual');
        });
    }
};
