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
    ];

    protected $casts = [
        'report_date' => 'date',
        'weather_cerah' => 'boolean',
        'weather_hujan' => 'boolean',
        'weather_mendung' => 'boolean',
        'weather_dll' => 'boolean',
    ];

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
