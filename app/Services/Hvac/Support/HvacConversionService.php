<?php

namespace App\Services\Hvac\Support;

class HvacConversionService
{
    public const WATT_TO_BTUH = 3.412142;
    public const BTUH_TO_WATT = 0.293071;
    public const TR_TO_BTUH   = 12000.0;
    public const TR_TO_KW     = 3.516853;
    public const PK_TO_BTUH   = 9000.0;  // Standard commercial HVAC in Indonesia/Asia
    public const CFM_TO_LS    = 0.471947;
    public const LS_TO_CFM    = 2.11888;

    public static function wattToBtuh(float $watt): float
    {
        return round($watt * self::WATT_TO_BTUH, 2);
    }

    public static function btuhToWatt(float $btuh): float
    {
        return round($btuh * self::BTUH_TO_WATT, 2);
    }

    public static function wattToKw(float $watt): float
    {
        return round($watt / 1000.0, 3);
    }

    public static function btuhToKw(float $btuh): float
    {
        return round(self::btuhToWatt($btuh) / 1000.0, 3);
    }

    public static function btuhToTr(float $btuh): float
    {
        return round($btuh / self::TR_TO_BTUH, 2);
    }

    public static function wattToTr(float $watt): float
    {
        return round(self::wattToBtuh($watt) / self::TR_TO_BTUH, 2);
    }

    public static function btuhToPk(float $btuh): float
    {
        return round($btuh / self::PK_TO_BTUH, 2);
    }

    public static function wattToPk(float $watt): float
    {
        return round(self::wattToBtuh($watt) / self::PK_TO_BTUH, 2);
    }

    public static function cfmToLs(float $cfm): float
    {
        return round($cfm * self::CFM_TO_LS, 2);
    }

    public static function lsToCfm(float $ls): float
    {
        return round($ls * self::LS_TO_CFM, 2);
    }
}
