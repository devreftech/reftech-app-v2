@extends('layouts.sales.app')
@section('title', 'Annual Budget - Finance Reftech')
@section('no-container') @endsection

@push('after-style')
<style>
    .budget-card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .budget-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
    }
    .dept-card {
        border-radius: 12px;
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
    }
    .dept-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.06);
    }
    .nav-tabs .nav-link {
        font-weight: 600;
        color: #64748b;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1.25rem;
    }
    .nav-tabs .nav-link.active {
        color: #4f46e5;
        border-bottom-color: #4f46e5;
        background: transparent;
    }
    .badge-soft-success {
        background-color: #dcfce7 !important;
        color: #15803d !important;
        border: 1px solid #86efac !important;
        font-weight: 700 !important;
        font-size: 0.78rem !important;
        padding: 0.35rem 0.65rem !important;
        letter-spacing: 0.2px;
        display: inline-flex;
        align-items: center;
    }
    .badge-soft-warning {
        background-color: #fef3c7 !important;
        color: #b45309 !important;
        border: 1px solid #fcd34d !important;
        font-weight: 700 !important;
        font-size: 0.78rem !important;
        padding: 0.35rem 0.65rem !important;
        letter-spacing: 0.2px;
        display: inline-flex;
        align-items: center;
    }
    .badge-soft-danger {
        background-color: #fee2e2 !important;
        color: #b91c1c !important;
        border: 1px solid #fca5a5 !important;
        font-weight: 700 !important;
        font-size: 0.78rem !important;
        padding: 0.35rem 0.65rem !important;
        letter-spacing: 0.2px;
        display: inline-flex;
        align-items: center;
    }
    .badge-soft-secondary {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 600 !important;
        font-size: 0.78rem !important;
        padding: 0.35rem 0.65rem !important;
        display: inline-flex;
        align-items: center;
    }
    .dark-style .badge-soft-success {
        background-color: rgba(34, 197, 94, 0.2) !important;
        color: #4ade80 !important;
        border: 1px solid rgba(74, 222, 128, 0.4) !important;
    }
    .dark-style .badge-soft-warning {
        background-color: rgba(245, 158, 11, 0.2) !important;
        color: #fbbf24 !important;
        border: 1px solid rgba(251, 191, 36, 0.4) !important;
    }
    .dark-style .badge-soft-danger {
        background-color: rgba(239, 68, 68, 0.2) !important;
        color: #f87171 !important;
        border: 1px solid rgba(248, 113, 113, 0.4) !important;
    }
    .dark-style .badge-soft-secondary {
        background-color: rgba(148, 163, 184, 0.15) !important;
        color: #cbd5e1 !important;
        border: 1px solid rgba(203, 213, 225, 0.3) !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance /</span> Annual Budget
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-chart-donut me-1"></i> Perencanaan plafon belanja tahunan, alokasi sub-budget per departemen, dan monitoring realisasi biaya operasional (Budget vs Actual)
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Year & Entity Selector Form --}}
            <form action="{{ route('finance.expense-budget.index') }}" method="GET" class="d-flex align-items-center gap-2">
                <select name="entity" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 150px;">
                    <option value="all" {{ $selectedEntity == 'all' ? 'selected' : '' }}>Semua Entitas (Konsolidasi)</option>
                    <option value="reftech" {{ $selectedEntity == 'reftech' ? 'selected' : '' }}>PT Reftech Jaya Optima</option>
                    <option value="kojisha" {{ $selectedEntity == 'kojisha' ? 'selected' : '' }}>PT Kojisha Innotiv</option>
                </select>
                <select name="year" class="form-select form-select-sm" onchange="this.form.submit()" style="min-width: 110px;">
                    @foreach ($allYears as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endforeach
                </select>
            </form>

            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#budgetModal">
                <i class="mdi mdi-tune-vertical me-1"></i> {{ $budget ? 'Atur / Ubah Annual Budget' : 'Buat Annual Budget' }}
            </button>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Nav Tabs: Monitoring Tahun Terpilih vs Sub-Departemen vs History Tahunan --}}
    <ul class="nav nav-tabs mb-4" id="budgetTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="monitoring-tab" data-bs-toggle="tab" data-bs-target="#monitoring-pane" type="button" role="tab">
                <i class="mdi mdi-chart-timeline-variant me-1"></i> Monitoring & Sub-Budget {{ $selectedYear }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="category-tab" data-bs-toggle="tab" data-bs-target="#category-pane" type="button" role="tab">
                <i class="mdi mdi-sitemap me-1"></i> Rincian Sub-Departemen & COA
                <span class="badge bg-label-secondary ms-1" style="font-size: 11px;">5 Divisi • {{ $categoryBreakdown->count() }} Akun</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab">
                <i class="mdi mdi-history me-1"></i> Riwayat Annual Budget
                <span class="badge bg-label-primary ms-1" style="font-size: 11px;">{{ count($historyYearsData) }} Tahun</span>
            </button>
        </li>
    </ul>

    <div class="tab-content p-0" id="budgetTabContent">
        {{-- ========================================================================= --}}
        {{-- TAB 1: MONITORING TAHUN TERPILIH --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade show active" id="monitoring-pane" role="tabpanel">
            {{-- Executive Summary KPI Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm budget-card h-100" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);">
                        <div class="card-body p-3 text-white">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-white-50 small fw-semibold text-uppercase" style="font-size: 11px;">Plafon Anggaran {{ $selectedYear }}</span>
                                <span class="avatar-initial rounded bg-white bg-opacity-25 p-2 text-white">
                                    <i class="mdi mdi-target-account fs-4"></i>
                                </span>
                            </div>
                            <h4 class="fw-bolder text-white mb-1">Rp {{ number_format($totalBudgetYear, 0, ',', '.') }}</h4>
                            <small class="text-white-50" style="font-size: 11px;">
                                {{ $budget ? 'Rata-rata: Rp ' . number_format($budget->monthly_budget, 0, ',', '.') . '/bln' : 'Belum ditentukan' }}
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm budget-card h-100 bg-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Realisasi Pengeluaran YTD</span>
                                <span class="avatar-initial rounded bg-label-danger p-2">
                                    <i class="mdi mdi-cash-minus fs-4"></i>
                                </span>
                            </div>
                            <h4 class="fw-bolder text-danger mb-1">Rp {{ number_format($totalActualYear, 0, ',', '.') }}</h4>
                            <small class="text-muted" style="font-size: 11px;">Total aktual tercatat s/d bulan ini</small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm budget-card h-100 bg-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Sisa Anggaran Belanja</span>
                                <span class="avatar-initial rounded bg-label-{{ $remainingBudget >= 0 ? 'success' : 'danger' }} p-2">
                                    <i class="mdi mdi-scale-balance fs-4"></i>
                                </span>
                            </div>
                            <h4 class="fw-bolder text-{{ $remainingBudget >= 0 ? 'success' : 'danger' }} mb-1">
                                Rp {{ number_format($remainingBudget, 0, ',', '.') }}
                            </h4>
                            <small class="text-muted" style="font-size: 11px;">
                                @if($totalBudgetYear > 0)
                                    {{ $remainingBudget >= 0 ? 'Sisa kuota anggaran aman' : 'Peringatan: Overbudget' }}
                                @else
                                    Plafon belum diset
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm budget-card h-100 bg-white">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Tingkat Pemakaian Anggaran</span>
                                <span class="badge bg-label-{{ $overallPctUsed > 100 ? 'danger' : ($overallPctUsed >= 85 ? 'warning' : 'info') }} rounded-pill">
                                    {{ $overallPctUsed }}%
                                </span>
                            </div>
                            <div class="progress mb-2" style="height: 8px;">
                                <div class="progress-bar bg-{{ $overallPctUsed > 100 ? 'danger' : ($overallPctUsed >= 85 ? 'warning' : 'primary') }}" role="progressbar" style="width: {{ min(100, $overallPctUsed) }}%" aria-valuenow="{{ $overallPctUsed }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted" style="font-size: 11px;">
                                {{ $overallPctUsed > 100 ? 'Pengeluaran melebihi anggaran tahunan' : 'Realisasi kumulatif terhadap target' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ALOKASI SUB-BUDGET PER DEPARTEMEN / DIVISI --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="card-title mb-1 fw-bold text-dark">
                            <i class="mdi mdi-sitemap text-primary me-1"></i> Alokasi & Realisasi Sub-Budget per Departemen ({{ $selectedYear }})
                        </h6>
                        <small class="text-muted">Plafon anggaran spesifik dan monitoring serapan pengeluaran berdasarkan 5 divisi kerja perusahaan</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-xs btn-outline-primary btn-open-dept-budget" data-bs-toggle="modal" data-bs-target="#budgetModal">
                            <i class="mdi mdi-tune-vertical me-1"></i> Atur Plafon Divisi
                        </button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3">
                        @foreach($departmentStats as $dKey => $dStat)
                            <div class="col-12 col-md-6 col-xl">
                                <div class="dept-card h-100 p-3 bg-white d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-start justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar-initial rounded-circle text-white p-2 d-inline-flex align-items-center justify-content-center" style="background-color: {{ $dStat['color'] }}; width: 34px; height: 34px;">
                                                    <i class="mdi {{ $dStat['icon'] }} fs-5"></i>
                                                </span>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 12.5px;">{{ $dStat['name'] }}</h6>
                                                    <small class="text-muted" style="font-size: 10px;">{{ count($dStat['accounts']) }} Akun COA</small>
                                                </div>
                                            </div>
                                            @if($dStat['status'] === 'safe')
                                                <span class="badge badge-soft-success rounded-pill" style="font-size: 10px;">Aman</span>
                                            @elseif($dStat['status'] === 'warning')
                                                <span class="badge badge-soft-warning rounded-pill" style="font-size: 10px;">Siaga</span>
                                            @elseif($dStat['status'] === 'over')
                                                <span class="badge badge-soft-danger rounded-pill" style="font-size: 10px;">Over</span>
                                            @elseif($dStat['status'] === 'no_budget')
                                                <span class="badge badge-soft-secondary rounded-pill" style="font-size: 10px;">Tanpa Plafon</span>
                                            @else
                                                <span class="badge badge-soft-secondary rounded-pill" style="font-size: 10px;">Belum Diset</span>
                                            @endif
                                        </div>

                                        <p class="text-muted mb-3" style="font-size: 10.5px; line-height: 1.35; min-height: 28px;">
                                            {{ $dStat['desc'] }}
                                        </p>

                                        <div class="mb-2">
                                            <div class="d-flex justify-content-between align-items-baseline mb-1">
                                                <small class="text-muted" style="font-size: 11px;">Realisasi:</small>
                                                <span class="fw-bold text-danger" style="font-size: 13px;">Rp {{ number_format($dStat['actual'], 0, ',', '.') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-baseline mb-2">
                                                <small class="text-muted" style="font-size: 11px;">Plafon:</small>
                                                <span class="fw-semibold text-dark" style="font-size: 12px;">
                                                    {{ $dStat['budget_annual'] > 0 ? 'Rp ' . number_format($dStat['budget_annual'], 0, ',', '.') : 'Belum Ditentukan' }}
                                                </span>
                                            </div>

                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar" role="progressbar"
                                                     style="width: {{ min(100, $dStat['pct_used']) }}%; background-color: {{ $dStat['color'] }};"
                                                     aria-valuenow="{{ $dStat['pct_used'] }}" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-2 border-top mt-2 d-flex align-items-center justify-content-between">
                                        <small class="text-muted" style="font-size: 10.5px;">
                                            @if($dStat['budget_annual'] > 0)
                                                Sisa: <strong class="text-{{ $dStat['variance'] >= 0 ? 'success' : 'danger' }}">Rp {{ number_format($dStat['variance'], 0, ',', '.') }}</strong>
                                            @else
                                                Kontribusi: <strong>{{ $dStat['pct_of_total'] }}%</strong>
                                            @endif
                                        </small>
                                        <span class="badge bg-label-secondary" style="font-size: 10px;">
                                            {{ $dStat['budget_annual'] > 0 ? $dStat['pct_used'] . '%' : $dStat['tx_count'] . ' tx' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- VISUAL CHARTS ROW: Tren Bulanan (Area/Line) & Donut Kategori --}}
            <div class="row g-3 mb-4">
                <div class="col-12 col-xl-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <h6 class="card-title mb-1 fw-bold text-dark">
                                    <i class="mdi mdi-chart-areaspline text-primary me-1"></i> Tren Realisasi Belanja vs Plafon Anggaran ({{ $selectedYear }})
                                </h6>
                                <small class="text-muted">Perbandingan serapan aktual bulanan terhadap batas target plafon</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-label-secondary text-uppercase">{{ $selectedEntity }}</span>
                            </div>
                        </div>
                        <div class="card-body pb-1">
                            <div id="budgetMonthlyTrendChart" style="min-height: 290px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-1 fw-bold text-dark">
                                    <i class="mdi mdi-chart-arc text-primary me-1"></i> Proporsi Kategori Biaya
                                </h6>
                                <small class="text-muted">Distribusi belanja COA terbesar</small>
                            </div>
                            <span class="badge bg-label-info">{{ $categoryBreakdown->count() }} Akun</span>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-center">
                            @if($categoryBreakdown->count() > 0)
                                <div id="budgetCategoryDonutChart" style="min-height: 250px;"></div>
                            @else
                                <div class="text-center py-5 text-muted">
                                    <i class="mdi mdi-chart-donut text-muted fs-1 d-block mb-1"></i>
                                    Belum ada data pengeluaran di tahun {{ $selectedYear }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Monthly Comparison Table --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-1 fw-bold text-dark">
                            <i class="mdi mdi-table-clock text-primary me-1"></i> Rincian Realisasi vs Plafon Anggaran Bulanan ({{ $selectedYear }})
                        </h5>
                        <p class="text-muted small mb-0">
                            Entitas: <span class="badge bg-label-secondary text-uppercase">{{ $selectedEntity }}</span> &bull; Klik <strong>"Rincian Transaksi"</strong> untuk melihat daftar voucher belanja di bulan terkait
                        </p>
                    </div>
                    @if(!$budget)
                        <span class="badge bg-label-warning px-3 py-1">Plafon Anggaran Tahun {{ $selectedYear }} Belum Ditetapkan</span>
                    @endif
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Bulan</th>
                                <th class="text-end">Plafon (Budget)</th>
                                <th class="text-end">Realisasi (Actual)</th>
                                <th class="text-end">Selisih (+/- Variance)</th>
                                <th class="text-center">% Terpakai</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 140px;">Rincian Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($monthlyRows as $row)
                                <tr class="{{ $row['is_current'] ? 'table-primary bg-opacity-25' : '' }}">
                                    <td class="text-center fw-semibold text-muted">{{ str_pad($row['month_num'], 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <strong>{{ $row['month_name'] }} {{ $selectedYear }}</strong>
                                        @if($row['is_current'])
                                            <span class="badge bg-primary text-white ms-1" style="font-size: 10px;">Bulan Ini</span>
                                        @elseif($row['is_future'])
                                            <span class="badge bg-label-secondary ms-1" style="font-size: 10px;">Mendatang</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold text-dark">
                                        Rp {{ number_format($row['budget'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-semibold text-danger">
                                        Rp {{ number_format($row['actual'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-end fw-semibold text-{{ $row['variance'] >= 0 ? 'success' : 'danger' }}">
                                        {{ $row['variance'] >= 0 ? '+' : '' }}Rp {{ number_format($row['variance'], 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @if($row['budget'] > 0)
                                            <span class="fw-bold text-{{ $row['pct_used'] > 100 ? 'danger' : ($row['pct_used'] >= 85 ? 'warning' : 'success') }}">
                                                {{ $row['pct_used'] }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($row['status'] === 'safe')
                                            <span class="badge badge-soft-success rounded-pill">
                                                <i class="mdi mdi-check-circle me-1"></i>Terkendali / Hemat
                                            </span>
                                        @elseif($row['status'] === 'warning')
                                            <span class="badge badge-soft-warning rounded-pill">
                                                <i class="mdi mdi-alert-circle me-1"></i>Waspada (&ge;85%)
                                            </span>
                                        @elseif($row['status'] === 'over')
                                            <span class="badge badge-soft-danger rounded-pill">
                                                <i class="mdi mdi-alert-octagon me-1"></i>Overbudget
                                            </span>
                                        @elseif($row['status'] === 'no_budget')
                                            <span class="badge badge-soft-secondary rounded-pill">
                                                <i class="mdi mdi-minus-circle-outline me-1"></i>Tanpa Budget
                                            </span>
                                        @else
                                            <span class="badge badge-soft-secondary rounded-pill">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($row['actual'] > 0)
                                            <button type="button" class="btn btn-xs btn-outline-primary px-2 py-1 view-monthly-tx"
                                                    data-year="{{ $selectedYear }}"
                                                    data-month="{{ $row['month_num'] }}"
                                                    data-monthname="{{ $row['month_name'] }}"
                                                    data-entity="{{ $selectedEntity }}">
                                                <i class="mdi mdi-format-list-bulleted me-1"></i> Rincian ({{ $row['tx_count'] }})
                                            </button>
                                        @else
                                            <span class="text-muted small">0 Transaksi</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold border-top">
                            <tr>
                                <td colspan="2" class="text-center">TOTAL KUMULATIF {{ $selectedYear }}</td>
                                <td class="text-end text-dark">Rp {{ number_format($totalBudgetYear, 0, ',', '.') }}</td>
                                <td class="text-end text-danger">Rp {{ number_format($totalActualYear, 0, ',', '.') }}</td>
                                <td class="text-end text-{{ $remainingBudget >= 0 ? 'success' : 'danger' }}">
                                    {{ $remainingBudget >= 0 ? '+' : '' }}Rp {{ number_format($remainingBudget, 0, ',', '.') }}
                                </td>
                                <td class="text-center">{{ $overallPctUsed }}%</td>
                                <td class="text-center">
                                    @if($totalBudgetYear > 0)
                                        <span class="badge bg-{{ $overallPctUsed > 100 ? 'danger' : 'success' }}">
                                            {{ $overallPctUsed > 100 ? 'Overbudget' : 'Aman' }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB 2: RINCIAN SUB-DEPARTEMEN & KATEGORI BIAYA (COA) --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade" id="category-pane" role="tabpanel">
            {{-- Departmental Accordion / Breakdown Cards --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-1 fw-bold text-dark">
                            <i class="mdi mdi-sitemap text-primary me-1"></i> Rincian Belanja Berdasarkan 5 Sub-Departemen ({{ $selectedYear }})
                        </h5>
                        <p class="text-muted small mb-0">
                            Distribusi alokasi plafon dan pemetaan akun beban operasional (COA) untuk masing-masing divisi perusahaan
                        </p>
                    </div>
                    <span class="badge bg-label-primary px-3 py-1">5 Divisi Terdaftar</span>
                </div>
                <div class="card-body p-3">
                    <div class="accordion" id="accordionDepartments">
                        @foreach($departmentStats as $dKey => $dStat)
                            <div class="accordion-item border mb-3 rounded overflow-hidden">
                                <h2 class="accordion-header" id="headingDept{{ $loop->iteration }}">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} bg-light py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDept{{ $loop->iteration }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                        <div class="d-flex align-items-center justify-content-between w-100 me-3 flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar-initial rounded-circle text-white p-2 d-inline-flex align-items-center justify-content-center" style="background-color: {{ $dStat['color'] }}; width: 32px; height: 32px;">
                                                    <i class="mdi {{ $dStat['icon'] }} fs-5"></i>
                                                </span>
                                                <div>
                                                    <strong class="text-dark">{{ $dStat['name'] }}</strong>
                                                    <span class="badge bg-label-secondary ms-2" style="font-size: 10.5px;">{{ count($dStat['accounts']) }} Akun Terhubung</span>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="text-end">
                                                    <small class="text-muted d-block" style="font-size: 10px;">Plafon / Realisasi:</small>
                                                    <span class="small fw-semibold text-dark">Rp {{ number_format($dStat['budget_annual'], 0, ',', '.') }}</span>
                                                    /
                                                    <span class="small fw-bold text-danger">Rp {{ number_format($dStat['actual'], 0, ',', '.') }}</span>
                                                </div>
                                                <div>
                                                    @if($dStat['status'] === 'safe')
                                                        <span class="badge badge-soft-success rounded-pill">Aman ({{ $dStat['pct_used'] }}%)</span>
                                                    @elseif($dStat['status'] === 'warning')
                                                        <span class="badge badge-soft-warning rounded-pill">Siaga ({{ $dStat['pct_used'] }}%)</span>
                                                    @elseif($dStat['status'] === 'over')
                                                        <span class="badge badge-soft-danger rounded-pill">Over ({{ $dStat['pct_used'] }}%)</span>
                                                    @elseif($dStat['status'] === 'no_budget')
                                                        <span class="badge badge-soft-secondary rounded-pill">Tanpa Plafon</span>
                                                    @else
                                                        <span class="badge badge-soft-secondary rounded-pill">Belum Diset</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapseDept{{ $loop->iteration }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#accordionDepartments">
                                    <div class="accordion-body p-0">
                                        <div class="p-3 bg-white border-bottom">
                                            <div class="row align-items-center">
                                                <div class="col-md-7">
                                                    <p class="text-muted small mb-0"><i class="mdi mdi-information-outline me-1"></i> {{ $dStat['desc'] }}</p>
                                                </div>
                                                <div class="col-md-5 text-md-end mt-2 mt-md-0">
                                                    <span class="small text-muted me-2">Sisa Kuota:</span>
                                                    <strong class="text-{{ $dStat['variance'] >= 0 ? 'success' : 'danger' }}">Rp {{ number_format($dStat['variance'], 0, ',', '.') }}</strong>
                                                    <span class="badge bg-label-info ms-2">Kontribusi Belanja: {{ $dStat['pct_of_total'] }}%</span>
                                                </div>
                                            </div>
                                        </div>

                                        @if(count($dStat['accounts']) > 0)
                                            <div class="table-responsive text-nowrap">
                                                <table class="table table-hover table-sm align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th style="width: 120px;">Kode COA</th>
                                                            <th>Nama Akun Beban</th>
                                                            <th class="text-center" style="width: 100px;">Transaksi</th>
                                                            <th class="text-end" style="width: 180px;">Total Realisasi (Rp)</th>
                                                            <th style="width: 180px;">% Thdp Total Perusahaan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($dStat['accounts'] as $acc)
                                                            <tr>
                                                                <td>
                                                                    <span class="badge bg-label-secondary font-monospace">{{ $acc->account_code ?? '-' }}</span>
                                                                </td>
                                                                <td>
                                                                    <span class="fw-semibold text-dark">{{ $acc->account_name }}</span>
                                                                </td>
                                                                <td class="text-center">
                                                                    <span class="badge rounded-pill bg-label-info" style="font-size: 10.5px;">{{ $acc->tx_count }} tx</span>
                                                                </td>
                                                                <td class="text-end fw-bold text-danger">
                                                                    Rp {{ number_format($acc->total_amount, 0, ',', '.') }}
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center gap-2">
                                                                        <div class="progress flex-grow-1" style="height: 5px;">
                                                                            <div class="progress-bar" role="progressbar" style="width: {{ $acc->pct_of_total }}%; background-color: {{ $dStat['color'] }};"></div>
                                                                        </div>
                                                                        <small class="text-muted" style="min-width: 38px;">{{ $acc->pct_of_total }}%</small>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="text-center py-3 text-muted small">
                                                <i class="mdi mdi-information-outline me-1"></i> Belum ada transaksi tercatat untuk pos akun di departemen ini pada tahun {{ $selectedYear }}.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Full Flat COA Table --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-1 fw-bold text-dark">
                            <i class="mdi mdi-format-list-numbered text-primary me-1"></i> Master Rekapitulasi Beban per Akun COA ({{ $selectedYear }})
                        </h5>
                        <p class="text-muted small mb-0">
                            Urutan beban terbesar (pareto) beserta pemetaan divisi kerja terkait
                        </p>
                    </div>
                    <span class="badge bg-label-primary px-3 py-1">{{ $categoryBreakdown->count() }} Akun Terpakai</span>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 120px;">Kode Akun</th>
                                <th>Nama Akun Biaya</th>
                                <th>Divisi / Sub-Departemen</th>
                                <th class="text-center" style="width: 100px;">Transaksi</th>
                                <th class="text-end" style="width: 180px;">Total Beban (Rp)</th>
                                <th style="width: 200px;">Proporsi Belanja (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categoryBreakdown as $cat)
                                <tr>
                                    <td>
                                        <span class="badge bg-label-secondary font-monospace">{{ $cat->account_code ?? '-' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $cat->account_name }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $deptInfo = $departmentDefs[$cat->dept_key] ?? null;
                                        @endphp
                                        @if($deptInfo)
                                            <span class="badge" style="background-color: {{ $deptInfo['color'] }}20; color: {{ $deptInfo['color'] }}; border: 1px solid {{ $deptInfo['color'] }}40;">
                                                <i class="mdi {{ $deptInfo['icon'] }} me-1"></i> {{ $deptInfo['short'] }}
                                            </span>
                                        @else
                                            <span class="badge bg-label-secondary">Operasional</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill bg-label-info">{{ $cat->tx_count }} tx</span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">
                                        Rp {{ number_format($cat->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $cat->pct_of_total }}%"></div>
                                            </div>
                                            <small class="fw-semibold text-muted" style="min-width: 40px;">{{ $cat->pct_of_total }}%</small>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="mdi mdi-information-outline fs-3 d-block mb-1"></i>
                                        Belum ada data transaksi pengeluaran tercatat untuk tahun {{ $selectedYear }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($categoryBreakdown->count() > 0)
                            <tfoot class="table-light fw-bold border-top">
                                <tr>
                                    <td colspan="4" class="text-center">TOTAL PENGELUARAN TERCATAT</td>
                                    <td class="text-end text-danger">Rp {{ number_format($totalActualYear, 0, ',', '.') }}</td>
                                    <td>100%</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- TAB 3: RIWAYAT BUDGET TAHUNAN (MULTI-YEAR HISTORY) --}}
        {{-- ========================================================================= --}}
        <div class="tab-pane fade" id="history-pane" role="tabpanel">
            {{-- VISUAL CHART: Komparasi Multi-Tahun Bar Chart --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1 fw-bold text-dark">
                            <i class="mdi mdi-chart-bar text-primary me-1"></i> Grafik Komparasi Anggaran Antar Tahun
                        </h6>
                        <small class="text-muted">Perbandingan Plafon Anggaran vs Realisasi Pengeluaran Tahunan</small>
                    </div>
                </div>
                <div class="card-body pb-1">
                    <div id="budgetMultiYearBarChart" style="min-height: 280px;"></div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-1 fw-bold text-dark">
                            <i class="mdi mdi-history text-primary me-1"></i> Riwayat &amp; Evaluasi Anggaran Multi-Tahun
                        </h5>
                        <p class="text-muted small mb-0">
                            Perbandingan historis target plafon, realisasi belanja, dan tingkat penyerapan anggaran dari tahun ke tahun
                        </p>
                    </div>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 80px;">Tahun</th>
                                <th>Entitas</th>
                                <th class="text-end">Plafon Anggaran</th>
                                <th class="text-end">Realisasi Pengeluaran</th>
                                <th class="text-end">Selisih (+/-)</th>
                                <th class="text-center">Penyerapan (%)</th>
                                <th class="text-center">Status</th>
                                <th>Catatan / Kebijakan</th>
                                <th>Ditetapkan Oleh</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historyYearsData as $hist)
                                <tr class="{{ $hist['is_selected'] ? 'table-primary bg-opacity-25' : '' }}">
                                    <td class="text-center">
                                        <strong class="fs-6 text-dark">{{ $hist['year'] }}</strong>
                                        @if($hist['is_selected'])
                                            <div class="badge bg-primary text-white" style="font-size: 9px;">Aktif</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-label-secondary text-uppercase">{{ $hist['entity'] }}</span>
                                    </td>
                                    <td class="text-end fw-semibold text-dark">
                                        @if($hist['ceiling'] > 0)
                                            Rp {{ number_format($hist['ceiling'], 0, ',', '.') }}
                                        @else
                                            <span class="text-muted fst-italic">Belum diset</span>
                                        @endif
                                    </td>
                                    <td class="text-end fw-semibold text-danger">
                                        Rp {{ number_format($hist['actual'], 0, ',', '.') }}
                                        <div class="small text-muted" style="font-size: 11px;">({{ $hist['tx_count'] }} transaksi)</div>
                                    </td>
                                    <td class="text-end fw-semibold text-{{ $hist['variance'] >= 0 ? 'success' : 'danger' }}">
                                        @if($hist['ceiling'] > 0)
                                            {{ $hist['variance'] >= 0 ? '+' : '' }}Rp {{ number_format($hist['variance'], 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($hist['ceiling'] > 0)
                                            <span class="fw-bold text-{{ $hist['pct_used'] > 100 ? 'danger' : ($hist['pct_used'] >= 85 ? 'warning' : 'success') }}">
                                                {{ $hist['pct_used'] }}%
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($hist['status'] === 'safe')
                                            <span class="badge badge-soft-success rounded-pill">
                                                <i class="mdi mdi-check-circle me-1"></i>Terkendali
                                            </span>
                                        @elseif($hist['status'] === 'warning')
                                            <span class="badge badge-soft-warning rounded-pill">
                                                <i class="mdi mdi-alert-circle me-1"></i>Waspada
                                            </span>
                                        @elseif($hist['status'] === 'over')
                                            <span class="badge badge-soft-danger rounded-pill">
                                                <i class="mdi mdi-alert-octagon me-1"></i>Overbudget
                                            </span>
                                        @elseif($hist['status'] === 'no_budget')
                                            <span class="badge badge-soft-secondary rounded-pill">
                                                <i class="mdi mdi-minus-circle-outline me-1"></i>Tanpa Budget
                                            </span>
                                        @else
                                            <span class="badge badge-soft-secondary rounded-pill">Kosong</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block small text-muted" style="max-width: 180px;" title="{{ $hist['notes'] ?? '-' }}">
                                            {{ $hist['notes'] ?: '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold text-dark">{{ $hist['creator_name'] }}</div>
                                        @if($hist['updated_at'])
                                            <div class="text-muted" style="font-size: 10px;">{{ \Carbon\Carbon::parse($hist['updated_at'])->format('d/m/Y H:i') }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(!$hist['is_selected'])
                                            <a href="{{ route('finance.expense-budget.index', ['year' => $hist['year'], 'entity' => $selectedEntity]) }}" class="btn btn-xs btn-outline-primary px-2 py-1" title="Buka monitoring tahun {{ $hist['year'] }}">
                                                <i class="mdi mdi-arrow-right me-1"></i> Buka
                                            </a>
                                        @else
                                            <span class="badge bg-label-primary px-2 py-1">Sedang Dibuka</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL DRILLDOWN TRANSAKSI BULANAN --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="monthlyDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-light border-bottom">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-0" id="modalMonthTitle">
                        <i class="mdi mdi-format-list-bulleted text-primary me-1"></i> Rincian Pengeluaran Bulan
                    </h5>
                    <small class="text-muted" id="modalMonthSubtitle">Memuat data...</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="modalLoadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2 small">Mengambil rincian transaksi...</p>
                </div>
                <div id="modalTxContainer" class="d-none">
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="badge bg-primary fs-6 px-3 py-1" id="modalTotalBadge">Total: Rp 0</span>
                        <span class="text-muted small" id="modalCountBadge">0 Transaksi tercatat</span>
                    </div>
                    <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 100px;">Tanggal</th>
                                    <th>No. Expense / Invoice</th>
                                    <th>Akun Perkiraan (COA)</th>
                                    <th>Keterangan / Memo</th>
                                    <th>Bank / Entitas</th>
                                    <th class="text-end" style="width: 140px;">Nominal (Rp)</th>
                                </tr>
                            </thead>
                            <tbody id="modalTxTableBody">
                                {{-- Rows populated via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="modalEmptyState" class="d-none text-center py-5 text-muted">
                    <i class="mdi mdi-alert-circle-outline fs-2 d-block mb-1"></i>
                    Tidak ada transaksi pengeluaran yang ditemukan pada bulan ini.
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <a href="{{ route('expense.index') }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                    <i class="mdi mdi-open-in-new me-1"></i> Buka Modul Expense Utama
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================================= --}}
{{-- MODAL ATUR / UBAH PLAFON ANNUAL BUDGET & SUB-DEPARTEMEN --}}
{{-- ========================================================================= --}}
<div class="modal fade" id="budgetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('finance.expense-budget.store') }}" method="POST" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header bg-light border-bottom">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="mdi mdi-tune-vertical text-primary me-1"></i> Pengaturan Annual Budget &amp; Sub-Departemen
                    </h5>
                    <small class="text-muted">Kelola plafon umum tahunan serta target batas belanja per divisi kerja</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Modal Nav Pills --}}
                <ul class="nav nav-pills nav-fill mb-3 p-1 bg-light rounded" id="modalBudgetTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active py-2 fw-semibold" id="modal-main-tab" data-bs-toggle="pill" data-bs-target="#modal-main-pane" type="button" role="tab">
                            <i class="mdi mdi-calculator me-1"></i> 1. Plafon Utama &amp; Periode
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link py-2 fw-semibold" id="modal-depts-tab" data-bs-toggle="pill" data-bs-target="#modal-depts-pane" type="button" role="tab">
                            <i class="mdi mdi-sitemap me-1"></i> 2. Alokasi 5 Sub-Departemen
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-0" id="modalBudgetTabContent">
                    {{-- PANE 1: PLAFON UTAMA & PERIODE --}}
                    <div class="tab-pane fade show active" id="modal-main-pane" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tahun Anggaran <span class="text-danger">*</span></label>
                                <input type="number" name="year" class="form-control" value="{{ $selectedYear }}" min="2020" max="2050" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Entitas Bisnis <span class="text-danger">*</span></label>
                                <select name="entity" class="form-select" required>
                                    <option value="all" {{ $selectedEntity == 'all' ? 'selected' : '' }}>Semua Entitas (Konsolidasi)</option>
                                    <option value="reftech" {{ $selectedEntity == 'reftech' ? 'selected' : '' }}>PT Reftech Jaya Optima</option>
                                    <option value="kojisha" {{ $selectedEntity == 'kojisha' ? 'selected' : '' }}>PT Kojisha Innotiv Indonesia</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Plafon Anggaran Tahunan (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="annual_budget" id="annual_budget_input" class="form-control rupiah-mask" value="{{ $budget && $budget->annual_budget ? number_format($budget->annual_budget, 0, ',', '.') : '' }}" placeholder="Contoh: 600.000.000">
                                </div>
                                <small class="text-muted">Total pagu tahunan untuk seluruh divisi perusahaan.</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Plafon Rata-rata Bulanan (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="monthly_budget" id="monthly_budget_input" class="form-control rupiah-mask" value="{{ $budget && $budget->monthly_budget ? number_format($budget->monthly_budget, 0, ',', '.') : '' }}" placeholder="Contoh: 50.000.000">
                                </div>
                                <small class="text-muted">Otomatis dibagi rata 12 bulan jika dikosongkan.</small>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="use_custom_monthly" value="1" id="customMonthlyToggle" {{ ($budget && ($budget->m1 || $budget->m2)) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="customMonthlyToggle">
                                        Atur nominal spesifik per bulan (Custom Bulan 1 - 12)
                                    </label>
                                </div>
                            </div>

                            {{-- Custom Monthly Inputs --}}
                            <div class="col-12 {{ ($budget && ($budget->m1 || $budget->m2)) ? '' : 'd-none' }}" id="customMonthlyContainer">
                                <div class="p-3 bg-light rounded border">
                                    <h6 class="fw-bold mb-2 small text-uppercase text-muted">Rincian Plafon Per Bulan:</h6>
                                    <div class="row g-2">
                                        @for($i = 1; $i <= 12; $i++)
                                            @php $mCol = 'm' . $i; @endphp
                                            <div class="col-6 col-md-4 col-lg-3">
                                                <label class="form-label small mb-1">{{ \Carbon\Carbon::create(2026, $i, 1)->translatedFormat('M') }}</label>
                                                <input type="text" inputmode="numeric" name="m{{ $i }}" class="form-control form-control-sm rupiah-mask" value="{{ $budget && $budget->$mCol ? number_format($budget->$mCol, 0, ',', '.') : '' }}" placeholder="0">
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan / Kebijakan Anggaran</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Catatan batas toleransi, pedoman belanja, atau instruksi manajemen...">{{ $budget ? $budget->notes : '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- PANE 2: ALOKASI SUB-DEPARTEMEN --}}
                    <div class="tab-pane fade" id="modal-depts-pane" role="tabpanel">
                        <div class="alert alert-primary bg-primary bg-opacity-10 border border-primary border-opacity-25 py-2 px-3 small mb-3">
                            <i class="mdi mdi-information-outline me-1"></i>
                            Tentukan alokasi plafon untuk 5 divisi kerja Reftech. Anda dapat mengisi plafon tahunan dan plafon bulanan akan otomatis terhitung rata.
                        </div>

                        <div class="dept-inputs-wrapper" style="max-height: 400px; overflow-y: auto; padding-right: 4px;">
                            @foreach($departmentDefs as $dKey => $dDef)
                                @php
                                    $currDept = $budget ? $budget->getDepartmentBudget($dKey) : ['annual' => 0, 'monthly' => 0];
                                @endphp
                                <div class="card border mb-3 shadow-none bg-light">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar-initial rounded-circle text-white p-2 d-inline-flex align-items-center justify-content-center" style="background-color: {{ $dDef['color'] }}; width: 30px; height: 30px;">
                                                    <i class="mdi {{ $dDef['icon'] }} fs-5"></i>
                                                </span>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 13px;">{{ $dDef['name'] }}</h6>
                                                    <small class="text-muted" style="font-size: 11px;">{{ $dDef['desc'] }}</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row g-2 mt-1">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold mb-1">Plafon Tahunan (Rp)</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" inputmode="numeric" name="dept_budget[{{ $dKey }}][annual]"
                                                           class="form-control rupiah-mask dept-annual-field"
                                                           id="dept_ann_{{ $dKey }}"
                                                           data-dept="{{ $dKey }}"
                                                           value="{{ $currDept['annual'] > 0 ? number_format($currDept['annual'], 0, ',', '.') : '' }}"
                                                           placeholder="0">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold mb-1">Plafon Bulanan (Rp)</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" inputmode="numeric" name="dept_budget[{{ $dKey }}][monthly]"
                                                           class="form-control rupiah-mask dept-monthly-field"
                                                           id="dept_mon_{{ $dKey }}"
                                                           data-dept="{{ $dKey }}"
                                                           value="{{ $currDept['monthly'] > 0 ? number_format($currDept['monthly'], 0, ',', '.') : '' }}"
                                                           placeholder="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Summary calculation banner --}}
                        <div class="p-3 bg-light border rounded mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                            <div>
                                <small class="text-muted d-block">Akumulasi Total 5 Sub-Departemen:</small>
                                <strong class="text-primary fs-5" id="deptAccumulatedTotalText">Rp 0</strong>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="form-check me-2">
                                    <input class="form-check-input" type="checkbox" name="sync_total_from_depts" value="1" id="syncTotalCheck" checked>
                                    <label class="form-check-label small fw-semibold" for="syncTotalCheck">
                                        Sinkronkan ke Plafon Utama
                                    </label>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary" id="btnApplyDeptToMain">
                                    <i class="mdi mdi-content-copy me-1"></i> Salin ke Plafon Utama
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Annual Budget</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('page-script')
<script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Rupiah Formatter
        function formatRp(val) {
            if (val === null || val === undefined) return 'Rp 0';
            return 'Rp ' + Math.round(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // -------------------------------------------------------------
        // 1. Chart Tren Bulanan: Actual vs Plafon (Area + Dashed Line)
        // -------------------------------------------------------------
        const trendEl = document.querySelector('#budgetMonthlyTrendChart');
        if (trendEl) {
            const trendOptions = {
                chart: {
                    height: 290,
                    type: 'line',
                    parentHeightOffset: 0,
                    toolbar: { show: false }
                },
                series: [
                    {
                        name: 'Realisasi Pengeluaran',
                        type: 'area',
                        data: @json($chartActualSeries)
                    },
                    {
                        name: 'Plafon Anggaran',
                        type: 'line',
                        data: @json($chartBudgetSeries)
                    }
                ],
                colors: ['#22c55e', '#4f46e5'],
                stroke: {
                    width: [3, 2],
                    curve: 'smooth',
                    dashArray: [0, 5]
                },
                fill: {
                    type: ['gradient', 'solid'],
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.5,
                        gradientToColors: ['#86efac'],
                        inverseColors: false,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 100]
                    }
                },
                markers: {
                    size: [4, 0],
                    colors: ['#22c55e'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: { size: 6 }
                },
                xaxis: {
                    categories: @json($chartMonthlyLabels),
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px' }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            if (val >= 1000000000) return (val / 1000000000).toFixed(1) + 'M';
                            if (val >= 1000000) return (val / 1000000).toFixed(0) + 'jt';
                            if (val >= 1000) return (val / 1000).toFixed(0) + 'rb';
                            return val;
                        },
                        style: { colors: '#64748b', fontSize: '11px' }
                    }
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: { formatter: formatRp }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px',
                    markers: { radius: 12 }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4,
                    padding: { top: -10, bottom: 0, left: 10, right: 10 }
                }
            };
            new ApexCharts(trendEl, trendOptions).render();
        }

        // -------------------------------------------------------------
        // 2. Chart Donut: Proporsi Kategori Biaya COA
        // -------------------------------------------------------------
        const donutEl = document.querySelector('#budgetCategoryDonutChart');
        const donutSeries = @json($chartCatSeries);
        if (donutEl && donutSeries.length > 0 && donutSeries.some(v => v > 0)) {
            const donutOptions = {
                chart: {
                    height: 250,
                    type: 'donut'
                },
                series: donutSeries,
                labels: @json($chartCatLabels),
                colors: ['#4f46e5', '#06b6d4', '#10b981', '#f59e0b', '#ef4444', '#64748b'],
                legend: {
                    position: 'bottom',
                    fontSize: '11px',
                    markers: { radius: 12 }
                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val.toFixed(1) + '%';
                    }
                },
                tooltip: {
                    y: { formatter: formatRp }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '68%',
                            labels: {
                                show: true,
                                name: { fontSize: '12px', color: '#64748b' },
                                value: {
                                    fontSize: '15px',
                                    fontWeight: 'bold',
                                    formatter: formatRp
                                },
                                total: {
                                    show: true,
                                    label: 'Total Belanja',
                                    fontSize: '11px',
                                    color: '#64748b',
                                    formatter: function (w) {
                                        const sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        return formatRp(sum);
                                    }
                                }
                            }
                        }
                    }
                }
            };
            new ApexCharts(donutEl, donutOptions).render();
        }

        // -------------------------------------------------------------
        // 3. Chart Bar: Multi-Year Budget vs Actual
        // -------------------------------------------------------------
        const multiYearEl = document.querySelector('#budgetMultiYearBarChart');
        if (multiYearEl) {
            const multiYearOptions = {
                chart: {
                    height: 280,
                    type: 'bar',
                    toolbar: { show: false }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '40%',
                        borderRadius: 4
                    }
                },
                series: [
                    {
                        name: 'Plafon Anggaran',
                        data: @json($chartHistoryBudgets)
                    },
                    {
                        name: 'Realisasi Pengeluaran',
                        data: @json($chartHistoryActuals)
                    }
                ],
                colors: ['#4f46e5', '#ef4444'],
                xaxis: {
                    categories: @json($chartHistoryYears),
                    labels: { style: { colors: '#64748b', fontSize: '12px', fontWeight: 'bold' } }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            if (val >= 1000000000) return (val / 1000000000).toFixed(1) + 'M';
                            if (val >= 1000000) return (val / 1000000).toFixed(0) + 'jt';
                            return val;
                        },
                        style: { colors: '#64748b', fontSize: '11px' }
                    }
                },
                dataLabels: { enabled: false },
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: { formatter: formatRp }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'right',
                    fontSize: '12px'
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 4
                }
            };
            window.multiYearChart = new ApexCharts(multiYearEl, multiYearOptions);
            window.multiYearChart.render();

            // Re-render when history tab is activated to ensure proper width calculation
            const histTabEl = document.getElementById('history-tab');
            if (histTabEl) {
                histTabEl.addEventListener('shown.bs.tab', function () {
                    if (window.multiYearChart) {
                        window.multiYearChart.render();
                    }
                });
            }
        }

        // -------------------------------------------------------------
        // Modal & Toggle Logic
        // -------------------------------------------------------------
        const toggle = document.getElementById('customMonthlyToggle');
        const container = document.getElementById('customMonthlyContainer');
        const annualInput = document.getElementById('annual_budget_input');
        const monthlyInput = document.getElementById('monthly_budget_input');

        if (toggle && container) {
            toggle.addEventListener('change', function () {
                if (this.checked) {
                    container.classList.remove('d-none');
                } else {
                    container.classList.add('d-none');
                }
            });
        }

        // Auto sync annual and monthly if flat
        if (annualInput && monthlyInput) {
            annualInput.addEventListener('input', function () {
                if (!toggle || !toggle.checked) {
                    const raw = this.value.replace(/\D/g, '');
                    const val = parseFloat(raw) || 0;
                    if (val > 0) {
                        monthlyInput.value = formatRp(Math.round(val / 12)).replace('Rp ', '');
                    } else {
                        monthlyInput.value = '';
                    }
                }
            });
            monthlyInput.addEventListener('input', function () {
                if (!toggle || !toggle.checked) {
                    const raw = this.value.replace(/\D/g, '');
                    const val = parseFloat(raw) || 0;
                    if (val > 0) {
                        annualInput.value = formatRp(Math.round(val * 12)).replace('Rp ', '');
                    } else {
                        annualInput.value = '';
                    }
                }
            });
        }

        // -------------------------------------------------------------
        // Sub-Departemen Modal Calculation & Sync Logic
        // -------------------------------------------------------------
        const deptAnnualFields = document.querySelectorAll('.dept-annual-field');
        const deptMonthlyFields = document.querySelectorAll('.dept-monthly-field');
        const deptAccumulatedTotalText = document.getElementById('deptAccumulatedTotalText');
        const btnApplyDeptToMain = document.getElementById('btnApplyDeptToMain');

        function calculateDeptTotal() {
            let total = 0;
            deptAnnualFields.forEach(function (field) {
                const raw = field.value.replace(/\D/g, '');
                total += parseFloat(raw) || 0;
            });
            if (deptAccumulatedTotalText) {
                deptAccumulatedTotalText.textContent = formatRp(total);
            }
            return total;
        }

        // Auto calculate monthly for each department when annual is typed
        deptAnnualFields.forEach(function (field) {
            field.addEventListener('input', function () {
                const dept = this.getAttribute('data-dept');
                const raw = this.value.replace(/\D/g, '');
                const val = parseFloat(raw) || 0;
                const monField = document.getElementById('dept_mon_' + dept);
                if (monField) {
                    if (val > 0) {
                        monField.value = formatRp(Math.round(val / 12)).replace('Rp ', '');
                    } else {
                        monField.value = '';
                    }
                }
                calculateDeptTotal();
            });
        });

        // Auto calculate annual for each department when monthly is typed
        deptMonthlyFields.forEach(function (field) {
            field.addEventListener('input', function () {
                const dept = this.getAttribute('data-dept');
                const raw = this.value.replace(/\D/g, '');
                const val = parseFloat(raw) || 0;
                const annField = document.getElementById('dept_ann_' + dept);
                if (annField) {
                    if (val > 0) {
                        annField.value = formatRp(Math.round(val * 12)).replace('Rp ', '');
                    } else {
                        annField.value = '';
                    }
                }
                calculateDeptTotal();
            });
        });

        // Apply Dept Total to Main Annual & Monthly inputs
        if (btnApplyDeptToMain) {
            btnApplyDeptToMain.addEventListener('click', function () {
                const total = calculateDeptTotal();
                if (total > 0 && annualInput && monthlyInput) {
                    annualInput.value = formatRp(total).replace('Rp ', '');
                    monthlyInput.value = formatRp(Math.round(total / 12)).replace('Rp ', '');

                    // Switch back to tab 1
                    const mainTabBtn = document.getElementById('modal-main-tab');
                    if (mainTabBtn) {
                        const tabTrigger = new bootstrap.Tab(mainTabBtn);
                        tabTrigger.show();
                    }
                }
            });
        }

        // Initial dept calculation on page load
        calculateDeptTotal();

        // Direct shortcut to open Tab 2 (Alokasi 5 Sub-Departemen)
        document.querySelectorAll('.btn-open-dept-budget').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const deptsTab = document.getElementById('modal-depts-tab');
                if (deptsTab) {
                    const tab = new bootstrap.Tab(deptsTab);
                    tab.show();
                }
            });
        });

        // -------------------------------------------------------------
        // AJAX Drilldown Transaksi Bulanan
        // -------------------------------------------------------------
        const detailModalEl = document.getElementById('monthlyDetailModal');
        const bsModal = new bootstrap.Modal(detailModalEl);

        const modalTitle = document.getElementById('modalMonthTitle');
        const modalSubtitle = document.getElementById('modalMonthSubtitle');
        const spinner = document.getElementById('modalLoadingSpinner');
        const txContainer = document.getElementById('modalTxContainer');
        const emptyState = document.getElementById('modalEmptyState');
        const txTableBody = document.getElementById('modalTxTableBody');
        const totalBadge = document.getElementById('modalTotalBadge');
        const countBadge = document.getElementById('modalCountBadge');

        document.querySelectorAll('.view-monthly-tx').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const year = this.getAttribute('data-year');
                const month = this.getAttribute('data-month');
                const monthName = this.getAttribute('data-monthname');
                const entity = this.getAttribute('data-entity');

                modalTitle.innerHTML = `<i class="mdi mdi-format-list-bulleted text-primary me-1"></i> Rincian Pengeluaran ${monthName} ${year}`;
                modalSubtitle.textContent = `Entitas: ${entity.toUpperCase()} • Mengambil data transaksi...`;

                spinner.classList.remove('d-none');
                txContainer.classList.add('d-none');
                emptyState.classList.add('d-none');
                txTableBody.innerHTML = '';

                bsModal.show();

                const url = `{{ route('finance.expense-budget.monthly-details') }}?year=${year}&month=${month}&entity=${entity}`;

                fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    spinner.classList.add('d-none');

                    if (data.status === 'success' && data.count > 0) {
                        modalSubtitle.textContent = `Entitas: ${entity.toUpperCase()} • Menampilkan ${data.count} transaksi pengeluaran`;
                        totalBadge.textContent = `Total: ${data.total_formatted}`;
                        countBadge.textContent = `${data.count} Transaksi tercatat`;

                        let html = '';
                        data.transactions.forEach(function (tx) {
                            html += `
                                <tr>
                                    <td><span class="badge bg-label-secondary">${tx.date}</span></td>
                                    <td>
                                        <div class="fw-semibold text-dark">${tx.no_expense}</div>
                                        ${tx.no_invoice !== '-' ? `<small class="text-muted">Inv: ${tx.no_invoice}</small>` : ''}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-muted border font-monospace me-1">${tx.account_code}</span>
                                        <span class="fw-semibold text-dark">${tx.account_name}</span>
                                    </td>
                                    <td>
                                        <div class="text-wrap" style="max-width: 280px;">${tx.memo}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold">${tx.bank_name}</div>
                                        <span class="badge bg-label-info" style="font-size: 10px;">${tx.bank_entity}</span>
                                    </td>
                                    <td class="text-end fw-bold text-danger">${tx.amount_formatted}</td>
                                </tr>
                            `;
                        });

                        txTableBody.innerHTML = html;
                        txContainer.classList.remove('d-none');
                    } else {
                        modalSubtitle.textContent = `Entitas: ${entity.toUpperCase()} • 0 Transaksi`;
                        emptyState.classList.remove('d-none');
                    }
                })
                .catch(err => {
                    spinner.classList.add('d-none');
                    modalSubtitle.textContent = 'Gagal memuat data transaksi.';
                    emptyState.classList.remove('d-none');
                    emptyState.innerHTML = `
                        <i class="mdi mdi-alert-circle text-danger fs-2 d-block mb-1"></i>
                        Terjadi kendala saat memuat data. Silakan coba kembali.
                    `;
                });
            });
        });
    });
</script>
@endpush
