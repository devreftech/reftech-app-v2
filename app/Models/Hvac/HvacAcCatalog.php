<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HvacAcCatalog extends Model
{
    use HasFactory;

    protected $table = 'hvac_ac_catalog';

    protected $fillable = [
        'brand',
        'model_name',
        'ac_type',
        'nominal_pk',
        'cooling_capacity_btuh',
        'cooling_capacity_kw',
        'power_input_watt',
        'refrigerant',
        'energy_rating',
        'is_active',
    ];

    protected $casts = [
        'nominal_pk'            => 'decimal:2',
        'cooling_capacity_btuh' => 'decimal:2',
        'cooling_capacity_kw'   => 'decimal:3',
        'power_input_watt'      => 'decimal:2',
        'is_active'             => 'boolean',
    ];

    public function getDisplayNameAttribute(): string
    {
        return "{$this->brand} {$this->model_name} ({$this->nominal_pk} PK / " . number_format($this->cooling_capacity_btuh) . " BTU/h)";
    }
}
