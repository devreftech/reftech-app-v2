<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\FixedAsset;
use App\Models\HrEmployeeAsset;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $employeeId = $request->input('employee_id');

        $query = HrEmployeeAsset::with([
            'employee.user',
            'employee.department',
            'employee.position',
            'fixedAsset.toolsMaster',
            'fixedAsset.unit',
            'fixedAsset.pic'
        ]);

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

        $employees = Employee::with(['user', 'department', 'position'])->where('employment_status', '!=', 'Resign')->get();

        // Fixed assets yang aktif / belum disposed untuk dihubungkan ke serah terima
        $fixedAssets = FixedAsset::with(['toolsMaster', 'unit', 'pic'])
            ->where('is_disposed', 0)
            ->orderBy('type')
            ->orderBy('code')
            ->get();

        return view('pages.hr.assets.index', compact('assets', 'stats', 'employees', 'fixedAssets', 'status', 'employeeId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'fixed_asset_id' => 'nullable|exists:fixed_asset,id',
            'asset_name'     => 'required|string|max:150',
            'asset_code'     => 'nullable|string|max:50',
            'serial_number'  => 'nullable|string|max:100',
            'condition'      => 'required|in:Baik,Normal,Rusak Ringan,Perlu Servis',
            'handover_date'  => 'required|date',
            'notes'          => 'nullable|string|max:500',
        ]);

        $employeeAsset = HrEmployeeAsset::create([
            'employee_id'    => $validated['employee_id'],
            'fixed_asset_id' => $validated['fixed_asset_id'] ?? null,
            'asset_name'     => $validated['asset_name'],
            'asset_code'     => $validated['asset_code'] ?? null,
            'serial_number'  => $validated['serial_number'] ?? null,
            'condition'      => $validated['condition'],
            'handover_date'  => $validated['handover_date'],
            'status'         => 'Digunakan',
            'notes'          => $validated['notes'] ?? null,
        ]);

        // Sinkronisasi otomatis ke Fixed Asset
        if (!empty($validated['fixed_asset_id'])) {
            $fixed = FixedAsset::find($validated['fixed_asset_id']);
            if ($fixed) {
                $emp = Employee::find($validated['employee_id']);
                if ($emp && $emp->user_id) {
                    $fixed->id_pic = $emp->user_id;
                }
                $fixed->tanggal_serah_terima = $validated['handover_date'];
                $fixed->kondisi = $validated['condition'];
                if (in_array($fixed->type, ['Tools', 'Kendaraan', 'Peralatan Kantor'])) {
                    $fixed->status_tools = 'Aktif';
                }
                if (empty($fixed->serial_number) && !empty($validated['serial_number'])) {
                    $fixed->serial_number = $validated['serial_number'];
                }
                $fixed->save();
            }
        }

        return redirect()->back()->with('success', 'Data inventaris alat kerja berhasil ditugaskan dan disinkronkan ke Fixed Asset.');
    }

    public function update(Request $request, HrEmployeeAsset $asset)
    {
        $validated = $request->validate([
            'status'        => 'required|in:Digunakan,Dikembalikan,Hilang/Rusak',
            'condition'     => 'required|in:Baik,Normal,Rusak Ringan,Perlu Servis',
            'returned_date' => 'nullable|date',
            'notes'         => 'nullable|string|max:500',
        ]);

        if ($validated['status'] === 'Dikembalikan' && empty($validated['returned_date'])) {
            $validated['returned_date'] = Carbon::today()->toDateString();
        }

        $asset->update($validated);

        // Sinkronisasi status pengembalian / kondisi ke Fixed Asset jika terhubung
        if ($asset->fixed_asset_id) {
            $fixed = FixedAsset::find($asset->fixed_asset_id);
            if ($fixed) {
                if ($validated['status'] === 'Dikembalikan') {
                    $fixed->id_pic = null;
                    if ($fixed->type === 'Tools') {
                        $fixed->status_tools = 'Tersedia';
                    }
                } elseif ($validated['status'] === 'Hilang/Rusak') {
                    if ($fixed->type === 'Tools') {
                        $fixed->status_tools = 'Rusak';
                    }
                } elseif ($validated['status'] === 'Digunakan') {
                    $emp = $asset->employee;
                    if ($emp && $emp->user_id) {
                        $fixed->id_pic = $emp->user_id;
                    }
                    if (in_array($fixed->type, ['Tools', 'Kendaraan', 'Peralatan Kantor'])) {
                        $fixed->status_tools = 'Aktif';
                    }
                }
                $fixed->kondisi = $validated['condition'];
                $fixed->save();
            }
        }

        return redirect()->back()->with('success', 'Status inventaris aset berhasil diperbarui dan disinkronkan ke Fixed Asset.');
    }

    public function destroy(HrEmployeeAsset $asset)
    {
        // Lepas PIC pada fixed asset jika aset sedang digunakan
        if ($asset->fixed_asset_id && $asset->status === 'Digunakan') {
            $fixed = FixedAsset::find($asset->fixed_asset_id);
            if ($fixed) {
                $fixed->id_pic = null;
                if ($fixed->type === 'Tools') {
                    $fixed->status_tools = 'Tersedia';
                }
                $fixed->save();
            }
        }

        $asset->delete();
        return redirect()->back()->with('success', 'Data inventaris alat kerja berhasil dihapus.');
    }
}
