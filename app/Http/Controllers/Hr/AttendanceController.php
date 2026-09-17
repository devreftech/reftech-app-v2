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
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
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

        // WiFi Networks & Restriction Settings
        $officeWifis = HrOfficeWifi::orderByDesc('is_active')->orderBy('id')->get();
        $wifiSetting = DB::table('hr_attendance_settings')->where('key', 'is_wifi_restriction_enabled')->first();
        $isWifiRestrictionEnabled = $wifiSetting && $wifiSetting->value === '1';
        $currentClientIp = $request->ip();

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
            'currentClientIp'
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
            'overtime_minutes' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

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
}

