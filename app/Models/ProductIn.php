<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIn extends Model
{
    use HasFactory, LogsActivity;
    protected $table = "product_in";
    protected $date = [
        'created_at',
        'updated_at',
        'date_invoice',
        'date'
    ];
    protected $fillable = [
        'no_product_in',
        'created_by',
        'no_do',
        'invoice',
        'id_purchase_order',
        'supplier',
        'note',
        'subtotal',
        'total_no_tax',
        'shipping',
        'tax',
        'total',
        'price',
    ];
    public function supp()
    {
        return $this->belongsTo('App\Models\Supplier', 'id_supplier', 'id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo('App\Models\PurchaseOrder', 'id_purchase_order', 'id');
    }

    public function creator()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\DetailProductIn', 'id_product_in');
    }
    public function return()
    {
        return $this->hasMany('App\Models\Retur', 'id_product_in');
    }
    public function costs()
    {
        return $this->hasMany('App\Models\ProductInCost', 'id_product_in');
    }
}
