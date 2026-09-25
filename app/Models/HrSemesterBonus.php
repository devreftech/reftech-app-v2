<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrSemesterBonus extends Model
{
    use HasFactory;

    protected $table = 'hr_semester_bonuses';

    protected $fillable = [
        'code',
        'title',
        'semester',
        'year',
        'payment_date',
        'status',
        'total_addition',
        'total_deduction',
        'total_net_amount',
        'total_recipients_count',
        'generated_by',
        'approved_by',
        'approved_at',
        'expense_id',
        'notes',
    ];

    protected $casts = [
        'semester' => 'integer',
        'year' => 'integer',
        'payment_date' => 'date',
        'approved_at' => 'datetime',
        'total_addition' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'total_net_amount' => 'decimal:2',
        'total_recipients_count' => 'integer',
    ];

    public function recipients()
    {
        return $this->hasMany(HrSemesterBonusRecipient::class, 'bonus_id');
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

    /**
     * Hitung ulang total akumulasi nominal pada header batch
     */
    public function recalculateTotals(): void
    {
        $this->loadMissing('recipients');
        $totalAddition = $this->recipients->sum('total_addition');
        $totalDeduction = $this->recipients->sum('total_deduction');
        $totalNet = $this->recipients->sum('net_bonus');
        $count = $this->recipients->count();

        $this->update([
            'total_addition' => $totalAddition,
            'total_deduction' => $totalDeduction,
            'total_net_amount' => $totalNet,
            'total_recipients_count' => $count,
        ]);
    }
}
