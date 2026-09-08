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
        'no_service',
        'type',
        'pm_level',
        'running',
        'load',
        'jobdesc',
        'desc',
        'recomendation',
        'sign_client',
        'sign_token',
        'customer_signature',
        'customer_signer_name',
        'customer_signer_position',
        'customer_signed_stamp',
        'customer_ip',
        'signed_at',
        'approval_status',
        'approved_by',
        'approved_at',
        'reject_note',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'signed_at'   => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($report) {
            if (empty($report->sign_token)) {
                $report->sign_token = \Illuminate\Support\Str::random(40);
            }
        });
    }

    /**
     * URL publik tanda tangan digital customer (tanpa perlu login).
     */
    public function getSignUrlAttribute(): string
    {
        if (empty($this->sign_token)) {
            $this->sign_token = \Illuminate\Support\Str::random(40);
            $this->saveQuietly();
        }
        return route('service-report.customer.sign', $this->sign_token);
    }

    /**
     * True jika sudah ditandatangani oleh customer.
     */
    public function isSignedByCustomer(): bool
    {
        return !empty($this->customer_signature) || !empty($this->sign_client);
    }

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
        $sig = $this->customer_signature ?: $this->sign_client;
        if (!$sig) {
            return null;
        }

        if (str_starts_with($sig, 'http://') || str_starts_with($sig, 'https://')) {
            return $sig;
        }

        if (str_starts_with($sig, 'service-reports/')) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($sig);
        }

        if (str_starts_with($sig, 'asset/')) {
            return asset($sig);
        }

        return url('/' . $sig);
    }
}
