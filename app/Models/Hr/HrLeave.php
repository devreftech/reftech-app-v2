<?php

namespace App\Models\Hr;

use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrLeave extends Model
{
    use HasFactory;

    protected $table = 'hr_leaves';

    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'reason',
        'attachment',
        'status',
        'approved_by',
        'approved_at',
        'rejection_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Cek apakah karyawan memiliki izin/sakit/cuti yang sudah disetujui (Approved) pada tanggal tertentu.
     */
    public static function getApprovedLeaveForDate(int $employeeId, string $date): ?self
    {
        $targetDate = Carbon::parse($date)->toDateString();

        return static::where('employee_id', $employeeId)
            ->where('status', 'Approved')
            ->whereDate('start_date', '<=', $targetDate)
            ->whereDate('end_date', '>=', $targetDate)
            ->first();
    }

    /**
     * Cek apakah karyawan sedang bertugas Dinas Luar atau WFH yang disetujui pada tanggal tertentu.
     */
    public static function isApprovedWfhOrTrip(int $employeeId, string $date): bool
    {
        $targetDate = Carbon::parse($date)->toDateString();

        return static::where('employee_id', $employeeId)
            ->where('status', 'Approved')
            ->whereIn('type', ['Dinas Luar', 'WFH'])
            ->whereDate('start_date', '<=', $targetDate)
            ->whereDate('end_date', '>=', $targetDate)
            ->exists();
    }
}
