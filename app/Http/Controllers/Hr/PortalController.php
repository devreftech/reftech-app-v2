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

        // Anti-fraud & Security settings
        $wifiSetting = DB::table('hr_attendance_settings')->where('key', 'is_wifi_restriction_enabled')->first();
        $isWifiRestrictionEnabled = $wifiSetting && $wifiSetting->value === '1';
        $deviceLockSetting = DB::table('hr_attendance_settings')->where('key', 'is_device_lock_enabled')->first();
        $isDeviceLockEnabled = !$deviceLockSetting || $deviceLockSetting->value === '1';
        $selfieSetting = DB::table('hr_attendance_settings')->where('key', 'is_selfie_required')->first();
        $isSelfieRequired = $selfieSetting && $selfieSetting->value === '1';

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
            'isDeviceLockEnabled',
            'isSelfieRequired',
            'clientIp',
            'activeWifis'
        ));
    }

    /**
     * Endpoint AJAX untuk verifikasi pra-Clock In (Jaringan WiFi, Device Lock, dll)
     */
    public function verifyPreClockIn(Request $request)
    {
        $employee = Auth::user()?->employee;
        if (!$employee) {
            return response()->json([
                'allowed' => false,
                'type' => 'unauthorized',
                'message' => 'Profil karyawan tidak ditemukan.'
            ], 404);
        }

        if (!$employee->can_online_attendance) {
            return response()->json([
                'allowed' => false,
                'type' => 'permission_denied',
                'message' => 'Akun Anda tidak memiliki hak akses untuk melakukan presensi online.'
            ]);
        }

        // 1. Validasi Pembatasan WiFi Kantor
        $wifiError = $this->validateWifiRestriction($request);
        if ($wifiError) {
            return response()->json([
                'allowed' => false,
                'type' => 'wifi_error',
                'message' => $wifiError,
                'client_ip' => $request->ip()
            ]);
        }

        $today = Carbon::today()->toDateString();

        // 2. Cek apakah sudah Clock In hari ini
        $alreadyClockedIn = HrAttendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->whereNotNull('clock_in')
            ->first();
        if ($alreadyClockedIn) {
            return response()->json([
                'allowed' => false,
                'type' => 'already_clocked_in',
                'message' => 'Anda sudah melakukan presensi masuk hari ini pada pukul ' . substr($alreadyClockedIn->clock_in, 0, 5) . ' WIB.'
            ]);
        }

        // 3. Validasi Device Lock (1 HP/Laptop per karyawan per hari)
        $deviceError = $this->validateDeviceLock($request, $employee, $today);
        if ($deviceError) {
            return response()->json([
                'allowed' => false,
                'type' => 'device_error',
                'message' => $deviceError
            ]);
        }

        return response()->json([
            'allowed' => true,
            'message' => 'Jaringan WiFi dan perangkat terverifikasi.'
        ]);
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

        // 3. Check Device Lock (1 HP/Laptop per employee per day)
        $deviceError = $this->validateDeviceLock($request, $employee, $today);
        if ($deviceError) {
            return redirect()->back()->with('error', $deviceError);
        }

        // 4. Check & Process Selfie Photo (if required by HR setting)
        $selfieSetting = DB::table('hr_attendance_settings')->where('key', 'is_selfie_required')->first();
        $isSelfieRequired = $selfieSetting && $selfieSetting->value === '1';
        $selfieInPath = null;

        if ($request->filled('selfie_image')) {
            $selfieInPath = $this->saveSelfieImage($request->input('selfie_image'), $employee->id, 'in', $today);
        } elseif ($isSelfieRequired) {
            return redirect()->back()->with('error', 'Presensi Ditolak! Foto selfie kamera langsung wajib diambil saat Clock In.');
        }

        // Late & Penalty calculation
        $workStartSetting = DB::table('hr_attendance_settings')->where('key', 'work_start_time')->first();
        $startTimeStr = ($workStartSetting && !empty($workStartSetting->value)) ? $workStartSetting->value : '08:30';
        $timeParts = explode(':', $startTimeStr);
        $startHour = (int) ($timeParts[0] ?? 8);
        $startMinute = (int) ($timeParts[1] ?? 30);

        $standardStart = Carbon::createFromTime($startHour, $startMinute, 0);
        $lateMinutes = 0;
        if (Carbon::now()->gt($standardStart)) {
            $lateMinutes = Carbon::now()->diffInMinutes($standardStart);
        }

        $penaltyInfo = $this->calculateLatePenalty($employee->id, $today, $lateMinutes);
        $penaltyAmount = $penaltyInfo['penalty'];

        $dataToSave = [
            'clock_in' => $nowTime,
            'work_type' => $request->input('work_type', 'WFO'),
            'status' => 'Hadir',
            'late_minutes' => $lateMinutes,
            'penalty_amount' => $penaltyAmount,
            'ip_address' => $request->ip(),
            'device_id' => $request->input('device_id'),
            'device_info' => $request->input('device_info', $request->userAgent()),
            'location_in' => $request->input('location', 'Kantor Reftech'),
        ];

        if ($selfieInPath) {
            $dataToSave['selfie_in'] = $selfieInPath;
        }

        HrAttendance::updateOrCreate(
            ['employee_id' => $employee->id, 'date' => $today],
            $dataToSave
        );

        $msg = 'Presensi masuk (Clock In) berhasil dicatat pukul ' . substr($nowTime, 0, 5);
        if ($lateMinutes > 0) {
            $msg .= " (Terlambat {$lateMinutes} menit";
            if ($penaltyAmount > 0) {
                $msg .= ", Denda Rp " . number_format($penaltyAmount, 0, ',', '.') . " - " . $penaltyInfo['status_label'] . ")";
            } else {
                $msg .= " - " . $penaltyInfo['status_label'] . ")";
            }
        }

        return redirect()->route('hr.portal.index')->with('success', $msg);
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

        // Check optional selfie for clock out
        $selfieOutPath = null;
        if ($request->filled('selfie_image')) {
            $selfieOutPath = $this->saveSelfieImage($request->input('selfie_image'), $employee->id, 'out', $today);
        }

        // Overtime calculation (e.g. after 17:30)
        $standardEnd = Carbon::createFromTime(17, 30, 0);
        $overtimeMinutes = 0;
        if (Carbon::now()->gt($standardEnd)) {
            $overtimeMinutes = Carbon::now()->diffInMinutes($standardEnd);
        }

        $updateData = [
            'clock_out' => $nowTime,
            'overtime_minutes' => $overtimeMinutes,
            'location_out' => $request->input('location', 'Kantor Reftech'),
        ];

        if ($selfieOutPath) {
            $updateData['selfie_out'] = $selfieOutPath;
        }

        $attendance->update($updateData);

        return redirect()->route('hr.portal.index')->with('success', 'Presensi pulang (Clock Out) berhasil dicatat pukul ' . substr($nowTime, 0, 5));
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

    /**
     * Validasi Device Lock: 1 Perangkat HP/Laptop hanya boleh dipakai 1 Karyawan per hari.
     */
    protected function validateDeviceLock(Request $request, $employee, string $today): ?string
    {
        $deviceLockSetting = DB::table('hr_attendance_settings')->where('key', 'is_device_lock_enabled')->first();
        $isDeviceLockEnabled = !$deviceLockSetting || $deviceLockSetting->value === '1';

        if (!$isDeviceLockEnabled) {
            return null;
        }

        $deviceId = $request->input('device_id');
        if (empty($deviceId)) {
            return null; // Device ID belum tersedia di browser lawas
        }

        // Cari apakah device ID ini sudah digunakan karyawan lain hari ini
        $existing = HrAttendance::whereDate('date', $today)
            ->where('device_id', $deviceId)
            ->where('employee_id', '!=', $employee->id)
            ->with(['employee.user'])
            ->first();

        if ($existing) {
            $otherName = $existing->employee?->user?->name ?? ($existing->employee?->nik ? 'NIK ' . $existing->employee?->nik : 'karyawan lain');
            return "Presensi Ditolak (Anti-Titip Absen)! Perangkat (HP/Laptop) ini sudah digunakan untuk presensi oleh [{$otherName}] hari ini. 1 Perangkat tidak dapat digunakan untuk presensi lebih dari 1 akun karyawan.";
        }

        return null;
    }

    /**
     * Decode dan simpan foto selfie base64 ke disk public storage.
     */
    protected function saveSelfieImage(string $base64Data, int $employeeId, string $type, string $today): ?string
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $typeMatch)) {
                $data = substr($base64Data, strpos($base64Data, ',') + 1);
                $typeExt = strtolower($typeMatch[1]); // jpg, png, jpeg
                if (!in_array($typeExt, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $typeExt = 'jpg';
                }
                $decoded = base64_decode($data);
                if ($decoded === false) {
                    return null;
                }

                $dir = storage_path('app/public/hr/attendances/selfies');
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                $filename = "selfie_{$type}_{$employeeId}_{$today}_" . time() . '.' . $typeExt;
                $fullPath = $dir . '/' . $filename;
                file_put_contents($fullPath, $decoded);

                return 'storage/hr/attendances/selfies/' . $filename;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan foto selfie absensi: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Hitung denda keterlambatan berdasarkan late_minutes dan kuota toleransi bulan berjalan.
     */
    protected function calculateLatePenalty(int $employeeId, string $date, int $lateMinutes): array
    {
        $settings = DB::table('hr_attendance_settings')->pluck('value', 'key')->toArray();
        $isEnabled = ($settings['is_late_penalty_enabled'] ?? '1') === '1';

        if (!$isEnabled || $lateMinutes <= 0) {
            return ['penalty' => 0, 'late_count' => 0, 'status_label' => 'Tepat Waktu', 'is_multiplier' => false];
        }

        $tolerance = (int) ($settings['late_tolerance_minutes'] ?? 0);
        if ($lateMinutes <= $tolerance) {
            return ['penalty' => 0, 'late_count' => 0, 'status_label' => 'Toleransi Menit Bebas Denda', 'is_multiplier' => false];
        }

        $billableMinutes = $lateMinutes - $tolerance;
        $penaltyType = $settings['late_penalty_type'] ?? 'per_minute';
        $rate = (float) ($settings['late_penalty_rate'] ?? 1000);
        $freeCount = (int) ($settings['late_free_count_per_month'] ?? 2);
        $multiplierThreshold = (int) ($settings['late_multiplier_threshold'] ?? 5);
        $multiplierRate = (float) ($settings['late_multiplier_rate'] ?? 2.0);

        // Hitung frekuensi terlambat di bulan berjalan
        $carbonDate = Carbon::parse($date);
        $previousLateCount = HrAttendance::where('employee_id', $employeeId)
            ->whereMonth('date', $carbonDate->month)
            ->whereYear('date', $carbonDate->year)
            ->where('date', '<', $date)
            ->where('late_minutes', '>', $tolerance)
            ->count();
        
        $currentLateCount = $previousLateCount + 1;

        if ($currentLateCount <= $freeCount) {
            return [
                'penalty' => 0,
                'late_count' => $currentLateCount,
                'status_label' => "Toleransi Bulanan ({$currentLateCount}/{$freeCount})",
                'is_multiplier' => false
            ];
        }

        $basePenalty = ($penaltyType === 'per_minute') ? ($billableMinutes * $rate) : $rate;
        $isMultiplier = ($currentLateCount >= $multiplierThreshold);
        $finalPenalty = $isMultiplier ? ($basePenalty * $multiplierRate) : $basePenalty;

        $statusLabel = $isMultiplier
            ? "Sanksi Berlipat ({$multiplierRate}x) - Terlambat ke-{$currentLateCount}"
            : "Terlambat ke-{$currentLateCount}";

        return [
            'penalty' => $finalPenalty,
            'late_count' => $currentLateCount,
            'status_label' => $statusLabel,
            'is_multiplier' => $isMultiplier
        ];
    }
}


