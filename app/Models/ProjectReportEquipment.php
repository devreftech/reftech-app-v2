<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectReportEquipment extends Model
{
    use HasFactory;

    protected $table = 'project_report_equipments';

    protected $fillable = [
        'id_project_report',
        'equipment_name',
        'qty',
        'unit',
        'sort_order',
    ];

    public function projectReport()
    {
        return $this->belongsTo(ProjectReport::class, 'id_project_report', 'id');
    }
}
