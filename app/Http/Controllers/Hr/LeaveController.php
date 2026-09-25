<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\HrLeaveBalance;
use App\Models\HrLeaveRequest;
use App\Models\HrLeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $typeId = $request->input('leave_type_id');
        $year = (int) $request->input('year', date('Y'));
        $activeTab = $request->input('tab', 'requests');

        $query = HrLeaveRequest::with(['employee.user', 'employee.department', 'leaveType', 'approver']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($typeId) {
            $query->where('leave_type_id', $typeId);
        }

        $leaveRequests = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $stats = [
            'pending' => HrLeaveRequest::where('status', 'Pending')->count(),
            'approved' => HrLeaveRequest::where('status', 'Approved')->whereYear('start_date', $year)->count(),
            'rejected' => HrLeaveRequest::where('status', 'Rejected')->whereYear('start_date', $year)->count(),
        ];

        $leaveTypes = HrLeaveType::where('is_active', true)->orderBy('name')->get();
        $employees = Employee::with(['user', 'department'])
            ->where('employment_status', '!=', 'Resign')
            ->orderBy('id', 'asc')
            ->get();

        // Ambil kuota cuti tahunan untuk tahun yang dipilih
        $leaveBalances = HrLeaveBalance::where('year', $year)
            ->get()
            ->keyBy('employee_id');

        // Available years
        $balanceYears = HrLeaveBalance::select('year')->distinct()->pluck('year')->toArray();
        $requestYears = HrLeaveRequest::selectRaw('YEAR(start_date) as yr')->distinct()->pluck('yr')->toArray();
        $availableYears = array_unique(array_merge([(int)date('Y'), (int)date('Y') + 1], $balanceYears, $requestYears));
        rsort($availableYears);

        return view('pages.hr.leaves.index', compact(
            'leaveRequests',
            'leaveTypes',
            'employees',
            'leaveBalances',
            'stats',
            'status',
            'typeId',
            'year',
            'availableYears',
            'activeTab'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:hr_leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $totalDays = $start->diffInDays($end) + 1;

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'leave_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('asset/hr/leaves'), $filename);
            $attachmentPath = 'asset/hr/leaves/' . $filename;
        }

        HrLeaveRequest::create([
            'employee_id' => $validated['employee_id'],
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'attachment' => $attachmentPath,
            'status' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Pengajuan cuti/izin berhasil diajukan dan menunggu persetujuan.');
    }

    public function approve(Request $request, HrLeaveRequest $leave)
    {
        if ($leave->status === 'Approved') {
            return redirect()->back()->with('info', 'Pengajuan ini sudah disetujui sebelumnya.');
        }

        $leave->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
        ]);

        // Deduct from leave balance if leave type counts towards quota (e.g. Cuti Tahunan)
        $leaveType = $leave->leaveType;
        if ($leaveType && $leaveType->code === 'CT') {
            $year = Carbon::parse($leave->start_date)->year;
            $balance = HrLeaveBalance::firstOrCreate(
                ['employee_id' => $leave->employee_id, 'year' => $year],
                ['total_quota' => 12, 'used_quota' => 0, 'remaining_quota' => 12, 'is_active' => true]
            );

            $balance->used_quota += $leave->total_days;
            $balance->remaining_quota = max(0, $balance->total_quota - $balance->used_quota);
            $balance->save();
        }

        return redirect()->back()->with('success', 'Pengajuan cuti berhasil disetujui (Approved).');
    }

    public function reject(Request $request, HrLeaveRequest $leave)
    {
        $validated = $request->validate([
            'rejection_note' => 'required|string|max:500',
        ]);

        $leave->update([
            'status' => 'Rejected',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
            'rejection_note' => $validated['rejection_note'],
        ]);

        return redirect()->back()->with('success', 'Pengajuan cuti telah ditolak.');
    }

    /**
     * Penetapan Kuota Cuti Masal (Semua Karyawan Aktif)
     */
    public function updateBulkBalance(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2020|max:2099',
            'total_quota' => 'required|integer|min:0|max:365',
            'is_active' => 'nullable',
        ]);

        $year = (int) $validated['year'];
        $quota = (int) $validated['total_quota'];
        $isActive = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        $employees = Employee::where('employment_status', '!=', 'Resign')->get();
        foreach ($employees as $emp) {
            $balance = HrLeaveBalance::firstOrNew([
                'employee_id' => $emp->id,
                'year' => $year,
            ]);

            $used = $balance->exists ? (int)$balance->used_quota : 0;
            $balance->total_quota = $quota;
            $balance->used_quota = $used;
            $balance->remaining_quota = max(0, $quota - $used);
            $balance->is_active = $isActive;
            $balance->save();
        }

        $statusLabel = $isActive ? 'Aktif' : 'Non-Aktif';
        return redirect()->route('hr.leaves.index', ['year' => $year, 'tab' => 'settings'])
            ->with('success', "Kuota cuti tahun {$year} berhasil ditetapkan untuk {$employees->count()} karyawan ({$quota} hari, Status: {$statusLabel}).");
    }

    /**
     * Penetapan Kuota Cuti Spesifik per Karyawan
     */
    public function updateIndividualBalance(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer|min:2020|max:2099',
            'total_quota' => 'required|integer|min:0|max:365',
            'is_active' => 'nullable',
        ]);

        $year = (int) $validated['year'];
        $quota = (int) $validated['total_quota'];
        $isActive = $request->has('is_active') ? (bool) $request->input('is_active') : false;

        $balance = HrLeaveBalance::firstOrNew([
            'employee_id' => $validated['employee_id'],
            'year' => $year,
        ]);

        $used = $balance->exists ? (int)$balance->used_quota : 0;
        $balance->total_quota = $quota;
        $balance->used_quota = $used;
        $balance->remaining_quota = max(0, $quota - $used);
        $balance->is_active = $isActive;
        $balance->save();

        $emp = Employee::with('user')->find($validated['employee_id']);
        $empName = $emp?->user?->name ?? 'Karyawan';
        $statusLabel = $isActive ? 'Aktif' : 'Non-Aktif';

        return redirect()->route('hr.leaves.index', ['year' => $year, 'tab' => 'settings'])
            ->with('success', "Kuota cuti tahun {$year} untuk {$empName} berhasil disimpan ({$quota} hari, Status: {$statusLabel}).");
    }

    /**
     * Toggle Status Aktif / Non-Aktif Kuota Cuti Karyawan
     */
    public function toggleBalanceStatus(Request $request, HrLeaveBalance $balance)
    {
        $balance->is_active = !$balance->is_active;
        $balance->save();

        $statusText = $balance->is_active ? 'Diaktifkan (Muncul di My Portal)' : 'Dinonaktifkan (Disembunyikan dari My Portal)';
        return redirect()->back()->with('success', "Status kuota cuti {$statusText}.");
    }
}
