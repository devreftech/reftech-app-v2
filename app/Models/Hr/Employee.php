<?php

namespace App\Models\Hr;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'user_id',
        'id_department',
        'id_position',
        'nik',
        'join_date',
        'birthday',
        'address',
        'phone',
        'employment_status',
        'can_online_attendance',
        'contract_start_date',
        'contract_end_date',
        'resign_date',
    ];

    protected $casts = [
        'can_online_attendance' => 'boolean',
        'join_date' => 'date',
        'birthday' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'resign_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'id_department');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'id_position');
    }

    public function attendances()
    {
        return $this->hasMany(HrAttendance::class, 'employee_id');
    }

    public function leaveRequests()
    {
        return $this->hasMany(HrLeaveRequest::class, 'employee_id');
    }

    public function leaveBalances()
    {
        return $this->hasMany(HrLeaveBalance::class, 'employee_id');
    }

    public function salary()
    {
        return $this->hasOne(HrSalary::class, 'employee_id');
    }

    public function salaryHistories()
    {
        return $this->hasMany(\App\Models\HrSalaryHistory::class, 'employee_id')->orderByDesc('effective_date')->orderByDesc('id');
    }

    public function payrollItems()
    {
        return $this->hasMany(HrPayrollItem::class, 'employee_id');
    }

    public function reimbursements()
    {
        return $this->hasMany(HrReimbursement::class, 'employee_id');
    }

    public function assets()
    {
        return $this->hasMany(HrEmployeeAsset::class, 'employee_id');
    }

    public function evaluations()
    {
        return $this->hasMany(HrEvaluation::class, 'employee_id');
    }
}
