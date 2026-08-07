<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailReturn extends Model
{
    protected $table = "detail_return";
    protected $fillable = [
        'id_retur',
        'id_replacement',
        'qty',
        // 'price',
        // 'amount',
        'note',
        'status',
    ];

    
    public function return()
    {
        return $this->belongsTo('App\Models\Retur', 'id_retur', 'id');
    }
    public function replacement()
    {
        return $this->belongsTo('App\Models\DetailProduct', 'id_replacement', 'id');
    }
}
