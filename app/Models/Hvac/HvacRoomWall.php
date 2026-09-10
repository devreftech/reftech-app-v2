<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HvacRoomWall extends Model
{
    use HasFactory;

    protected $table = 'hvac_room_walls';

    protected $fillable = [
        'id_hvac_room',
        'wall_name',
        'orientation',
        'is_external',
        'length',
        'height',
        'gross_area',
        'window_deduction_area',
        'net_area',
        'id_material',
        'u_value',
        'temp_difference',
        'calculated_sensible_load_w',
    ];

    protected $casts = [
        'is_external'                => 'boolean',
        'length'                     => 'decimal:2',
        'height'                     => 'decimal:2',
        'gross_area'                 => 'decimal:2',
        'window_deduction_area'      => 'decimal:2',
        'net_area'                   => 'decimal:2',
        'u_value'                    => 'decimal:4',
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
