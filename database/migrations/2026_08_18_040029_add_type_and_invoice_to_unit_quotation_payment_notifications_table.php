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
     * @return void
     */
    public function up()
    {
        Schema::table('unit_quotation_payment_notifications', function (Blueprint $table) {
            $table->string('type')->default('payment')->after('id_user');
            $table->unsignedBigInteger('id_invoice')->nullable()->after('id_payment');

            $table->foreign('id_invoice')->references('id')->on('invoice')->onDelete('cascade');
        });

        // id_payment tidak lagi wajib diisi untuk notifikasi tipe 'po_uploaded'
        // (tidak diikat ke record Payment, melainkan ke Invoice yang menunggu diterbitkan).
        DB::statement('ALTER TABLE unit_quotation_payment_notifications MODIFY id_payment BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unit_quotation_payment_notifications', function (Blueprint $table) {
            $table->dropForeign(['id_invoice']);
            $table->dropColumn(['type', 'id_invoice']);
        });

        DB::statement('ALTER TABLE unit_quotation_payment_notifications MODIFY id_payment BIGINT UNSIGNED NOT NULL');
    }
};
