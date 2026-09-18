<?php

namespace App\Models\Hr;

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
        'notes',
        'ip_address',
        'device_id',
        'device_info',
        'location_in',
        'location_out',
        'selfie_in',
        'selfie_out',
    ];

    protected $casts = [
        'date' => 'date',
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
        return \App\Models\HrAttendance::processAutoClockOutIfDue($targetDate);
    }
}
