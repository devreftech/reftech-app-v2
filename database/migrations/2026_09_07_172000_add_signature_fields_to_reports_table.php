<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->string('sign_token', 64)->nullable()->unique()->after('sign_client');
            $table->string('customer_signature')->nullable()->after('sign_token');
            $table->string('customer_signer_name')->nullable()->after('customer_signature');
            $table->string('customer_signer_position')->nullable()->after('customer_signer_name');
            $table->string('customer_signed_stamp')->nullable()->after('customer_signer_position');
            $table->string('customer_ip', 45)->nullable()->after('customer_signed_stamp');
            $table->dateTime('signed_at')->nullable()->after('customer_ip');
        });

        // Backfill sign_token untuk data existing
        $reports = DB::table('reports')->whereNull('sign_token')->get(['id', 'sign_client', 'updated_at']);
        foreach ($reports as $r) {
            DB::table('reports')->where('id', $r->id)->update([
                'sign_token'         => Str::random(40),
                'customer_signature' => $r->sign_client ?: null,
                'signed_at'          => $r->sign_client ? ($r->updated_at ?: now()) : null,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'sign_token',
                'customer_signature',
                'customer_signer_name',
                'customer_signer_position',
                'customer_signed_stamp',
                'customer_ip',
                'signed_at',
            ]);
        });
    }
};
