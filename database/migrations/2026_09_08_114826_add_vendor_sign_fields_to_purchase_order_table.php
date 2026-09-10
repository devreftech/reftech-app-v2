<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order', 'sign_token')) {
                $table->string('sign_token', 64)->nullable()->unique()->after('note');
            }
            if (!Schema::hasColumn('purchase_order', 'vendor_signature')) {
                $table->string('vendor_signature')->nullable()->after('sign_token');
            }
            if (!Schema::hasColumn('purchase_order', 'vendor_signer_name')) {
                $table->string('vendor_signer_name')->nullable()->after('vendor_signature');
            }
            if (!Schema::hasColumn('purchase_order', 'vendor_signer_position')) {
                $table->string('vendor_signer_position')->nullable()->after('vendor_signer_name');
            }
            if (!Schema::hasColumn('purchase_order', 'vendor_signed_stamp')) {
                $table->string('vendor_signed_stamp')->nullable()->after('vendor_signer_position');
            }
            if (!Schema::hasColumn('purchase_order', 'vendor_signed_at')) {
                $table->dateTime('vendor_signed_at')->nullable()->after('vendor_signed_stamp');
            }
            if (!Schema::hasColumn('purchase_order', 'vendor_ip')) {
                $table->string('vendor_ip', 45)->nullable()->after('vendor_signed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_order', function (Blueprint $table) {
            $table->dropColumn([
                'sign_token',
                'vendor_signature',
                'vendor_signer_name',
                'vendor_signer_position',
                'vendor_signed_stamp',
                'vendor_signed_at',
                'vendor_ip',
            ]);
        });
    }
};
