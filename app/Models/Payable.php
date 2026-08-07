<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payable extends Model
{
    use HasFactory;
    protected $table = "payable";
    protected $date = [
        'date',
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
    public function bank()
    {
        return $this->belongsTo('App\Models\Bank', 'id_bank', 'id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailPayable', 'id_payable');
    }
}
