<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_request', function (Blueprint $table) {
            $table->dropColumn([
                'id_equivalent',
                'qty',
                'price',
                'amount',
                'note',
                'purchase_type',
                'cargo',
                'no_resi',
                'purchase_date',
                'qty_received',
                'gr_status',
                'gr_note',
                'no_do',
                'gr_date',
            ]);
        });

        Schema::table('purchase_request', function (Blueprint $table) {
            $table->unique('no_pr');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_request', function (Blueprint $table) {
            $table->dropUnique(['no_pr']);
        });

        Schema::table('purchase_request', function (Blueprint $table) {
            $table->unsignedBigInteger('id_equivalent')->nullable();
            $table->integer('qty')->nullable();
            $table->bigInteger('price')->nullable();
            $table->bigInteger('amount')->nullable();
            $table->longText('note')->nullable();
            $table->string('purchase_type')->nullable();
            $table->string('cargo')->nullable();
            $table->string('no_resi')->nullable();
            $table->date('purchase_date')->nullable();
            $table->integer('qty_received')->nullable();
            $table->string('gr_status')->nullable();
            $table->text('gr_note')->nullable();
            $table->string('no_do')->nullable();
            $table->date('gr_date')->nullable();
        });
    }
};
