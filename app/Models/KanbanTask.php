<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_id',
        'column_id',
        'title',
        'description',
        'due_date',
        'labels',
        'position',
        'assigned_to',
        'priority',
        'pending_po_id',
        'service_report_id',
    ];

    protected $casts = [
        'labels' => 'array',
    ];

    public function board()
    {
        return $this->belongsTo(KanbanBoard::class, 'board_id');
    }

    public function column()
    {
        return $this->belongsTo(KanbanColumn::class, 'column_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(KanbanTaskComment::class, 'task_id')->latest();
    }

    public function activities()
    {
        return $this->hasMany(KanbanTaskActivity::class, 'task_id')->latest();
    }

    public function checklists()
    {
        return $this->hasMany(KanbanChecklist::class, 'task_id');
    }

    public function attachments()
    {
        return $this->hasMany(KanbanTaskAttachment::class, 'task_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'kanban_task_assignees', 'task_id', 'user_id')->withTimestamps();
    }

    public function pendingPo()
    {
        return $this->belongsTo(PendingPO::class, 'pending_po_id');
    }

    public function bast()
    {
        return $this->hasOne(Bast::class, 'id_kanban_task');
    }
}
