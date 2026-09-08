<?php

namespace App\Services\Hvac;

use App\Models\Hvac\HvacCalculationResult;
use App\Models\Hvac\HvacRoom;
use App\Services\Hvac\Calculators\EquipmentLoadCalculator;
use App\Services\Hvac\Calculators\GlassLoadCalculator;
use App\Services\Hvac\Calculators\InfiltrationLoadCalculator;
use App\Services\Hvac\Calculators\LightingLoadCalculator;
use App\Services\Hvac\Calculators\PeopleLoadCalculator;
use App\Services\Hvac\Calculators\QuickCoolingLoadCalculator;
use App\Services\Hvac\Calculators\RoofLoadCalculator;
use App\Services\Hvac\Calculators\VentilationLoadCalculator;
use App\Services\Hvac\Calculators\WallLoadCalculator;
use App\Services\Hvac\Support\HvacConversionService;
use Illuminate\Support\Facades\DB;

class HvacCoolingLoadCalculator
{
    protected QuickCoolingLoadCalculator $quickCalculator;
    protected WallLoadCalculator $wallCalculator;
    protected RoofLoadCalculator $roofCalculator;
    protected GlassLoadCalculator $glassCalculator;
    protected PeopleLoadCalculator $peopleCalculator;
    protected LightingLoadCalculator $lightingCalculator;
    protected EquipmentLoadCalculator $equipmentCalculator;
    protected VentilationLoadCalculator $ventilationCalculator;
    protected InfiltrationLoadCalculator $infiltrationCalculator;
    protected AcRecommendationService $acRecommendationService;

    public function __construct(
        QuickCoolingLoadCalculator $quickCalculator,
        WallLoadCalculator $wallCalculator,
        RoofLoadCalculator $roofCalculator,
        GlassLoadCalculator $glassCalculator,
        PeopleLoadCalculator $peopleCalculator,
        LightingLoadCalculator $lightingCalculator,
        EquipmentLoadCalculator $equipmentCalculator,
        VentilationLoadCalculator $ventilationCalculator,
        InfiltrationLoadCalculator $infiltrationCalculator,
        AcRecommendationService $acRecommendationService
    ) {
        $this->quickCalculator = $quickCalculator;
        $this->wallCalculator = $wallCalculator;
        $this->roofCalculator = $roofCalculator;
        $this->glassCalculator = $glassCalculator;
        $this->peopleCalculator = $peopleCalculator;
        $this->lightingCalculator = $lightingCalculator;
        $this->equipmentCalculator = $equipmentCalculator;
        $this->ventilationCalculator = $ventilationCalculator;
        $this->infiltrationCalculator = $infiltrationCalculator;
        $this->acRecommendationService = $acRecommendationService;
    }

    /**
     * Calculate room cooling load and persist results
     */
    public function calculate(HvacRoom $room): HvacCalculationResult
    {
        return DB::transaction(function () use ($room) {
            // Eager load relations
            $room->loadMissing([
                'walls.material',
                'roofs.material',
                'windows.glassType',
                'occupants.activity',
                'lightings',
                'equipments',
                'ventilations',
                'infiltrations',
            ]);

            if ($room->calculation_mode === 'quick') {
                $resultData = $this->runQuickMode($room);
            } else {
                $resultData = $this->runDetailedMode($room);
            }

            // Safety factor (e.g. 10%)
            $safetyFactorPercent = (float) $room->safety_factor_percent >= 0 ? (float) $room->safety_factor_percent : 10.0;
            $designLoadW = round($resultData['subtotal_load_w'] * (1.0 + ($safetyFactorPercent / 100.0)), 2);

            // Unit conversions
            $designLoadBtuh = HvacConversionService::wattToBtuh($designLoadW);
            $designLoadKw   = HvacConversionService::wattToKw($designLoadW);
            $designLoadTr   = HvacConversionService::wattToTr($designLoadW);
            $designLoadPk   = HvacConversionService::wattToPk($designLoadW);

            // Sensible Heat Ratio (SHR)
            $totalSensible = $resultData['total_sensible_load_w'];
            $subtotalLoad = $resultData['subtotal_load_w'];
            $shr = $subtotalLoad > 0 ? round($totalSensible / $subtotalLoad, 3) : 1.000;

            // AC Recommendation
            $recommendation = $this->acRecommendationService->recommend($designLoadBtuh);

            $payload = array_merge($resultData, [
                'id_hvac_room'                 => $room->id,
                'safety_factor_percent'        => $safetyFactorPercent,
                'design_load_w'                => $designLoadW,
                'sensible_heat_ratio'          => $shr,
                'design_load_btuh'             => $designLoadBtuh,
                'design_load_kw'               => $designLoadKw,
                'design_load_tr'               => $designLoadTr,
                'design_load_pk'               => $designLoadPk,
                'id_recommended_ac'            => $recommendation['ac_id'],
                'recommended_ac_model'         => $recommendation['model'],
                'recommended_ac_capacity_btuh' => $recommendation['capacity_btuh'],
                'recommended_ac_pk'            => $recommendation['capacity_pk'],
                'recommended_unit_qty'         => $recommendation['unit_qty'],
                'recommendation_notes'         => $recommendation['notes'],
            ]);

            $calculationResult = HvacCalculationResult::updateOrCreate(
                ['id_hvac_room' => $room->id],
                $payload
            );

            // Update project status to calculated if not yet
            if ($room->project && $room->project->status === 'draft') {
                $room->project->update(['status' => 'calculated']);
            }

            return $calculationResult;
        });
    }

