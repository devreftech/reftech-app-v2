<?php

namespace App\Services\Hvac\Calculators;

use App\Models\Hvac\HvacRoom;
use App\Models\Hvac\HvacRoomWall;

class WallLoadCalculator
{
    /**
     * Calculate wall heat gain (100% sensible)
     * Q = Net Area * U * Temp Difference
     */
    public function calculate(HvacRoom $room): array
    {
        $totalSensibleWatt = 0.0;

        foreach ($room->walls as $wall) {
            $grossArea = (float) $wall->gross_area;
            if ($grossArea <= 0 && $wall->length > 0 && $wall->height > 0) {
                $grossArea = (float) ($wall->length * $wall->height);
            }

            $deduction = (float) $wall->window_deduction_area;
            $netArea = max(0.0, $grossArea - $deduction);

            $uValue = (float) $wall->u_value;
            if ($uValue <= 0 && $wall->material) {
                $uValue = (float) $wall->material->u_value;
            }
            if ($uValue <= 0) {
                $uValue = 2.80; // Standard brick fallback
            }

            $deltaT = (float) $wall->temp_difference;
            if ($deltaT <= 0) {
                $deltaT = max(1.0, (float) ($room->outdoor_temp - $room->indoor_temp));
            }

            $load = round($netArea * $uValue * $deltaT, 2);

            // Update individual wall model
            $wall->gross_area = $grossArea;
            $wall->net_area = $netArea;
            $wall->u_value = $uValue;
            $wall->temp_difference = $deltaT;
            $wall->calculated_sensible_load_w = $load;
            $wall->saveQuietly();

            $totalSensibleWatt += $load;
        }

        return [
            'sensible_watt' => round($totalSensibleWatt, 2),
            'latent_watt'   => 0.0,
            'total_watt'    => round($totalSensibleWatt, 2),
        ];
    }
}
