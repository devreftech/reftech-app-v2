<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrSalaryHistory extends Model
{
    use HasFactory;

    protected $table = 'hr_salary_histories';

    protected $fillable = [
        'employee_id',
        'effective_date',
        'basic_salary',
        'previous_basic_salary',
        'transport_allowance',
        'meal_allowance',
        'position_allowance',
        'other_allowance',
        'bpjs_kesehatan',
        'bpjs_ketenagakerjaan',
        'total_gross',
        'previous_total_gross',
        'increment_amount',
        'increment_percentage',
        'change_type',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'basic_salary' => 'decimal:2',
        'previous_basic_salary' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'meal_allowance' => 'decimal:2',
        'position_allowance' => 'decimal:2',
        'other_allowance' => 'decimal:2',
        'bpjs_kesehatan' => 'decimal:2',
        'bpjs_ketenagakerjaan' => 'decimal:2',
        'total_gross' => 'decimal:2',
        'previous_total_gross' => 'decimal:2',
        'increment_amount' => 'decimal:2',
        'increment_percentage' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
