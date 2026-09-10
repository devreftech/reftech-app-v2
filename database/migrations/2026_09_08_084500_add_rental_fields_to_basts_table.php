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
            $table->string('type', 50)->default('Default')->after('no_bast');
            $table->date('rental_start_date')->nullable()->after('work_date');
            $table->date('rental_end_date')->nullable()->after('rental_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('basts', function (Blueprint $table) {
            $table->dropColumn(['type', 'rental_start_date', 'rental_end_date']);
        });
    }
};
