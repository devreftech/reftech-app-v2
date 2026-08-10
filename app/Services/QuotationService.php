<?php

namespace App\Services;

use App\Models\DetailQuotation;
use App\Models\Prospect;
use App\Models\Quotation;
use App\Models\UnitQuotation;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    /**
     * Calculate card statistics for Quotation dashboard.
     *
     * @param int|string $year
     * @param int|string|null $salesId
     * @return array
     */
    public function calculateCardStats($year, $salesId = null)
    {
        $isAdmin = in_array(Auth::user()->role, ['Admin', 'Sales Manager']);

        // 1. Forecast (Quotation)
        $qForecast = DB::table('quotation as q')
            ->join('users as u', 'u.id', '=', 'q.id_sales')
            ->whereIn('q.status', ['20', '30', '40', '60', '80'])
            ->where('q.level', '1')
            ->where('q.is_primary', '1');

        // 2. Hot Prospect
        $qProspect = DB::table('quotation as q')
            ->join('users as u', 'u.id', '=', 'q.id_sales')
            ->where('q.status', 80)
            ->where('q.level', '1')
            ->where('q.is_primary', '1')
            ->where('q.type', '!=', 'Unit');

        $uqProspect = DB::table('unit_quotation as uq')
            ->join('users as u2', 'u2.id', '=', 'uq.id_sales')
            ->where('uq.status', 'hot_prospect')
            ->where('uq.is_latest', 1);

        // 3. Purchase Order
        $qPo = DB::table('quotation as q')
            ->join('users as u', 'u.id', '=', 'q.id_sales')
            ->where('q.status', '100')
            ->where('q.level', '1')
            ->where('q.is_primary', '1');

        $uqPo = DB::table('unit_quotation as uq')
            ->join('users as u2', 'u2.id', '=', 'uq.id_sales')
            ->where('uq.status', 'po_received')
            ->where('uq.is_latest', 1);

        // 4. Loss Order
        $qLoss = DB::table('quotation as q')
            ->join('users as u', 'u.id', '=', 'q.id_sales')
            ->where('q.status', '0')
            ->where('q.level', '1');

        // Apply sales filter
        if ($isAdmin) {
            if (!empty($salesId)) {
                $qForecast->where('q.id_sales', $salesId);
                $qProspect->where('q.id_sales', $salesId);
                $uqProspect->where('uq.id_sales', $salesId);
                $qPo->where('q.id_sales', $salesId);
                $uqPo->where('uq.id_sales', $salesId);
                $qLoss->where('q.id_sales', $salesId);
            }
        } else {
            $effectiveSalesId = $salesId ?? Auth::id();
            $qForecast->where('q.id_sales', $effectiveSalesId);
            $qProspect->where('q.id_sales', $effectiveSalesId);
            $uqProspect->where('uq.id_sales', $effectiveSalesId);
            $qPo->where('q.id_sales', $effectiveSalesId);
            $uqPo->where('uq.id_sales', $effectiveSalesId);
            $qLoss->where('q.id_sales', $effectiveSalesId);
        }

        // Apply year filter
        if ($year) {
            $qForecast->whereYear('q.status_date', $year);
            $qProspect->whereYear('q.status_date', $year);
            $uqProspect->whereYear('uq.date', $year);
            $qPo->whereYear('q.po_date', $year);
            $uqPo->whereYear('uq.po_received', $year);
            $qLoss->whereYear('q.status_date', $year);
        }

        // Forecast total & count
        $forecastSum = (float) $qForecast->sum('q.nett');
        $forecastCount = (int) $qForecast->count();

        // Prospect total & count
        $prospectSum = (float) $qProspect->sum('q.nett') + (float) $uqProspect->sum('uq.total');
        $prospectCount = (int) $qProspect->count() + (int) $uqProspect->count();

        // PO total & count
        $poSum = (float) $qPo->sum('q.nett') + (float) $uqPo->sum('uq.total');
        $poCount = (int) $qPo->count() + (int) $uqPo->count();

        // Loss total & count
        $lossSum = (float) $qLoss->sum('q.nett');
        $lossCount = (int) $qLoss->count();

        return [
            'forecast_sum' => $forecastSum,
            'forecast_count' => $forecastCount,
            'prospect_sum' => $prospectSum,
            'prospect_count' => $prospectCount,
            'po_sum' => $poSum,
            'po_count' => $poCount,
            'loss_sum' => $lossSum,
            'loss_count' => $lossCount,
        ];
    }

    /**
     * Fetch initial index page data aggregations for QuotationController.
     *
     * @return array
     */
    public function getIndexPageData()
    {
        $currentYear = now()->year;
        $salesStats = $this->calculateCardStats($currentYear, null);

        $noSaleProspect = Prospect::whereNULL('id_sales')->whereNull('provide')->count();
        $leveledProspect = Prospect::whereNULL('level')->where('id_sales', Auth::id())->count();
        $salesList = User::where('role', 'Sales')->where('id', '!=', 23)->orderBy('name')->get(['id', 'name']);

        return [
            'currentYear' => $currentYear,
            'salesStats' => $salesStats,
            'noSaleProspect' => $noSaleProspect,
            'leveledProspect' => $leveledProspect,
            'salesList' => $salesList,
        ];
    }

    /**
     * Fetch quotation with details and primary quotation data.
     *
     * @param int|string $id
     * @return array
     */
    public function getQuotationWithDetails($id)
    {
        $quote = Quotation::with(['pic.client', 'sales', 'detail_quotation', 'termncon'])->findOrFail($id);
        $lastQuote = Quotation::where('primary_id', $quote->primary_id)->orderByDesc('num_rev')->first();
        $primQuote = Quotation::where('primary_id', $quote->primary_id)->where('is_primary', '1')->first();
        $detquote = DetailQuotation::where('id_quotation', $id)->get();

        return [
            'quote' => $quote,
            'lastQuote' => $lastQuote,
            'primQuote' => $primQuote,
            'detquote' => $detquote,
        ];
    }
}
