<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $table = 'app_settings';
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key with optional default.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever('app_setting_' . $key, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );

        Cache::forget('app_setting_' . $key);
    }

    /**
     * Check if Sales Forecast menu is enabled.
     */
    public static function isSalesForecastMenuEnabled(): bool
    {
        $val = static::get('sales_forecast_menu_enabled', '1');
        return in_array($val, ['1', 'true', true, 1], true);
    }

    /**
     * Toggle Sales Forecast menu visibility.
     */
    public static function toggleSalesForecastMenu(): bool
    {
        $current = static::isSalesForecastMenuEnabled();
        $new = !$current;
        static::set('sales_forecast_menu_enabled', $new ? '1' : '0');
        return $new;
    }
}
