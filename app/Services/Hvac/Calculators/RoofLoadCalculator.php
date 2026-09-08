<?php

namespace App\Services\Hvac\Calculators;

use App\Models\Hvac\HvacRoom;
use App\Models\Hvac\HvacRoomRoof;

class RoofLoadCalculator
{
    /**
     * Calculate roof/ceiling heat gain (100% sensible)
     * Q = Area * U * Temp Difference
     */
    public function calculate(HvacRoom $room): array
    {
        $totalSensibleWatt = 0.0;

        foreach ($room->roofs as $roof) {
            $area = (float) $roof->area;
            if ($area <= 0) {
                $area = (float) $room->floor_area;
            }

            $uValue = (float) $roof->u_value;
            if ($uValue <= 0 && $roof->material) {
                $uValue = (float) $roof->material->u_value;
            }
            if ($uValue <= 0) {
                $uValue = 1.50; // Standard ceiling fallback
            }

            $deltaT = (float) $roof->temp_difference;
            if ($deltaT <= 0) {
                // If exposed directly to tropical sun, surface heats up considerably
                $baseDelta = max(1.0, (float) ($room->outdoor_temp - $room->indoor_temp));
                $deltaT = $roof->is_exposed_to_sun ? ($baseDelta + 10.0) : $baseDelta;
            }

            $load = round($area * $uValue * $deltaT, 2);

            $roof->area = $area;
            $roof->u_value = $uValue;
            $roof->temp_difference = $deltaT;
            $roof->calculated_sensible_load_w = $load;
            $roof->saveQuietly();

            $totalSensibleWatt += $load;
        }

        return [
            'sensible_watt' => round($totalSensibleWatt, 2),
            'latent_watt'   => 0.0,
            'total_watt'    => round($totalSensibleWatt, 2),
        ];
    }
}
