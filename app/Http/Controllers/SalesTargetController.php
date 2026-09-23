<?php

namespace App\Http\Controllers;

use App\Models\EcommerceKpiPeriod;
use App\Models\EcommerceKpiTemplate;
use App\Models\SalesReports;
use App\Models\SalesTargetHistory;
use App\Models\Target;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SalesTargetController extends Controller
{
    public function index(Request $request)
    {
        $years = SalesReports::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        if ($years->isEmpty()) {
            $years = collect([date('Y')]);
        }
        $currentYear = (int) ($request->year ?? ($years->first() ?? date('Y')));

        // Inisialisasi awal jika tahun ini belum memiliki riwayat sama sekali di SalesTargetHistory
        $hasYearHistory = SalesTargetHistory::where('year', $currentYear)->exists();
        if (!$hasYearHistory) {
            $activeSales = User::where('role', 'Sales')->where('active', '1')->get();
            foreach ($activeSales as $u) {
                SalesTargetHistory::firstOrCreate(
                    ['user_id' => $u->id, 'year' => $currentYear],
                    [
                        'target_annual'    => 0,
                        'is_active_roster' => 1,
                        'sales_type'       => in_array($u->id, [16, 23]) ? 'ecommerce' : ($u->id == 4 ? 'crm' : 'field'),
                        'subtitle'         => $u->area ?? '',
                        'status'           => 'active',
                        'join_date'        => $u->date_in ?? date('Y-m-d'),
                        'set_by'           => Auth::id(),
                    ]
                );
            }
        }

        // Target & Roster records per sales untuk tahun yang dipilih
        $yearRecords = SalesTargetHistory::where('year', $currentYear)
            ->get()
            ->keyBy('user_id');

        $yearTargets = $yearRecords->pluck('target_annual', 'user_id');

        // Sales users yang ada dalam roster tahun yang dipilih
        $rosterUserIds = $yearRecords->keys();
        $salesUsers = User::whereIn('id', $rosterUserIds)
            ->get()
            ->sortBy(function ($u) use ($yearRecords) {
                $rec = $yearRecords[$u->id] ?? null;
                $isRoster = $rec ? (int)$rec->is_active_roster : ($u->active == '1' ? 1 : 0);
                return [$isRoster ? 0 : 1, $u->name];
            })
            ->values();

        // Semua user dengan role Sales (untuk tab Riwayat Tim & Turnover)
        $allSalesUsers = User::where('role', 'Sales')
            ->orderByRaw("active = '1' DESC")
            ->orderBy('name')
            ->get();

        // Semester records untuk aggregate target (S1 & S2)
        $semesterRecords = SalesReports::where('year', $currentYear)
            ->orderBy('semester')
            ->get()
            ->keyBy('semester');

        // Semua histori per sales (untuk tab riwayat tim & turnover)
        $allHistories = SalesTargetHistory::with(['sales', 'setBy'])
            ->orderBy('year', 'asc')
            ->get()
            ->groupBy('user_id');

        // Total target tim per tahun dari history (untuk hitung % kontribusi)
        $teamTargetByYear = SalesTargetHistory::selectRaw('year, SUM(target_annual) as total')
            ->groupBy('year')
            ->pluck('total', 'year')
            ->toArray();

        // Total dari sales_reports aggregate (fallback untuk tahun tanpa history per-sales)
        $reportAnnualByYear = SalesReports::selectRaw('year, SUM(target) as total')
            ->whereNotNull('target')
            ->groupBy('year')
            ->pluck('total', 'year')
            ->toArray();

        // Gabungkan: prefer history, fallback ke aggregate
        $annualByYear = [];
        foreach ($years as $y) {
            $annualByYear[$y] = $teamTargetByYear[$y] ?? ($reportAnnualByYear[$y] ?? 0);
        }

        $teamTargetThisYear = $annualByYear[$currentYear] ?? 0;

        // Hitung % perubahan vs tahun sebelumnya per tab
        $yearGrowth = [];
        foreach ($years as $y) {
            $prev = $annualByYear[$y - 1] ?? 0;
            $curr = $annualByYear[$y] ?? 0;
            if ($prev > 0 && $curr > 0) {
                $yearGrowth[$y] = round(($curr - $prev) / $prev * 100, 1);
            } else {
                $yearGrowth[$y] = null;
            }
        }

        // Semua user yang belum masuk ke dalam roster tahun ini (untuk pilihan Tambah Sales)
        $allExistingUsers = User::whereNotIn('id', $rosterUserIds)
            ->orderBy('name')
            ->get();

        // ── Data untuk tab KPI E-Commerce (menggantikan menu terpisah) ──
        $kpiYears = EcommerceKpiPeriod::select('year')->distinct()->orderByDesc('year')->pluck('year');
        if ($kpiYears->isEmpty()) {
            $kpiYears = collect([(int) date('Y')]);
        }
        $kpiCurrentYear = (int) ($request->kpi_year ?? date('Y'));
        $kpiCurrentMonth = (int) ($request->kpi_month ?? date('n'));

        $kpiPeriod = EcommerceKpiPeriod::where('year', $kpiCurrentYear)
            ->where('month', $kpiCurrentMonth)
            ->with(['assignments.user', 'assignments.evaluator', 'assignments.items'])
            ->first();

        $kpiAssignments = $kpiPeriod ? $kpiPeriod->assignments : collect();
        $kpiTemplates = EcommerceKpiTemplate::where('is_active', true)->orderBy('sort_order')->get();

        return view('pages.admin.sales-target', compact(
            'years', 'currentYear', 'salesUsers', 'allSalesUsers', 'allExistingUsers',
            'yearRecords', 'yearTargets', 'allHistories',
            'teamTargetThisYear', 'teamTargetByYear',
            'annualByYear', 'yearGrowth',
            'semesterRecords',
            'kpiYears', 'kpiCurrentYear', 'kpiCurrentMonth', 'kpiPeriod', 'kpiAssignments', 'kpiTemplates'
        ));
    }

    public function addSales(Request $request, $year)
    {
        $mode = $request->input('mode', 'existing');

        if ($mode === 'new') {
            $request->validate([
                'name'          => 'required|string|max:255',
                'email'         => 'required|email|unique:users,email',
                'password'      => 'required|string|min:6',
                'area'          => 'nullable|string|max:255',
                'phone'         => 'nullable|string|max:20',
                'sales_type'    => 'required|string|in:field,crm,ecommerce',
                'target_annual' => 'nullable|numeric|min:0',
                'join_date'     => 'nullable|date',
            ], [
                'name.required'     => 'Nama sales wajib diisi.',
                'email.required'    => 'Email wajib diisi.',
                'email.unique'      => 'Email sudah terdaftar untuk pengguna lain.',
                'password.required' => 'Password akun wajib diisi.',
                'password.min'      => 'Password minimal 6 karakter.',
            ]);

            $user = new User();
            $user->name     = trim($request->name);
            $user->email    = trim($request->email);
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $user->role     = 'Sales';
            $user->active   = '1';
            $user->area     = $request->area;
            $user->phone    = $request->phone ? (str_starts_with($request->phone, '+62') ? $request->phone : '+62' . ltrim($request->phone, '0')) : null;
            $user->date_in  = $request->join_date ?? date('Y-m-d');
            $user->image    = 'asset/profile/profile.jpg';
            $user->save();

            $userId = $user->id;
        } else {
            $request->validate([
                'user_id'       => 'required|exists:users,id',
                'sales_type'    => 'required|string|in:field,crm,ecommerce',
                'target_annual' => 'nullable|numeric|min:0',
                'subtitle'      => 'nullable|string|max:255',
            ], [
                'user_id.required' => 'Pilih sales yang ingin ditambahkan ke roster.',
            ]);

            $userId = $request->user_id;
            $user = User::find($userId);
            if ($user) {
                if ($user->role !== 'Sales') {
                    $user->role = 'Sales';
                }
                $user->active = '1';
                if ($request->filled('subtitle') && empty($user->area)) {
                    $user->area = $request->subtitle;
                }
                $user->save();
            }
        }

        $annual = (int) ($request->target_annual ?? 0);
        $type   = $request->sales_type ?? 'field';
        $sub    = $request->subtitle ?? ($request->area ?? ($user?->area ?? ''));

        SalesTargetHistory::updateOrCreate(
            ['user_id' => $userId, 'year' => $year],
            [
                'target_annual'    => $annual,
                'is_active_roster' => 1,
                'sales_type'       => $type,
                'subtitle'         => $sub,
                'status'           => 'active',
                'join_date'        => $request->join_date ?? ($user?->date_in ?? date('Y-m-d')),
                'set_by'           => Auth::id(),
            ]
        );

        // Sync target bulanan aktif jika tahun berjalan
        if ($year == date('Y') && $annual > 0) {
            Target::updateOrCreate(
                ['id_sales' => $userId],
                ['total' => intval($annual / 12)]
            );
        }

        // Auto-update aggregate semester di sales_reports
        $totalAnnual = SalesTargetHistory::where('year', $year)->sum('target_annual');
        if ($totalAnnual > 0) {
            $semTarget = intval($totalAnnual / 2);
            foreach (['1', '2'] as $sem) {
                SalesReports::updateOrCreate(
                    ['year' => $year, 'semester' => $sem],
                    ['target' => $semTarget]
                );
            }
        }

        Cache::forget("sales_leaderboard_rank_{$year}_" . date('n'));

        return redirect()->route('sales-target.index', ['year' => $year])
            ->with('success', "Sales ({$user->name}) berhasil ditambahkan ke Active Roster tahun {$year}.");
    }

    public function updateSales(Request $request, $year, $userId)
    {
        $user = User::findOrFail($userId);

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $userId,
            'sales_type'       => 'required|string|in:field,crm,ecommerce',
            'subtitle'         => 'nullable|string|max:255',
            'target_annual'    => 'nullable|numeric|min:0',
            'status'           => 'required|string|in:active,resigned,cuti,transferred',
            'phone'            => 'nullable|string|max:20',
            'password'         => 'nullable|string|min:6',
            'join_date'        => 'nullable|date',
            'resign_date'      => 'nullable|date',
            'notes'            => 'nullable|string',
        ], [
            'name.required'  => 'Nama sales wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan pengguna lain.',
        ]);

        // Update User info
        $user->name = trim($request->name);
        $user->email = trim($request->email);
        if ($request->filled('subtitle')) {
            $user->area = $request->subtitle;
        }
        if ($request->filled('phone')) {
            $user->phone = str_starts_with($request->phone, '+62') ? $request->phone : '+62' . ltrim($request->phone, '0');
        }
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
        $user->active = ($request->status === 'active') ? '1' : '0';
        if ($request->filled('join_date')) {
            $user->date_in = $request->join_date;
        }
        $user->save();

        $annual   = (int) ($request->target_annual ?? 0);
        $isRoster = $request->has('is_active_roster') ? 1 : 0;

        SalesTargetHistory::updateOrCreate(
            ['user_id' => $userId, 'year' => $year],
            [
                'target_annual'    => $annual,
                'is_active_roster' => $isRoster,
                'sales_type'       => $request->sales_type,
                'subtitle'         => $request->subtitle,
                'status'           => $request->status,
                'join_date'        => $request->join_date,
                'resign_date'      => $request->resign_date,
                'notes'            => $request->notes,
                'set_by'           => Auth::id(),
            ]
        );

        if ($year == date('Y')) {
            if ($annual > 0) {
                Target::updateOrCreate(
                    ['id_sales' => $userId],
                    ['total' => intval($annual / 12)]
                );
            }
        }

        $totalAnnual = SalesTargetHistory::where('year', $year)->sum('target_annual');
        if ($totalAnnual > 0) {
            $semTarget = intval($totalAnnual / 2);
            foreach (['1', '2'] as $sem) {
                SalesReports::updateOrCreate(
                    ['year' => $year, 'semester' => $sem],
                    ['target' => $semTarget]
                );
            }
        }

        Cache::forget("sales_leaderboard_rank_{$year}_" . date('n'));

        return redirect()->route('sales-target.index', ['year' => $year])
            ->with('success', "Data sales ({$user->name}) tahun {$year} berhasil diperbarui.");
    }

    public function removeSales(Request $request, $year, $userId)
    {
        $user = User::findOrFail($userId);

        // Hapus record target & roster tahun tersebut
        SalesTargetHistory::where('user_id', $userId)->where('year', $year)->delete();

        // Opsi nonaktifkan user secara global jika dipilih
        if ($request->input('deactivate_user') == '1') {
            $user->active = '0';
            $user->save();
        }

        // Hapus target bulanan jika dihapus dari tahun berjalan
        if ($year == date('Y')) {
            Target::where('id_sales', $userId)->delete();
        }

        // Re-sync aggregate semester
        $totalAnnual = SalesTargetHistory::where('year', $year)->sum('target_annual');
        $semTarget = $totalAnnual > 0 ? intval($totalAnnual / 2) : 0;
        foreach (['1', '2'] as $sem) {
            SalesReports::updateOrCreate(
                ['year' => $year, 'semester' => $sem],
                ['target' => $semTarget]
            );
        }

        Cache::forget("sales_leaderboard_rank_{$year}_" . date('n'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Sales {$user->name} berhasil dihapus dari roster tahun {$year}.",
            ]);
        }

        return redirect()->route('sales-target.index', ['year' => $year])
            ->with('success', "Sales ({$user->name}) berhasil dihapus dari roster tahun {$year}.");
    }

    public function saveYearTargets(Request $request, $year)
    {
        $request->validate([
            'targets'     => 'required|array',
            'targets.*'   => 'nullable|numeric|min:0',
            'sales_type'  => 'nullable|array',
            'is_roster'   => 'nullable|array',
        ]);

        $adminId = Auth::id();
        $isRosterInputs = $request->input('is_roster', []);
        $salesTypes     = $request->input('sales_type', []);
        $subtitles      = $request->input('subtitles', []);
        $displayNames   = $request->input('display_names', []);

        foreach ($request->targets as $userId => $annual) {
            $annual = (int) $annual;
            $isRoster = isset($isRosterInputs[$userId]) ? 1 : 0;
            $type = $salesTypes[$userId] ?? 'field';
            $subtitle = $subtitles[$userId] ?? null;
            $displayName = $displayNames[$userId] ?? null;

            SalesTargetHistory::updateOrCreate(
                ['user_id' => $userId, 'year' => $year],
                [
                    'target_annual'    => $annual,
                    'is_active_roster' => $isRoster,
                    'sales_type'       => $type,
                    'display_name'     => $displayName,
                    'subtitle'         => $subtitle,
                    'set_by'           => $adminId,
                ]
            );

            // Sync target bulanan aktif di tabel target (jika tahun berjalan)
            if ($year == date('Y') && $annual > 0) {
                Target::updateOrCreate(
                    ['id_sales' => $userId],
                    ['total' => intval($annual / 12)]
                );
            }
        }

        // Auto-update aggregate semester di sales_reports
        $totalAnnual = SalesTargetHistory::where('year', $year)->sum('target_annual');
        if ($totalAnnual > 0) {
            $semTarget = intval($totalAnnual / 2);
            foreach (['1', '2'] as $sem) {
                SalesReports::updateOrCreate(
                    ['year' => $year, 'semester' => $sem],
                    ['target' => $semTarget]
                );
            }
        }

        // Clear dashboard ranking & overview cache
        Cache::forget("sales_leaderboard_rank_{$year}_" . date('n'));

        return back()->with('success', "Target dan roster tim sales tahun {$year} berhasil disimpan.");
    }

    public function saveKpiConfig(Request $request, $year)
    {
        $request->validate([
            'kpi' => 'required|array',
        ]);

        $adminId = Auth::id();

        foreach ($request->kpi as $userId => $cfg) {
            $config = [
                'has_new_leads' => isset($cfg['has_new_leads']) && $cfg['has_new_leads'] == '1',
                'has_crm'       => isset($cfg['has_crm']) && $cfg['has_crm'] == '1',
                'target_calls'  => !empty($cfg['target_calls']) ? (int) $cfg['target_calls'] : null,
                'target_visits' => !empty($cfg['target_visits']) ? (int) $cfg['target_visits'] : null,
                'target_leads'  => !empty($cfg['target_leads']) ? (int) $cfg['target_leads'] : null,
                'notes'         => $cfg['notes'] ?? null,
            ];

            SalesTargetHistory::updateOrCreate(
                ['user_id' => $userId, 'year' => $year],
                [
                    'kpi_config' => $config,
                    'set_by'     => $adminId,
                ]
            );
        }

        return back()->with('success', "Pengaturan KPI sales tahun {$year} berhasil disimpan.");
    }

    public function saveRosterHistory(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'status'      => 'required|string|in:active,resigned,cuti,transferred',
            'join_date'   => 'nullable|date',
            'resign_date' => 'nullable|date',
            'notes'       => 'nullable|string',
        ]);

        $userId = $request->user_id;

        // Update di User table
        $user = User::find($userId);
        if ($user) {
            $user->active = ($request->status === 'active') ? '1' : '0';
            if ($request->filled('join_date')) {
                $user->date_in = $request->join_date;
            }
            $user->save();
        }

        // Update di semua SalesTargetHistory user ini
        SalesTargetHistory::where('user_id', $userId)->update([
            'status'      => $request->status,
            'join_date'   => $request->join_date,
            'resign_date' => $request->resign_date,
            'notes'       => $request->notes,
        ]);

        return back()->with('success', "Riwayat status tim sales ({$user->name}) berhasil diperbarui.");
    }

    public function toggleRoster(Request $request, $year)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_active' => 'required|boolean',
        ]);

        $record = SalesTargetHistory::updateOrCreate(
            ['user_id' => $request->user_id, 'year' => $year],
            [
                'is_active_roster' => $request->is_active,
                'set_by' => Auth::id(),
            ]
        );

        // Clear dashboard ranking cache
        Cache::forget("sales_leaderboard_rank_{$year}_" . date('n'));

        return response()->json([
            'success' => true,
            'message' => 'Status tampilan sales di Dashboard berhasil diubah.',
            'is_active' => $record->is_active_roster,
        ]);
    }

    public function saveAggregateTarget(Request $request, $year)
    {
        $request->validate([
            'target_annual' => 'required|numeric|min:1',
        ]);

        $annual    = (int) $request->target_annual;
        $semTarget = intval($annual / 2);

        foreach (['1', '2'] as $sem) {
            SalesReports::updateOrCreate(
                ['year' => $year, 'semester' => $sem],
                ['target' => $semTarget]
            );
        }

        return back()->with('success', "Target agregat tim tahun {$year} berhasil disimpan.");
    }

    public function addYear(Request $request)
    {
        $request->validate(['year' => 'required|integer|min:2020|max:2099']);
        $year = (int) $request->year;

        foreach (['1', '2'] as $sem) {
            SalesReports::firstOrCreate(['year' => $year, 'semester' => $sem]);
        }

        return redirect()->route('sales-target.index', ['year' => $year])
            ->with('success', "Tahun {$year} berhasil ditambahkan.");
    }
}

