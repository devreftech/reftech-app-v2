<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Chat dari marketplace (Tokopedia/Shopee) sering gak nampilin no. HP pembeli
// sebelum di-follow-up, jadi field phone gak boleh wajib buat channel-channel itu.
return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE online_leads MODIFY COLUMN phone VARCHAR(50) NULL");
        } catch (\Throwable $e) {
            Schema::table('online_leads', function (Blueprint $table) {
                $table->string('phone', 50)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE online_leads MODIFY COLUMN phone VARCHAR(50) NOT NULL");
        } catch (\Throwable $e) {
            //
        }
    }
};
