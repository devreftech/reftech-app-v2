<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\HrEvaluation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('evaluation_type');

        $query = HrEvaluation::with(['employee.user', 'employee.department', 'employee.position', 'evaluator']);

        if ($type) {
            $query->where('evaluation_type', $type);
        }

        $evaluations = $query->orderByDesc('evaluation_date')->paginate(20)->withQueryString();

        $stats = [
            'probation' => HrEvaluation::where('evaluation_type', 'Probation 3 Bulan')->count(),
            'annual' => HrEvaluation::where('evaluation_type', 'Tahunan (Annual)')->count(),
            'passed_permanent' => HrEvaluation::where('recommendation', 'Lolos Pegawai Tetap')->count(),
        ];

        $employees = Employee::with(['user', 'department', 'position'])->where('employment_status', '!=', 'Resign')->get();

        return view('pages.hr.evaluations.index', compact('evaluations', 'stats', 'employees', 'type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'evaluation_type' => 'required|in:Probation 3 Bulan,Tahunan (Annual),Kenaikan Jabatan,Evaluasi Khusus',
            'evaluation_date' => 'required|date',
            'score' => 'required|integer|min:0|max:100',
            'strengths' => 'nullable|string|max:1000',
            'improvements' => 'nullable|string|max:1000',
            'recommendation' => 'required|in:Lolos Pegawai Tetap,Perpanjang Kontrak,Peringatan / Evaluasi Ulang,Tidak Dilanjutkan / PHK',
            'notes' => 'nullable|string|max:1000',
        ]);

        HrEvaluation::create([
            'employee_id' => $validated['employee_id'],
            'evaluator_id' => Auth::id(),
            'evaluation_type' => $validated['evaluation_type'],
            'evaluation_date' => $validated['evaluation_date'],
            'score' => $validated['score'],
            'strengths' => $validated['strengths'],
            'improvements' => $validated['improvements'],
            'recommendation' => $validated['recommendation'],
            'status' => 'Final',
            'notes' => $validated['notes'],
        ]);

        return redirect()->back()->with('success', 'Hasil evaluasi kinerja karyawan berhasil disimpan.');
    }

    public function destroy(HrEvaluation $evaluation)
    {
        $evaluation->delete();
        return redirect()->back()->with('success', 'Data evaluasi berhasil dihapus.');
    }
}
