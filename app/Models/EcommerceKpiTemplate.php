<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceKpiTemplate extends Model
{
    use HasFactory;

    protected $table = 'ecommerce_kpi_templates';

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'unit',
        'default_target',
        'default_weight',
        'calculation_handler',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'default_target' => 'float',
        'default_weight' => 'float',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function assignmentItems()
    {
        return $this->hasMany(EcommerceKpiAssignmentItem::class, 'template_id');
    }
}
