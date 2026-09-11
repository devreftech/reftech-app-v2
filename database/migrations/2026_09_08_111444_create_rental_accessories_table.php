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
        if (!Schema::hasTable('rental_accessories')) {
            Schema::create('rental_accessories', function (Blueprint $table) {
                $table->id();
                $table->string('category'); // 'hose', 'header', 'reducer', 'cable', 'nipple'
                $table->string('code')->nullable(); // Kode Item / Barcode / Asset Code
                $table->string('name'); // Nama / Deskripsi
                $table->string('brand')->nullable(); // Merk
                $table->string('size')->nullable(); // Ukuran / Diameter / Thread
                $table->string('length')->nullable(); // Panjang (e.g. 5 Meter, 10 Meter)
                $table->string('max_pressure')->nullable(); // Max Bar / PSI
                $table->string('connection')->nullable(); // Tipe Koneksi (Fitting, Flange, Camlock, Plug, etc.)
                $table->string('material')->nullable(); // Material (Rubber, SS, Carbon Steel, etc.)
                $table->string('extra_spec')->nullable(); // Spesifikasi Tambahan (Port Outlet, Ampere, dll)
                $table->integer('stock')->default(1); // Qty / Stok
                $table->string('condition')->default('ok'); // ok (Bagus), fair (Cukup), damaged (Rusak)
                $table->string('rental_status')->default('available'); // available, rental, reserved, maintenance, broken
                $table->string('location')->nullable(); // Lokasi Rak / Gudang
                $table->text('notes')->nullable(); // Catatan
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rental_accessories');
    }
};
