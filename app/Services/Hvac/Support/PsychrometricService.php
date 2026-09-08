<?php

namespace App\Services\Hvac\Support;

class PsychrometricService
{
    public const ATM_PRESSURE_KPA = 101.325; // Standard atmospheric pressure at sea level (kPa)

    /**
     * Saturation vapor pressure P_ws (kPa) using Tetens equation
     */
    public static function saturationVaporPressure(float $temperatureC): float
    {
        return 0.61078 * exp((17.27 * $temperatureC) / ($temperatureC + 237.3));
    }

    /**
     * Partial vapor pressure P_w (kPa)
     */
    public static function vaporPressure(float $temperatureC, float $relativeHumidityPercent): float
    {
        $pws = self::saturationVaporPressure($temperatureC);
        return ($relativeHumidityPercent / 100.0) * $pws;
    }

    /**
     * Humidity ratio W (g of moisture / kg of dry air)
     */
    public static function humidityRatio(float $temperatureC, float $relativeHumidityPercent): float
    {
        $pw = self::vaporPressure($temperatureC, $relativeHumidityPercent);
        $w_kg = 0.62198 * ($pw / (self::ATM_PRESSURE_KPA - $pw));
        return $w_kg * 1000.0; // Return in g/kg
    }

    /**
     * Specific enthalpy of moist air (kJ / kg dry air)
     */
    public static function enthalpy(float $temperatureC, float $relativeHumidityPercent): float
    {
        $w_g = self::humidityRatio($temperatureC, $relativeHumidityPercent);
        $w_kg = $w_g / 1000.0;
        return (1.006 * $temperatureC) + ($w_kg * (2501.0 + 1.86 * $temperatureC));
    }
}
