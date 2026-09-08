<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HvacRoomEquipment extends Model
{
    use HasFactory;

    protected $table = 'hvac_room_equipments';

    protected $fillable = [
        'id_hvac_room',
        'equipment_name',
        'quantity',
        'watt_per_unit',
        'usage_factor',
        'sensible_fraction',
        'latent_fraction',
        'calculated_sensible_load_w',
        'calculated_latent_load_w',
    ];

    protected $casts = [
        'quantity'                   => 'integer',
        'watt_per_unit'              => 'decimal:2',
        'usage_factor'               => 'decimal:2',
        'sensible_fraction'          => 'decimal:2',
        'latent_fraction'            => 'decimal:2',
        'calculated_sensible_load_w' => 'decimal:2',
        'calculated_latent_load_w'   => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(HvacRoom::class, 'id_hvac_room');
    }
}
