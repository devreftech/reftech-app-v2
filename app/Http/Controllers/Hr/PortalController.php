<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrAttendance;
use App\Models\HrEmployeeAsset;
use App\Models\HrLeaveBalance;
use App\Models\HrLeaveRequest;
use App\Models\HrLeaveType;
use App\Models\HrPayrollItem;
use App\Models\HrReimbursement;
use App\Models\Hr\HrOfficeWifi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PortalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = $user?->employee;

        if (!$employee) {
            return view('pages.hr.portal.no_employee', compact('user'));
        }

        $employee->loadMissing(['department', 'position']);

        $today = Carbon::today()->toDateString();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Today's attendance
        $todayAttendance = HrAttendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        // Monthly attendance records
        $monthAttendances = HrAttendance::where('employee_id', $employee->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->orderByDesc('date')
            ->get();

        // Leave balances & requests
        $leaveBalance = HrLeaveBalance::firstOrCreate(
            ['employee_id' => $employee->id, 'year' => $currentYear],
            ['total_quota' => 12, 'used_quota' => 0, 'remaining_quota' => 12]
        );
        $myLeaves = HrLeaveRequest::with('leaveType')
            ->where('employee_id', $employee->id)
            ->orderByDesc('created_at')
            ->get();
        $leaveTypes = HrLeaveType::where('is_active', true)->get();

        // Payslips
        $myPayslips = HrPayrollItem::with('payroll')
            ->where('employee_id', $employee->id)
            ->orderByDesc('id')
            ->get();

        // Reimbursements
        $myReimbursements = HrReimbursement::where('employee_id', $employee->id)
            ->orderByDesc('created_at')
            ->get();

        // Assets
        $myAssets = HrEmployeeAsset::where('employee_id', $employee->id)
            ->where('status', 'Digunakan')
            ->get();

        // WiFi validation status
        $wifiSetting = DB::table('hr_attendance_settings')->where('key', 'is_wifi_restriction_enabled')->first();
        $isWifiRestrictionEnabled = $wifiSetting && $wifiSetting->value === '1';
        $activeWifis = HrOfficeWifi::where('is_active', true)->get();
        $allowedIps = $activeWifis->pluck('ip_address')->toArray();
        $clientIp = $request->ip();

        $isWifiVerified = !$isWifiRestrictionEnabled
            || in_array($clientIp, $allowedIps)
            || (app()->isLocal() && in_array($clientIp, ['127.0.0.1', '::1']));

        return view('pages.hr.portal.index', compact(
            'user',
            'employee',
            'todayAttendance',
            'monthAttendances',
            'leaveBalance',
            'myLeaves',
            'leaveTypes',
            'myPayslips',
            'myReimbursements',
            'myAssets',
            'isWifiRestrictionEnabled',
            'isWifiVerified',
            'clientIp',
            'activeWifis'
        ));
    }

    public function clockIn(Request $request)
    {
        $employee = Auth::user()?->employee;
        if (!$employee) {
            return redirect()->back()->with('error', 'Profil karyawan tidak ditemukan.');
        }

        // 1. Check if employee is permitted for online attendance
        if (!$employee->can_online_attendance) {
            return redirect()->back()->with('error', 'Akun Anda tidak memiliki hak akses untuk melakukan presensi online. Silakan hubungi HR.');
        }

        // 2. Check WiFi network restriction
        $wifiError = $this->validateWifiRestriction($request);
        if ($wifiError) {
            return redirect()->back()->with('error', $wifiError);
        }

        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');

        // Late calculation (e.g. after 08:30)
        $standardStart = Carbon::createFromTime(8, 30, 0);
        $lateMinutes = 0;
        if (Carbon::now()->gt($standardStart)) {
            $lateMinutes = Carbon::now()->diffInMinutes($standardStart);
        }

        HrAttendance::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $today],
            [
                'clock_in' => $nowTime,
                'work_type' => $request->input('work_type', 'WFO'),
                'status' => 'Hadir',
                'late_minutes' => $lateMinutes,
                'ip_address' => $request->ip(),
                'location_in' => $request->input('location', 'Kantor Reftech'),
            ]
        );

        return redirect()->back()->with('success', 'Presensi masuk (Clock In) berhasil dicatat pukul ' . substr($nowTime, 0, 5));
    }

    public function clockOut(Request $request)
    {
        $employee = Auth::user()?->employee;
        if (!$employee) {
            return redirect()->back()->with('error', 'Profil karyawan tidak ditemukan.');
        }

        // 1. Check if employee is permitted for online attendance
        if (!$employee->can_online_attendance) {
            return redirect()->back()->with('error', 'Akun Anda tidak memiliki hak akses untuk melakukan presensi online.');
        }

        // 2. Check WiFi network restriction
        $wifiError = $this->validateWifiRestriction($request);
        if ($wifiError) {
            return redirect()->back()->with('error', $wifiError);
        }

        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');

        $attendance = HrAttendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'Anda belum melakukan presensi masuk (Clock In) hari ini.');
        }

        // Overtime calculation (e.g. after 17:30)
        $standardEnd = Carbon::createFromTime(17, 30, 0);
        $overtimeMinutes = 0;
        if (Carbon::now()->gt($standardEnd)) {
            $overtimeMinutes = Carbon::now()->diffInMinutes($standardEnd);
        }

        $attendance->update([
            'clock_out' => $nowTime,
            'overtime_minutes' => $overtimeMinutes,
            'location_out' => $request->input('location', 'Kantor Reftech'),
        ]);

        return redirect()->back()->with('success', 'Presensi pulang (Clock Out) berhasil dicatat pukul ' . substr($nowTime, 0, 5));
    }

    /**
     * Validasi apakah request berasal dari IP WiFi kantor terdaftar.
     */
    protected function validateWifiRestriction(Request $request): ?string
    {
        $wifiSetting = DB::table('hr_attendance_settings')->where('key', 'is_wifi_restriction_enabled')->first();
        $isWifiRestrictionEnabled = $wifiSetting && $wifiSetting->value === '1';

        if (!$isWifiRestrictionEnabled) {
            return null; // Restriction is OFF
        }

        $clientIp = $request->ip();
        $allowedIps = HrOfficeWifi::where('is_active', true)->pluck('ip_address')->toArray();

        // If local development environment, allow localhost
        if (app()->isLocal() && in_array($clientIp, ['127.0.0.1', '::1'])) {
            return null;
        }

        // If whitelist is active and has registered IPs
        if (!empty($allowedIps) && !in_array($clientIp, $allowedIps)) {
            return "Presensi Ditolak! Anda tidak terhubung ke jaringan WiFi Kantor yang diizinkan (IP Anda terdeteksi: {$clientIp}). Silakan hubungkan perangkat Anda ke jaringan WiFi Kantor.";
        }

        return null;
    }
}

