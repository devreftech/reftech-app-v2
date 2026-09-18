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
        $selectedDate = $request->input('date', Carbon::today('Asia/Jakarta')->toDateString());
        
        // Evaluasi Auto Clock-Out otomatis (Asia/Jakarta GMT+7)
        HrAttendance::processAutoClockOutIfDue($selectedDate);

        $departmentId = $request->input('department_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = HrAttendance::with(['employee.user', 'employee.department', 'employee.position'])
            ->whereDate('date', $selectedDate);

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

        $attendances = $query->orderBy('clock_in', 'asc')->paginate(20)->withQueryString();

        // Stats summary for the selected date
        $allForDate = HrAttendance::whereDate('date', $selectedDate)->get();
        $stats = [
            'total_present' => $allForDate->where('status', 'Hadir')->count(),
            'total_late' => $allForDate->where('status', 'Hadir')->where('late_minutes', '>', 0)->count(),
            'total_leave' => $allForDate->whereIn('status', ['Cuti', 'Izin'])->count(),
            'total_sick' => $allForDate->where('status', 'Sakit')->count(),
            'total_alpha' => $allForDate->where('status', 'Alpa')->count(),
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
        $workStartTime = $settings['work_start_time'] ?? '08:30';
        $lateToleranceMinutes = (int) ($settings['late_tolerance_minutes'] ?? 0);
        $isLatePenaltyEnabled = ($settings['is_late_penalty_enabled'] ?? '1') === '1';
        $latePenaltyType = $settings['late_penalty_type'] ?? 'per_minute';
        $latePenaltyRate = (float) ($settings['late_penalty_rate'] ?? 1000);
        $lateFreeCountPerMonth = (int) ($settings['late_free_count_per_month'] ?? 2);
        $lateMultiplierThreshold = (int) ($settings['late_multiplier_threshold'] ?? 5);
        $lateMultiplierRate = (float) ($settings['late_multiplier_rate'] ?? 2.0);

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

        $monthAllAttendances = HrAttendance::with(['employee.user', 'employee.department', 'employee.position'])
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

            // Hitung status sanksi / strike
            $strikeStatus = 'Disiplin';
            $strikeBadgeClass = 'bg-label-success';
            if ($lateDaysCount > 0 && $lateDaysCount <= $lateFreeCountPerMonth) {
                $strikeStatus = "Toleransi ({$lateDaysCount}/{$lateFreeCountPerMonth})";
                $strikeBadgeClass = 'bg-label-info';
            } elseif ($lateDaysCount > $lateFreeCountPerMonth && $lateDaysCount < $lateMultiplierThreshold) {
                $strikeStatus = "Denda Kena ({$lateDaysCount}x)";
                $strikeBadgeClass = 'bg-label-warning';
            } elseif ($lateDaysCount >= $lateMultiplierThreshold) {
                $strikeStatus = "Peringatan SP-1 ({$lateDaysCount}x)";
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
            'selectedDate',
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
            'latePenaltyType',
            'latePenaltyRate',
            'lateFreeCountPerMonth',
            'lateMultiplierThreshold',
            'lateMultiplierRate',
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
        $workStartTime = $settings['work_start_time'] ?? '08:30';
        $lateToleranceMinutes = (int) ($settings['late_tolerance_minutes'] ?? 0);
        $isLatePenaltyEnabled = ($settings['is_late_penalty_enabled'] ?? '1') === '1';
        $latePenaltyType = $settings['late_penalty_type'] ?? 'per_minute';
        $latePenaltyRate = (float) ($settings['late_penalty_rate'] ?? 1000);
        $lateFreeCountPerMonth = (int) ($settings['late_free_count_per_month'] ?? 2);
        $lateMultiplierThreshold = (int) ($settings['late_multiplier_threshold'] ?? 5);
        $lateMultiplierRate = (float) ($settings['late_multiplier_rate'] ?? 2.0);

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
        $empQuery = Employee::with(['user', 'department', 'position'])
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
        $allAttendances = HrAttendance::with(['employee.user', 'employee.department'])
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

            // Hitung status sanksi / strike
            $strikeStatus = 'Disiplin';
            $strikeBadgeClass = 'bg-label-success';
            $strikeKey = 'disciplined';

            if ($lateDaysCount > 0 && $lateDaysCount <= $lateFreeCountPerMonth) {
                $strikeStatus = "Toleransi ({$lateDaysCount}/{$lateFreeCountPerMonth})";
                $strikeBadgeClass = 'bg-label-info';
                $strikeKey = 'tolerance';
            } elseif ($lateDaysCount > $lateFreeCountPerMonth && $lateDaysCount < $lateMultiplierThreshold) {
                $strikeStatus = "Denda Kena ({$lateDaysCount}x)";
                $strikeBadgeClass = 'bg-label-warning';
                $strikeKey = 'penalized';
            } elseif ($lateDaysCount >= $lateMultiplierThreshold) {
                $strikeStatus = "Peringatan SP ({$lateDaysCount}x)";
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
            if ($lateDaysCount >= $lateMultiplierThreshold) {
                $totalEmployeesWarning++;
            }

            $totalPenaltyAccumulated += $empPenaltyTotal;
            $totalLateMinutes += $empLateMinutes;

            $penaltyRecap[] = [
                'employee' => $emp,
                'present_days' => $presentDays,
                'late_days_count' => $lateDaysCount,
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
            'latePenaltyType',
            'latePenaltyRate',
            'lateFreeCountPerMonth',
            'lateMultiplierThreshold',
            'lateMultiplierRate'
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

        $lateMinutes = (int) ($validated['late_minutes'] ?? 0);
        if ($lateMinutes > 0 && !isset($validated['penalty_amount'])) {
            $settings = DB::table('hr_attendance_settings')->pluck('value', 'key')->toArray();
            $rate = (float) ($settings['late_penalty_rate'] ?? 1000);
            $type = $settings['late_penalty_type'] ?? 'per_minute';
            $validated['penalty_amount'] = ($type === 'per_minute') ? ($lateMinutes * $rate) : $rate;
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

        $workStartTime = $request->input('work_start_time', '08:30');
        $lateToleranceMinutes = $request->input('late_tolerance_minutes', 0);
        $isLatePenalty = $request->boolean('is_late_penalty_enabled') ? '1' : '0';
        $latePenaltyType = $request->input('late_penalty_type', 'per_minute');
        $latePenaltyRate = $request->input('late_penalty_rate', 1000);
        $lateFreeCount = $request->input('late_free_count_per_month', 2);
        $lateMultiplierThreshold = $request->input('late_multiplier_threshold', 5);
        $lateMultiplierRate = $request->input('late_multiplier_rate', 2.0);

        $settings = [
            'is_wifi_restriction_enabled' => [$isWifi, 'Batasi absensi online hanya dari jaringan WiFi kantor yang terdaftar'],
            'is_device_lock_enabled' => [$isDeviceLock, 'Kunci 1 Perangkat per Karyawan (Mencegah 1 HP/Laptop dipakai banyak akun)'],
            'is_selfie_required' => [$isSelfie, 'Wajibkan Foto Selfie Kamera Live saat Presensi Masuk (Clock In)'],
            'is_auto_clock_out_enabled' => [$isAutoClockOut, 'Auto Clock-Out Otomatis pada jam pulang yang ditentukan'],
            'auto_clock_out_time' => [$autoClockOutTime, 'Jam default auto Clock-Out (contoh: 17:00)'],
            'work_start_time' => [$workStartTime, 'Jam Masuk Standar Kantor (Contoh: 08:30)'],
            'late_tolerance_minutes' => [$lateToleranceMinutes, 'Toleransi Keterlambatan Harian dalam Menit'],
            'is_late_penalty_enabled' => [$isLatePenalty, 'Aktifkan Kebijakan Denda & Pemotongan Keterlambatan'],
            'late_penalty_type' => [$latePenaltyType, 'Tipe Denda: per_minute atau flat'],
            'late_penalty_rate' => [$latePenaltyRate, 'Tarif Nominal Denda (Rp per menit atau Rp flat)'],
            'late_free_count_per_month' => [$lateFreeCount, 'Kuota Frekuensi Terlambat Bebas Denda (Toleransi Bulanan)'],
            'late_multiplier_threshold' => [$lateMultiplierThreshold, 'Batas Frekuensi Terlambat untuk Sanksi Eskalasi (SP-1)'],
            'late_multiplier_rate' => [$lateMultiplierRate, 'Faktor Pengali Denda setelah Melewati Batas Eskalasi'],
        ];

        foreach ($settings as $key => $data) {
            DB::table('hr_attendance_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $data[0], 'description' => $data[1], 'updated_at' => now()]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan Keamanan Presensi, Kebijakan Denda, & Auto Clock-Out berhasil diperbarui.');
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

