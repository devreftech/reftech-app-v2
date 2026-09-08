<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HvacRoomRoof extends Model
{
    use HasFactory;

    protected $table = 'hvac_room_roofs';

    protected $fillable = [
        'id_hvac_room',
        'roof_name',
        'area',
        'id_material',
        'u_value',
        'is_exposed_to_sun',
        'temp_difference',
        'calculated_sensible_load_w',
    ];

    protected $casts = [
        'area'                       => 'decimal:2',
        'u_value'                    => 'decimal:4',
        'is_exposed_to_sun'          => 'boolean',
        'temp_difference'            => 'decimal:2',
        'calculated_sensible_load_w' => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(HvacRoom::class, 'id_hvac_room');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(HvacMaterial::class, 'id_material');
    }
}
