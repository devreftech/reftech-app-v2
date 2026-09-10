<?php

namespace App\Services\Hvac\Calculators;

use App\Models\Hvac\HvacRoom;
use App\Services\Hvac\Support\HvacConversionService;

class QuickCoolingLoadCalculator
{
    /**
     * Calculate quick cooling load for Sales estimation
     * Formula: Q_quick = (Area * BaseIndex * HeightFactor) + (Occupants * 500) + SunCorrection
     */
    public function calculate(HvacRoom $room): array
    {
        $area = (float) $room->floor_area;
        if ($area <= 0 && $room->length > 0 && $room->width > 0) {
            $area = (float) ($room->length * $room->width);
        }

        // Base Index based on room condition (BTU/h per m²)
        $baseIndex = match ($room->quick_room_condition) {
            'light_insulated' => 500.0, // Insulasi bagus / ruangan sejuk
            'glass_heavy'     => 750.0, // Banyak kaca / jendela besar
            'high_heat'       => 900.0, // Atap dak / lantai teratas / dapur ringan
            default           => 600.0, // Standard ruko / rumah / kantor di Indonesia
        };

        // Height factor: standard room height is 3.0 m
        $height = (float) $room->height > 0 ? (float) $room->height : 3.0;
        $heightMultiplier = $height > 3.0 ? ($height / 3.0) : 1.0;

        // Occupant heat addition (standard 500 BTU/h per person above base 1)
        $occupants = max(0, (int) $room->quick_occupants_count);
        $occupantLoadBtuh = $occupants * 500.0;

        // Sun exposure addition
        $sunLoadBtuh = match ($room->quick_sun_exposure) {
            'low'    => 0.0,
            'high'   => 2500.0, // Paparan barat terik langsung
            default  => 1000.0, // Paparan sedang
        };

        $baseAreaLoadBtuh = $area * $baseIndex * $heightMultiplier;
        $subtotalBtuh = $baseAreaLoadBtuh + $occupantLoadBtuh + $sunLoadBtuh;
        $subtotalWatt = HvacConversionService::btuhToWatt($subtotalBtuh);

        // Standard estimate: ~85% sensible, ~15% latent for normal occupancy
        $sensibleWatt = round($subtotalWatt * 0.85, 2);
        $latentWatt = round($subtotalWatt * 0.15, 2);

        return [
            'base_area_load_btuh' => round($baseAreaLoadBtuh, 2),
            'occupant_load_btuh'  => round($occupantLoadBtuh, 2),
            'sun_load_btuh'       => round($sunLoadBtuh, 2),
            'subtotal_btuh'       => round($subtotalBtuh, 2),
            'sensible_watt'       => $sensibleWatt,
            'latent_watt'         => $latentWatt,
            'total_watt'          => $subtotalWatt,
        ];
    }
}
