<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrSalary extends Model
{
    use HasFactory;

    protected $table = 'hr_salaries';

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'transport_allowance',
        'meal_allowance',
        'position_allowance',
        'other_allowance',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'effective_date',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'position_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'bpjs_kesehatan' => 'decimal:2',
        'bpjs_ketenagakerjaan' => 'decimal:2',
        'effective_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getTotalAllowanceAttribute(): float
    {
        return (float) ($this->transport_allowance + $this->meal_allowance + $this->position_allowance + $this->other_allowance);
    }

    public function getTotalGrossAttribute(): float
    {
        return (float) ($this->basic_salary + $this->total_allowance);
    }
}
