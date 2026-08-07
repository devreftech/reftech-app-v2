<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bast extends Model
{
    use HasFactory;

    protected $table = 'basts';

    protected $fillable = [
        'no_bast',
        'id_kanban_task',
        'id_quotation',
        'entity',
        'customer_name',
        'work_title',
        'po_number',
        'work_date',
        'test_running_result',
        'created_by',
    ];

    protected $casts = [
        'work_date' => 'date',
    ];

    public function kanbanTask()
    {
        return $this->belongsTo(KanbanTask::class, 'id_kanban_task');
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'id_quotation');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function units()
    {
        return $this->hasMany(BastUnit::class, 'id_bast')->orderBy('position');
    }
}
