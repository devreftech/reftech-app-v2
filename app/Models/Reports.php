<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reports extends Model
{
    use HasFactory;
    protected $table = "reports";
    protected $date = [
        'date',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'id_pic',
        'id_machine',
        'id_technician',
        'id_monitoring',
        'type',
        'pm_level',
        'running',
        'load',
        'jobdesc',
        'desc',
        'sign_client',
    ];

    // Connection Table
    public function pic()
    {
        return $this->belongsTo('App\Models\Pic', 'id_pic', 'id');
    }
    public function machine()
    {
        return $this->belongsTo('App\Models\Machine', 'id_machine', 'id');
    }
    public function monitoring()
    {
        return $this->belongsTo('App\Models\Monitoring', 'id_monitoring', 'id');
    }
    
    public function technician()
    {
        return $this->belongsTo('App\Models\User', 'id_technician', 'id');
    }

    // Extend Table
    public function picture()
    {
        return $this->hasMany('App\Models\ReportsPict', 'id_reports');
    }
    
}
