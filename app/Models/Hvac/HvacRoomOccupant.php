<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HvacRoomOccupant extends Model
{
    use HasFactory;

    protected $table = 'hvac_room_occupants';

    protected $fillable = [
        'id_hvac_room',
        'id_activity',
        'activity_name',
        'quantity',
        'sensible_watt_per_person',
        'latent_watt_per_person',
        'calculated_sensible_load_w',
        'calculated_latent_load_w',
    ];

    protected $casts = [
        'quantity'                   => 'integer',
        'sensible_watt_per_person'   => 'decimal:2',
        'latent_watt_per_person'     => 'decimal:2',
        'calculated_sensible_load_w' => 'decimal:2',
        'calculated_latent_load_w'   => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(HvacRoom::class, 'id_hvac_room');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(HvacActivityLoad::class, 'id_activity');
    }
}
