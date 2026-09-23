<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceKpiAssignmentItem extends Model
{
    use HasFactory;

    protected $table = 'ecommerce_kpi_assignment_items';

    protected $fillable = [
        'assignment_id',
        'template_id',
        'kpi_code',
        'kpi_name',
        'kpi_type',
        'unit',
        'calculation_handler',
        'target',
        'actual_system',
        'actual_final',
        'achievement_rate',
        'weight',
        'score',
        'evaluator_notes',
        'sort_order',
    ];

    protected $casts = [
        'target' => 'float',
        'actual_system' => 'float',
        'actual_final' => 'float',
        'achievement_rate' => 'float',
        'weight' => 'float',
        'score' => 'float',
        'sort_order' => 'integer',
    ];

    public function assignment()
    {
        return $this->belongsTo(EcommerceKpiAssignment::class, 'assignment_id');
    }

    public function template()
    {
        return $this->belongsTo(EcommerceKpiTemplate::class, 'template_id');
    }

    public function getTypeBadgeColorAttribute(): string
    {
        return match ($this->kpi_type) {
            'automatic' => 'primary',
            'hybrid' => 'warning',
            'manual' => 'info',
            default => 'secondary',
        };
    }
}
