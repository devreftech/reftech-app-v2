<?php

namespace App\Services\Hvac\Calculators;

use App\Models\Hvac\HvacRoom;
use App\Models\Hvac\HvacRoomLighting;

class LightingLoadCalculator
{
    /**
     * Calculate lighting heat gain (100% sensible)
     * Q = Qty * Watt * BallastFactor * UsageFactor
     */
    public function calculate(HvacRoom $room): array
    {
        $totalSensibleWatt = 0.0;

        foreach ($room->lightings as $light) {
            $qty = max(1, (int) $light->quantity);
            $watt = (float) $light->watt_per_unit > 0 ? (float) $light->watt_per_unit : 20.0;
            $ballast = (float) $light->ballast_factor > 0 ? (float) $light->ballast_factor : 1.0;
            $usage = (float) $light->usage_factor > 0 ? (float) $light->usage_factor : 1.0;

            $load = round($qty * $watt * $ballast * $usage, 2);

            $light->quantity = $qty;
            $light->watt_per_unit = $watt;
            $light->ballast_factor = $ballast;
            $light->usage_factor = $usage;
            $light->calculated_sensible_load_w = $load;
            $light->saveQuietly();

            $totalSensibleWatt += $load;
        }

        return [
            'sensible_watt' => round($totalSensibleWatt, 2),
            'latent_watt'   => 0.0,
            'total_watt'    => round($totalSensibleWatt, 2),
        ];
    }
}
