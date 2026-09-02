<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('unit_quotation')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                if (!Schema::hasColumn('unit_quotation', 'fee_bank_name')) {
                    $table->string('fee_bank_name', 100)->nullable()->after('fee_note');
                }
                if (!Schema::hasColumn('unit_quotation', 'fee_bank_account')) {
                    $table->string('fee_bank_account', 100)->nullable()->after('fee_bank_name');
                }
                if (!Schema::hasColumn('unit_quotation', 'fee_bank_holder')) {
                    $table->string('fee_bank_holder', 150)->nullable()->after('fee_bank_account');
                }
                if (!Schema::hasColumn('unit_quotation', 'fee_payment_status')) {
                    $table->string('fee_payment_status', 50)->default('unpaid')->after('fee_bank_holder');
                }
                if (!Schema::hasColumn('unit_quotation', 'fee_transfer_date')) {
                    $table->dateTime('fee_transfer_date')->nullable()->after('fee_payment_status');
                }
                if (!Schema::hasColumn('unit_quotation', 'fee_transfer_proof')) {
                    $table->string('fee_transfer_proof', 255)->nullable()->after('fee_transfer_date');
                }
                if (!Schema::hasColumn('unit_quotation', 'fee_transfer_note')) {
                    $table->text('fee_transfer_note')->nullable()->after('fee_transfer_proof');
                }
                if (!Schema::hasColumn('unit_quotation', 'fee_paid_by')) {
                    $table->unsignedBigInteger('fee_paid_by')->nullable()->after('fee_transfer_note');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('unit_quotation')) {
            Schema::table('unit_quotation', function (Blueprint $table) {
                $cols = [
                    'fee_bank_name',
                    'fee_bank_account',
                    'fee_bank_holder',
                    'fee_payment_status',
                    'fee_transfer_date',
                    'fee_transfer_proof',
                    'fee_transfer_note',
                    'fee_paid_by',
                ];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('unit_quotation', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
