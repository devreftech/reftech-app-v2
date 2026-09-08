<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HvacGlassType extends Model
{
    use HasFactory;

    protected $table = 'hvac_glass_types';

    protected $fillable = [
        'glass_name',
        'u_value',
        'shgc',
        'shading_coefficient',
        'description',
        'is_active',
    ];

    protected $casts = [
        'u_value'             => 'decimal:4',
        'shgc'                => 'decimal:3',
        'shading_coefficient' => 'decimal:3',
        'is_active'           => 'boolean',
    ];
}
