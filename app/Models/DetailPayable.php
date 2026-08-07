<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPayable extends Model
{
    use HasFactory;
    protected $table = "detail_payable";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'amount',
        'memo',
    ];
    public function payable()
    {
        return $this->belongsTo('App\Models\Payable', 'id_payable', 'id');
    }
    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'id_account', 'id');
    }
}
