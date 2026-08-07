<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringActivities extends Model
{
    use HasFactory;
    protected $table = "monitoring_activities";
    protected $date = [
        'date'
    ];
    protected $fillable = [
        'planing',
        'sync',
        'abnormal',
        'log',
        'timeline',
        'preventive',
    ];
}
