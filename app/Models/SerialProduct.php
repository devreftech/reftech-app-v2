<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerialProduct extends Model
{
    use HasFactory;
    protected $table = "serial_product";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_product',
        'brand',
        'pn',
        'fxp_parts',
        'image',
        'detail',
        'price',
        'rental',
        'second',
        'new',
        'bar',
        'air_cap',
    ];
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'id_product', 'id');
    }

    public function sparePartVendorPrices()
    {
        return $this->hasMany(\App\Models\SparePartVendorPrice::class, 'id_serial_product');
    }
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'id_product', 'id');
    }
    public function quotation()
    {
        return $this->hasMany('App\Models\Quotation', 'id_equivalent');
    }
    public function detail_return()
    {
        return $this->hasMany('App\Models\DetailReturn', 'id_pn');
    }
    public function detail_delivery()
    {
        return $this->hasMany('App\Models\DetailDelivery', 'id_pn');
    }
    public function detail_pending()
    {
        return $this->hasMany('App\Models\DetailPending', 'id_equivalent');
    }
    public function pr()
    {
        return $this->hasMany('App\Models\PurchaseRequest', 'id_equivalent');
    }
    public function machine()
    {
        return $this->hasMany('App\Models\Machine', 'id_unit');
    }
}
