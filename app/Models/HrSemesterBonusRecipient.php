<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrSemesterBonusRecipient extends Model
{
    use HasFactory;

    protected $table = 'hr_semester_bonus_recipients';

    protected $fillable = [
        'bonus_id',
        'employee_id',
        'total_addition',
        'total_deduction',
        'net_bonus',
        'payment_status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'total_addition' => 'decimal:2',
        'total_deduction' => 'decimal:2',
        'net_bonus' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function bonus()
    {
        return $this->belongsTo(HrSemesterBonus::class, 'bonus_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function items()
    {
        return $this->hasMany(HrSemesterBonusItem::class, 'recipient_id')->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Hitung ulang total penambahan, pengurangan, dan total bonus bersih karyawan
     */
    public function recalculateTotals(): void
    {
        $this->loadMissing('items');
        $additions = $this->items->where('type', 'addition')->sum('amount');
        $deductions = $this->items->where('type', 'deduction')->sum('amount');
        $net = max(0, $additions - $deductions);

        $this->update([
            'total_addition' => $additions,
            'total_deduction' => $deductions,
            'net_bonus' => $net,
        ]);

        if ($this->bonus) {
            $this->bonus->recalculateTotals();
        }
    }
}
