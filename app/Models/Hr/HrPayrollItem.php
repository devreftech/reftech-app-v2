<?php

namespace App\Models\Hr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrPayrollItem extends Model
{
    use HasFactory;

    protected $table = 'hr_payroll_items';

    protected $fillable = [
        'payroll_id',
        'employee_id',
        'slip_number',
        'basic_salary',
        'transport_allowance',
        'meal_allowance',
        'position_allowance',
        'other_allowance',
        'overtime_pay',
        'deduction_absence',
        'deduction_bpjs',
        'deduction_other',
        'net_salary',
        'attendance_days',
        'absence_days',
        'overtime_hours',
        'payment_status',
        'paid_at',
        'meta_data',
    ];

    protected $casts = [
        'basic_salary' => 'float',
        'transport_allowance' => 'float',
        'meal_allowance' => 'float',
        'position_allowance' => 'float',
        'other_allowance' => 'float',
        'overtime_pay' => 'float',
        'deduction_absence' => 'float',
        'deduction_bpjs' => 'float',
        'deduction_other' => 'float',
        'net_salary' => 'float',
        'paid_at' => 'datetime',
        'meta_data' => 'array',
    ];

    public function payroll()
    {
        return $this->belongsTo(HrPayroll::class, 'payroll_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getTotalAllowanceAttribute()
    {
        return $this->transport_allowance + $this->meal_allowance + $this->position_allowance + $this->other_allowance;
    }

    public function getTotalDeductionAttribute()
    {
        return $this->deduction_absence + $this->deduction_bpjs + $this->deduction_other;
    }
}
