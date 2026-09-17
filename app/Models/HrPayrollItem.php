<?php

namespace App\Models;

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
        'basic_salary' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'position_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'deduction_absence' => 'decimal:2',
        'deduction_bpjs' => 'decimal:2',
        'deduction_other' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'attendance_days' => 'integer',
        'absence_days' => 'integer',
        'overtime_hours' => 'integer',
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

    public function getTotalAllowanceAttribute(): float
    {
        return (float) ($this->transport_allowance + $this->meal_allowance + $this->position_allowance + $this->other_allowance);
    }

    public function getTotalDeductionAttribute(): float
    {
        return (float) ($this->deduction_absence + $this->deduction_bpjs + $this->deduction_other);
    }
}
