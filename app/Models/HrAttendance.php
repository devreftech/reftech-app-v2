<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrAttendance extends Model
{
    use HasFactory;

    protected $table = 'hr_attendances';

    protected $fillable = [
        'employee_id',
        'date',
        'clock_in',
        'clock_out',
        'work_type',
        'status',
        'late_minutes',
        'penalty_amount',
        'overtime_minutes',
        'ip_address',
        'device_id',
        'device_info',
        'location_in',
        'location_out',
        'selfie_in',
        'selfie_out',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'late_minutes' => 'integer',
        'overtime_minutes' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Proses otomatis Clock Out untuk seluruh karyawan yang telah Clock In pada jam masuk
     * namun belum Clock Out saat jam pulang default (Asia/Jakarta GMT+7) telah tercapai.
     */
    public static function processAutoClockOutIfDue(?string $targetDate = null): int
    {
        try {
            $setting = \Illuminate\Support\Facades\Cache::remember('hr_att_setting_is_auto_clock_out_enabled', 300, function () {
                return \Illuminate\Support\Facades\DB::table('hr_attendance_settings')
                    ->where('key', 'is_auto_clock_out_enabled')
                    ->value('value');
            });

            if ($setting !== '1') {
                return 0;
            }

            $nowJakarta = \Carbon\Carbon::now('Asia/Jakarta');
            $todayJakarta = $nowJakarta->toDateString();
            $evalDate = $targetDate ?: $todayJakarta;

            // Throttle: don't hit the DB more than once every 60 seconds for today's evaluation
            if ($evalDate === $todayJakarta) {
                $cacheKey = 'hr_auto_clock_out_ran_' . $todayJakarta . '_' . $nowJakarta->format('H_i');
                if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                    return 0;
                }
                \Illuminate\Support\Facades\Cache::put($cacheKey, true, 60);
            }

            $timeSetting = \Illuminate\Support\Facades\Cache::remember('hr_att_setting_auto_clock_out_time', 300, function () {
                return \Illuminate\Support\Facades\DB::table('hr_attendance_settings')
                    ->where('key', 'auto_clock_out_time')
                    ->value('value');
            });

            $clockOutTime = !empty($timeSetting) ? $timeSetting : '17:00';
            $timeParts = explode(':', $clockOutTime);
            $hour = (int) ($timeParts[0] ?? 17);
            $minute = (int) ($timeParts[1] ?? 0);

            $autoTimeCarbon = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', "{$evalDate} {$hour}:{$minute}:00", 'Asia/Jakarta');
            $formattedTime = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute, 2, '0', STR_PAD_LEFT) . ':00';

            $affectedCount = 0;

            // Jika mengevaluasi hari ini dan jam sekarang di Jakarta sudah >= jam auto clock out
            if ($evalDate === $todayJakarta) {
                if ($nowJakarta->gte($autoTimeCarbon)) {
                    $affectedCount += self::whereDate('date', $todayJakarta)
                        ->whereNotNull('clock_in')
                        ->whereNull('clock_out')
                        ->where('status', 'Hadir')
                        ->update([
                            'clock_out' => $formattedTime,
                            'location_out' => 'Kantor Reftech',
                            'notes' => \Illuminate\Support\Facades\DB::raw("CASE WHEN notes IS NULL OR notes = '' THEN '(Auto Clock-Out Sistem)' ELSE CONCAT(notes, ' | (Auto Clock-Out Sistem)') END"),
                            'updated_at' => $nowJakarta,
                        ]);
                }
            } elseif ($evalDate < $todayJakarta) {
                // Untuk tanggal lampau yang masih belum ada jam pulang
                $affectedCount += self::whereDate('date', $evalDate)
                    ->whereNotNull('clock_in')
                    ->whereNull('clock_out')
                    ->where('status', 'Hadir')
                    ->update([
                        'clock_out' => $formattedTime,
                        'location_out' => 'Kantor Reftech',
                        'notes' => \Illuminate\Support\Facades\DB::raw("CASE WHEN notes IS NULL OR notes = '' THEN '(Auto Clock-Out Sistem)' ELSE CONCAT(notes, ' | (Auto Clock-Out Sistem)') END"),
                        'updated_at' => $nowJakarta,
                    ]);
            }

            return $affectedCount;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Error auto clock out check: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Hitung denda keterlambatan berdasarkan late_minutes dan kuota bertingkat bulan berjalan.
     */
    public static function calculatePenaltyInfo(int $employeeId, string $date, int $lateMinutes, ?Employee $employee = null): array
    {
        $settings = \Illuminate\Support\Facades\DB::table('hr_attendance_settings')->pluck('value', 'key')->toArray();
        $isEnabled = ($settings['is_late_penalty_enabled'] ?? '1') === '1';
        $isWeekendOff = ($settings['is_weekend_off_enabled'] ?? '1') === '1';

        $carbonDate = \Carbon\Carbon::parse($date);
        
        // Jika akhir pekan dan setting libur akhir pekan aktif -> Bebas denda
        if ($isWeekendOff && $carbonDate->isWeekend()) {
            return [
                'penalty' => 0,
                'late_count' => 0,
                'status_label' => 'Libur Akhir Pekan (Sabtu/Minggu)',
                'strike_status' => 'Libur Akhir Pekan',
                'strike_badge' => 'bg-label-secondary',
                'strike_key' => 'weekend_off',
                'is_multiplier' => false,
            ];
        }

        if (!$isEnabled || $lateMinutes <= 0) {
            return [
                'penalty' => 0,
                'late_count' => 0,
                'status_label' => 'Tepat Waktu',
                'strike_status' => 'Disiplin',
                'strike_badge' => 'bg-label-success',
                'strike_key' => 'disciplined',
                'is_multiplier' => false,
            ];
        }

        $tolerance = (int) ($settings['late_tolerance_minutes'] ?? 0);
        if ($lateMinutes <= $tolerance) {
            return [
                'penalty' => 0,
                'late_count' => 0,
                'status_label' => 'Toleransi Bebas Denda',
                'strike_status' => 'Toleransi Menit',
                'strike_badge' => 'bg-label-info',
                'strike_key' => 'tolerance',
                'is_multiplier' => false,
            ];
        }

        $tier1Rate = (float) ($settings['late_tier_1_rate'] ?? 50000);
        $tier2Rate = (float) ($settings['late_tier_2_rate'] ?? 75000);
        $tier3Rate = (float) ($settings['late_tier_3_rate'] ?? 100000);
        $tierExcessPercent = (float) ($settings['late_tier_excess_percent'] ?? 10);

        // Hitung frekuensi terlambat di bulan berjalan sebelum tanggal ini
        $previousLateCount = self::where('employee_id', $employeeId)
            ->whereMonth('date', $carbonDate->month)
            ->whereYear('date', $carbonDate->year)
            ->where('date', '<', $date)
            ->where('status', 'Hadir')
            ->where('late_minutes', '>', $tolerance)
            ->count();
        
        $currentLateCount = $previousLateCount + 1;

        if ($currentLateCount === 1) {
            return [
                'penalty' => $tier1Rate,
                'late_count' => 1,
                'status_label' => 'Terlambat ke-1 (Denda Rp ' . number_format($tier1Rate, 0, ',', '.') . ')',
                'strike_status' => 'Terlambat ke-1',
                'strike_badge' => 'bg-label-warning',
                'strike_key' => 'penalized',
                'is_multiplier' => false,
            ];
        }

        if ($currentLateCount === 2) {
            return [
                'penalty' => $tier2Rate,
                'late_count' => 2,
                'status_label' => 'Terlambat ke-2 (Denda Rp ' . number_format($tier2Rate, 0, ',', '.') . ')',
                'strike_status' => 'Terlambat ke-2',
                'strike_badge' => 'bg-label-warning',
                'strike_key' => 'penalized',
                'is_multiplier' => false,
            ];
        }

        if ($currentLateCount === 3) {
            return [
                'penalty' => $tier3Rate,
                'late_count' => 3,
                'status_label' => 'Terlambat ke-3 (Denda Rp ' . number_format($tier3Rate, 0, ',', '.') . ')',
                'strike_status' => 'Terlambat ke-3',
                'strike_badge' => 'bg-label-danger',
                'strike_key' => 'penalized',
                'is_multiplier' => false,
            ];
        }

        // Terlambat > 3 kali (ke-4, ke-5, dst): Potong Gaji Basic Salary 10%
        if (!$employee) {
            $employee = Employee::with('salary')->find($employeeId);
        }
        $basicSalary = (float) ($employee?->salary?->basic_salary ?? 0);
        $salaryCut = $basicSalary > 0 ? round(($basicSalary * $tierExcessPercent) / 100) : $tier3Rate;

        return [
            'penalty' => $salaryCut,
            'late_count' => $currentLateCount,
            'status_label' => "Terlambat ke-{$currentLateCount} (Sanksi Potong Gaji {$tierExcessPercent}%: Rp " . number_format($salaryCut, 0, ',', '.') . ")",
            'strike_status' => "Sanksi SP-1 (>3x Potong Gaji {$tierExcessPercent}%)",
            'strike_badge' => 'bg-label-danger',
            'strike_key' => 'warning_sp',
            'is_multiplier' => true,
        ];
    }
}

