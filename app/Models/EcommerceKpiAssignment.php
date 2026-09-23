<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceKpiAssignment extends Model
{
    use HasFactory;

    protected $table = 'ecommerce_kpi_assignments';

    protected $fillable = [
        'period_id',
        'user_id',
        'evaluator_id',
        'total_score',
        'grade',
        'status',
        'notes',
        'evaluated_at',
    ];

    protected $casts = [
        'total_score' => 'float',
        'evaluated_at' => 'datetime',
    ];

    public function period()
    {
        return $this->belongsTo(EcommerceKpiPeriod::class, 'period_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function items()
    {
        return $this->hasMany(EcommerceKpiAssignmentItem::class, 'assignment_id')->orderBy('sort_order');
    }

    public function getGradeBadgeColorAttribute(): string
    {
        return match ($this->grade) {
            'A' => 'success',
            'B' => 'info',
            'C' => 'warning',
            'D' => 'danger',
            default => 'secondary',
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'published' => 'success',
            'review' => 'warning',
            'draft' => 'secondary',
            default => 'secondary',
        };
    }
}
