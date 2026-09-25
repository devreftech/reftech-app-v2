<?php

namespace App\Models\Hr;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrHoliday extends Model
{
    use HasFactory;

    protected $table = 'hr_holidays';

    protected $fillable = [
        'name',
        'holiday_date',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'holiday_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Cek apakah tanggal tertentu adalah hari libur / kalender merah.
     */
    public static function isHoliday($date): bool
    {
        $dateStr = $date instanceof Carbon ? $date->toDateString() : Carbon::parse($date)->toDateString();

        return static::where('is_active', true)
            ->whereDate('holiday_date', $dateStr)
            ->exists();
    }

    /**
     * Ambil data libur untuk tanggal tertentu.
     */
    public static function getHolidayInfo($date): ?self
    {
        $dateStr = $date instanceof Carbon ? $date->toDateString() : Carbon::parse($date)->toDateString();

        return static::where('is_active', true)
            ->whereDate('holiday_date', $dateStr)
            ->first();
    }
}
