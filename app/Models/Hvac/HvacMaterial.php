<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HvacMaterial extends Model
{
    use HasFactory;

    protected $table = 'hvac_materials';

    protected $fillable = [
        'category',
        'material_name',
        'description',
        'u_value',
        'default_cltd',
        'is_active',
    ];

    protected $casts = [
        'u_value'      => 'decimal:4',
        'default_cltd' => 'decimal:2',
        'is_active'    => 'boolean',
    ];
}
