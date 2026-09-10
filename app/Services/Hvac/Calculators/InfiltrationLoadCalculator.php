<?php

namespace App\Services\Hvac\Calculators;

use App\Models\Hvac\HvacRoom;
use App\Models\Hvac\HvacRoomInfiltration;
use App\Services\Hvac\Support\PsychrometricService;

class InfiltrationLoadCalculator
{
    /**
     * Calculate air infiltration heat gain
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
        $wOut = PsychrometricService::humidityRatio($tOut, $rhOut);
        $wIn  = PsychrometricService::humidityRatio($tIn, $rhIn);
        $deltaW = max(0.0, $wOut - $wIn);

        foreach ($room->infiltrations as $infilt) {
            $ach = (float) $infilt->ach_value > 0 ? (float) $infilt->ach_value : 0.50;
            $volume = (float) $room->room_volume;
            if ($volume <= 0 && $room->length > 0 && $room->width > 0 && $room->height > 0) {
                $volume = (float) ($room->length * $room->width * $room->height);
            }

            // Infiltration flow rate in L/s
            $airflowLs = round(($volume * $ach) / 3.6, 2);

            $sensibleLoad = round(1.204 * $airflowLs * $deltaT, 2);
            $latentLoad   = round(3.01 * $airflowLs * $deltaW, 2);

            $infilt->ach_value = $ach;
            $infilt->airflow_ls = $airflowLs;
            $infilt->calculated_sensible_load_w = $sensibleLoad;
            $infilt->calculated_latent_load_w = $latentLoad;
            $infilt->saveQuietly();

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
