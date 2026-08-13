<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BearingKitPrice extends Model
{
    use HasFactory;

    protected $table = 'bearing_kit_prices';

    protected $fillable = [
        'power',
        'price_bearing_kit',
        'price_bearing_kit_main_motor',
        'price_bearing_kit_fan_motor',
    ];
}
