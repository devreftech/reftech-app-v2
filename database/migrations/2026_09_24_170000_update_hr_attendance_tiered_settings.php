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
        if (Schema::hasTable('hr_attendance_settings')) {
            $settings = [
                [
                    'key' => 'work_start_time',
                    'value' => '08:00',
                    'description' => 'Jam Masuk Standar Kantor (08:00 WIB)',
                ],
                [
                    'key' => 'late_tolerance_minutes',
                    'value' => '0',
                    'description' => 'Toleransi Keterlambatan Harian (0 Menit / Tidak Ada Toleransi)',
                ],
                [
                    'key' => 'is_late_penalty_enabled',
                    'value' => '1',
                    'description' => 'Aktifkan Kebijakan Denda & Sanksi Keterlambatan',
                ],
                [
                    'key' => 'late_tier_1_rate',
                    'value' => '50000',
                    'description' => 'Nominal Denda Terlambat ke-1 dalam Bulan Berjalan (Rp 50.000)',
                ],
                [
                    'key' => 'late_tier_2_rate',
                    'value' => '75000',
                    'description' => 'Nominal Denda Terlambat ke-2 dalam Bulan Berjalan (Rp 75.000)',
                ],
                [
                    'key' => 'late_tier_3_rate',
                    'value' => '100000',
                    'description' => 'Nominal Denda Terlambat ke-3 dalam Bulan Berjalan (Rp 100.000)',
                ],
                [
                    'key' => 'late_tier_excess_percent',
                    'value' => '10',
                    'description' => 'Persentase Pemotongan Gaji Pokok untuk Keterlambatan Lebih dari 3 Kali (10%)',
                ],
                [
                    'key' => 'alpha_penalty_rate',
                    'value' => '50000',
                    'description' => 'Nominal Denda/Potongan Alpa Tanpa Kabar Seharian Penuh (Rp 50.000)',
                ],
                [
                    'key' => 'is_weekend_off_enabled',
                    'value' => '1',
                    'description' => 'Sabtu & Minggu adalah Hari Libur Bebas Absensi & Bebas Denda Alpa',
                ],
            ];

            foreach ($settings as $setting) {
                DB::table('hr_attendance_settings')->updateOrInsert(
                    ['key' => $setting['key']],
                    [
                        'value' => $setting['value'],
                        'description' => $setting['description'],
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No destructive rollback needed
    }
};
