<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('hr_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('hr_attendances', 'device_id')) {
                $table->string('device_id', 64)->nullable()->after('ip_address');
                $table->index('device_id');
            }
            if (!Schema::hasColumn('hr_attendances', 'device_info')) {
                $table->string('device_info', 255)->nullable()->after('device_id');
            }
            if (!Schema::hasColumn('hr_attendances', 'selfie_in')) {
                $table->string('selfie_in', 255)->nullable()->after('location_in');
            }
            if (!Schema::hasColumn('hr_attendances', 'selfie_out')) {
                $table->string('selfie_out', 255)->nullable()->after('location_out');
            }
        });

        // Ensure settings exist
        if (Schema::hasTable('hr_attendance_settings')) {
            DB::table('hr_attendance_settings')->insertOrIgnore([
                [
                    'key' => 'is_device_lock_enabled',
                    'value' => '1',
                    'description' => 'Kunci 1 Perangkat per Karyawan (Mencegah 1 HP/Laptop dipakai banyak akun)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'is_selfie_required',
                    'value' => '0',
                    'description' => 'Wajibkan Foto Selfie Kamera Live saat Presensi Masuk (Clock In)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
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
        Schema::table('hr_attendances', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('hr_attendances', 'device_id')) $cols[] = 'device_id';
            if (Schema::hasColumn('hr_attendances', 'device_info')) $cols[] = 'device_info';
            if (Schema::hasColumn('hr_attendances', 'selfie_in')) $cols[] = 'selfie_in';
            if (Schema::hasColumn('hr_attendances', 'selfie_out')) $cols[] = 'selfie_out';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });

        if (Schema::hasTable('hr_attendance_settings')) {
            DB::table('hr_attendance_settings')
                ->whereIn('key', ['is_device_lock_enabled', 'is_selfie_required'])
                ->delete();
        }
    }
};

