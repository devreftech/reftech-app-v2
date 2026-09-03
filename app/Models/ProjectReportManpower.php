<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectReportManpower extends Model
{
    use HasFactory;

    protected $table = 'project_report_manpowers';

    protected $fillable = [
        'id_project_report',
        'position',
        'manpower_count',
        'sort_order',
    ];

    public function projectReport()
    {
        return $this->belongsTo(ProjectReport::class, 'id_project_report', 'id');
    }
}
