<?php

namespace App\Http\Controllers;

use App\Models\EcommerceKpiAssignment;
use App\Models\EcommerceKpiAssignmentItem;
use App\Models\EcommerceKpiPeriod;
use App\Models\EcommerceKpiTemplate;
use App\Models\User;
use App\Services\Kpi\EcommerceKpiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EcommerceKpiController extends Controller
{
    protected EcommerceKpiService $kpiService;

    public function __construct(EcommerceKpiService $kpiService)
    {
        $this->kpiService = $kpiService;
    }

    /**
     * Dashboard / Daftar Periode & KPI Tim E-Commerce (Akses Admin & Sales Manager).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isManagerOrAdmin = in_array($user->role, ['Admin', 'Developer', 'Sales Manager']);

        if (!$isManagerOrAdmin) {
            return redirect()->route('ecommerce.my-kpi');
        }

        $years = EcommerceKpiPeriod::select('year')->distinct()->orderByDesc('year')->pluck('year');
        if ($years->isEmpty()) {
            $years = collect([(int) date('Y')]);
        }

        $currentYear = (int) ($request->year ?? date('Y'));
        $currentMonth = (int) ($request->month ?? date('n'));

        // Cari atau pastikan periode untuk bulan & tahun terpilih
        $period = EcommerceKpiPeriod::where('year', $currentYear)
            ->where('month', $currentMonth)
            ->with(['assignments.user', 'assignments.evaluator', 'assignments.items'])
            ->first();

        // Jika periode belum ada, siapkan list kosong atau auto-create jika diminta
        $assignments = $period ? $period->assignments : collect();
        $templates = EcommerceKpiTemplate::where('is_active', true)->orderBy('sort_order')->get();

        return view('pages.ecommerce.kpi.index', compact(
            'years',
            'currentYear',
            'currentMonth',
            'period',
            'assignments',
            'templates'
        ));
    }

    /**
     * Buka / Inisialisasi Periode Baru dan Buat Assignment KPI E-Commerce.
     */
    public function createPeriod(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2099',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year = (int) $request->year;
        $month = (int) $request->month;

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        $period = EcommerceKpiPeriod::firstOrCreate(
            ['year' => $year, 'month' => $month],
            [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'open',
            ]
        );

        $this->kpiService->initializePeriodAssignments($period, Auth::id());

        return redirect()->route('ecommerce.kpi.index', ['year' => $year, 'month' => $month])
            ->with('success', "Periode KPI E-Commerce {$period->period_label} berhasil dibuka & diinisialisasi.");
    }

    /**
     * Sinkronkan ulang data actual sistem dari tabel transaksi.
     */
    public function sync($id)
    {
        $assignment = EcommerceKpiAssignment::with(['period', 'items'])->findOrFail($id);
        $this->kpiService->syncSystemActuals($assignment);

        return back()->with('success', "Aktual data sistem untuk {$assignment->user->name} berhasil disinkronkan.");
    }

    /**
     * Form Penilaian & Review Evaluator (Hybrid & Manual).
     */
    public function evaluate($id)
    {
        $assignment = EcommerceKpiAssignment::with(['period', 'user', 'evaluator', 'items.template'])->findOrFail($id);

        return view('pages.ecommerce.kpi.evaluate', compact('assignment'));
    }

    /**
     * Simpan Hasil Penilaian Evaluator.
     */
    public function saveEvaluation(Request $request, $id)
    {
        $assignment = EcommerceKpiAssignment::with('items')->findOrFail($id);

        $request->validate([
            'items' => 'required|array',
            'items.*.target' => 'required|numeric|min:0',
            'items.*.actual_final' => 'required|numeric|min:0',
            'items.*.weight' => 'required|numeric|min:0|max:100',
            'items.*.evaluator_notes' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,review,published',
        ]);

        // Validasi total bobot = 100%
        $totalWeight = collect($request->items)->sum('weight');
        if (abs($totalWeight - 100.00) > 0.01) {
            return back()->withInput()->with('error', "Total bobot harus tepat 100%. Total saat ini: {$totalWeight}%");
        }

        foreach ($request->items as $itemId => $data) {
            $item = EcommerceKpiAssignmentItem::where('assignment_id', $assignment->id)->findOrFail($itemId);

            $target = (float) $data['target'];
            $actualFinal = (float) $data['actual_final'];
            $weight = (float) $data['weight'];

            $achievementRate = $this->kpiService->calculateAchievementRate($target, $actualFinal);
            $score = $this->kpiService->calculateScore($achievementRate, $weight);

            $item->update([
                'target' => $target,
                'actual_final' => $actualFinal,
                'weight' => $weight,
                'achievement_rate' => $achievementRate,
                'score' => $score,
                'evaluator_notes' => $data['evaluator_notes'] ?? null,
            ]);
        }

        $assignment->update([
            'evaluator_id' => Auth::id(),
            'notes' => $request->notes,
            'status' => $request->status,
            'evaluated_at' => now(),
        ]);

        $this->kpiService->recalculateAssignmentTotals($assignment);

        if ($request->status === 'published') {
            $this->checkAndPublishPeriod($assignment->period);
        }

        return redirect()->route('ecommerce.kpi.index', [
            'year' => $assignment->period->year,
            'month' => $assignment->period->month
        ])->with('success', "Penilaian KPI untuk {$assignment->user->name} berhasil disimpan.");
    }

    /**
     * Publish KPI Assignment secara langsung.
     */
    public function publish($id)
    {
        $assignment = EcommerceKpiAssignment::with('period')->findOrFail($id);
        $assignment->update([
            'status' => 'published',
            'evaluator_id' => Auth::id(),
            'evaluated_at' => now(),
        ]);

        $this->checkAndPublishPeriod($assignment->period);

        return back()->with('success', "Rapor KPI {$assignment->user->name} berhasil di-publish.");
    }

    /**
     * Halaman Rapor KPI Khusus Employee E-Commerce.
     */
    public function myKpi(Request $request)
    {
        $user = Auth::user();

        // Cari semua assignment milik user yang login
        $assignments = EcommerceKpiAssignment::where('user_id', $user->id)
            ->with(['period', 'items', 'evaluator'])
            ->get()
            ->sortByDesc(fn ($a) => sprintf('%04d%02d', $a->period->year, $a->period->month));

        if ($assignments->isEmpty()) {
            // Cek jika periode bulan ini ada, auto-assign
            $currentPeriod = EcommerceKpiPeriod::where('year', date('Y'))
                ->where('month', date('n'))
                ->first();

            if ($currentPeriod) {
                $this->kpiService->initializePeriodAssignments($currentPeriod);
                $assignments = EcommerceKpiAssignment::where('user_id', $user->id)
                    ->with(['period', 'items', 'evaluator'])
                    ->get();
            }
        }

        $selectedAssignmentId = $request->assignment_id ?? $assignments->first()?->id;
        $selectedAssignment = $assignments->firstWhere('id', $selectedAssignmentId) ?? $assignments->first();

        return view('pages.ecommerce.kpi.my-kpi', compact('assignments', 'selectedAssignment'));
    }

    /**
     * Helper untuk cek jika seluruh assignment sudah published, ubah status periode.
     */
    protected function checkAndPublishPeriod(EcommerceKpiPeriod $period): void
    {
        $allPublished = $period->assignments()->where('status', '!=', 'published')->count() === 0;
        if ($allPublished && $period->assignments()->count() > 0) {
            $period->update([
                'status' => 'published',
                'published_at' => now(),
                'published_by' => Auth::id(),
            ]);
        }
    }
}
