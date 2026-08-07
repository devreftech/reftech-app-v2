<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForecastHistory extends Model
{
    use HasFactory;

    protected $table = 'forecast_history';

    protected $fillable = [
        'id_machine',
        'year',
        'forecast_type',
        'is_forecasted',
        'visit_1_type',
        'visit_1_date',
        'visit_2_type',
        'visit_2_date',
        'visit_3_type',
        'visit_3_date',
        'visit_4_type',
        'visit_4_date',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class, 'id_machine', 'id');
    }
}
