@extends('layouts.sales.app')
@section('title', 'Sales Forecast Dashboard')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <!-- Custom Modern Styling -->
    <style>
        .forecast-header {
            margin-bottom: 2rem;
        }
        .forecast-title {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.2rem;
        }
        .forecast-subtitle {
            color: #6b7280;
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        /* Metric Card Stylings */
        .metric-card {
            border-radius: 20px;
            border: 1px solid rgba(229, 231, 235, 0.5);
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.08);
        }
        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: transparent;
        }
        .card-target::before { background: linear-gradient(90deg, #9ca3af, #6b7280); }
        .card-projection::before { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
        .card-actual::before { background: linear-gradient(90deg, #10b981, #34d399); }
        
        .metric-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.2rem;
            transition: all 0.3s ease;
        }
        .card-target .metric-icon-box {
            background-color: rgba(156, 163, 175, 0.12);
            color: #4b5563;
        }
        .card-projection .metric-icon-box {
            background-color: rgba(99, 102, 241, 0.12);
            color: #4f46e5;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
        }
        .card-actual .metric-icon-box {
            background-color: rgba(16, 185, 129, 0.12);
            color: #059669;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }
        
        .metric-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.025em;
        }
        .metric-label {
            font-size: 0.85rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        /* Dropdowns & Forms */
        .filter-select {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 0.6rem 1rem;
            font-weight: 500;
            color: #374151;
            background-color: #f9fafb;
            transition: all 0.2s ease;
        }
        .filter-select:focus {
            border-color: #6366f1;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        
        /* Table enhancements */
        .custom-table-card {
            border-radius: 20px;
            border: 1px solid rgba(229, 231, 235, 0.5);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            background: #ffffff;
        }
        .custom-table-card .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #f3f4f6;
            padding: 1.5rem;
        }
        .custom-table {
            margin-bottom: 0;
            border: 1px solid #e5e7eb !important;
        }
        .custom-table th {
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #374151;
            background-color: #f8fafc;
            padding: 1rem 1.25rem;
            border-bottom: 2px solid #cbd5e1 !important;
            border-right: 1px solid #e2e8f0;
        }
        .custom-table td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
            border-right: 1px solid #f1f5f9;
            color: #374151;
            font-size: 0.9rem;
        }
        .custom-table th:last-child, .custom-table td:last-child {
            border-right: none;
        }
        .custom-table tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.03);
        }
        
        /* Scrollbar custom */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .badge-pill-custom {
            padding: 0.35em 0.8em;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 30px;
        }
    </style>

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center forecast-header">
        <div>
            <h1 class="forecast-title">Sales Forecast Dashboard</h1>
            <p class="forecast-subtitle">Visualize annual sales targets, rolling projections, and realized purchase orders.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2 align-self-stretch align-self-md-auto">
            <a href="{{ route('forecast.setup') }}" class="btn btn-outline-primary d-flex align-items-center gap-2" style="border-radius: 12px; font-weight: 600;">
                <i class="mdi mdi-cog-outline"></i> Forecast Setup
            </a>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; background: #ffffff;">
                <div class="card-body p-3">
                    <form action="{{ route('forecast.index') }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-auto d-flex align-items-center gap-2 px-3 border-end">
                            <i class="mdi mdi-filter-variant text-primary mdi-24px"></i>
                            <h6 class="fw-bold mb-0" style="color: #374151;">Forecast Filters</h6>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label fw-semibold text-muted mb-0 text-nowrap" for="yearSelect" style="font-size: 0.85rem;">Year:</label>
                                <select class="form-select filter-select" id="yearSelect" name="year" onchange="this.form.submit()">
                                    @for($i = Carbon\Carbon::now()->year + 1; $i >= 2024; $i--)
                                        <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        @if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Sales Manager')
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label fw-semibold text-muted mb-0 text-nowrap" for="salesSelect" style="font-size: 0.85rem;">Sales Rep:</label>
                                <select class="form-select filter-select" id="salesSelect" name="id_sales" onchange="this.form.submit()">
                                    <option value="">-- All Sales --</option>
                                    @foreach($salesUsers as $user)
                                        <option value="{{ $user->id }}" {{ $salesId == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="form-label fw-semibold text-muted mb-0 text-nowrap" for="semesterSelect" style="font-size: 0.85rem;">Period:</label>
                                <select class="form-select filter-select" id="semesterSelect" name="semester" onchange="this.form.submit()">
                                    <option value="all" {{ $semester == 'all' ? 'selected' : '' }}>Full Fiscal Year</option>
                                    <option value="1" {{ $semester == '1' ? 'selected' : '' }}>H1 (Jan - Jun)</option>
                                    <option value="2" {{ $semester == '2' ? 'selected' : '' }}>H2 (Jul - Dec)</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="row g-4 mb-4">
        <!-- Target Awal Tahun -->
        <div class="col-lg-4">
            <div class="card metric-card card-target border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; border-top: 4px solid #6c757d !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="metric-label" style="color: #4b5563;">Annual Target</span>
                        <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(108, 117, 125, 0.12); color: #6c757d;">
                            <i class="mdi mdi-calendar-lock mdi-24px"></i>
                        </div>
                    </div>
                    <h3 class="metric-value mb-1" style="font-size: 1.85rem; font-weight: 800; color: #374151;">Rp {{ number_format($totalTarget, 0, ',', '.') }}</h3>
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">Initial target established at fiscal start</p>
                </div>
            </div>
        </div>

        <!-- Proyeksi Berjalan -->
        <div class="col-lg-4">
            <div class="card metric-card card-projection border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; border-top: 4px solid #6366f1 !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="metric-label text-primary">Rolling Projection</span>
                        <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(99, 102, 241, 0.12); color: #6366f1;">
                            <i class="mdi mdi-trending-up mdi-24px"></i>
                        </div>
                    </div>
                    <h3 class="metric-value mb-1" style="font-size: 1.85rem; font-weight: 800; color: #6366f1;">Rp {{ number_format($totalProjection, 0, ',', '.') }}</h3>
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">Realistic projection based on dynamic schedules</p>
                </div>
            </div>
        </div>

        <!-- Realisasi PO (Won) -->
        <div class="col-lg-4">
            <div class="card metric-card card-actual border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; border-top: 4px solid #10b981 !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="metric-label text-success">PO Realization (Won)</span>
                        <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
                            <i class="mdi mdi-check-decagram mdi-24px"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <h3 class="metric-value mb-1" style="font-size: 1.85rem; font-weight: 800; color: #10b981;">Rp {{ number_format($totalActual, 0, ',', '.') }}</h3>
                        <span class="badge bg-success text-white px-3 py-1 fw-bold" style="font-size: 0.85rem; border-radius: 20px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);">{{ $realizationRate }}%</span>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 0.8rem;">Cumulative value of won purchase orders</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Panel -->
    <div class="card custom-table-card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0" style="color: #374151;">Monthly Forecast Trend</h5>
            <span class="badge bg-label-primary font-semibold">Calendar Year {{ $year }}</span>
        </div>
        <div class="card-body p-4">
            <div id="forecastChart" style="height: 350px;"></div>
        </div>
    </div>

    <!-- Tables Panel -->
    <div class="row mb-4">
        <!-- Monthly Breakdown Table -->
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden; background: #ffffff;">
                <div class="card-header bg-transparent border-0 p-4 pb-2 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: #374151;">Monthly Performance Breakdown</h5>
                        <p class="text-muted small mb-0">Klik tombol detail di tiap bulan untuk melihat rincian unit forecast & PO yang berhasil diraih.</p>
                    </div>
                </div>
                <div class="table-responsive p-3">
                    <table class="table table-bordered custom-table align-middle">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th class="text-end">Annual Target</th>
                                <th class="text-end">Rolling Projection</th>
                                <th class="text-end">PO Realized</th>
                                <th class="text-center">Realization %</th>
                                <th class="text-end">Variance to Target</th>
                                <th class="text-center" style="width: 120px;">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($months as $key => $data)
                            @php
                                $deviation = $data['actual'] - $data['target'];
                                $badgeClass = $deviation >= 0 ? 'bg-label-success text-success' : 'bg-label-danger text-danger';
                                $sign = $deviation >= 0 ? '+' : '-';
                                $monthId = str_replace('-', '_', $key);

                                $monthRate = $data['target'] > 0 ? round(($data['actual'] / $data['target']) * 100, 1) : 0;
                                $rateBadgeClass = $monthRate >= 100 ? 'bg-success text-white' : ($monthRate >= 50 ? 'bg-primary text-white' : 'bg-label-secondary text-dark');
                            @endphp
                            <tr>
                                <td><strong>{{ $data['name'] }}</strong></td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted" style="font-size: 0.8rem; font-weight: 500;">Rp</span>
                                        <span>{{ number_format($data['target'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="text-end text-primary fw-semibold">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-primary text-opacity-75" style="font-size: 0.8rem; font-weight: 500;">Rp</span>
                                        <span>{{ number_format($data['projection'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="text-end text-success fw-bold">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-success text-opacity-75" style="font-size: 0.8rem; font-weight: 500;">Rp</span>
                                        <span>{{ number_format($data['actual'], 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $rateBadgeClass }} px-2 py-1 fw-bold" style="font-size: 0.78rem; border-radius: 12px;">
                                        {{ $monthRate }}%
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="badge badge-pill-custom {{ $badgeClass }}">
                                        {{ $sign }} Rp {{ number_format(abs($deviation), 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-label-primary waves-effect" data-bs-toggle="modal" data-bs-target="#modalMonthlyDetail_{{ $monthId }}">
                                        <i class="mdi mdi-eye-outline me-1"></i>Detail
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Render Modals Outside Table to avoid HTML markup breakage --}}
    @foreach($months as $key => $data)
    @php
        $monthId = str_replace('-', '_', $key);
    @endphp
    <div class="modal fade" id="modalMonthlyDetail_{{ $monthId }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <div class="modal-header bg-lighter py-3">
                    <div>
                        <h5 class="modal-title fw-bold text-primary mb-0">
                            <i class="mdi mdi-calendar-text me-1"></i>Detail Performance Month: {{ $data['name'] }} {{ $year }}
                        </h5>
                        <small class="text-muted">Rincian breakdown target forecast & pencapaian PO pada bulan {{ $data['name'] }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- Ringkasan Angka Bulan Ini --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <span class="text-muted small d-block">Target Forecast</span>
                                <span class="fw-bold text-secondary fs-5">Rp {{ number_format($data['target'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <span class="text-muted small d-block">Proyeksi Berjalan</span>
                                <span class="fw-bold text-primary fs-5">Rp {{ number_format($data['projection'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <span class="text-muted small d-block">Realisasi PO (Won)</span>
                                <span class="fw-bold text-success fs-5">Rp {{ number_format($data['actual'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Sub-Nav Tabs Modal --}}
                    <ul class="nav nav-pills nav-fill mb-3 border-bottom pb-2" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active fw-semibold" data-bs-toggle="pill" data-bs-target="#tab-modal-forecast-{{ $monthId }}" type="button" role="tab">
                                <i class="mdi mdi-chart-timeline-variant me-1"></i>Rencana Forecast Unit ({{ count($data['forecast_details'] ?? []) }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link fw-semibold" data-bs-toggle="pill" data-bs-target="#tab-modal-actual-{{ $monthId }}" type="button" role="tab">
                                <i class="mdi mdi-file-check-outline me-1"></i>Realisasi PO Masuk ({{ count($data['actual_details'] ?? []) }})
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content p-0">
                        {{-- Tab 1: Forecast Details --}}
                        <div class="tab-pane fade show active" id="tab-modal-forecast-{{ $monthId }}" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle m-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Company / Client</th>
                                            <th>Unit Mesin</th>
                                            <th>Kunjungan</th>
                                            <th>Tgl Rencana</th>
                                            <th class="text-end">Part Cost</th>
                                            <th class="text-end">Jasa Fee</th>
                                            <th class="text-end fw-bold">Total Forecast</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($data['forecast_details'] ?? [] as $fIndex => $fDetail)
                                        @php
                                            $badgePm = 'bg-label-primary';
                                            if($fDetail['type'] == 'PM2') $badgePm = 'bg-label-warning';
                                            if($fDetail['type'] == 'PM3') $badgePm = 'bg-label-danger';
                                            if($fDetail['type'] == 'PM4') $badgePm = 'bg-label-info';
                                        @endphp
                                        <tr>
                                            <td class="text-center">{{ $fIndex + 1 }}</td>
                                            <td class="fw-bold">{{ $fDetail['company'] }}</td>
                                            <td>
                                                <span>{{ $fDetail['brand'] }} {{ $fDetail['model'] }}</span>
                                                <small class="text-muted d-block">S/N: {{ $fDetail['serial'] }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ $badgePm }}">{{ $fDetail['visit_name'] }} ({{ $fDetail['type'] }})</span>
                                            </td>
                                            <td><span class="fw-semibold text-secondary">{{ $fDetail['date'] }}</span></td>
                                            <td class="text-end">Rp {{ number_format($fDetail['parts_cost'], 0, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format($fDetail['service_fee'], 0, ',', '.') }}</td>
                                            <td class="text-end text-primary fw-bold">Rp {{ number_format($fDetail['total_revenue'], 0, ',', '.') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">Tidak ada jadwal forecast unit pada bulan ini.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tab 2: Actual PO Details --}}
                        <div class="tab-pane fade" id="tab-modal-actual-{{ $monthId }}" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle m-0" style="font-size: 0.85rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>No Quotation</th>
                                            <th>No PO</th>
                                            <th>Company / Client</th>
                                            <th>Tgl PO</th>
                                            <th class="text-end fw-bold">Nominal PO (Nett)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($data['actual_details'] ?? [] as $aIndex => $aDetail)
                                        <tr>
                                            <td class="text-center">{{ $aIndex + 1 }}</td>
                                            <td><span class="fw-semibold text-primary">{{ $aDetail['no_quotation'] }}</span></td>
                                            <td><span class="fw-semibold text-dark">{{ $aDetail['no_po'] }}</span></td>
                                            <td class="fw-bold">{{ $aDetail['company'] }}</td>
                                            <td><span class="fw-semibold text-secondary">{{ $aDetail['date'] }}</span></td>
                                            <td class="text-end text-success fw-bold">Rp {{ number_format($aDetail['total'], 0, ',', '.') }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">Belum ada PO terealisasi yang masuk pada bulan ini.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>

@push('script')
<!-- ApexCharts implementation -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $(document).ready(function() {
        var targetData = [
            @foreach($months as $data)
                {{ $data['target'] }},
            @endforeach
        ];

        var projectionData = [
            @foreach($months as $data)
                {{ $data['projection'] }},
            @endforeach
        ];

        var actualData = [
            @foreach($months as $data)
                {{ $data['actual'] }},
            @endforeach
        ];

        var monthNames = [
            @foreach($months as $data)
                "{{ $data['name'] }}",
            @endforeach
        ];

        var options = {
            series: [{
                name: 'Annual Target',
                type: 'column',
                data: targetData
            }, {
                name: 'Rolling Projection',
                type: 'column',
                data: projectionData
            }, {
                name: 'PO Realization (Won)',
                type: 'line',
                data: actualData
            }],
            chart: {
                height: 350,
                type: 'line',
                stacked: false,
                fontFamily: 'Inter, sans-serif',
                toolbar: {
                    show: false
                }
            },
            stroke: {
                width: [0, 0, 4],
                curve: 'smooth',
                dashArray: [0, 0, 0]
            },
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    borderRadius: 6,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            colors: ['#cbd5e1', '#6366f1', '#10b981'],
            fill: {
                opacity: [0.75, 0.85, 0.85],
                type: ['solid', 'solid', 'gradient'],
                gradient: {
                    shade: 'dark',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#34d399'],
                    inverseColors: false,
                    opacityFrom: 0.4,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            labels: monthNames,
            markers: {
                size: 5,
                colors: ['#10b981'],
                strokeColors: '#ffffff',
                strokeWidth: 2,
                hover: {
                    size: 7
                }
            },
            grid: {
                borderColor: '#f3f4f6',
                strokeDashArray: 4,
                padding: {
                    left: 20,
                    right: 20
                }
            },
            xaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontWeight: 600
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#9ca3af',
                        fontWeight: 600
                    },
                    formatter: function (value) {
                        if (value >= 1000000) {
                            return "Rp " + (value / 1000000).toFixed(1) + " M";
                        }
                        return "Rp " + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontWeight: 600,
                markers: {
                    radius: 12
                },
                itemMargin: {
                    horizontal: 10
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (y) {
                        if (typeof y !== "undefined") {
                            return "Rp " + y.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                        return y;
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#forecastChart"), options);
        chart.render();


    });
</script>
@endpush
@endsection
