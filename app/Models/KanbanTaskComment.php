<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanTaskComment extends Model
{
    use HasFactory;

    protected $table = 'kanban_task_comments';

    protected $fillable = [
        'task_id',
        'user_id',
        'comment',
    ];

    public function task()
    {
        return $this->belongsTo(KanbanTask::class, 'task_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function mentions()
    {
        return $this->belongsToMany(User::class, 'kanban_task_comment_mentions', 'comment_id', 'user_id')->withTimestamps();
    }
}
