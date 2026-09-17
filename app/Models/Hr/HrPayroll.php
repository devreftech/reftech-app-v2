<?php

namespace App\Models\Hr;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrPayroll extends Model
{
    use HasFactory;

    protected $table = 'hr_payrolls';

    protected $fillable = [
        'code',
        'title',
        'period_month',
        'period_year',
        'start_date',
        'end_date',
        'payment_date',
        'total_basic',
        'total_allowance',
        'total_overtime',
        'total_deductions',
        'total_net_amount',
        'status',
        'generated_by',
        'approved_by',
        'approved_at',
        'notes',
        'expense_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_date' => 'date',
        'approved_at' => 'datetime',
        'total_basic' => 'float',
        'total_allowance' => 'float',
        'total_overtime' => 'float',
        'total_deductions' => 'float',
        'total_net_amount' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(HrPayrollItem::class, 'payroll_id');
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function expense()
    {
        return $this->belongsTo(\App\Models\Expense::class, 'expense_id');
    }
}
