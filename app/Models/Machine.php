<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory; 
    protected $table = "machine";
    protected $fillable = [
        'id_client',
        'id_unit',
        'desc',
        'serial',
        'tag',
        'status',
        'location',
        'is_forecasted',
        'forecast_type',
        'last_service_date',
        'visit_1_type',
        'visit_1_date',
        'visit_2_type',
        'visit_2_date',
        'visit_3_type',
        'visit_3_date',
        'visit_4_type',
        'visit_4_date',
    ];

    
    public function client()
    {
        return $this->belongsTo('App\Models\Client', 'id_client', 'id');
    }
    public function unit()
    {
        return $this->belongsTo('App\Models\SerialProduct', 'id_unit', 'id');
    }
    public function reports()
    {
        return $this->hasMany('App\Models\Reports', 'id_machine');
    }
    public function monitoring()
    {
        return $this->hasMany('App\Models\Monitoring', 'id_machine');
    }
    public function mainlog()
    {
        return $this->hasMany('App\Models\Mainlog', 'id_machine');
    }
    public function monitoringW()
    {
        return $this->hasMany('App\Models\MonitoringWeekly', 'id_machine');
    }
    public function monitoringM()
    {
        return $this->hasMany('App\Models\MonitoringMonthly', 'id_machine');
    }

    public function getActualLastServiceDateAttribute()
    {
        $lastReport = $this->reports()
            ->where('type', 'Service')
            ->orderBy('date', 'desc')
            ->first();
        if ($lastReport && $lastReport->date) {
            return \Carbon\Carbon::parse($lastReport->date)->format('Y-m-d');
        }
        return $this->last_service_date ? \Carbon\Carbon::parse($this->last_service_date)->format('Y-m-d') : $this->created_at->format('Y-m-d');
    }

    public function forecastHistories()
    {
        return $this->hasMany(\App\Models\ForecastHistory::class, 'id_machine');
    }

    public function getForecastNominal($year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        // Apply historical forecast snapshot if it exists for the requested year
        $history = $this->forecastHistories->where('year', $year)->first();
        
        $forecast_type = $history ? $history->forecast_type : $this->forecast_type;
        $visit_1_type = $history ? $history->visit_1_type : $this->visit_1_type;
        $visit_1_date = $history ? $history->visit_1_date : $this->visit_1_date;
        $visit_2_type = $history ? $history->visit_2_type : $this->visit_2_type;
        $visit_2_date = $history ? $history->visit_2_date : $this->visit_2_date;
        $visit_3_type = $history ? $history->visit_3_type : $this->visit_3_type;
        $visit_3_date = $history ? $history->visit_3_date : $this->visit_3_date;
        $visit_4_type = $history ? $history->visit_4_type : $this->visit_4_type;
        $visit_4_date = $history ? $history->visit_4_date : $this->visit_4_date;
        $is_forecasted = $history ? $history->is_forecasted : $this->is_forecasted;

        if (!$is_forecasted) {
            return 0;
        }

        if ($forecast_type == 'contract') {
            return \App\Models\ContractVisitSchedule::whereHas('contract', function($q) {
                    $q->where('id_quotation', function($sub) {
                        $sub->select('id_quotation')
                            ->from('quotation')
                            ->where('pic.id_client', $this->id_client)
                            ->join('pic', 'pic.id', '=', 'quotation.id_pic')
                            ->limit(1);
                    });
                })
                ->whereYear('planned_date', $year)
                ->where('status', 'Pending')
                ->sum('estimated_revenue');
        }

        // For Regular Service and Parts Only
        $power = $this->unit->unit->power ?? null;
        $servicePrices = null;
        if ($power) {
            $normalizedPower = \App\Http\Controllers\ForecastController::normalizePower($power);
            $servicePrices = \App\Models\PowerServicePrice::where('power', $normalizedPower)->first();
        }

        // Get Template Penawaran PM items (manually-curated per unit + level)
        $spareparts = collect();
        if ($this->unit && $this->unit->unit) {
            $spareparts = \App\Models\UnitPmTemplateItem::where('id_unit', $this->unit->unit->id)->get();
        }

        $visits = [
            ['date' => $visit_1_date, 'type' => $visit_1_type],
            ['date' => $visit_2_date, 'type' => $visit_2_type],
            ['date' => $visit_3_date, 'type' => $visit_3_type],
            ['date' => $visit_4_date, 'type' => $visit_4_type],
        ];

        $total = 0;
        foreach ($visits as $v) {
            if (empty($v['date']) || empty($v['type'])) {
                continue;
            }

            if (\Carbon\Carbon::parse($v['date'])->year != $year) {
                continue;
            }

            $pmLevel = $v['type'];

            // 1. Parts — sourced from the manually-curated PM template for this unit + level
            $partsTotal = 0;
            foreach ($spareparts as $sp) {
                if ($sp->level == $pmLevel) {
                    $partsTotal += ($sp->qty * $sp->price);
                }
            }

            // 2. Service
            $serviceFee = 0;
            if ($forecast_type == 'regular_service' && $servicePrices) {
                switch ($pmLevel) {
                    case 'PM1': $serviceFee = $servicePrices->price_pm1; break;
                    case 'PM2': $serviceFee = $servicePrices->price_pm2; break;
                }
            }

            $total += ($partsTotal + $serviceFee);
        }

        return $total;
    }
}
