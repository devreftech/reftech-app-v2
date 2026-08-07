<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KanbanChecklistItem extends Model
{
    use HasFactory;

    protected $table = 'kanban_checklist_items';

    protected $fillable = [
        'checklist_id',
        'title',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function checklist()
    {
        return $this->belongsTo(KanbanChecklist::class, 'checklist_id');
    }
}
