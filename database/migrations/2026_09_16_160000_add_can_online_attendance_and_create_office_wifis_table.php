<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add can_online_attendance to employees table
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'can_online_attendance')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->boolean('can_online_attendance')->default(true)->after('employment_status');
                $table->index('can_online_attendance');
            });
        }

        // 2. Create hr_office_wifis table for whitelisted office network IPs
        if (!Schema::hasTable('hr_office_wifis')) {
            Schema::create('hr_office_wifis', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100); // e.g. "WiFi Kantor Utama", "WiFi Gudang"
                $table->string('ip_address', 45); // IPv4 or IPv6
                $table->boolean('is_active')->default(true);
                $table->string('notes')->nullable();
                $table->timestamps();

                $table->index('is_active');
            });
        }

        // 3. Create hr_attendance_settings table for global attendance policies (e.g. WiFi restriction toggle)
        if (!Schema::hasTable('hr_attendance_settings')) {
            Schema::create('hr_attendance_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key', 60)->unique();
                $table->text('value')->nullable();
                $table->string('description')->nullable();
                $table->timestamps();
            });

            // Seed default setting: wifi restriction disabled by default until admin configures IPs
            DB::table('hr_attendance_settings')->insertOrIgnore([
                [
                    'key' => 'is_wifi_restriction_enabled',
                    'value' => '0',
                    'description' => 'Batasi absensi online hanya dari jaringan WiFi kantor yang terdaftar',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'can_online_attendance')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('can_online_attendance');
            });
        }

        Schema::dropIfExists('hr_office_wifis');
        Schema::dropIfExists('hr_attendance_settings');
    }
};
