<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanChecklist extends Model
{
    use HasFactory;

    protected $table = 'kanban_checklists';

    protected $fillable = [
        'task_id',
        'title',
    ];

    public function task()
    {
        return $this->belongsTo(KanbanTask::class, 'task_id');
    }

    public function items()
    {
        return $this->hasMany(KanbanChecklistItem::class, 'checklist_id');
    }
}
