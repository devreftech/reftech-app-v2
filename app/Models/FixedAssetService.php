<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAssetService extends Model
{
    use HasFactory;
    protected $table = "fixed_asset_service";
    protected $fillable = [
        'id_fixed_asset',
        'id_detail_product',
        'warehouse',
        'qty',
        'price',
        'amount',
        'note',
        'date',
        'created_by',
    ];
    public function fixedAsset()
    {
        return $this->belongsTo('App\Models\FixedAsset', 'id_fixed_asset', 'id');
    }
    public function detailProduct()
    {
        return $this->belongsTo('App\Models\DetailProduct', 'id_detail_product', 'id');
    }
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}
