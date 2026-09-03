<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectReportMaterial extends Model
{
    use HasFactory;

    protected $table = 'project_report_materials';

    protected $fillable = [
        'id_project_report',
        'material_name',
        'sort_order',
    ];

    public function projectReport()
    {
        return $this->belongsTo(ProjectReport::class, 'id_project_report', 'id');
    }
}
