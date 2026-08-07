<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSet extends Model
{
    use HasFactory;
    protected $table = "product_set";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_product',
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'id_product', 'id');
    }
    public function item()
    {
        return $this->hasMany('App\Models\ItemProductSet', 'id_product_set');
    }
}
