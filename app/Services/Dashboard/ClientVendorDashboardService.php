<?php

namespace App\Services\Dashboard;

use App\Models\ProjectReport;
use Carbon\Carbon;

class ClientVendorDashboardService
{
    /**
     * Get dashboard data payload for Client Vendor role
     */
    public function getDashboardData($notulens = null): array
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $currentMonth = $now->month;
        $currentYear = $now->year;
        $userId = \Illuminate\Support\Facades\Auth::id();

        // Daily Project Reports Query (Strictly filtered for the logged in Client Vendor)
        $query = ProjectReport::query();
        if ($userId) {
            $query->where('created_by', $userId);
        }

        $dailyReportsTotal = (clone $query)->count();
        $dailyReportsToday = (clone $query)->whereDate('report_date', $today)->count();
        $dailyReportsThisMonth = (clone $query)->whereYear('report_date', $currentYear)
            ->whereMonth('report_date', $currentMonth)
            ->count();
        $dailyReportsCompleted = (clone $query)->whereIn('status', ['completed', 'approved'])->count();
        $dailyReportsDraft = (clone $query)->where(function ($q) {
            $q->where('status', 'draft')->orWhereNull('status');
        })->count();
        $dailyReportsSigned = (clone $query)->whereNotNull('customer_signature')->count();

        // Recent Daily Reports created by this user
        $recentDailyReports = (clone $query)->with(['creator', 'client', 'photos', 'kanbanTask'])
            ->orderByDesc('report_date')
            ->orderByDesc('id')
            ->take(6)
            ->get();

        $notulens = $notulens ?? collect();

        return [
            'notulens' => $notulens,
            'dailyReportsTotal' => $dailyReportsTotal,
            'dailyReportsToday' => $dailyReportsToday,
            'dailyReportsThisMonth' => $dailyReportsThisMonth,
            'dailyReportsCompleted' => $dailyReportsCompleted,
            'dailyReportsDraft' => $dailyReportsDraft,
            'dailyReportsSigned' => $dailyReportsSigned,
            'recentDailyReports' => $recentDailyReports,
            'adminView' => 'clientvendor'
        ];
    }
}
