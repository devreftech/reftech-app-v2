<?php

namespace App\Services\Hvac\Calculators;

use App\Models\Hvac\HvacRoom;
use App\Models\Hvac\HvacRoomEquipment;

class EquipmentLoadCalculator
{
    /**
     * Calculate equipment internal heat gain
     */
    public function calculate(HvacRoom $room): array
    {
        $totalSensibleWatt = 0.0;
        $totalLatentWatt = 0.0;

        foreach ($room->equipments as $eq) {
            $qty = max(1, (int) $eq->quantity);
            $watt = (float) $eq->watt_per_unit > 0 ? (float) $eq->watt_per_unit : 100.0;
            $usage = (float) $eq->usage_factor > 0 ? (float) $eq->usage_factor : 0.80;
            $sensibleFrac = (float) $eq->sensible_fraction;
            $latentFrac = (float) $eq->latent_fraction;

            $sensibleLoad = round($qty * $watt * $usage * $sensibleFrac, 2);
            $latentLoad = round($qty * $watt * $usage * $latentFrac, 2);

            $eq->quantity = $qty;
            $eq->watt_per_unit = $watt;
            $eq->usage_factor = $usage;
            $eq->calculated_sensible_load_w = $sensibleLoad;
            $eq->calculated_latent_load_w = $latentLoad;
            $eq->saveQuietly();

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
