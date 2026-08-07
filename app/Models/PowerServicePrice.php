<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PowerServicePrice extends Model
{
    use HasFactory;

    protected $table = 'power_service_prices';

    protected $fillable = [
        'power',
        'price_pm1',
        'price_pm2',
        'price_pm3',
        'price_pm4',
        'desc_pm1',
        'desc_pm2',
        'desc_pm3',
        'desc_pm4',
        'note_pm1',
        'note_pm2',
        'note_pm3',
        'note_pm4',
    ];
}
