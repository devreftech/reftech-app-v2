<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $table = "expense";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'no_expense',
        'no_voucher',
        'no_cheque',
        'memo',
        'payee',
        'amount',
    ];
    public function bank()
    {
        return $this->belongsTo('App\Models\Bank', 'id_bank', 'id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailExpense', 'id_expense');
    }
}
