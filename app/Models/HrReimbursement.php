<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrReimbursement extends Model
{
    use HasFactory;

    protected $table = 'hr_reimbursements';

    protected $fillable = [
        'employee_id',
        'claim_number',
        'claim_type',
        'amount',
        'event_date',
        'description',
        'receipt_image',
        'status',
        'approved_by',
        'approved_at',
        'paid_at',
        'rejection_reason',
        'expense_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'event_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
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
