<?php

namespace App\Services\Hvac\Calculators;

use App\Models\Hvac\HvacRoom;
use App\Models\Hvac\HvacRoomWindow;

class GlassLoadCalculator
{
    /**
     * Default solar irradiance (W/m²) based on orientation in Indonesia
     */
    public const DEFAULT_SOLAR_IRRADIANCE = [
        'E'  => 500.0,
        'W'  => 550.0,
        'NE' => 400.0,
        'SE' => 400.0,
        'NW' => 450.0,
        'SW' => 450.0,
        'N'  => 250.0,
        'S'  => 250.0,
    ];

    /**
     * Calculate glass conduction + solar heat gain
     */
    public function calculate(HvacRoom $room): array
    {
        $totalConductionWatt = 0.0;
        $totalSolarWatt = 0.0;

        $deltaT = max(1.0, (float) ($room->outdoor_temp - $room->indoor_temp));

        foreach ($room->windows as $win) {
            $width = (float) $win->width;
            $height = (float) $win->height;
            $qty = max(1, (int) $win->quantity);
            $area = (float) $win->total_area;
            if ($area <= 0 && $width > 0 && $height > 0) {
                $area = round($width * $height * $qty, 2);
            }

            $uValue = (float) $win->u_value;
            $shgc = (float) $win->shgc;

            if ($win->glassType) {
                if ($uValue <= 0) {
                    $uValue = (float) $win->glassType->u_value;
                }
                if ($shgc <= 0) {
                    $shgc = (float) $win->glassType->shgc;
                }
            }

            if ($uValue <= 0) {
                $uValue = 5.80; // Standard 6mm clear glass fallback
            }
            if ($shgc <= 0) {
                $shgc = 0.820;
            }

            $shading = (float) $win->internal_shading_factor > 0 ? (float) $win->internal_shading_factor : 1.0;

            $orientation = strtoupper(trim($win->orientation ?: 'N'));
            $irradiance = (float) $win->solar_irradiance_w_m2 > 0
                ? (float) $win->solar_irradiance_w_m2
                : (self::DEFAULT_SOLAR_IRRADIANCE[$orientation] ?? 300.0);

            // Conduction load
            $condLoad = round($area * $uValue * $deltaT, 2);

            // Solar radiation load
            $solarLoad = round($area * $shgc * $irradiance * $shading, 2);

            $winTotal = round($condLoad + $solarLoad, 2);

            $win->total_area = $area;
            $win->u_value = $uValue;
            $win->shgc = $shgc;
            $win->internal_shading_factor = $shading;
            $win->solar_irradiance_w_m2 = $irradiance;
            $win->calculated_conduction_load_w = $condLoad;
            $win->calculated_solar_load_w = $solarLoad;
            $win->calculated_total_load_w = $winTotal;
            $win->saveQuietly();

            $totalConductionWatt += $condLoad;
            $totalSolarWatt += $solarLoad;
        }

        return [
            'conduction_watt' => round($totalConductionWatt, 2),
            'solar_watt'      => round($totalSolarWatt, 2),
            'sensible_watt'   => round($totalConductionWatt + $totalSolarWatt, 2),
            'latent_watt'     => 0.0,
            'total_watt'      => round($totalConductionWatt + $totalSolarWatt, 2),
        ];
    }
}
