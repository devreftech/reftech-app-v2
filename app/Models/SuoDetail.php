<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuoDetail extends Model
{
    use HasFactory;

    protected $table = 'suo_detail';

    protected $fillable = [
        'id_suo',
        'item_name',
        'qty',
        'unit',
        'stock_status',
        'notes',
    ];

    public function suo()
    {
        return $this->belongsTo('App\Models\Suo', 'id_suo', 'id');
    }
}
