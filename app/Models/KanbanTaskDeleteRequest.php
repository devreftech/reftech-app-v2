<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanTaskDeleteRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'board_id',
        'task_id',
        'requested_by',
        'status',
    ];

    public function board()
    {
        return $this->belongsTo(KanbanBoard::class, 'board_id');
    }

    public function task()
    {
        return $this->belongsTo(KanbanTask::class, 'task_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
