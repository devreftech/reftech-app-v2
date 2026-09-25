<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\HrAttendance;
use App\Models\Hr\HrOfficeWifi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Late & Penalty Policy Settings
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

        // Sort recap by highest penalty and late days first
        usort($monthlyPenaltyRecap, function ($a, $b) {
            if ($b['penalty_total'] != $a['penalty_total']) {
                return $b['penalty_total'] <=> $a['penalty_total'];
            }
            return $b['late_days_count'] <=> $a['late_days_count'];
        });

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
            'totalMonthlyPenaltyAccumulated',
            'totalMonthlyLateMinutes',
            'totalEmployeesLateCount'
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

        // Get attendances for this month & year
        $allAttendances = HrAttendance::with(['employee.user', 'employee.department', 'employee.salary'])
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

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
            } elseif ($lateDaysCount === 3) {
                $strikeStatus = 'Terlambat 3x (Rp ' . number_format($lateTier3Rate, 0, ',', '.') . ')';
                $strikeBadgeClass = 'bg-label-danger';
                $strikeKey = 'penalized';
            } elseif ($lateDaysCount > 3) {
                $strikeStatus = "Sanksi SP-1 ({$lateDaysCount}x - Potong {$lateTierExcessPercent}%)";
                $strikeBadgeClass = 'bg-label-danger';
                $strikeKey = 'warning_sp';
            }

            // Filter status sanksi
            if ($strikeFilter && $strikeFilter !== 'all' && $strikeKey !== $strikeFilter) {
                continue;
            }

            if ($lateDaysCount > 0) {
                $totalEmployeesLate++;
            }
            if ($lateDaysCount > 3) {
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
            'isWeekendOffEnabled'
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

        $workStartTime = $request->input('work_start_time', '08:00');
        $lateToleranceMinutes = $request->input('late_tolerance_minutes', 0);
        $isLatePenalty = $request->boolean('is_late_penalty_enabled') ? '1' : '0';
        $lateTier1Rate = $request->input('late_tier_1_rate', 50000);
        $lateTier2Rate = $request->input('late_tier_2_rate', 75000);
        $lateTier3Rate = $request->input('late_tier_3_rate', 100000);
        $lateTierExcessPercent = $request->input('late_tier_excess_percent', 10);
        $alphaPenaltyRate = $request->input('alpha_penalty_rate', 50000);
        $isWeekendOff = $request->boolean('is_weekend_off_enabled') ? '1' : '0';

        $settings = [
            'is_wifi_restriction_enabled' => [$isWifi, 'Batasi absensi online hanya dari jaringan WiFi kantor yang terdaftar'],
            'is_device_lock_enabled' => [$isDeviceLock, 'Kunci 1 Perangkat per Karyawan (Mencegah 1 HP/Laptop dipakai banyak akun)'],
            'is_selfie_required' => [$isSelfie, 'Wajibkan Foto Selfie Kamera Live saat Presensi Masuk (Clock In)'],
            'is_auto_clock_out_enabled' => [$isAutoClockOut, 'Auto Clock-Out Otomatis pada jam pulang yang ditentukan'],
            'auto_clock_out_time' => [$autoClockOutTime, 'Jam default auto Clock-Out (contoh: 17:00)'],
            'work_start_time' => [$workStartTime, 'Jam Masuk Standar Kantor (08:00 WIB)'],
            'late_tolerance_minutes' => [$lateToleranceMinutes, 'Toleransi Keterlambatan Harian dalam Menit (0 = Tanpa Toleransi)'],
            'is_late_penalty_enabled' => [$isLatePenalty, 'Aktifkan Kebijakan Denda & Sanksi Keterlambatan'],
            'late_tier_1_rate' => [$lateTier1Rate, 'Nominal Denda Terlambat ke-1 dalam Bulan Berjalan (Rp)'],
            'late_tier_2_rate' => [$lateTier2Rate, 'Nominal Denda Terlambat ke-2 dalam Bulan Berjalan (Rp)'],
            'late_tier_3_rate' => [$lateTier3Rate, 'Nominal Denda Terlambat ke-3 dalam Bulan Berjalan (Rp)'],
            'late_tier_excess_percent' => [$lateTierExcessPercent, 'Persentase Pemotongan Gaji Pokok untuk Keterlambatan > 3 Kali (%)'],
            'alpha_penalty_rate' => [$alphaPenaltyRate, 'Nominal Denda/Potongan Alpa Tanpa Kabar Seharian Penuh (Rp)'],
            'is_weekend_off_enabled' => [$isWeekendOff, 'Sabtu & Minggu adalah Hari Libur Bebas Absensi & Bebas Denda Alpa'],
        ];

        foreach ($settings as $key => $data) {
            DB::table('hr_attendance_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $data[0], 'description' => $data[1], 'updated_at' => now()]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan Jam Kerja, Kebijakan Denda Bertingkat, Hari Libur Akhir Pekan, & Anti-Fraud berhasil diperbarui.');
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
}

