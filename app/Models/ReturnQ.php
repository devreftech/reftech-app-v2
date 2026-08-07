<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnQ extends Model
{
    use HasFactory;
    
    protected $table = "return";
    protected $fillable = [
        'id_quotation',
        'no_return',
        'lvl',
        'subtotal',
        'tax',
        'total',
        'date',
        'note',
    ];

    
    public function quote()
    {
        return $this->belongsTo('App\Models\Quotation', 'id_quotation', 'id');
    }
    public function detail_return()
    {
        return $this->hasMany('App\Models\DetailReturn', 'id_return');
    }
}
