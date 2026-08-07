<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailUnitProductOut extends Model
{
    protected $table = 'detail_unit_product_out';

    protected $fillable = [
        'id_unit_product_out',
        'source_type',
        'id_unit_inventory',
        'id_fixed_asset',
        'harga_jual',
        'nilai_pokok',
        'selisih',
    ];

    public function unitProductOut()
    {
        return $this->belongsTo(UnitProductOut::class, 'id_unit_product_out', 'id');
    }

    public function unitInventory()
    {
        return $this->belongsTo(UnitInventory::class, 'id_unit_inventory', 'id');
    }

    public function fixedAsset()
    {
        return $this->belongsTo(FixedAsset::class, 'id_fixed_asset', 'id');
    }
}
