<?php

namespace App\Services\Hvac\Calculators;

use App\Models\Hvac\HvacRoom;
use App\Models\Hvac\HvacRoomVentilation;
use App\Services\Hvac\Support\HvacConversionService;
use App\Services\Hvac\Support\PsychrometricService;

class VentilationLoadCalculator
{
    /**
     * Calculate ventilation (fresh air) cooling load
     * Q_sensible = 1.20 * V_L/s * (T_out - T_in)
     * Q_latent   = 3.01 * V_L/s * (W_out - W_in)
     */
    public function calculate(HvacRoom $room): array
    {
        $totalSensibleWatt = 0.0;
        $totalLatentWatt = 0.0;

        $tOut = (float) $room->outdoor_temp;
        $tIn  = (float) $room->indoor_temp;
        $rhOut = (float) $room->outdoor_rh;
        $rhIn  = (float) $room->indoor_rh;

        $deltaT = max(0.0, $tOut - $tIn);

        // Humidity ratio in g/kg dry air
        $wOut = PsychrometricService::humidityRatio($tOut, $rhOut);
        $wIn  = PsychrometricService::humidityRatio($tIn, $rhIn);
        $deltaW = max(0.0, $wOut - $wIn);

        foreach ($room->ventilations as $vent) {
            $inputVal = (float) $vent->input_value;
            $airflowLs = 0.0;

            switch ($vent->method) {
                case 'ach':
                    // ACH: V (m³) * ACH / 3.6 -> L/s
                    $volume = (float) $room->room_volume;
                    $airflowLs = round(($volume * $inputVal) / 3.6, 2);
                    break;

                case 'direct_airflow':
                    // Direct CFM or L/s
                    $airflowLs = $inputVal;
                    break;

                case 'per_person':
                default:
                    // L/s per person
                    $totalOccupants = $room->occupants->sum('quantity') ?: max(1, $room->quick_occupants_count);
                    $airflowLs = round($totalOccupants * ($inputVal > 0 ? $inputVal : 10.0), 2);
                    break;
            }

            $airflowCfm = HvacConversionService::lsToCfm($airflowLs);

            $sensibleLoad = round(1.204 * $airflowLs * $deltaT, 2);
            $latentLoad   = round(3.01 * $airflowLs * $deltaW, 2);

            $vent->airflow_ls = $airflowLs;
            $vent->airflow_cfm = $airflowCfm;
            $vent->calculated_sensible_load_w = $sensibleLoad;
            $vent->calculated_latent_load_w = $latentLoad;
            $vent->saveQuietly();

            $totalSensibleWatt += $sensibleLoad;
            $totalLatentWatt += $latentLoad;
        }

        return [
            'sensible_watt' => round($totalSensibleWatt, 2),
            'latent_watt'   => round($totalLatentWatt, 2),
            'total_watt'    => round($totalSensibleWatt + $totalLatentWatt, 2),
        ];
    }
}
