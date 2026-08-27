<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->timestamp('on_delivery_at')->nullable()->after('receipt_status');
            $table->string('on_delivery_cargo')->nullable()->after('on_delivery_at');
            $table->string('on_delivery_no_resi')->nullable()->after('on_delivery_cargo');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->dropColumn(['on_delivery_at', 'on_delivery_cargo', 'on_delivery_no_resi']);
        });
    }
};
