<?php

namespace App\Services;

use App\Models\SalesReports;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
}
