<?php

namespace App\Services\Hvac\Calculators;

use App\Models\Hvac\HvacRoom;
use App\Models\Hvac\HvacRoomOccupant;

class PeopleLoadCalculator
{
    /**
     * Calculate people heat gain (Sensible and Latent)
     */
    public function calculate(HvacRoom $room): array
    {
        $totalSensibleWatt = 0.0;
        $totalLatentWatt = 0.0;

        foreach ($room->occupants as $occ) {
            $qty = max(1, (int) $occ->quantity);

            $sensiblePerPerson = (float) $occ->sensible_watt_per_person;
            $latentPerPerson = (float) $occ->latent_watt_per_person;

            if ($occ->activity) {
                if ($sensiblePerPerson <= 0) {
                    $sensiblePerPerson = (float) $occ->activity->sensible_watt;
                }
                if ($latentPerPerson <= 0) {
                    $latentPerPerson = (float) $occ->activity->latent_watt;
                }
            }

            if ($sensiblePerPerson <= 0) {
                $sensiblePerPerson = 75.00; // Office work default
            }
            if ($latentPerPerson <= 0) {
                $latentPerPerson = 55.00;
            }

            $sensibleLoad = round($qty * $sensiblePerPerson, 2);
            $latentLoad = round($qty * $latentPerPerson, 2);

            $occ->quantity = $qty;
            $occ->sensible_watt_per_person = $sensiblePerPerson;
            $occ->latent_watt_per_person = $latentPerPerson;
            $occ->calculated_sensible_load_w = $sensibleLoad;
            $occ->calculated_latent_load_w = $latentLoad;
            $occ->saveQuietly();

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
