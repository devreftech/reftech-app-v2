<?php

namespace App\Services\Dashboard;

use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\Target;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SupportDashboardService
{
    /**
     * Get dashboard data payload for Support role
     */
    public function getDashboardData($notulens, $yearNow, $monthNow)
    {
        $support = Auth::id();

        $previousMonth = Carbon::now()->subMonth();
        $yearPrev = $previousMonth->year;
        $monthPrev = $previousMonth->month;

        $prospect = Prospect::whereYear('date', $yearNow)
            ->whereMonth('date', $monthNow)
            ->where('id_support', $support)
            ->count();

        $provided = Prospect::whereYear('date', $yearNow)
            ->whereMonth('date', $monthNow)
            ->where('provide', '!=', '0')
            ->where('id_support', $support)
            ->count();

        $quotation = Quotation::whereYear('estimated_date', $yearNow)
            ->whereMonth('estimated_date', $monthNow)
            ->where('id_support', $support)
            ->where('level', '1')
            ->where('is_primary', '1')
            ->count();

        $po = Quotation::whereYear('po_date', $yearNow)
            ->whereMonth('po_date', $monthNow)
            ->where('id_support', $support)
            ->where('status', '100')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->count();

        $loss = Quotation::whereYear('estimated_date', $yearNow)
            ->whereMonth('estimated_date', $monthNow)
            ->where('id_support', $support)
            ->where('status', '0')
            ->where('level', '1')
            ->where('is_primary', '1')
            ->count();

        $prospectLastMonth = Prospect::whereYear('date', $yearPrev)
            ->whereMonth('date', $monthPrev)
            ->where('id_support', $support)
            ->count();

        $diffProspect = $prospect - $prospectLastMonth;

        $closingRate = $quotation > 0 ? round(($po / $quotation) * 100, 1) : 0;
        $conversionRate = $prospect > 0 ? round(($quotation / $prospect) * 100, 1) : 0;
        $providedRate = $prospect > 0 ? round(($provided / $prospect) * 100, 1) : 0;
        
        $targetRecord = Target::where('id_sales', Auth::id())->first();
        $targetProspect = $targetRecord->prospect ?? 100;
        $progress = $targetProspect > 0
            ? round(($prospect / $targetProspect) * 100, 1)
            : 0;

        return compact(
            'notulens',
            'prospect',
            'provided',
            'quotation',
            'po',
            'loss',
            'closingRate',
            'conversionRate',
            'providedRate',
            'targetProspect',
            'diffProspect'
        );
    }
}
