<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitInventoryRebrandingCost extends Model
{
    protected $table = 'unit_inventory_rebranding_costs';

    protected $fillable = [
        'id_unit_inventory',
        'date',
        'item',
        'amount',
        'note',
        'created_by',
    ];

    public function unitInventory()
    {
        return $this->belongsTo(UnitInventory::class, 'id_unit_inventory', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
