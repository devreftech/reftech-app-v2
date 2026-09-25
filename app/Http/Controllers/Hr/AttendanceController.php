<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\HrAttendance;
use App\Models\Hr\HrOfficeWifi;
use App\Models\Hr\HrHoliday;
use App\Models\Hr\HrLeave;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $filterType = $request->input('filter_type', 'daily');
        $selectedDate = $request->input('date', Carbon::today('Asia/Jakarta')->toDateString());
        
        $selectedMonth = $request->input('month', Carbon::parse($selectedDate)->format('Y-m'));
        $monthParts = explode('-', $selectedMonth);
        $selectedYear = isset($monthParts[0]) ? (int) $monthParts[0] : (int) Carbon::now()->year;
        $selectedMonthNum = isset($monthParts[1]) ? (int) $monthParts[1] : (int) Carbon::now()->month;

        if (!$request->has('filter_type') && $request->has('month') && !$request->has('date')) {
            $filterType = 'monthly';
        }

        // Evaluasi Auto Clock-Out otomatis (Asia/Jakarta GMT+7)
        HrAttendance::processAutoClockOutIfDue($selectedDate);

        $departmentId = $request->input('department_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = HrAttendance::with(['employee.user', 'employee.department', 'employee.position']);

        if ($filterType === 'monthly') {
            $query->whereYear('date', $selectedYear)
                ->whereMonth('date', $selectedMonthNum)
                ->orderBy('date', 'desc')
                ->orderBy('clock_in', 'asc');

            // Stats summary for the selected month
            $allForPeriod = HrAttendance::whereYear('date', $selectedYear)
                ->whereMonth('date', $selectedMonthNum)
                ->get();
        } else {
            $query->whereDate('date', $selectedDate)
                ->orderBy('clock_in', 'asc');

            // Stats summary for the selected date
            $allForPeriod = HrAttendance::whereDate('date', $selectedDate)->get();
        }

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('id_department', $departmentId);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $attendances = $query->paginate(20)->withQueryString();

        // Stats summary for the selected period
        $stats = [
            'total_present' => $allForPeriod->where('status', 'Hadir')->count(),
            'total_late' => $allForPeriod->where('status', 'Hadir')->where('late_minutes', '>', 0)->count(),
            'total_leave' => $allForPeriod->whereIn('status', ['Cuti', 'Izin'])->count(),
            'total_sick' => $allForPeriod->where('status', 'Sakit')->count(),
            'total_alpha' => $allForPeriod->where('status', 'Alpa')->count(),
        ];

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $employees = Employee::with(['user', 'department', 'position'])
            ->where('employment_status', '!=', 'Resign')
            ->orderBy('id')
            ->get();

        // Settings Map
        $settings = DB::table('hr_attendance_settings')->pluck('value', 'key')->toArray();

        $officeWifis = HrOfficeWifi::orderByDesc('is_active')->orderBy('id')->get();
        $isWifiRestrictionEnabled = ($settings['is_wifi_restriction_enabled'] ?? '0') === '1';
        $isDeviceLockEnabled = ($settings['is_device_lock_enabled'] ?? '1') === '1';
        $isSelfieRequired = ($settings['is_selfie_required'] ?? '0') === '1';
        $isAutoClockOutEnabled = ($settings['is_auto_clock_out_enabled'] ?? '0') === '1';
        $autoClockOutTime = $settings['auto_clock_out_time'] ?? '17:00';
        $isClockOutGreetingEnabled = ($settings['is_clock_out_greeting_enabled'] ?? '1') === '1';
        $clockOutGreetingTitle = $settings['clock_out_greeting_title'] ?? 'Terima Kasih Atas Kerja Keras Hari Ini! 🎉';
        $clockOutGreetingMessage = $settings['clock_out_greeting_message'] ?? 'Jam kerja operasional kantor hari ini telah selesai. Selamat beristirahat, nikmati waktu berkualitas bersama keluarga, dan sampai jumpa besok!';

        // Late & Penalty Policy Settings
        $earliestClockInTime = $settings['earliest_clock_in_time'] ?? '07:00';
        $workStartTime = $settings['work_start_time'] ?? '08:00';
        $lateToleranceMinutes = (int) ($settings['late_tolerance_minutes'] ?? 0);
        $isLatePenaltyEnabled = ($settings['is_late_penalty_enabled'] ?? '1') === '1';
        $lateTier1Rate = (float) ($settings['late_tier_1_rate'] ?? 50000);
        $lateTier2Rate = (float) ($settings['late_tier_2_rate'] ?? 75000);
        $lateTier3Rate = (float) ($settings['late_tier_3_rate'] ?? 100000);
        $lateTierExcessPercent = (float) ($settings['late_tier_excess_percent'] ?? 10);
        $alphaPenaltyRate = (float) ($settings['alpha_penalty_rate'] ?? 50000);
        $isWeekendOffEnabled = ($settings['is_weekend_off_enabled'] ?? '1') === '1';

        $currentClientIp = $request->ip();

        // ── Monthly Late Penalty Recap (Rekap Akumulasi Denda Bulanan & Riwayat Bulan Lampau) ──
        $recapMonth = (int) $request->input('recap_month', Carbon::parse($selectedDate)->month);
        $recapYear = (int) $request->input('recap_year', Carbon::parse($selectedDate)->year);

        // Siapkan daftar 12 bulan terakhir untuk switcher riwayat bulan
        $availableRecapMonths = [];
        $currentMonthCursor = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $monthObj = $currentMonthCursor->copy()->subMonths($i);
            $availableRecapMonths[] = [
                'month' => $monthObj->month,
                'year' => $monthObj->year,
                'label' => $monthObj->translatedFormat('F Y'),
                'is_current' => ($monthObj->month === Carbon::now()->month && $monthObj->year === Carbon::now()->year),
            ];
        }

        $monthAllAttendances = HrAttendance::with(['employee.user', 'employee.department', 'employee.position', 'employee.salary'])
            ->whereMonth('date', $recapMonth)
            ->whereYear('date', $recapYear)
            ->get();

        $monthlyPenaltyRecap = [];
        $totalMonthlyPenaltyAccumulated = 0;
        $totalMonthlyLateMinutes = 0;
        $totalEmployeesLateCount = 0;

        $groupedByEmp = $monthAllAttendances->groupBy('employee_id');

        foreach ($employees as $emp) {
            $empAtts = $groupedByEmp->get($emp->id, collect());
            $presentDays = $empAtts->where('status', 'Hadir')->count();
            $lateRecords = $empAtts->where('status', 'Hadir')->where('late_minutes', '>', 0)->sortBy('date');
            $lateDaysCount = $lateRecords->count();
            $empLateMinutes = $lateRecords->sum('late_minutes');
            $empPenaltyTotal = $empAtts->sum('penalty_amount');
            $alphaDaysCount = $empAtts->where('status', 'Alpa')->count();

            // Hitung status sanksi / strike berdasarkan frekuensi keterlambatan bertingkat
            $strikeStatus = 'Disiplin';
            $strikeBadgeClass = 'bg-label-success';
            if ($lateDaysCount === 1) {
                $strikeStatus = 'Terlambat 1x (Rp ' . number_format($lateTier1Rate, 0, ',', '.') . ')';
                $strikeBadgeClass = 'bg-label-warning';
            } elseif ($lateDaysCount === 2) {
                $strikeStatus = 'Terlambat 2x (Rp ' . number_format($lateTier2Rate, 0, ',', '.') . ')';
                $strikeBadgeClass = 'bg-label-warning';
            } elseif ($lateDaysCount === 3) {
                $strikeStatus = 'Terlambat 3x (Rp ' . number_format($lateTier3Rate, 0, ',', '.') . ')';
                $strikeBadgeClass = 'bg-label-danger';
            } elseif ($lateDaysCount > 3) {
                $strikeStatus = "Sanksi SP-1 ({$lateDaysCount}x - Potong {$lateTierExcessPercent}%)";
                $strikeBadgeClass = 'bg-label-danger';
            }

            if ($lateDaysCount > 0) {
                $totalEmployeesLateCount++;
            }
            $totalMonthlyPenaltyAccumulated += $empPenaltyTotal;
            $totalMonthlyLateMinutes += $empLateMinutes;

            $monthlyPenaltyRecap[] = [
                'employee' => $emp,
                'present_days' => $presentDays,
                'late_days_count' => $lateDaysCount,
                'alpha_days_count' => $alphaDaysCount,
                'late_minutes' => $empLateMinutes,
                'penalty_total' => $empPenaltyTotal,
                'strike_status' => $strikeStatus,
                'strike_badge' => $strikeBadgeClass,
                'late_records' => $lateRecords,
            ];
        }

        // Sort recap by highest late minutes, late days, and penalty
        usort($monthlyPenaltyRecap, function ($a, $b) {
            if ($b['late_minutes'] != $a['late_minutes']) {
                return $b['late_minutes'] <=> $a['late_minutes'];
            }
            if ($b['late_days_count'] != $a['late_days_count']) {
                return $b['late_days_count'] <=> $a['late_days_count'];
            }
            return $b['penalty_total'] <=> $a['penalty_total'];
        });

        $topLateRankings = collect($monthlyPenaltyRecap)
            ->filter(fn($item) => ($item['late_days_count'] ?? 0) > 0 || ($item['late_minutes'] ?? 0) > 0)
            ->take(3)
            ->values();

        return view('pages.hr.attendances.index', compact(
            'attendances',
            'filterType',
            'selectedDate',
            'selectedMonth',
            'selectedYear',
            'selectedMonthNum',
            'departmentId',
            'status',
            'search',
            'stats',
            'departments',
            'employees',
            'officeWifis',
            'isWifiRestrictionEnabled',
            'isDeviceLockEnabled',
            'isSelfieRequired',
            'isAutoClockOutEnabled',
            'autoClockOutTime',
            'isClockOutGreetingEnabled',
            'clockOutGreetingTitle',
            'clockOutGreetingMessage',
            'earliestClockInTime',
            'workStartTime',
            'lateToleranceMinutes',
            'isLatePenaltyEnabled',
            'lateTier1Rate',
            'lateTier2Rate',
            'lateTier3Rate',
            'lateTierExcessPercent',
            'alphaPenaltyRate',
            'isWeekendOffEnabled',
            'currentClientIp',
            'recapMonth',
            'recapYear',
            'availableRecapMonths',
            'monthlyPenaltyRecap',
            'topLateRankings',
            'totalMonthlyPenaltyAccumulated',
            'totalMonthlyLateMinutes',
            'totalEmployeesLateCount'
        ));
    }

    /**
     * Halaman Dedicated Pengaturan Presensi, Anti-Fraud & Kebijakan Denda HRMS
     */
    public function settings(Request $request)
    {
        $settings = DB::table('hr_attendance_settings')->pluck('value', 'key')->toArray();

        $officeWifis = HrOfficeWifi::orderByDesc('is_active')->orderBy('id')->get();
        $isWifiRestrictionEnabled = ($settings['is_wifi_restriction_enabled'] ?? '0') === '1';
        $isDeviceLockEnabled = ($settings['is_device_lock_enabled'] ?? '1') === '1';
        $isSelfieRequired = ($settings['is_selfie_required'] ?? '0') === '1';
        $isAutoClockOutEnabled = ($settings['is_auto_clock_out_enabled'] ?? '0') === '1';
        $autoClockOutTime = $settings['auto_clock_out_time'] ?? '17:00';
        $isClockOutGreetingEnabled = ($settings['is_clock_out_greeting_enabled'] ?? '1') === '1';
        $clockOutGreetingTitle = $settings['clock_out_greeting_title'] ?? 'Terima Kasih Atas Kerja Keras Hari Ini! 🎉';
        $clockOutGreetingMessage = $settings['clock_out_greeting_message'] ?? 'Jam kerja operasional kantor hari ini telah selesai. Selamat beristirahat, nikmati waktu berkualitas bersama keluarga, dan sampai jumpa besok!';

        $earliestClockInTime = $settings['earliest_clock_in_time'] ?? '07:00';
        $workStartTime = $settings['work_start_time'] ?? '08:00';
        $lateToleranceMinutes = (int) ($settings['late_tolerance_minutes'] ?? 0);
        $isLatePenaltyEnabled = ($settings['is_late_penalty_enabled'] ?? '1') === '1';
        $lateTier1Rate = (float) ($settings['late_tier_1_rate'] ?? 50000);
        $lateTier2Rate = (float) ($settings['late_tier_2_rate'] ?? 75000);
        $lateTier3Rate = (float) ($settings['late_tier_3_rate'] ?? 100000);
        $lateTierExcessPercent = (float) ($settings['late_tier_excess_percent'] ?? 10);
        $alphaPenaltyRate = (float) ($settings['alpha_penalty_rate'] ?? 50000);
        $isWeekendOffEnabled = ($settings['is_weekend_off_enabled'] ?? '1') === '1';
        $isHolidayPenaltyFree = ($settings['is_holiday_penalty_free'] ?? '1') === '1';

        // Konfigurasi Payroll Cutoff & Simulasi
        $payrollPayDate = (int) ($settings['payroll_pay_date'] ?? 28);
        $payrollCutoffReleaseTime = $settings['payroll_cutoff_release_time'] ?? '09:00';
        $payrollAutoStepbackWeekend = ($settings['payroll_auto_stepback_weekend'] ?? '1') === '1';
        $payrollAutoStepbackHoliday = ($settings['payroll_auto_stepback_holiday'] ?? '1') === '1';

        // Simulasi Cutoff Bulan Berjalan & Bulan Depan
        $currentCutoffPeriod = \App\Services\Hr\PayrollCutoffService::getPayrollPeriod();
        $nextMonthDate = Carbon::now()->addMonth();
        $nextCutoffPeriod = \App\Services\Hr\PayrollCutoffService::getPayrollPeriod($nextMonthDate->month, $nextMonthDate->year);

        $holidayYear = (int) $request->input('holiday_year', Carbon::now()->year);
        $holidays = HrHoliday::whereYear('holiday_date', $holidayYear)->orderBy('holiday_date', 'asc')->get();
        $availableHolidayYears = HrHoliday::selectRaw('YEAR(holiday_date) as year')->distinct()->pluck('year')->filter()->toArray();
        if (!in_array(Carbon::now()->year, $availableHolidayYears)) {
            $availableHolidayYears[] = Carbon::now()->year;
        }
        sort($availableHolidayYears);

        $currentClientIp = $request->ip();

        return view('pages.hr.attendances.settings', compact(
            'officeWifis',
            'isWifiRestrictionEnabled',
            'isDeviceLockEnabled',
            'isSelfieRequired',
            'isAutoClockOutEnabled',
            'autoClockOutTime',
            'isClockOutGreetingEnabled',
            'clockOutGreetingTitle',
            'clockOutGreetingMessage',
            'earliestClockInTime',
            'workStartTime',
            'lateToleranceMinutes',
            'isLatePenaltyEnabled',
            'lateTier1Rate',
            'lateTier2Rate',
            'lateTier3Rate',
            'lateTierExcessPercent',
            'alphaPenaltyRate',
            'isWeekendOffEnabled',
            'isHolidayPenaltyFree',
            'payrollPayDate',
            'payrollCutoffReleaseTime',
            'payrollAutoStepbackWeekend',
            'payrollAutoStepbackHoliday',
            'currentCutoffPeriod',
            'nextCutoffPeriod',
            'holidays',
            'holidayYear',
            'availableHolidayYears',
            'currentClientIp'
        ));
    }

    /**
     * Halaman Khusus Rekap & Laporan Akumulasi Denda Keterlambatan Bulanan
     */
    public function penalties(Request $request)
    {
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        $departmentId = $request->input('department_id');
        $strikeFilter = $request->input('strike_status');
        $search = $request->input('search');

        // Settings Map
        $settings = DB::table('hr_attendance_settings')->pluck('value', 'key')->toArray();
        $workStartTime = $settings['work_start_time'] ?? '08:00';
        $lateToleranceMinutes = (int) ($settings['late_tolerance_minutes'] ?? 0);
        $isLatePenaltyEnabled = ($settings['is_late_penalty_enabled'] ?? '1') === '1';
        $lateTier1Rate = (float) ($settings['late_tier_1_rate'] ?? 50000);
        $lateTier2Rate = (float) ($settings['late_tier_2_rate'] ?? 75000);
        $lateTier3Rate = (float) ($settings['late_tier_3_rate'] ?? 100000);
        $lateTierExcessPercent = (float) ($settings['late_tier_excess_percent'] ?? 10);
        $alphaPenaltyRate = (float) ($settings['alpha_penalty_rate'] ?? 50000);
        $isWeekendOffEnabled = ($settings['is_weekend_off_enabled'] ?? '1') === '1';

        // Daftar 12 bulan terakhir
        $availableMonths = [];
        $currentMonthCursor = Carbon::now()->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $monthObj = $currentMonthCursor->copy()->subMonths($i);
            $availableMonths[] = [
                'month' => $monthObj->month,
                'year' => $monthObj->year,
                'label' => $monthObj->translatedFormat('F Y'),
                'is_current' => ($monthObj->month === Carbon::now()->month && $monthObj->year === Carbon::now()->year),
            ];
        }

        // Query Employee
        $empQuery = Employee::with(['user', 'department', 'position', 'salary'])
            ->where('employment_status', '!=', 'Resign');

        if ($departmentId) {
            $empQuery->where('id_department', $departmentId);
        }

        if ($search) {
            $empQuery->where(function ($q) use ($search) {
                $q->where('nik', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $employees = $empQuery->orderBy('id')->get();

        // Ambil periode cutoff presensi dinamis (target tgl 28 dengan auto stepback)
        $cutoffPeriod = \App\Services\Hr\PayrollCutoffService::getPayrollPeriod($month, $year);
        $startDateStr = $cutoffPeriod['start_date']->toDateString();
        $endDateStr = $cutoffPeriod['end_date']->toDateString();

        // Get attendances for this cutoff period
        $allAttendances = HrAttendance::with(['employee.user', 'employee.department', 'employee.salary'])
            ->whereBetween('date', [$startDateStr, $endDateStr])
            ->get();

        // Cek apakah batch payroll untuk periode ini sudah pernah digenerate
        $existingPayroll = \App\Models\HrPayroll::where('period_month', $month)
            ->where('period_year', $year)
            ->first();

        $groupedByEmp = $allAttendances->groupBy('employee_id');

        $penaltyRecap = [];
        $totalPenaltyAccumulated = 0;
        $totalLateMinutes = 0;
        $totalEmployeesLate = 0;
        $totalEmployeesWarning = 0;

        foreach ($employees as $emp) {
            $empAtts = $groupedByEmp->get($emp->id, collect());
            $presentDays = $empAtts->where('status', 'Hadir')->count();
            $lateRecords = $empAtts->where('status', 'Hadir')->where('late_minutes', '>', 0)->sortBy('date');
            $lateDaysCount = $lateRecords->count();
            $empLateMinutes = $lateRecords->sum('late_minutes');
            $empPenaltyTotal = $empAtts->sum('penalty_amount');
            $alphaDaysCount = $empAtts->where('status', 'Alpa')->count();

            // Hitung status sanksi / strike
            $strikeStatus = 'Disiplin';
            $strikeBadgeClass = 'bg-label-success';
            $strikeKey = 'disciplined';

            if ($lateDaysCount === 1) {
                $strikeStatus = 'Terlambat 1x (Rp ' . number_format($lateTier1Rate, 0, ',', '.') . ')';
                $strikeBadgeClass = 'bg-label-warning';
                $strikeKey = 'penalized';
            } elseif ($lateDaysCount === 2) {
                $strikeStatus = 'Terlambat 2x (Rp ' . number_format($lateTier2Rate, 0, ',', '.') . ')';
                $strikeBadgeClass = 'bg-label-warning';
                $strikeKey = 'penalized';
            } elseif ($lateDaysCount >= 3) {
                $strikeStatus = "Terlambat {$lateDaysCount}x (Rp " . number_format($lateTier3Rate, 0, ',', '.') . ')';
                $strikeBadgeClass = 'bg-label-danger';
                $strikeKey = 'penalized';
            }

            // Filter status sanksi
            if ($strikeFilter && $strikeFilter !== 'all' && $strikeKey !== $strikeFilter) {
                continue;
            }

            if ($lateDaysCount > 0) {
                $totalEmployeesLate++;
            }
            if ($lateDaysCount >= 3) {
                $totalEmployeesWarning++;
            }

            $totalPenaltyAccumulated += $empPenaltyTotal;
            $totalLateMinutes += $empLateMinutes;

            $penaltyRecap[] = [
                'employee' => $emp,
                'present_days' => $presentDays,
                'late_days_count' => $lateDaysCount,
                'alpha_days_count' => $alphaDaysCount,
                'late_minutes' => $empLateMinutes,
                'penalty_total' => $empPenaltyTotal,
                'strike_status' => $strikeStatus,
                'strike_badge' => $strikeBadgeClass,
                'strike_key' => $strikeKey,
                'late_records' => $lateRecords,
            ];
        }

        // Urutkan: Denda terbesar dan keterlambatan tertinggi di atas
        usort($penaltyRecap, function ($a, $b) {
            if ($b['penalty_total'] != $a['penalty_total']) {
                return $b['penalty_total'] <=> $a['penalty_total'];
            }
            return $b['late_days_count'] <=> $a['late_days_count'];
        });

        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('pages.hr.attendances.penalties', compact(
            'month',
            'year',
            'departmentId',
            'strikeFilter',
            'search',
            'departments',
            'availableMonths',
            'penaltyRecap',
            'totalPenaltyAccumulated',
            'totalLateMinutes',
            'totalEmployeesLate',
            'totalEmployeesWarning',
            'workStartTime',
            'lateToleranceMinutes',
            'isLatePenaltyEnabled',
            'lateTier1Rate',
            'lateTier2Rate',
            'lateTier3Rate',
            'lateTierExcessPercent',
            'alphaPenaltyRate',
            'isWeekendOffEnabled',
            'cutoffPeriod',
            'existingPayroll'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'work_type' => 'required|in:WFO,WFH,Site/Lapangan',
            'status' => 'required|in:Hadir,Izin,Sakit,Cuti,Alpa',
            'late_minutes' => 'nullable|integer|min:0',
            'penalty_amount' => 'nullable|numeric|min:0',
            'overtime_minutes' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $settings = DB::table('hr_attendance_settings')->pluck('value', 'key')->toArray();

        // Jika Alpa dan belum diset nominal penalti, isi default denda alpa
        if ($validated['status'] === 'Alpa' && !isset($validated['penalty_amount'])) {
            $validated['penalty_amount'] = (float) ($settings['alpha_penalty_rate'] ?? 50000);
        }

        $lateMinutes = (int) ($validated['late_minutes'] ?? 0);
        if ($lateMinutes > 0 && !isset($validated['penalty_amount'])) {
            $penaltyInfo = HrAttendance::calculatePenaltyInfo($validated['employee_id'], $validated['date'], $lateMinutes);
            $validated['penalty_amount'] = $penaltyInfo['penalty'];
        }

        HrAttendance::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
            $validated
        );

        return redirect()->back()->with('success', 'Data presensi berhasil disimpan.');
    }

    public function destroy(HrAttendance $attendance)
    {
        $attendance->delete();
        return redirect()->back()->with('success', 'Data presensi berhasil dihapus.');
    }

    /**
     * Simpan jaringan WiFi kantor baru.
     */
    public function storeWifi(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'ip_address' => 'required|string|max:45',
            'notes' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['ip_address'] = trim($validated['ip_address']);

        HrOfficeWifi::create($validated);

        return redirect()->back()->with('success', 'Jaringan WiFi Kantor berhasil didaftarkan: ' . $validated['name'] . ' (' . $validated['ip_address'] . ')');
    }

    /**
     * Perbarui jaringan WiFi kantor.
     */
    public function updateWifi(Request $request, HrOfficeWifi $wifi)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'ip_address' => 'required|string|max:45',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['ip_address'] = trim($validated['ip_address']);

        $wifi->update($validated);

        return redirect()->back()->with('success', 'Pengaturan WiFi Kantor berhasil diperbarui.');
    }

    /**
     * Hapus jaringan WiFi kantor.
     */
    public function destroyWifi(HrOfficeWifi $wifi)
    {
        $name = $wifi->name;
        $wifi->delete();

        return redirect()->back()->with('success', 'Jaringan WiFi Kantor "' . $name . '" berhasil dihapus.');
    }

    /**
     * Toggle saklar master pembatasan presensi hanya dari WiFi kantor.
     */
    public function toggleWifiRestriction(Request $request)
    {
        $enabled = $request->boolean('is_wifi_restriction_enabled') ? '1' : '0';

        DB::table('hr_attendance_settings')->updateOrInsert(
            ['key' => 'is_wifi_restriction_enabled'],
            [
                'value' => $enabled,
                'description' => 'Batasi absensi online hanya dari jaringan WiFi kantor yang terdaftar',
                'updated_at' => now(),
            ]
        );

        $msg = $enabled === '1'
            ? 'Pembatasan WiFi DIAKTIFKAN: Karyawan hanya bisa clock-in/out melalui WiFi kantor terdaftar.'
            : 'Pembatasan WiFi DINONAKTIFKAN: Karyawan dapat clock-in/out dari jaringan manapun.';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Simpan pengaturan Anti-Fraud, Keamanan Presensi, Auto Clock-Out, & Kebijakan Denda Keterlambatan.
     */
    public function updateSecuritySettings(Request $request)
    {
        $isWifi = $request->boolean('is_wifi_restriction_enabled') ? '1' : '0';
        $isDeviceLock = $request->boolean('is_device_lock_enabled') ? '1' : '0';
        $isSelfie = $request->boolean('is_selfie_required') ? '1' : '0';
        $isAutoClockOut = $request->boolean('is_auto_clock_out_enabled') ? '1' : '0';
        $autoClockOutTime = $request->input('auto_clock_out_time', '17:00');
        $isClockOutGreeting = $request->boolean('is_clock_out_greeting_enabled') ? '1' : '0';
        $clockOutGreetingTitle = $request->input('clock_out_greeting_title', 'Terima Kasih Atas Kerja Keras Hari Ini! 🎉');
        $clockOutGreetingMessage = $request->input('clock_out_greeting_message', 'Jam kerja operasional kantor hari ini telah selesai. Selamat beristirahat, nikmati waktu berkualitas bersama keluarga, dan sampai jumpa besok!');

        $earliestClockInTime = $request->input('earliest_clock_in_time', '07:00');
        $workStartTime = $request->input('work_start_time', '08:00');
        $lateToleranceMinutes = $request->input('late_tolerance_minutes', 0);
        $isLatePenalty = $request->boolean('is_late_penalty_enabled') ? '1' : '0';
        $lateTier1Rate = $request->input('late_tier_1_rate', 50000);
        $lateTier2Rate = $request->input('late_tier_2_rate', 75000);
        $lateTier3Rate = $request->input('late_tier_3_rate', 100000);
        $lateTierExcessPercent = $request->input('late_tier_excess_percent', 10);
        $alphaPenaltyRate = $request->input('alpha_penalty_rate', 50000);
        $isWeekendOff = $request->boolean('is_weekend_off_enabled') ? '1' : '0';
        $isHolidayPenaltyFree = $request->boolean('is_holiday_penalty_free') ? '1' : '0';

        // Payroll Cutoff Settings
        $payrollPayDate = (int) $request->input('payroll_pay_date', 28);
        $payrollCutoffReleaseTime = $request->input('payroll_cutoff_release_time', '09:00');
        $payrollAutoStepbackWeekend = $request->boolean('payroll_auto_stepback_weekend') ? '1' : '0';
        $payrollAutoStepbackHoliday = $request->boolean('payroll_auto_stepback_holiday') ? '1' : '0';

        $settings = [
            'is_wifi_restriction_enabled' => [$isWifi, 'Batasi absensi online hanya dari jaringan WiFi kantor yang terdaftar'],
            'is_device_lock_enabled' => [$isDeviceLock, 'Kunci 1 Perangkat per Karyawan (Mencegah 1 HP/Laptop dipakai banyak akun)'],
            'is_selfie_required' => [$isSelfie, 'Wajibkan Foto Selfie Kamera Live saat Presensi Masuk (Clock In)'],
            'is_auto_clock_out_enabled' => [$isAutoClockOut, 'Auto Clock-Out Otomatis pada jam pulang yang ditentukan'],
            'auto_clock_out_time' => [$autoClockOutTime, 'Jam default auto Clock-Out (contoh: 17:00)'],
            'is_clock_out_greeting_enabled' => [$isClockOutGreeting, 'Aktifkan Modal Pop-up Ucapan Jam Pulang Otomatis'],
            'clock_out_greeting_title' => [$clockOutGreetingTitle, 'Judul Pesan Modal Ucapan Jam Pulang'],
            'clock_out_greeting_message' => [$clockOutGreetingMessage, 'Isi Pesan Modal Ucapan Jam Pulang'],
            'earliest_clock_in_time' => [$earliestClockInTime, 'Jam Minimal Pembukaan Presensi Masuk (07:00 WIB)'],
            'work_start_time' => [$workStartTime, 'Jam Masuk Standar Kantor (08:00 WIB)'],
            'late_tolerance_minutes' => [$lateToleranceMinutes, 'Toleransi Keterlambatan Harian dalam Menit (0 = Tanpa Toleransi)'],
            'is_late_penalty_enabled' => [$isLatePenalty, 'Aktifkan Kebijakan Denda & Sanksi Keterlambatan'],
            'late_tier_1_rate' => [$lateTier1Rate, 'Nominal Denda Terlambat ke-1 dalam Bulan Berjalan (Rp)'],
            'late_tier_2_rate' => [$lateTier2Rate, 'Nominal Denda Terlambat ke-2 dalam Bulan Berjalan (Rp)'],
            'late_tier_3_rate' => [$lateTier3Rate, 'Nominal Denda Terlambat ke-3 dalam Bulan Berjalan (Rp)'],
            'late_tier_excess_percent' => [$lateTierExcessPercent, 'Persentase Pemotongan Gaji Pokok untuk Keterlambatan > 3 Kali (%)'],
            'alpha_penalty_rate' => [$alphaPenaltyRate, 'Nominal Denda/Potongan Alpa Tanpa Kabar Seharian Penuh (Rp)'],
            'is_weekend_off_enabled' => [$isWeekendOff, 'Sabtu & Minggu adalah Hari Libur Bebas Absensi & Bebas Denda Alpa'],
            'is_holiday_penalty_free' => [$isHolidayPenaltyFree, 'Karyawan Bebas Presensi & Bebas Denda Keterlambatan/Alpa pada Tanggal Kalender Merah'],
            'payroll_pay_date' => [$payrollPayDate, 'Tanggal Standar Pembayaran Gaji Karyawan Reftech (Default: 28)'],
            'payroll_cutoff_release_time' => [$payrollCutoffReleaseTime, 'Jam Rilis Rekapitulasi Denda & Absensi ke Payroll (Default: 09:00 WIB)'],
            'payroll_auto_stepback_weekend' => [$payrollAutoStepbackWeekend, 'Mundurkan Tanggal Cut-off Otomatis jika Jatuh di Hari Sabtu/Minggu/Senin'],
            'payroll_auto_stepback_holiday' => [$payrollAutoStepbackHoliday, 'Mundurkan Tanggal Cut-off Otomatis jika Bertepatan dengan Hari Libur Nasional'],
        ];

        foreach ($settings as $key => $data) {
            DB::table('hr_attendance_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $data[0], 'description' => $data[1], 'updated_at' => now()]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan Jam Kerja, Kebijakan Denda Bertingkat, Siklus Cut-off Payroll, Hari Libur & Kalender Merah, serta Anti-Fraud berhasil diperbarui.');
    }

    /**
     * Tambah Hari Libur / Kalender Merah Baru
     */
    public function storeHoliday(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date|unique:hr_holidays,holiday_date',
            'type' => 'required|in:National,Joint_Leave,Company',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        HrHoliday::create($validated);

        return redirect()->back()->with('success', 'Hari libur / kalender merah "' . $validated['name'] . '" (' . Carbon::parse($validated['holiday_date'])->isoFormat('D MMMM Y') . ') berhasil ditambahkan.');
    }

    /**
     * Update Hari Libur / Kalender Merah
     */
    public function updateHoliday(Request $request, HrHoliday $holiday)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|date|unique:hr_holidays,holiday_date,' . $holiday->id,
            'type' => 'required|in:National,Joint_Leave,Company',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $holiday->update($validated);

        return redirect()->back()->with('success', 'Data hari libur "' . $holiday->name . '" berhasil diperbarui.');
    }

    /**
     * Hapus Hari Libur / Kalender Merah
     */
    public function destroyHoliday(HrHoliday $holiday)
    {
        $name = $holiday->name;
        $date = $holiday->holiday_date->isoFormat('D MMMM Y');
        $holiday->delete();

        return redirect()->back()->with('success', 'Hari libur "' . $name . '" (' . $date . ') berhasil dihapus dari kalender.');
    }

    /**
     * Impor Preset Hari Libur Nasional & Cuti Bersama Indonesia (Tahun Berjalan / 2026)
     */
    public function importDefaultHolidays(Request $request)
    {
        $year = (int) $request->input('year', Carbon::now()->year);

        $defaults = [
            ['name' => 'Tahun Baru 2026 Masehi', 'holiday_date' => "{$year}-01-01", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Isra Mikraj Nabi Muhammad SAW', 'holiday_date' => "{$year}-01-16", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Tahun Baru Imlek 2577 Kongzili', 'holiday_date' => "{$year}-02-17", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Cuti Bersama Tahun Baru Imlek', 'holiday_date' => "{$year}-02-18", 'type' => 'Joint_Leave', 'description' => 'Cuti Bersama Nasional'],
            ['name' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)', 'holiday_date' => "{$year}-03-20", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Hari Raya Idul Fitri 1447 H (Hari 1)', 'holiday_date' => "{$year}-03-21", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Hari Raya Idul Fitri 1447 H (Hari 2)', 'holiday_date' => "{$year}-03-22", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Cuti Bersama Hari Raya Idul Fitri', 'holiday_date' => "{$year}-03-23", 'type' => 'Joint_Leave', 'description' => 'Cuti Bersama Nasional'],
            ['name' => 'Cuti Bersama Hari Raya Idul Fitri', 'holiday_date' => "{$year}-03-24", 'type' => 'Joint_Leave', 'description' => 'Cuti Bersama Nasional'],
            ['name' => 'Wafat Yesus Kristus', 'holiday_date' => "{$year}-04-03", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Hari Buruh Internasional', 'holiday_date' => "{$year}-05-01", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Kenaikan Yesus Kristus', 'holiday_date' => "{$year}-05-14", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Hari Raya Waisak 2570 BE', 'holiday_date' => "{$year}-05-31", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Hari Lahir Pancasila', 'holiday_date' => "{$year}-06-01", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Hari Raya Idul Adha 1447 H', 'holiday_date' => "{$year}-05-27", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Tahun Baru Islam 1448 H', 'holiday_date' => "{$year}-06-16", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Hari Kemerdekaan Republik Indonesia ke-81', 'holiday_date' => "{$year}-08-17", 'type' => 'National', 'description' => 'Hari Libur Nasional Kemerdekaan RI'],
            ['name' => 'Maulid Nabi Muhammad SAW', 'holiday_date' => "{$year}-08-25", 'type' => 'National', 'description' => 'Hari Libur Nasional'],
            ['name' => 'Hari Raya Natal', 'holiday_date' => "{$year}-12-25", 'type' => 'National', 'description' => 'Hari Libur Nasional Hari Raya Natal'],
            ['name' => 'Cuti Bersama Hari Raya Natal', 'holiday_date' => "{$year}-12-26", 'type' => 'Joint_Leave', 'description' => 'Cuti Bersama Hari Raya Natal'],
        ];

        $inserted = 0;
        foreach ($defaults as $holiday) {
            $exists = HrHoliday::whereDate('holiday_date', $holiday['holiday_date'])->exists();
            if (!$exists) {
                HrHoliday::create([
                    'name' => $holiday['name'],
                    'holiday_date' => $holiday['holiday_date'],
                    'type' => $holiday['type'],
                    'description' => $holiday['description'],
                    'is_active' => true,
                ]);
                $inserted++;
            }
        }

        return redirect()->back()->with('success', "Preset kalender merah tahun {$year} berhasil disinkronkan ({$inserted} hari libur baru ditambahkan).");
    }

    /**
     * Eksekusi langsung Auto Clock-Out 1-Klik oleh HR untuk tanggal tertentu.
     */
    public function runAutoClockOutNow(Request $request)
    {
        $targetDate = $request->input('date', Carbon::today()->toDateString());
        
        \Illuminate\Support\Facades\Artisan::call('hr:auto-clock-out', [
            '--date' => $targetDate,
            '--force' => true,
        ]);

        $output = trim(\Illuminate\Support\Facades\Artisan::output());

        return redirect()->back()->with('success', $output ?: "Proses Auto Clock-Out untuk tanggal {$targetDate} selesai dijalankan.");
    }

    /**
     * Halaman Manajemen Pengajuan Izin, Sakit, Cuti, dan Dinas Luar untuk HR
     */
    public function leaves(Request $request)
    {
        $status = $request->input('status');
        $type = $request->input('type');
        $month = $request->input('month', Carbon::now()->format('Y-m'));
        $departmentId = $request->input('department_id');

        $query = HrLeave::with(['employee.user', 'employee.department', 'approver'])
            ->orderByDesc('id');

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($month) {
            $parts = explode('-', $month);
            $yearNum = (int) ($parts[0] ?? Carbon::now()->year);
            $monthNum = (int) ($parts[1] ?? Carbon::now()->month);
            $query->where(function ($q) use ($yearNum, $monthNum) {
                $q->whereYear('start_date', $yearNum)->whereMonth('start_date', $monthNum)
                  ->orWhere(function ($q2) use ($yearNum, $monthNum) {
                      $q2->whereYear('end_date', $yearNum)->whereMonth('end_date', $monthNum);
                  });
            });
        }

        if ($departmentId) {
            $query->whereHas('employee', function ($q) use ($departmentId) {
                $q->where('id_department', $departmentId);
            });
        }

        $leaves = $query->paginate(15)->appends($request->query());
        $departments = Department::orderBy('name')->get();

        $stats = [
            'pending' => HrLeave::where('status', 'Pending')->count(),
            'approved' => HrLeave::where('status', 'Approved')->whereMonth('start_date', Carbon::now()->month)->whereYear('start_date', Carbon::now()->year)->count(),
            'rejected' => HrLeave::where('status', 'Rejected')->whereMonth('start_date', Carbon::now()->month)->whereYear('start_date', Carbon::now()->year)->count(),
            'sick' => HrLeave::where('type', 'Sakit')->where('status', 'Approved')->whereMonth('start_date', Carbon::now()->month)->whereYear('start_date', Carbon::now()->year)->count(),
        ];

        return view('pages.hr.attendances.leaves', compact('leaves', 'departments', 'stats', 'status', 'type', 'month', 'departmentId'));
    }

    /**
     * Persetujuan (Approve / Reject) Pengajuan Izin / Sakit / Cuti oleh HR
     */
    public function updateLeaveStatus(Request $request, HrLeave $leave)
    {
        $validated = $request->validate([
            'status' => 'required|in:Approved,Rejected',
            'rejection_note' => 'nullable|string|max:500',
        ]);

        $leave->status = $validated['status'];
        $leave->approved_by = auth()->id();
        $leave->approved_at = now();
        $leave->rejection_note = $validated['rejection_note'] ?? null;
        $leave->save();

        // Jika disetujui (Approved), otomatis sinkronkan ke tabel Presensi (HrAttendance)
        if ($leave->status === 'Approved' && in_array($leave->type, ['Sakit', 'Izin', 'Cuti', 'Dinas Luar', 'WFH'])) {
            $cur = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);

            while ($cur->lte($end)) {
                $curDateStr = $cur->toDateString();
                // Hanya isi untuk hari kerja non-weekend
                if (!$cur->isWeekend() && !HrHoliday::isHoliday($curDateStr)) {
                    $attendanceStatus = in_array($leave->type, ['Sakit', 'Izin', 'Cuti']) ? $leave->type : 'Hadir';
                    $workType = in_array($leave->type, ['Dinas Luar', 'WFH']) ? $leave->type : 'WFO';

                    HrAttendance::updateOrCreate(
                        ['employee_id' => $leave->employee_id, 'date' => $curDateStr],
                        [
                            'status' => $attendanceStatus,
                            'work_type' => $workType,
                            'late_minutes' => 0,
                            'penalty_amount' => 0,
                            'location_in' => $leave->type . ': ' . ($leave->reason ?: 'Disetujui HR'),
                        ]
                    );
                }
                $cur->addDay();
            }
        }

        $msg = $leave->status === 'Approved'
            ? 'Pengajuan ' . $leave->type . ' berhasil DISETUJUI. Rekap kehadiran karyawan telah diperbarui secara otomatis.'
            : 'Pengajuan ' . $leave->type . ' telah DITOLAK.';

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Ekspor Rekapitulasi Denda Keterlambatan Bulanan ke Format CSV / Spreadsheet
     */
    public function exportPenalties(Request $request)
    {
        $month = (int) $request->input('month', Carbon::now()->month);
        $year = (int) $request->input('year', Carbon::now()->year);
        $departmentId = $request->input('department_id');

        $settings = DB::table('hr_attendance_settings')->pluck('value', 'key')->toArray();
        $tolerance = (int) ($settings['late_tolerance_minutes'] ?? 0);
        $alphaPenaltyRate = (float) ($settings['alpha_penalty_rate'] ?? 50000);

        $cutoffPeriod = \App\Services\Hr\PayrollCutoffService::getPayrollPeriod($month, $year);
        $startDateStr = $cutoffPeriod['start_date']->toDateString();
        $endDateStr = $cutoffPeriod['end_date']->toDateString();

        $employeeQuery = Employee::with(['user', 'department', 'position'])
            ->where('employment_status', '!=', 'Resign');

        if ($departmentId) {
            $employeeQuery->where('id_department', $departmentId);
        }

        $employees = $employeeQuery->orderBy('id_department')->get();
        $monthCarbon = Carbon::createFromDate($year, $month, 1);
        $monthName = $monthCarbon->isoFormat('MMMM Y');
        $cutoffInfo = $cutoffPeriod['start_date']->translatedFormat('d M Y') . ' s/d ' . $cutoffPeriod['end_date']->translatedFormat('d M Y');

        $filename = "Rekap_Denda_Presensi_{$year}_{$month}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($employees, $startDateStr, $endDateStr, $tolerance, $alphaPenaltyRate, $monthName, $cutoffInfo) {
            $file = fopen('php://output', 'w');
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Title Rows
            fputcsv($file, ['REKAPITULASI DENDA & SANKSI KETERLAMBATAN HRMS']);
            fputcsv($file, ['Bulan Periode', $monthName]);
            fputcsv($file, ['Siklus Cut-Off Presensi', $cutoffInfo]);
            fputcsv($file, ['Tanggal Unduh', now()->isoFormat('D MMMM Y, HH:mm') . ' WIB']);
            fputcsv($file, []);

            // Header Column
            fputcsv($file, [
                'No',
                'NIK',
                'Nama Karyawan',
                'Departemen',
                'Jabatan',
                'Total Hadir',
                'Terlambat (Kali)',
                'Status Sanksi / Strike',
                'Akumulasi Denda Terlambat (Rp)',
                'Jumlah Alpa',
                'Total Denda Alpa (Rp)',
                'Total Potongan Denda (Rp)'
            ]);

            $no = 1;
            $grandTotalLateDenda = 0;
            $grandTotalAlphaDenda = 0;
            $grandTotalPotongan = 0;

            foreach ($employees as $emp) {
                $attendances = HrAttendance::where('employee_id', $emp->id)
                    ->whereBetween('date', [$startDateStr, $endDateStr])
                    ->get();

                $hadirCount = $attendances->where('status', 'Hadir')->count();
                $lateRecords = $attendances->where('status', 'Hadir')->where('late_minutes', '>', $tolerance);
                $lateCount = $lateRecords->count();
                $totalLatePenalty = $lateRecords->sum('penalty_amount');
                $alphaCount = $attendances->where('status', 'Alpa')->count();
                $totalAlphaPenalty = $alphaCount * $alphaPenaltyRate;
                $totalPotongan = $totalLatePenalty + $totalAlphaPenalty;

                $strikeStatus = 'Disiplin';
                if ($lateCount === 1) $strikeStatus = 'Terlambat ke-1';
                elseif ($lateCount === 2) $strikeStatus = 'Terlambat ke-2';
                elseif ($lateCount >= 3) $strikeStatus = 'Terlambat ke-' . $lateCount;

                fputcsv($file, [
                    $no++,
                    $emp->nik ?: '-',
                    $emp->user?->name ?: $emp->nama_lengkap,
                    $emp->department?->name ?: '-',
                    $emp->position?->name ?: '-',
                    $hadirCount,
                    $lateCount,
                    $strikeStatus,
                    $totalLatePenalty,
                    $alphaCount,
                    $totalAlphaPenalty,
                    $totalPotongan
                ]);

                $grandTotalLateDenda += $totalLatePenalty;
                $grandTotalAlphaDenda += $totalAlphaPenalty;
                $grandTotalPotongan += $totalPotongan;
            }

            fputcsv($file, []);
            fputcsv($file, ['', '', '', '', 'TOTAL KESELURUHAN', '', '', '', $grandTotalLateDenda, '', $grandTotalAlphaDenda, $grandTotalPotongan]);

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}