    protected function runQuickMode(HvacRoom $room): array
    {
        $quick = $this->quickCalculator->calculate($room);

        return [
            'wall_sensible_w'             => round($quick['sensible_watt'] * 0.40, 2),
            'roof_sensible_w'             => round($quick['sensible_watt'] * 0.20, 2),
            'glass_conduction_sensible_w' => 0.0,
            'glass_solar_sensible_w'      => round($quick['sensible_watt'] * 0.15, 2),
            'occupant_sensible_w'         => round($quick['sensible_watt'] * 0.15, 2),
            'lighting_sensible_w'         => round($quick['sensible_watt'] * 0.05, 2),
            'equipment_sensible_w'        => round($quick['sensible_watt'] * 0.05, 2),
            'ventilation_sensible_w'      => 0.0,
            'infiltration_sensible_w'     => 0.0,
            'total_sensible_load_w'       => $quick['sensible_watt'],

            'occupant_latent_w'           => $quick['latent_watt'],
            'equipment_latent_w'          => 0.0,
            'ventilation_latent_w'        => 0.0,
            'infiltration_latent_w'       => 0.0,
            'total_latent_load_w'         => $quick['latent_watt'],

            'subtotal_load_w'             => $quick['total_watt'],
        ];
    }

    protected function runDetailedMode(HvacRoom $room): array
    {
        $wall = $this->wallCalculator->calculate($room);
        $roof = $this->roofCalculator->calculate($room);
        $glass = $this->glassCalculator->calculate($room);
        $people = $this->peopleCalculator->calculate($room);
        $light = $this->lightingCalculator->calculate($room);
        $equip = $this->equipmentCalculator->calculate($room);
        $vent = $this->ventilationCalculator->calculate($room);
        $infilt = $this->infiltrationCalculator->calculate($room);

        $totalSensible = round(
            $wall['sensible_watt'] +
            $roof['sensible_watt'] +
            $glass['sensible_watt'] +
            $people['sensible_watt'] +
            $light['sensible_watt'] +
            $equip['sensible_watt'] +
            $vent['sensible_watt'] +
            $infilt['sensible_watt'],
            2
        );

        $totalLatent = round(
            $people['latent_watt'] +
            $equip['latent_watt'] +
            $vent['latent_watt'] +
            $infilt['latent_watt'],
            2
        );

        $subtotal = round($totalSensible + $totalLatent, 2);

        return [
            'wall_sensible_w'             => $wall['sensible_watt'],
            'roof_sensible_w'             => $roof['sensible_watt'],
            'glass_conduction_sensible_w' => $glass['conduction_watt'],
            'glass_solar_sensible_w'      => $glass['solar_watt'],
            'occupant_sensible_w'         => $people['sensible_watt'],
            'lighting_sensible_w'         => $light['sensible_watt'],
            'equipment_sensible_w'        => $equip['sensible_watt'],
            'ventilation_sensible_w'      => $vent['sensible_watt'],
            'infiltration_sensible_w'     => $infilt['sensible_watt'],
            'total_sensible_load_w'       => $totalSensible,

            'occupant_latent_w'           => $people['latent_watt'],
            'equipment_latent_w'          => $equip['latent_watt'],
            'ventilation_latent_w'        => $vent['latent_watt'],
            'infiltration_latent_w'       => $infilt['latent_watt'],
            'total_latent_load_w'         => $totalLatent,

            'subtotal_load_w'             => $subtotal,
        ];
    }
}
