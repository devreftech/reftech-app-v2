<?php

namespace App\Models\Hr;

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
    ];

    protected $casts = [
        'basic_salary' => 'float',
        'transport_allowance' => 'float',
        'meal_allowance' => 'float',
        'position_allowance' => 'float',
        'other_allowance' => 'float',
        'bpjs_kesehatan' => 'float',
        'bpjs_ketenagakerjaan' => 'float',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getTotalAllowanceAttribute()
    {
        return $this->transport_allowance + $this->meal_allowance + $this->position_allowance + $this->other_allowance;
    }

    public function getGrossSalaryAttribute()
    {
        return $this->basic_salary + $this->total_allowance;
    }
}
