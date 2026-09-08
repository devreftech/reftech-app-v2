<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HvacActivityLoad extends Model
{
    use HasFactory;

    protected $table = 'hvac_activity_loads';

    protected $fillable = [
        'activity_name',
        'sensible_watt',
        'latent_watt',
        'description',
    ];

    protected $casts = [
        'sensible_watt' => 'decimal:2',
        'latent_watt'   => 'decimal:2',
    ];

    public function getTotalWattAttribute(): float
    {
        return (float) ($this->sensible_watt + $this->latent_watt);
    }
}
