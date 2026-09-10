<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE basts MODIFY COLUMN work_date DATE NULL");
        } catch (\Throwable $e) {
            // Fallback schema if supported
            Schema::table('basts', function (Blueprint $table) {
                $table->date('work_date')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE basts MODIFY COLUMN work_date DATE NOT NULL");
        } catch (\Throwable $e) {
            //
        }
    }
};
