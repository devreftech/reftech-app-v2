<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashTransaction extends Model
{
    use HasFactory;

    protected $table = 'petty_cash_transactions';

    protected $fillable = [
        'id_bank',
        'voucher_number',
        'type',
        'date',
        'category',
        'recipient',
        'amount',
        'description',
        'proof_attachment',
        'id_source_bank',
        'created_by',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'float',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank');
    }

    public function sourceBank()
    {
        return $this->belongsTo(Bank::class, 'id_source_bank');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
