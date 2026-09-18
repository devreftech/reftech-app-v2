<?php

namespace App\Console\Commands;

use App\Models\Hr\HrAttendance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoClockOutCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:auto-clock-out {--date= : Tanggal spesifik YYYY-MM-DD (default: hari ini)} {--force : Paksa eksekusi tanpa mengecek switch pengaturan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis mengisi jam pulang (Clock Out) untuk semua karyawan yang telah Clock In namun belum Clock Out';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $force = $this->option('force');
        $settingEnabled = DB::table('hr_attendance_settings')->where('key', 'is_auto_clock_out_enabled')->first();
        $isEnabled = $settingEnabled && $settingEnabled->value === '1';

        if (!$isEnabled && !$force) {
            $this->info('Fitur Auto Clock-Out sedang dinonaktifkan di pengaturan HR.');
            return 0;
        }

        $timeSetting = DB::table('hr_attendance_settings')->where('key', 'auto_clock_out_time')->first();
        $clockOutTime = ($timeSetting && !empty($timeSetting->value)) ? $timeSetting->value : '17:00';
        if (strlen($clockOutTime) === 5) {
            $clockOutTime .= ':00';
        }

        $targetDate = $this->option('date') ?: Carbon::today('Asia/Jakarta')->toDateString();

        $attendances = HrAttendance::whereDate('date', $targetDate)
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->where('status', 'Hadir')
            ->get();

        $count = 0;
        foreach ($attendances as $att) {
            $note = $att->notes ? trim($att->notes) . ' | (Auto Clock-Out Sistem)' : '(Auto Clock-Out Sistem)';
            $att->update([
                'clock_out' => $clockOutTime,
                'location_out' => $att->location_out ?: 'Kantor Reftech',
                'notes' => $note,
            ]);
            $count++;
        }

        $this->info("Berhasil melakukan Auto Clock-Out untuk {$count} karyawan pada tanggal {$targetDate} pukul {$clockOutTime} (WIB / GMT+7).");
        return 0;
    }
}
