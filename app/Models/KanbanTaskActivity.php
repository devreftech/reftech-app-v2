<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanTaskActivity extends Model
{
    use HasFactory;

    protected $table = 'kanban_task_activities';

    protected $fillable = [
        'task_id',
        'user_id',
        'activity_type',
        'activity_data',
    ];

    protected $casts = [
        'activity_data' => 'array',
    ];

    public function task()
    {
        return $this->belongsTo(KanbanTask::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
