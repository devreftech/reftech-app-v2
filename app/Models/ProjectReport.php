<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectReport extends Model
{
    use HasFactory;

    protected $table = 'project_reports';

    protected $fillable = [
        'report_number',
        'job_name',
        'contract_no',
        'report_date',
        'contractor_name',
        'day_number',
        'day_name',
        'days_remaining',
        'client_id',
        'kanban_task_id',
        'created_by',
        'weather_cerah',
        'weather_cerah_time',
        'weather_hujan',
        'weather_hujan_time',
        'weather_mendung',
        'weather_mendung_time',
        'weather_dll',
        'weather_dll_time',
        'planning_today',
        'achievement_today',
        'issues_constraints',
        'next_plan',
        'client_sign',
        'client_pic_name',
        'contractor_sign',
        'contractor_pic_name',
        'status',
        'sign_token',
        'customer_signature',
        'customer_signer_name',
        'customer_signer_position',
        'customer_signed_stamp',
        'customer_signed_at',
        'customer_ip',
    ];

    protected $casts = [
        'report_date' => 'date',
        'weather_cerah' => 'boolean',
        'weather_hujan' => 'boolean',
        'weather_mendung' => 'boolean',
        'weather_dll' => 'boolean',
        'customer_signed_at' => 'datetime',
    ];

    /**
     * Get or auto-generate a secure token for customer online signature.
     */
    public function getSignTokenAttribute($value)
    {
        if (empty($value)) {
            $newToken = bin2hex(random_bytes(20));
            \Illuminate\Support\Facades\DB::table('project_reports')
                ->where('id', $this->id)
                ->update(['sign_token' => $newToken]);
            $this->attributes['sign_token'] = $newToken;
            return $newToken;
        }
        return $value;
    }

    /**
     * URL publik untuk customer menandatangani Daily Project Report online.
     */
    public function getSignUrlAttribute(): string
    {
        return url('/project-report/sign/' . $this->sign_token);
    }

    /**
     * Cek apakah Daily Project Report sudah ditandatangani oleh customer secara online.
     */
    public function isSignedByCustomer(): bool
    {
        return !empty($this->customer_signature) && !empty($this->customer_signed_at);
    }

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function kanbanTask()
    {
        return $this->belongsTo(KanbanTask::class, 'kanban_task_id', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(ProjectReportTask::class, 'id_project_report', 'id')->orderBy('sort_order', 'asc');
    }

    public function materials()
    {
        return $this->hasMany(ProjectReportMaterial::class, 'id_project_report', 'id')->orderBy('sort_order', 'asc');
    }

    public function equipments()
    {
        return $this->hasMany(ProjectReportEquipment::class, 'id_project_report', 'id')->orderBy('sort_order', 'asc');
    }

    public function manpowers()
    {
        return $this->hasMany(ProjectReportManpower::class, 'id_project_report', 'id')->orderBy('sort_order', 'asc');
    }

    public function photos()
    {
        return $this->hasMany(ProjectReportPhoto::class, 'id_project_report', 'id')->orderBy('sort_order', 'asc');
    }
}
