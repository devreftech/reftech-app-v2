<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringMonthly extends Model
{ 
    use HasFactory;
    protected $table = "monitoring_monthly";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_pic',
        'id_machine',
        'refrigerasi',
        'strainer',
        'lp',
        'hp',
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
