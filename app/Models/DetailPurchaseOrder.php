<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPurchaseOrder extends Model
{
    use HasFactory;
    protected $table = "detail_purchase_order";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'product',
        'id_unit',
        'category',
        'id_product',
        'qty',
        'info_qty',
        'price',
        'disc',
        'amount',
        'pph',
    ];
    public function purchase()
    {
        return $this->belongsTo('App\Models\PurchaseOrder', 'id_purchase_order', 'id');
    }
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'id_unit', 'id');
    }
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'id_product', 'id');
    }
}
