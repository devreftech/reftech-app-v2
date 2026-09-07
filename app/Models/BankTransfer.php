<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'bank_transfers';

    protected $fillable = [
        'transfer_number',
        'id_from_bank',
        'id_to_bank',
        'amount',
        'fee',
        'date',
        'note',
        'proof_file',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'date' => 'date',
    ];

    public function fromBank()
    {
        return $this->belongsTo(Bank::class, 'id_from_bank');
    }

    public function toBank()
    {
        return $this->belongsTo(Bank::class, 'id_to_bank');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
