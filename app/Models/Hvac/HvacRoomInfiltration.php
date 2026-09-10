<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HvacRoomInfiltration extends Model
{
    use HasFactory;

    protected $table = 'hvac_room_infiltrations';

    protected $fillable = [
        'id_hvac_room',
        'method',
        'ach_value',
        'airflow_ls',
        'calculated_sensible_load_w',
        'calculated_latent_load_w',
    ];

    protected $casts = [
        'ach_value'                  => 'decimal:2',
        'airflow_ls'                 => 'decimal:2',
        'calculated_sensible_load_w' => 'decimal:2',
        'calculated_latent_load_w'   => 'decimal:2',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(HvacRoom::class, 'id_hvac_room');
    }
}
