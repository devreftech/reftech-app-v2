<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    protected $table = "bank";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'no_voucher',
        'no_cheque',
        'memo',
        'payee',
        'amount',
    ];
    public function payable()
    {
        return $this->hasMany('App\Models\Payable', 'id_bank');
    }
}
