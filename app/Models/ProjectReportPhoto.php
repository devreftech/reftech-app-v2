<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectReportPhoto extends Model
{
    use HasFactory;

    protected $table = 'project_report_photos';

    protected $fillable = [
        'id_project_report',
        'photo_path',
        'caption',
        'sort_order',
    ];

    public function projectReport()
    {
        return $this->belongsTo(ProjectReport::class, 'id_project_report', 'id');
    }

    public function getUrlAttribute()
    {
        if (!$this->photo_path) {
            return null;
        }

        if (str_starts_with($this->photo_path, 'project-reports/')) {
            return Storage::disk('public')->url($this->photo_path);
        }

        return url('/' . $this->photo_path);
    }
}
