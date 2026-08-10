<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\SalesReports;
use App\Models\UnitQuotation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OverviewService
{
    /**
     * Resolve active SalesReport for the given request parameters or current date.
     *
     * @param string|null $reportId
     * @return array
     */
    public function resolveReportContext($reportId = null)
    {
        if ($reportId && str_starts_with($reportId, 'full_')) {
            $year = (int) str_replace('full_', '', $reportId);
            return [
                'type' => 'full_year',
                'year' => $year,
                'report' => null,
            ];
        }

        if ($reportId) {
            $report = SalesReports::find($reportId);
        } else {
            $year = now()->year;
            $semester = now()->month <= 6 ? 1 : 2;
            $report = SalesReports::where('year', $year)->where('semester', $semester)->first();
            if (!$report) {
                $report = SalesReports::orderBy('year', 'desc')->orderBy('semester', 'desc')->first();
            }
        }

        return [
            'type' => 'semester',
            'year' => $report->year ?? now()->year,
            'report' => $report,
        ];
    }

    /**
     * Fetch active sales users list for overview page.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveSalesList()
    {
        return User::where('role', 'Sales')->where('active', '1')->get();
    }

    /**
     * Calculate support report stats and data arrays for OverviewController.
     *
     * @param int $year
     * @param int $month
     * @param int|null $semester
     * @param string $mode
     * @param int $supportId
     * @return array
     */
    public function getSupportReportData(int $year, int $month, ?int $semester, string $mode, int $supportId)
    {
        if ($mode === 'semester') {
            $firstDay = $semester == 1 ? "{$year}-01-01" : "{$year}-07-01";
            $lastDay  = $semester == 1
                ? date('Y-m-t', strtotime("{$year}-06-01"))
                : date('Y-m-t', strtotime("{$year}-12-01"));
        } else {
            $firstDay = Carbon::create($year, $month, 1)->startOfMonth()->toDateString();
            $lastDay  = Carbon::create($year, $month, 1)->endOfMonth()->toDateString();
        }

        $yearList = SalesReports::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        if (!$yearList->contains($year)) {
            $yearList = $yearList->push($year)->sortDesc()->values();
        }

        return [
            'year' => $year,
            'month' => $month,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'yearList' => $yearList,
            'supportId' => $supportId,
        ];
    }
}
