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
            if (!Schema::hasColumn('hr_attendances', 'penalty_amount')) {
                $table->decimal('penalty_amount', 12, 2)->default(0)->after('late_minutes');
            }
        });

        if (Schema::hasTable('hr_attendance_settings')) {
            DB::table('hr_attendance_settings')->insertOrIgnore([
                [
                    'key' => 'work_start_time',
                    'value' => '08:30',
                    'description' => 'Jam Masuk Standar Kantor (Contoh: 08:30)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'late_tolerance_minutes',
                    'value' => '0',
                    'description' => 'Toleransi Keterlambatan Harian dalam Menit (Bebas Denda jika terlambat <= nilai ini)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'is_late_penalty_enabled',
                    'value' => '1',
                    'description' => 'Aktifkan Kebijakan Denda & Pemotongan Keterlambatan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'late_penalty_type',
                    'value' => 'per_minute',
                    'description' => 'Tipe Denda: per_minute (per menit) atau flat (nominal tetap per hari)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'late_penalty_rate',
                    'value' => '1000',
                    'description' => 'Tarif Nominal Denda (Rp per menit atau Rp flat per kejadian)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'late_free_count_per_month',
                    'value' => '2',
                    'description' => 'Kuota Frekuensi Terlambat Bebas Denda (Toleransi Bulanan)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'late_multiplier_threshold',
                    'value' => '5',
                    'description' => 'Batas Frekuensi Terlambat untuk Sanksi Eskalasi / Denda Berlipat (SP-1)',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'late_multiplier_rate',
                    'value' => '2.0',
                    'description' => 'Faktor Pengali Denda setelah Melewati Batas Eskalasi (Contoh: 2.0 untuk 2x lipat)',
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
            if (Schema::hasColumn('hr_attendances', 'penalty_amount')) {
                $table->dropColumn('penalty_amount');
            }
        });

        if (Schema::hasTable('hr_attendance_settings')) {
            DB::table('hr_attendance_settings')
                ->whereIn('key', [
                    'work_start_time',
                    'late_tolerance_minutes',
                    'is_late_penalty_enabled',
                    'late_penalty_type',
                    'late_penalty_rate',
                    'late_free_count_per_month',
                    'late_multiplier_threshold',
                    'late_multiplier_rate',
                ])
                ->delete();
        }
    }
};

