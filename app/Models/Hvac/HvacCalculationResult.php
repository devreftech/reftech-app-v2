<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HvacCalculationResult extends Model
{
    use HasFactory;

    protected $table = 'hvac_calculation_results';

    protected $fillable = [
        'id_hvac_room',
        'wall_sensible_w',
        'roof_sensible_w',
        'glass_conduction_sensible_w',
        'glass_solar_sensible_w',
        'occupant_sensible_w',
        'lighting_sensible_w',
        'equipment_sensible_w',
        'ventilation_sensible_w',
        'infiltration_sensible_w',
        'total_sensible_load_w',
        'occupant_latent_w',
        'equipment_latent_w',
        'ventilation_latent_w',
        'infiltration_latent_w',
        'total_latent_load_w',
        'subtotal_load_w',
        'safety_factor_percent',
        'design_load_w',
        'sensible_heat_ratio',
        'design_load_btuh',
        'design_load_kw',
        'design_load_tr',
        'design_load_pk',
        'id_recommended_ac',
        'recommended_ac_model',
        'recommended_ac_capacity_btuh',
        'recommended_ac_pk',
        'recommended_unit_qty',
        'recommendation_notes',
    ];

    protected $casts = [
        'wall_sensible_w'              => 'decimal:2',
        'roof_sensible_w'              => 'decimal:2',
        'glass_conduction_sensible_w'  => 'decimal:2',
        'glass_solar_sensible_w'       => 'decimal:2',
        'occupant_sensible_w'          => 'decimal:2',
        'lighting_sensible_w'          => 'decimal:2',
        'equipment_sensible_w'         => 'decimal:2',
        'ventilation_sensible_w'       => 'decimal:2',
        'infiltration_sensible_w'      => 'decimal:2',
        'total_sensible_load_w'        => 'decimal:2',
        'occupant_latent_w'            => 'decimal:2',
        'equipment_latent_w'           => 'decimal:2',
        'ventilation_latent_w'         => 'decimal:2',
        'infiltration_latent_w'        => 'decimal:2',
        'total_latent_load_w'          => 'decimal:2',
        'subtotal_load_w'              => 'decimal:2',
        'safety_factor_percent'        => 'decimal:2',
        'design_load_w'                => 'decimal:2',
        'sensible_heat_ratio'          => 'decimal:3',
        'design_load_btuh'             => 'decimal:2',
        'design_load_kw'               => 'decimal:2',
        'design_load_tr'               => 'decimal:2',
        'design_load_pk'               => 'decimal:2',
        'recommended_ac_capacity_btuh' => 'decimal:2',
        'recommended_ac_pk'            => 'decimal:2',
        'recommended_unit_qty'         => 'integer',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(HvacRoom::class, 'id_hvac_room');
    }

    public function recommendedAc(): BelongsTo
    {
        return $this->belongsTo(HvacAcCatalog::class, 'id_recommended_ac');
    }
}
