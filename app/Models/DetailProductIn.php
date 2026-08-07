<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProductIn extends Model
{
    use HasFactory;
    protected $table = "detail_product_in";
    protected $date = [
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'id_detail_product',
        'id_product_in',
        'qty',
        'warehouse',
        'modal',
    ];
    public function productIn()
    {
        return $this->belongsTo('App\Models\ProductIn', 'id_product_in', 'id');
    }
    public function detailProduct()
    {
        return $this->belongsTo('App\Models\DetailProduct', 'id_detail_product', 'id');
    }
}
