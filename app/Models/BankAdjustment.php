<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAdjustment extends Model
{
    use HasFactory;

    protected $table = 'bank_adjustments';

    protected $fillable = [
        'adjustment_number',
        'id_bank',
        'previous_balance',
        'adjusted_balance',
        'difference',
        'type',
        'date',
        'reason',
        'proof_file',
        'is_cutoff_initial',
        'created_by',
    ];

    protected $casts = [
        'date'              => 'date',
        'previous_balance'  => 'float',
        'adjusted_balance'  => 'float',
        'difference'        => 'float',
        'is_cutoff_initial' => 'boolean',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
