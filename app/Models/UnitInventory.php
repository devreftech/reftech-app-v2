<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitInventory extends Model
{
    protected $table = 'unit_inventory';

    protected $fillable = [
        'id_unit',
        'serial_number',
        'harga_modal',
        'biaya_rebranding',
        'total_modal',
        'status',
        'id_unit_product_in',
        'created_by',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'id_unit', 'id');
    }

    public function unitProductIn()
    {
        return $this->belongsTo(UnitProductIn::class, 'id_unit_product_in', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
