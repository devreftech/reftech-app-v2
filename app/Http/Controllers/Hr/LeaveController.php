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
        $employees = Employee::with(['user', 'department'])->where('employment_status', '!=', 'Resign')->get();

        return view('pages.hr.leaves.index', compact(
            'leaveRequests',
            'leaveTypes',
            'employees',
            'stats',
            'status',
            'typeId',
            'year'
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
                ['total_quota' => 12, 'used_quota' => 0, 'remaining_quota' => 12]
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
}
