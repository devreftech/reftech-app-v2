<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HvacRoomLighting extends Model
{
    use HasFactory;

    protected $table = 'hvac_room_lightings';

    protected $fillable = [
        'id_hvac_room',
        'lighting_name',
        'quantity',
        'watt_per_unit',
        'ballast_factor',
        'usage_factor',
        'calculated_sensible_load_w',
    ];

    protected $casts = [
        'quantity'                   => 'integer',
        'watt_per_unit'              => 'decimal:2',
        'ballast_factor'             => 'decimal:2',
        'usage_factor'               => 'decimal:2',
        'calculated_sensible_load_w' => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(HvacRoom::class, 'id_hvac_room');
    }
}
