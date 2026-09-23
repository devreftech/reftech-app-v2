<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesTargetHistory extends Model
{
    protected $table = 'sales_target_histories';

    protected $fillable = [
        'user_id',
        'year',
        'target_annual',
        'is_active_roster',
        'sales_type',
        'display_name',
        'subtitle',
        'status',
        'join_date',
        'resign_date',
        'kpi_config',
        'notes',
        'set_by',
    ];

    protected $casts = [
        'is_active_roster' => 'boolean',
        'kpi_config' => 'array',
        'join_date' => 'date',
        'resign_date' => 'date',
    ];

    public function sales()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function setBy()
    {
        return $this->belongsTo(User::class, 'set_by');
    }
}
