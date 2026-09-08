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
        Schema::table('project_reports', function (Blueprint $table) {
            $table->string('sign_token', 64)->nullable()->unique()->after('status');
            $table->string('customer_signature', 255)->nullable()->after('sign_token');
            $table->string('customer_signer_name', 255)->nullable()->after('customer_signature');
            $table->string('customer_signer_position', 255)->nullable()->after('customer_signer_name');
            $table->string('customer_signed_stamp', 255)->nullable()->after('customer_signer_position');
            $table->timestamp('customer_signed_at')->nullable()->after('customer_signed_stamp');
            $table->string('customer_ip', 45)->nullable()->after('customer_signed_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_reports', function (Blueprint $table) {
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
