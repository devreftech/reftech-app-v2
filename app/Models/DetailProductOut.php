<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProductOut extends Model
{
    use HasFactory;
    protected $table = "detail_product_out";
    protected $date = [
        'created_at',
        'updated_at',
    ];
    protected $fillable = [
        'id_detail_product',
        'id_serial_product',
        'id_product_out',
        'qty',
        'warehouse',
        'price',
        'amount',
    ];
    public function productOut()
    {
        return $this->belongsTo('App\Models\ProductOut', 'id_product_out', 'id');
    }
    public function detailProduct()
    {
        return $this->belongsTo('App\Models\DetailProduct', 'id_detail_product', 'id');
    }
    public function serialProduct()
    {
        return $this->belongsTo('App\Models\SerialProduct', 'id_serial_product', 'id');
    }
}
