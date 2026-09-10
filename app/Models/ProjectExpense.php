<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectExpense extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'project_expenses';

    protected $fillable = [
        'id_pending',
        'id_kanban_task',
        'id_user',
        'name',
        'category',
        'amount',
        'payment_info',
        'date',
        'receipt',
    ];

    protected $dates = ['date'];

    public function pending()
    {
        return $this->belongsTo('App\Models\PendingPO', 'id_pending', 'id');
    }

    public function kanbanTask()
    {
        return $this->belongsTo('App\Models\KanbanTask', 'id_kanban_task', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id_user', 'id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\PurchasePayment', 'id_project_expense');
    }

    public function activityLogReferenceLabel(): ?string
    {
        return $this->name ? "{$this->name} (" . ($this->category ?: 'Biaya') . " - Rp " . number_format($this->amount, 0, '', '.') . ")" : null;
    }

    public function activityLogExtraProperties(): array
    {
        return [
            'id_pending'     => $this->id_pending,
            'id_kanban_task' => $this->id_kanban_task,
            'expense_name'   => $this->name,
            'category'       => $this->category,
            'amount'         => (float) $this->amount,
        ];
    }
}
