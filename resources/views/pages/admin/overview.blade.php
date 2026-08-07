@extends('layouts.sales.app')
@section('title', 'Overview Sales')
@section('content')

    @push('after-style')
        <style>
            .overview-card {
                border: 1px solid #e7e9ed;
                border-radius: 16px;
                background: #ffffff;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
                transition: all 0.2s ease-in-out;
            }

            .overview-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
                border-color: rgba(105, 108, 255, 0.3);
            }

            .stat-tile {
                background: #f8f9fa;
                border: 1px solid #ebedf0;
                border-radius: 12px;
                padding: 10px 12px;
                transition: all 0.2s ease;
            }

            .stat-tile:hover {
                background: #ffffff;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
                border-color: rgba(105, 108, 255, 0.2);
            }

            .avatar-shadow {
                border: 2px solid #ffffff;
            }

            .kpi-header-card {
                border-radius: 14px;
                border: 1px solid #e7e9ed;
                background: #ffffff;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.015);
            }

            .bg-gradient-primary-soft {
                background: rgba(105, 108, 255, 0.05);
            }

            .badge-glow-success {
                background: #28c76f;
            }

            .badge-glow-warning {
                background: #ff9f43;
            }

            .badge-glow-danger {
                background: #ea5455;
            }
        </style>
    @endpush

    @php
        $now         = \Carbon\Carbon::now();
        $semesterNow = \App\Models\SalesReports::where('semester', $now->month > 6 ? 2 : 1)
                           ->where('year', $now->year)->first();
        $bulanLabel  = $now->locale('id')->translatedFormat('F Y');

        $grandPO     = array_sum($totalPO ?? []);
        $grandTarget = array_sum($targett ?? []);
        $grandPct    = $grandTarget > 0 ? round(($grandPO / $grandTarget) * 100, 1) : 0;
        $grandPctColor = $grandPct >= 100 ? 'success' : ($grandPct >= 80 ? 'warning' : 'danger');
    @endphp

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-label-primary fs-7 px-3 py-1 rounded-pill">
                    <i class="mdi mdi-view-dashboard-outline me-1"></i> Admin Dashboard
                </span>
                <span class="badge bg-label-secondary fs-7 px-3 py-1 rounded-pill fw-semibold">{{ $bulanLabel }}</span>
            </div>
            <h3 class="fw-bold mb-0 text-heading">Overview Kinerja Tim Sales</h3>
            <small class="text-muted">Ringkasan pencapaian dan aktivitas target bulan {{ $bulanLabel }}</small>
        </div>
        @if ($semesterNow)
            <a href="{{ route('report.semester', $semesterNow->id) }}"
               class="btn btn-primary rounded-pill px-4 shadow-sm waves-effect">
                <i class="mdi mdi-chart-box-outline me-1 fs-5"></i> Lihat Report Semester
            </a>
        @endif
    </div>

    {{-- Global KPI Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-header-card h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted fw-semibold d-block mb-1">Total Target Tim</small>
                        <h4 class="mb-0 fw-bold text-dark">Rp {{ number_format($grandTarget, 0, ',', '.') }}</h4>
                    </div>
                    <div class="avatar avatar-md">
                        <div class="avatar-initial bg-label-primary rounded-circle">
                            <i class="mdi mdi-target fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-header-card h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted fw-semibold d-block mb-1">Total PO Tim Bulan Ini</small>
                        <h4 class="mb-0 fw-bold text-success">Rp {{ number_format($grandPO, 0, ',', '.') }}</h4>
                    </div>
                    <div class="avatar avatar-md">
                        <div class="avatar-initial bg-label-success rounded-circle">
                            <i class="mdi mdi-cart-outline fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-header-card h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted fw-semibold d-block mb-1">Capaian Target Tim</small>
                        <h4 class="mb-0 fw-bold text-{{ $grandPctColor }}">{{ $grandPct }}%</h4>
                    </div>
                    <div class="avatar avatar-md">
                        <div class="avatar-initial bg-label-{{ $grandPctColor }} rounded-circle">
                            <i class="mdi mdi-chart-line fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card kpi-header-card h-100 p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-muted fw-semibold d-block mb-1">Sales Aktif</small>
                        <h4 class="mb-0 fw-bold text-dark">{{ count($sales) }} Sales</h4>
                    </div>
                    <div class="avatar avatar-md">
                        <div class="avatar-initial bg-label-info rounded-circle">
                            <i class="mdi mdi-account-group-outline fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Cards --}}
    <div class="row g-3">
        @foreach ($sales as $i => $sale)
            @php
                $po       = $totalPO[$i] ?? 0;
                $target   = $targett[$i] ?? 0;
                $pct      = $target > 0 ? round(($po / $target) * 100, 1) : 0;
                $pctColor = $pct >= 100 ? 'success' : ($pct >= 80 ? 'warning' : 'danger');
                $glowClass = 'badge-glow-' . $pctColor;
                $barWidth = min($pct, 100);
                $forecast = $totalForecast[$i] ?? '0';

                $stats = [
                    ['icon' => 'mdi-account-multiple-plus-outline', 'color' => 'secondary', 'val' => $filteredLeads[$i] ?? 0, 'label' => 'Leads'],
                    ['icon' => 'mdi-phone-outline',                  'color' => 'info',      'val' => $filteredDC[$i]    ?? 0, 'label' => 'Daily Call'],
                    ['icon' => 'mdi-account-multiple-outline',       'color' => 'primary',   'val' => $filteredCRM[$i]   ?? 0, 'label' => 'CRM'],
                    ['icon' => 'mdi-map-marker-outline',             'color' => 'warning',   'val' => $filteredVisit[$i] ?? 0, 'label' => 'Visit'],
                    ['icon' => 'mdi-email-multiple-outline',         'color' => 'info',      'val' => $filteredQuote[$i] ?? 0, 'label' => 'Quotation'],
                    ['icon' => 'mdi-cart-plus',                      'color' => 'success',   'val' => $filteredPO[$i]    ?? 0, 'label' => 'PO'],
                ];
            @endphp
            <div class="col-12 col-lg-6">
                <div class="card overview-card h-100 border-0">
                    <div class="card-body p-4">

                        {{-- Header: foto + nama + % badge --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative">
                                    <img src="{{ url('') . '/' . $sale->image }}" alt="{{ $sale->name }}"
                                        class="rounded-circle avatar-shadow border border-2 border-white"
                                        style="width:52px;height:52px;object-fit:cover;">
                                </div>
                                <div>
                                    <h5 class="mb-1 fw-bold text-dark">{{ $sale->name }}</h5>
                                    <div class="d-flex align-items-center gap-1 text-muted small">
                                        <i class="mdi mdi-target text-secondary fs-6"></i>
                                        <span>Target: <strong class="text-dark">Rp {{ number_format($target, 0, ',', '.') }}</strong></span>
                                    </div>
                                </div>
                            </div>
                            <span class="badge {{ $glowClass }} text-white rounded-pill px-3 py-2 fs-6">
                                {{ $pct }}%
                            </span>
                        </div>

                        {{-- Total PO + progress bar --}}
                        <div class="p-3 rounded-3 mb-3 bg-light border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted fw-semibold">Total PO Bulan Ini</small>
                                <span class="fw-bold fs-6 text-{{ $pctColor }}">
                                    Rp {{ number_format($po, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="progress" style="height:10px;border-radius:50px;background:rgba(0,0,0,0.06);">
                                <div class="progress-bar bg-{{ $pctColor }}"
                                     role="progressbar"
                                     style="width:{{ $barWidth }}%;border-radius:50px;"
                                     aria-valuenow="{{ $barWidth }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>

                        {{-- Forecast --}}
                        <div class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 mb-3 bg-gradient-primary-soft border border-primary-subtle">
                            <div class="avatar avatar-xs me-1">
                                <div class="avatar-initial bg-label-primary rounded-circle">
                                    <i class="mdi mdi-trending-up"></i>
                                </div>
                            </div>
                            <small class="text-muted fw-semibold">Estimasi Forecast</small>
                            <small class="fw-bold text-primary ms-auto fs-6">Rp {{ $forecast }}</small>
                        </div>

                        {{-- Stats grid --}}
                        <div class="row g-2">
                            @foreach ($stats as $stat)
                                <div class="col-4">
                                    <div class="stat-tile d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm flex-shrink-0">
                                            <div class="avatar-initial bg-label-{{ $stat['color'] }} rounded-circle">
                                                <i class="mdi {{ $stat['icon'] }} mdi-18px"></i>
                                            </div>
                                        </div>
                                        <div class="overflow-hidden">
                                            <h6 class="mb-0 fw-bold text-dark">{{ $stat['val'] }}</h6>
                                            <small class="text-muted d-block text-truncate" style="font-size:0.72rem;">{{ $stat['label'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>

                    {{-- Footer: link ke detail per sales --}}
                    @if ($semesterNow)
                        <div class="card-footer bg-transparent border-top py-3 px-4">
                            <a href="{{ route('overview-sales.semester', [$semesterNow->id, $sale->id]) }}"
                               class="btn btn-sm btn-outline-primary w-100 rounded-pill waves-effect d-flex align-items-center justify-content-center gap-1 py-2 fw-semibold">
                                <span>Lihat Detail Kinerja</span>
                                <i class="mdi mdi-arrow-right fs-6"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

@endsection

