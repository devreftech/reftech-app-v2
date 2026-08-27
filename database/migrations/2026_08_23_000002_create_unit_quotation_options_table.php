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
        // 1 Smart Quote bisa punya >1 "Opsi" perbandingan harga (misal: Unit Baru vs
        // Unit Second) — tiap opsi punya set item & finance summary sendiri-sendiri.
        // Note/Terms & Conditions tetap 1 aja, disimpan di unit_quotation seperti biasa.
        Schema::create('unit_quotation_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_unit_quotation');
            $table->string('title');
            $table->unsignedInteger('sort_order')->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->string('diskon_type')->default('percent');
            $table->boolean('tax')->default(false);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_unit_quotation')->references('id')->on('unit_quotation')->onDelete('cascade');
        });

        Schema::table('unit_quotation_detail', function (Blueprint $table) {
            // Nullable — detail lama (dibuat sebelum fitur opsi ada) tetap valid tanpa opsi.
            $table->unsignedBigInteger('id_option')->nullable()->after('id_unit_quotation');
            $table->foreign('id_option')->references('id')->on('unit_quotation_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unit_quotation_detail', function (Blueprint $table) {
            $table->dropForeign(['id_option']);
            $table->dropColumn('id_option');
        });

        Schema::dropIfExists('unit_quotation_options');
    }
};
