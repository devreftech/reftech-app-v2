<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HvacRoomWindow extends Model
{
    use HasFactory;

    protected $table = 'hvac_room_windows';

    protected $fillable = [
        'id_hvac_room',
        'window_name',
        'width',
        'height',
        'quantity',
        'total_area',
        'orientation',
        'id_glass_type',
        'u_value',
        'shgc',
        'internal_shading_factor',
        'solar_irradiance_w_m2',
        'calculated_conduction_load_w',
        'calculated_solar_load_w',
        'calculated_total_load_w',
    ];

    protected $casts = [
        'width'                        => 'decimal:2',
        'height'                       => 'decimal:2',
        'quantity'                     => 'integer',
        'total_area'                   => 'decimal:2',
        'u_value'                      => 'decimal:4',
        'shgc'                         => 'decimal:3',
        'internal_shading_factor'      => 'decimal:2',
        'solar_irradiance_w_m2'        => 'decimal:2',
        'calculated_conduction_load_w' => 'decimal:2',
        'calculated_solar_load_w'      => 'decimal:2',
        'calculated_total_load_w'      => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(HvacRoom::class, 'id_hvac_room');
    }

    public function glassType(): BelongsTo
    {
        return $this->belongsTo(HvacGlassType::class, 'id_glass_type');
    }
}
