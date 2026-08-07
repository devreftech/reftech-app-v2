<?php

namespace App\Http\Controllers;

use App\Models\ToolAudit;
use App\Models\ToolAuditPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToolAuditSummaryController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role != 'Admin') {
            abort(403, 'Hanya Admin yang bisa mengakses Summary Audit Tools.');
        }

        $periods = ToolAuditPeriod::orderByDesc('tahun')->orderByDesc('semester')->get();
        $period = $request->filled('period_id')
            ? $periods->firstWhere('id', (int) $request->period_id)
            : $periods->first();

        $audits = $period
            ? ToolAudit::with('technician')->where('id_audit_period', $period->id)->orderBy('id')->get()
            : collect();

        $summary = [
            'total_teknisi' => $audits->count(),
            'draft' => $audits->where('status_submit', 'Draft')->count(),
            'submitted' => $audits->where('status_submit', 'Submitted')->count(),
            'verified' => $audits->where('status_submit', 'Verified')->count(),
            'rejected' => $audits->where('status_submit', 'Rejected')->count(),
            'total_tools' => $audits->sum('total_tools'),
            'total_ada' => $audits->sum('total_ada'),
            'total_rusak' => $audits->sum('total_rusak'),
            'total_hilang' => $audits->sum('total_hilang'),
        ];

        return view('pages.admin.tool-audit-summary.index', compact('periods', 'period', 'audits', 'summary'));
    }
}
