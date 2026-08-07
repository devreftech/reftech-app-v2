<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanTaskAttachment extends Model
{
    use HasFactory;

    protected $table = 'kanban_task_attachments';

    protected $fillable = [
        'task_id',
        'user_id',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
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
