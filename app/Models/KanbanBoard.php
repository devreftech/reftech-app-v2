<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanBoard extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'labels',
        'type',
        'notification_sound',
    ];

    protected $casts = [
        'labels' => 'array',
    ];

    public function columns()
    {
        return $this->hasMany(KanbanColumn::class, 'board_id')->orderBy('position');
    }

    public function tasks()
    {
        return $this->hasMany(KanbanTask::class, 'board_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'kanban_board_members', 'board_id', 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
