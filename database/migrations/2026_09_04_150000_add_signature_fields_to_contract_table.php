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
        Schema::table('contract', function (Blueprint $table) {
            if (!Schema::hasColumn('contract', 'sign_token')) {
                $table->string('sign_token', 64)->nullable()->unique()->after('type');
            }
            if (!Schema::hasColumn('contract', 'signed_at')) {
                $table->timestamp('signed_at')->nullable()->after('sign_token');
            }
            if (!Schema::hasColumn('contract', 'customer_signature')) {
                $table->text('customer_signature')->nullable()->after('signed_at');
            }
            if (!Schema::hasColumn('contract', 'customer_signer_name')) {
                $table->string('customer_signer_name')->nullable()->after('customer_signature');
            }
            if (!Schema::hasColumn('contract', 'customer_signer_position')) {
                $table->string('customer_signer_position')->nullable()->after('customer_signer_name');
            }
            if (!Schema::hasColumn('contract', 'customer_signed_stamp')) {
                $table->string('customer_signed_stamp')->nullable()->after('customer_signer_position');
            }
            if (!Schema::hasColumn('contract', 'customer_ip')) {
                $table->string('customer_ip', 45)->nullable()->after('customer_signed_stamp');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            $columns = [
                'sign_token',
                'signed_at',
                'customer_signature',
                'customer_signer_name',
                'customer_signer_position',
                'customer_signed_stamp',
                'customer_ip',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('contract', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
