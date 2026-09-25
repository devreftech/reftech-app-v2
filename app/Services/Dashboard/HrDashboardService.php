<?php

namespace App\Services\Dashboard;

use App\Models\Employee;
use App\Models\HrAttendance;
use App\Models\HrEvaluation;
use App\Models\HrLeaveRequest;
use App\Models\HrPayroll;
use App\Models\HrReimbursement;
use Carbon\Carbon;

class HrDashboardService
{
    /**
     * Get dashboard data payload for HR Dashboard
     */
    public function getHrDashboardData(): array
    {
        $today = Carbon::today();
        $currentMonth = $today->month;
        $currentYear = $today->year;

        // 1. Employee Statistics
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('employment_status', '!=', 'Resign')->count();
        $tetapCount = Employee::where('employment_status', 'Tetap')->count();
        $kontrakCount = Employee::where('employment_status', 'Kontrak')->count();
        $probationCount = Employee::where('employment_status', 'Probation')->count();

        // 2. Today's Attendance
        $todayAttendances = HrAttendance::whereDate('date', $today)->count();
        $lateToday = HrAttendance::whereDate('date', $today)->where('late_minutes', '>', 0)->count();

        // 3. Pending Approvals
        $pendingLeaves = HrLeaveRequest::where('status', 'Pending')->count();
        $pendingReimbursements = HrReimbursement::where('status', 'Pending')->count();
        $pendingReimbursementAmount = (float) HrReimbursement::where('status', 'Pending')->sum('amount');

        // 4. Current Month Payroll Status
        $currentPayroll = HrPayroll::where('period_month', $currentMonth)->where('period_year', $currentYear)->first();

        // 5. Recent Feeds
        $recentLeaves = HrLeaveRequest::with(['employee.user', 'leaveType'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentAttendances = HrAttendance::with(['employee.user'])
            ->whereDate('date', $today)
            ->orderByDesc('clock_in')
            ->limit(6)
            ->get();

        $recentReimbursements = HrReimbursement::with(['employee.user'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return compact(
            'totalEmployees',
            'activeEmployees',
            'tetapCount',
            'kontrakCount',
            'probationCount',
            'todayAttendances',
            'lateToday',
            'pendingLeaves',
            'pendingReimbursements',
            'pendingReimbursementAmount',
            'currentPayroll',
            'recentLeaves',
            'recentAttendances',
            'recentReimbursements'
        );
    }
}
