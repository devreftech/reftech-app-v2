<?php

namespace App\Models;

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
        'status',
        'total_basic',
        'total_allowance',
        'total_overtime',
        'total_deductions',
        'total_net_amount',
        'generated_by',
        'approved_by',
        'approved_at',
        'notes',
        'expense_id',
    ];

    protected $casts = [
        'period_month' => 'integer',
        'period_year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'payment_date' => 'date',
        'approved_at' => 'datetime',
        'total_basic' => 'decimal:2',
        'total_allowance' => 'decimal:2',
        'total_overtime' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_net_amount' => 'decimal:2',
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
        return $this->belongsTo(Expense::class, 'expense_id');
    }
}
