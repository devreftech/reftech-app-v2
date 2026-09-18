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
            $setting = \Illuminate\Support\Facades\DB::table('hr_attendance_settings')
                ->where('key', 'is_auto_clock_out_enabled')
                ->value('value');

            if ($setting !== '1') {
                return 0;
            }

            $nowJakarta = \Carbon\Carbon::now('Asia/Jakarta');
            $todayJakarta = $nowJakarta->toDateString();
            $evalDate = $targetDate ?: $todayJakarta;

            $timeSetting = \Illuminate\Support\Facades\DB::table('hr_attendance_settings')
                ->where('key', 'auto_clock_out_time')
                ->value('value');

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
}
