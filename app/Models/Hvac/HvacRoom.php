<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HvacRoom extends Model
{
    use HasFactory;

    protected $table = 'hvac_rooms';

    protected $fillable = [
        'id_hvac_project',
        'room_name',
        'calculation_mode',
        'room_type',
        'length',
        'width',
        'height',
        'floor_area',
        'room_volume',
        'indoor_temp',
        'indoor_rh',
        'outdoor_temp',
        'outdoor_rh',
        'quick_occupants_count',
        'quick_room_condition',
        'quick_sun_exposure',
        'safety_factor_percent',
        'notes',
    ];

    protected $casts = [
        'length'                => 'decimal:2',
        'width'                 => 'decimal:2',
        'height'                => 'decimal:2',
        'floor_area'            => 'decimal:2',
        'room_volume'           => 'decimal:2',
        'indoor_temp'           => 'decimal:2',
        'indoor_rh'             => 'decimal:2',
        'outdoor_temp'          => 'decimal:2',
        'outdoor_rh'            => 'decimal:2',
        'safety_factor_percent' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::saving(function ($room) {
            $room->floor_area = round($room->length * $room->width, 2);
            $room->room_volume = round($room->floor_area * $room->height, 2);
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(HvacProject::class, 'id_hvac_project');
    }

    public function walls(): HasMany
    {
        return $this->hasMany(HvacRoomWall::class, 'id_hvac_room');
    }

    public function roofs(): HasMany
    {
        return $this->hasMany(HvacRoomRoof::class, 'id_hvac_room');
    }

    public function windows(): HasMany
    {
        return $this->hasMany(HvacRoomWindow::class, 'id_hvac_room');
    }

    public function occupants(): HasMany
    {
        return $this->hasMany(HvacRoomOccupant::class, 'id_hvac_room');
    }

    public function lightings(): HasMany
    {
        return $this->hasMany(HvacRoomLighting::class, 'id_hvac_room');
    }

    public function equipments(): HasMany
    {
        return $this->hasMany(HvacRoomEquipment::class, 'id_hvac_room');
    }

    public function ventilations(): HasMany
    {
        return $this->hasMany(HvacRoomVentilation::class, 'id_hvac_room');
    }

    public function infiltrations(): HasMany
    {
        return $this->hasMany(HvacRoomInfiltration::class, 'id_hvac_room');
    }

    public function result(): HasOne
    {
        return $this->hasOne(HvacCalculationResult::class, 'id_hvac_room');
    }
}
