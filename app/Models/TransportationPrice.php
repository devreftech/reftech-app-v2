<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportationPrice extends Model
{
    use HasFactory;

    protected $table = 'transportation_prices';

    protected $fillable = [
        'city',
        'price',
    ];
}
