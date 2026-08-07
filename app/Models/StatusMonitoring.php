<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusMonitoring extends Model
{
    use HasFactory;
    protected $table = "status_monitoring";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'desc',
        'status'
    ];

    public function monitoring()
    {
        return $this->belongsTo('App\Models\Monitoring', 'id_monitoring', 'id');
    }
    public function pic()
    {
        return $this->belongsTo('App\Models\User', 'id_pic', 'id');
    }
}
