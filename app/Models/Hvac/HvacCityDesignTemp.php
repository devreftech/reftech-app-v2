<?php

namespace App\Models\Hvac;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HvacCityDesignTemp extends Model
{
    use HasFactory;

    protected $table = 'hvac_city_design_temps';

    protected $fillable = [
        'city_name',
        'outdoor_db_temp',
        'outdoor_rh_percent',
        'outdoor_wb_temp',
    ];

    protected $casts = [
        'outdoor_db_temp'    => 'decimal:2',
        'outdoor_rh_percent' => 'decimal:2',
        'outdoor_wb_temp'    => 'decimal:2',
    ];
}
