<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PnMonitoring extends Model
{
    use HasFactory;
    protected $table = "pn_monitoring";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'desc',
        'pn'
    ];

    public function monitoring()
    {
        return $this->belongsTo('App\Models\Monitoring', 'id_monitoring', 'id');
    }
}
