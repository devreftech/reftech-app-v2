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
        Schema::table('basts', function (Blueprint $table) {
            if (!Schema::hasColumn('basts', 'sign_token')) {
                $table->string('sign_token', 64)->nullable()->unique()->after('sign');
            }
            if (!Schema::hasColumn('basts', 'customer_signature')) {
                $table->string('customer_signature')->nullable()->after('sign_token');
            }
            if (!Schema::hasColumn('basts', 'customer_signer_name')) {
                $table->string('customer_signer_name')->nullable()->after('customer_signature');
            }
            if (!Schema::hasColumn('basts', 'customer_signer_position')) {
                $table->string('customer_signer_position')->nullable()->after('customer_signer_name');
            }
            if (!Schema::hasColumn('basts', 'customer_signed_stamp')) {
                $table->string('customer_signed_stamp')->nullable()->after('customer_signer_position');
            }
            if (!Schema::hasColumn('basts', 'customer_signed_at')) {
                $table->dateTime('customer_signed_at')->nullable()->after('customer_signed_stamp');
            }
            if (!Schema::hasColumn('basts', 'customer_ip')) {
                $table->string('customer_ip', 45)->nullable()->after('customer_signed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basts', function (Blueprint $table) {
            $table->dropColumn([
                'sign_token',
                'customer_signature',
                'customer_signer_name',
                'customer_signer_position',
                'customer_signed_stamp',
                'customer_signed_at',
                'customer_ip',
            ]);
        });
    }
};
