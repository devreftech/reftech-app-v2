<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemProductSet extends Model
{
    use HasFactory;
    protected $table = "item_product_set";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_product_set',
        'id_replacement',
    ];

    public function product_set()
    {
        return $this->belongsTo('App\Models\ProductSet', 'id_product_set', 'id');
    }
    public function replacement()
    {
        return $this->belongsTo('App\Models\DetailProduct', 'id_replacement', 'id');
    }
}
