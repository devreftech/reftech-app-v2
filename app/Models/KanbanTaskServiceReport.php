<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanTaskServiceReport extends Model
{
    use HasFactory;

    protected $table = 'kanban_task_service_reports';

    protected $fillable = [
        'kanban_task_id',
        'service_report_id',
        'note',
    ];

    public function task()
    {
        return $this->belongsTo(KanbanTask::class, 'kanban_task_id');
    }

    public function serviceReport()
    {
        return $this->belongsTo(Reports::class, 'service_report_id');
    }
}
