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
        'approval_status',
        'approved_by',
        'approved_at',
        'reject_note',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function approver()
    {
        return $this->belongsTo('App\Models\User', 'approved_by', 'id');
    }

    public function isPendingApproval()
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected()
    {
        return $this->approval_status === 'rejected';
    }

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

    public function getSignClientUrlAttribute()
    {
        if (!$this->sign_client) {
            return null;
        }

        if (str_starts_with($this->sign_client, 'service-reports/')) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->sign_client);
        }

        return url('/' . $this->sign_client);
    }
}
