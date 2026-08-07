<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class 
DetailProduct extends Model
{
    use HasFactory;
    protected $table = "detail_product";
    protected $date = [
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_product',
        'replacement',
        'modal',
        'hpp',
        'stock',
        'warehouse_stock',
    ];
    
    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'id_product', 'id');
    }
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'id_product', 'id');
    }
    public function detailProductIn()
    {
        return $this->hasMany('App\Models\DetailProductIn', 'id_detail_product');
    }
    public function detailChangeWarehouse()
    {
        return $this->hasMany('App\Models\DetailChangeWarehouse', 'id_replacement');
    }
    public function return()
    {
        return $this->hasMany('App\Models\Retur', 'id_replacement');
    }
    public function opname()
    {
        return $this->hasMany('App\Models\DetailStockOpname', 'id_replacement');
    }
    public function item()
    {
        return $this->hasMany('App\Models\ItemProductSet', 'id_replacement');
    }
}
