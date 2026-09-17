<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\HrEmployeeAsset;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $employeeId = $request->input('employee_id');

        $query = HrEmployeeAsset::with(['employee.user', 'employee.department', 'employee.position']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $assets = $query->orderByDesc('handover_date')->paginate(20)->withQueryString();

        $stats = [
            'total_active' => HrEmployeeAsset::where('status', 'Digunakan')->count(),
            'total_returned' => HrEmployeeAsset::where('status', 'Dikembalikan')->count(),
            'total_damaged' => HrEmployeeAsset::where('status', 'Hilang/Rusak')->count(),
        ];

        $employees = Employee::with('user')->where('employment_status', '!=', 'Resign')->get();

        return view('pages.hr.assets.index', compact('assets', 'stats', 'employees', 'status', 'employeeId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'asset_name' => 'required|string|max:150',
            'asset_code' => 'nullable|string|max:50',
            'serial_number' => 'nullable|string|max:100',
            'condition' => 'required|in:Baik,Normal,Rusak Ringan,Perlu Servis',
            'handover_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        HrEmployeeAsset::create([
            'employee_id' => $validated['employee_id'],
            'asset_name' => $validated['asset_name'],
            'asset_code' => $validated['asset_code'],
            'serial_number' => $validated['serial_number'],
            'condition' => $validated['condition'],
            'handover_date' => $validated['handover_date'],
            'status' => 'Digunakan',
            'notes' => $validated['notes'],
        ]);

        return redirect()->back()->with('success', 'Data inventaris alat kerja berhasil ditugaskan.');
    }

    public function update(Request $request, HrEmployeeAsset $asset)
    {
        $validated = $request->validate([
            'status' => 'required|in:Digunakan,Dikembalikan,Hilang/Rusak',
            'condition' => 'required|in:Baik,Normal,Rusak Ringan,Perlu Servis',
            'returned_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['status'] === 'Dikembalikan' && empty($validated['returned_date'])) {
            $validated['returned_date'] = Carbon::today()->toDateString();
        }

        $asset->update($validated);

        return redirect()->back()->with('success', 'Status inventaris aset berhasil diperbarui.');
    }

    public function destroy(HrEmployeeAsset $asset)
    {
        $asset->delete();
        return redirect()->back()->with('success', 'Data inventaris alat kerja berhasil dihapus.');
    }
}
