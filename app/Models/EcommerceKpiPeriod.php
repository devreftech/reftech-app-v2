<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceKpiPeriod extends Model
{
    use HasFactory;

    protected $table = 'ecommerce_kpi_periods';

    protected $fillable = [
        'year',
        'month',
        'start_date',
        'end_date',
        'status',
        'published_at',
        'published_by',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'published_at' => 'datetime',
    ];

    public function assignments()
    {
        return $this->hasMany(EcommerceKpiAssignment::class, 'period_id');
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function getMonthNameAttribute(): string
    {
        return Carbon::createFromDate($this->year, $this->month, 1)->locale('id')->isoFormat('MMMM');
    }

    public function getPeriodLabelAttribute(): string
    {
        return "{$this->month_name} {$this->year}";
    }
}
