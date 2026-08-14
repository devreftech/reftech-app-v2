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
    public function detailQuotations()
    {
        return $this->hasMany('App\Models\DetailQuotation', 'id_equivalent');
    }
    public function detailQuotation()
    {
        return $this->hasMany('App\Models\DetailQuotation', 'id_equivalent');
    }
    public function detailPending()
    {
        return $this->hasMany('App\Models\DetailPendingPO', 'id_equivalent');
    }
    public function purchaseRequests()
    {
        return $this->hasMany('App\Models\PurchaseRequestDetail', 'id_equivalent');
    }
    public function spareparts()
    {
        return $this->hasMany('App\Models\Sparepart', 'id_equivalent');
    }
    public function detail_return()
    {
        return $this->hasMany('App\Models\DetailReturn', 'id_pn');
    }
    public function detailReturn()
    {
        return $this->hasMany('App\Models\DetailReturn', 'id_pn');
    }
    public function detail_delivery()
    {
        return $this->hasMany('App\Models\DetailDelivery', 'id_pn');
    }
    public function detailDelivery()
    {
        return $this->hasMany('App\Models\DetailDelivery', 'id_pn');
    }
    public function detail_pending()
    {
        return $this->hasMany('App\Models\DetailPendingPO', 'id_equivalent');
    }
    public function machine()
    {
        return $this->hasMany('App\Models\Machine', 'id_unit');
    }
}
