<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankReconciliation extends Model
{
    use HasFactory;

    protected $table = 'bank_reconciliations';

    protected $fillable = [
        'id_bank',
        'period_month',
        'period_year',
        'statement_date',
        'statement_balance',
        'book_balance',
        'difference',
        'status',
        'reconciled_items',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'reconciled_items' => 'array',
        'statement_date' => 'date',
        'statement_balance' => 'float',
        'book_balance' => 'float',
        'difference' => 'float',
    ];

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'id_bank', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
