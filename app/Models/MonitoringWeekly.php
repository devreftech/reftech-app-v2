<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringWeekly extends Model
{use HasFactory;
    protected $table = "monitoring_weekly";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_pic',
        'id_machine',
        'condition',
        'voltage',
        'ampere',
        'vibration',
        'idle',
        'week',
        'drain',
        'pre',
        'after',
        'desc',
        'type',
    ];

    public function machine()
    {
        return $this->belongsTo('App\Models\Machine', 'id_machine', 'id');
    }
    public function pic()
    {
        return $this->belongsTo('App\Models\User', 'id_pic', 'id');
    }
}
