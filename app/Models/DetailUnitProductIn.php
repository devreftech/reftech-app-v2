<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailUnitProductIn extends Model
{
    protected $table = 'detail_unit_product_in';

    protected $fillable = [
        'id_unit_product_in',
        'id_unit',
        'serial_number',
        'harga',
        'biaya_tambahan',
        'kondisi',
        'id_unit_inventory',
        'id_fixed_asset',
    ];

    public function unitProductIn()
    {
        return $this->belongsTo(UnitProductIn::class, 'id_unit_product_in', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id');
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
