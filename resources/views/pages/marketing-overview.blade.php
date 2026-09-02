@extends('layouts.sales.app')
@section('title', 'Marketing Overview - ' . ($user->name ?? 'Marketing'))

@push('before-style')
    <style>
        .clean-card {
            border: 1px solid #edf2f9;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease-in-out;
            background: #fff;
        }
        .clean-card:hover {
            box-shadow: 0 6px 22px rgba(0, 0, 0, 0.07);
        }
        .metric-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .funnel-step {
            position: relative;
            padding: 1.25rem 1rem;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.2s;
        }
        .funnel-step:hover {
            transform: translateY(-3px);
        }
        .funnel-arrow {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8592a3;
        }
        .funnel-badge-rate {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
        }
        .table-custom-overview th {
            background-color: #f8f9fa;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #566a7f;
            border-bottom: 2px solid #e7eaf0;
            vertical-align: middle;
        }
        .table-custom-overview td {
            vertical-align: middle;
            font-size: 0.85rem;
        }
        .table-custom-overview tfoot td {
            background-color: #f8faff !important;
            border-top: 2px solid #e2e8f3 !important;
            border-bottom: 2px solid #e2e8f3 !important;
            font-size: 0.88rem;
            font-weight: 700;
        }
        .avatar-sales {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
        }
        .progress-slim {
            height: 6px;
            border-radius: 3px;
        }
    </style>
@endpush

@section('content')
    @php
        $isAdminView = !in_array(Auth::user()->role, ['Support', 'Marketing']);
        $periodTitle = $report->semester == 'full'
            ? 'Full Year (S1+S2)'
            : 'Semester ' . $report->semester;
        $periodSubtitle = $report->semester == 'full'
            ? 'Januari – Desember (12 Bulan)'
            : ($report->semester == 1 ? 'Januari – Juni' : 'Juli – Desember');
    @endphp

    <!-- Header Card with S1/S2/Full Year Toggle & Year Filter -->
    <div class="card clean-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ $user->image ? url($user->image) : asset('assets/img/avatars/1.png') }}"
                         alt="{{ $user->name }}"
                         class="rounded-circle shadow-sm"
                         style="width: 60px; height: 60px; object-fit: cover; border: 3px solid #666cff;">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            <span class="badge bg-label-primary fs-6 px-3 py-1">
                                <i class="mdi mdi-bullseye-arrow me-1"></i>
                                {{ $periodTitle }}
                            </span>
                            <span class="text-muted fw-semibold fs-5">{{ $report->year }}</span>
                            <span class="text-muted">•</span>
                            <small class="text-muted fw-semibold">{{ $periodSubtitle }}</small>
                            <span class="badge bg-label-info ms-1">Marketing Acquisition</span>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">Marketing & Acquisition Overview</h4>
                        <small class="text-muted">
                            Performa Akuisisi & Konversi Leads &mdash; <strong>{{ $user->name }}</strong> ({{ $user->detail->last()->area ?? 'Marketing Team' }})
                        </small>
                    </div>
                </div>

                <!-- Filter Options -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Semester & Full Year Toggle -->
                    <div class="btn-group" role="group" aria-label="Pilih Semester">
                        @if ($s1Report)
                            <a href="{{ $isAdminView ? url('/overview/' . $s1Report->id . '/' . $user->id) : url('/overview?report_id=' . $s1Report->id) }}"
                               class="btn btn-sm waves-effect {{ $report->semester == 1 ? 'btn-primary' : 'btn-outline-primary' }}">
                                Semester 1
                            </a>
                        @endif
                        @if ($s2Report)
                            <a href="{{ $isAdminView ? url('/overview/' . $s2Report->id . '/' . $user->id) : url('/overview?report_id=' . $s2Report->id) }}"
                               class="btn btn-sm waves-effect {{ $report->semester == 2 ? 'btn-primary' : 'btn-outline-primary' }}">
                                Semester 2
                            </a>
                        @endif
                        <a href="{{ $isAdminView ? url('/overview/full_' . $report->year . '/' . $user->id) : url('/overview?report_id=full_' . $report->year) }}"
                           class="btn btn-sm waves-effect {{ $report->semester == 'full' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="mdi mdi-calendar-blank-multiple me-1"></i> Full Year (12 Bulan)
                        </a>
                    </div>

                    <!-- Year Dropdown -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle waves-effect"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-calendar-range me-1"></i> Tahun {{ $report->year }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach ($yearsList as $yr)
                                <li>
                                    <a class="dropdown-item {{ $yr == $report->year ? 'active' : '' }}"
                                       href="{{ $isAdminView ? url('/overview/full_' . $yr . '/' . $user->id) : url('/overview?report_id=full_' . $yr) }}">
                                        {{ $yr }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5 Top Executive KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- 1. Total Leads / Prospects -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card clean-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small">Total Leads Masuk</span>
                        <div class="metric-icon-box bg-label-primary text-primary">
                            <i class="mdi mdi-account-search-outline fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark">{{ number_format($totalProspects, 0, ',', '.') }}</h3>
                    <div class="d-flex align-items-center gap-1 flex-wrap mt-2">
                        <span class="badge bg-label-success" title="Dialokasikan ke sales">
                            <i class="mdi mdi-check-circle-outline me-1"></i>{{ $totalProvided }} Provided
                        </span>
                        <span class="badge bg-label-warning" title="Sedang diproses / pending">
                            {{ $totalPending }} Pending
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Active Pipeline (Quotation) Generated -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card clean-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small">Pipeline Penawaran</span>
                        <div class="metric-icon-box bg-label-info text-info">
                            <i class="mdi mdi-file-document-edit-outline fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark">{{ number_format($totalQuoteCount, 0, ',', '.') }}</h3>
                    <div class="text-info fw-semibold small mt-2">
                        Rp {{ number_format($totalQuoteNominal, 0, ',', '.') }}
                    </div>
                    <small class="text-muted">Lead-to-Quote: <strong>{{ $overallLeadToQuoteRate }}%</strong></small>
                </div>
            </div>
        </div>

        <!-- 3. Closed Won (Marketing Revenue / PO) -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card clean-card h-100" style="border-left: 4px solid #28c76f;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small">Marketing PO Closing</span>
                        <div class="metric-icon-box bg-label-success text-success">
                            <i class="mdi mdi-cart-check fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ number_format($totalPoCount, 0, ',', '.') }} <small class="fs-6 text-muted">PO</small></h3>
                    <div class="text-success fw-bold small mt-2">
                        Rp {{ number_format($totalPoNominal, 0, ',', '.') }}
                    </div>
                    <small class="text-muted">Quote-to-PO: <strong>{{ $overallQuoteToPoRate }}%</strong></small>
                </div>
            </div>
        </div>

        <!-- 4. Overall Win / Conversion Rate -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card clean-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small">Total Win Rate</span>
                        <div class="metric-icon-box bg-label-warning text-warning">
                            <i class="mdi mdi-trophy-outline fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark">{{ $overallWinRate }}%</h3>
                    <div class="progress progress-slim mt-2 mb-1 bg-label-secondary">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ min(100, $overallWinRate * 2) }}%"></div>
                    </div>
                    <small class="text-muted">Rasio Prospek &rarr; Closing PO</small>
                </div>
            </div>
        </div>

        <!-- 5. Quotation Loss Summary -->
        <div class="col-12 col-sm-6 col-xl">
            <div class="card clean-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small">Quotation Loss</span>
                        <div class="metric-icon-box bg-label-danger text-danger">
                            <i class="mdi mdi-close-circle-outline fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-danger">{{ number_format($totalLossCount, 0, ',', '.') }}</h3>
                    <div class="text-danger small mt-2 fw-semibold">
                        Rp {{ number_format($totalLossNominal, 0, ',', '.') }}
                    </div>
                    <small class="text-muted">Evaluasi Kualitas Lead</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Visual Interactive Marketing Funnel -->
    <div class="card clean-card mb-4">
        <div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="mdi mdi-filter-variant me-2 text-primary"></i>Marketing Acquisition & Conversion Funnel
                </h5>
                <small class="text-muted">Alur konversi dari Leads masuk hingga menghasilkan Purchase Order (PO)</small>
            </div>
            <span class="badge bg-label-primary px-3 py-1">Periode: {{ $periodTitle }} {{ $report->year }}</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-center justify-content-center">
                <!-- Step 1: Total Leads -->
                <div class="col-12 col-md-3">
                    <div class="funnel-step bg-label-secondary border border-secondary border-opacity-25 h-100">
                        <div class="avatar mx-auto mb-2">
                            <div class="avatar-initial bg-secondary text-white rounded">
                                <i class="mdi mdi-account-group-outline fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0 text-dark">{{ number_format($totalProspects, 0, ',', '.') }}</h2>
                        <span class="fw-semibold text-muted d-block">1. Leads / Prospek</span>
                        <small class="text-muted">100% Inflow Leads Masuk</small>
                    </div>
                </div>

                <!-- Arrow 1 -->
                <div class="col-12 col-md-1 funnel-arrow text-center">
                    <div class="d-flex flex-column align-items-center">
                        <i class="mdi mdi-arrow-right fs-1 text-muted d-none d-md-block"></i>
                        <i class="mdi mdi-arrow-down fs-1 text-muted d-block d-md-none"></i>
                        <span class="funnel-badge-rate bg-label-primary mt-1" title="Lead to Quote Rate">
                            {{ $overallLeadToQuoteRate }}%
                        </span>
                    </div>
                </div>

                <!-- Step 2: Quotation Sent -->
                <div class="col-12 col-md-3">
                    <div class="funnel-step bg-label-primary border border-primary border-opacity-25 h-100">
                        <div class="avatar mx-auto mb-2">
                            <div class="avatar-initial bg-primary text-white rounded">
                                <i class="mdi mdi-file-document-outline fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0 text-primary">{{ number_format($totalQuoteCount, 0, ',', '.') }}</h2>
                        <span class="fw-semibold text-dark d-block">2. Penawaran (Quotation)</span>
                        <small class="text-muted">Rp {{ number_format($totalQuoteNominal, 0, ',', '.') }}</small>
                    </div>
                </div>

                <!-- Arrow 2 -->
                <div class="col-12 col-md-1 funnel-arrow text-center">
                    <div class="d-flex flex-column align-items-center">
                        <i class="mdi mdi-arrow-right fs-1 text-muted d-none d-md-block"></i>
                        <i class="mdi mdi-arrow-down fs-1 text-muted d-block d-md-none"></i>
                        <span class="funnel-badge-rate bg-label-success mt-1" title="Quote to PO Win Rate">
                            {{ $overallQuoteToPoRate }}%
                        </span>
                    </div>
                </div>

                <!-- Step 3: PO Closed -->
                <div class="col-12 col-md-3">
                    <div class="funnel-step bg-label-success border border-success border-opacity-25 h-100">
                        <div class="avatar mx-auto mb-2">
                            <div class="avatar-initial bg-success text-white rounded">
                                <i class="mdi mdi-cart-check fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0 text-success">{{ number_format($totalPoCount, 0, ',', '.') }}</h2>
                        <span class="fw-semibold text-dark d-block">3. Closing PO (Won)</span>
                        <small class="text-success fw-bold">Rp {{ number_format($totalPoNominal, 0, ',', '.') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Chart 1: Monthly Trend Acquisition & Conversion -->
        <div class="col-12 col-lg-7">
            <div class="card clean-card h-100">
                <div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Tren Volume Leads & Konversi Bulanan</h5>
                        <small class="text-muted">Jumlah Prospek Masuk vs Penawaran vs Closing PO</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="mktVolumeTrendChart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Pipeline Nominal vs Realisasi PO -->
        <div class="col-12 col-lg-5">
            <div class="card clean-card h-100">
                <div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">Nominal Pipeline vs Realisasi PO</h5>
                        <small class="text-muted">Perbandingan Nilai Penawaran & PO (Rp)</small>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div id="mktNominalTrendChart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown Performance Table -->
    <div class="card clean-card mb-4">
        <div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="mdi mdi-table-large me-2 text-primary"></i>Rincian Metrik Performa Marketing Bulanan
                </h5>
                <small class="text-muted">Breakdown metrik bulanan lengkap pada periode {{ $periodTitle }} {{ $report->year }}</small>
            </div>
            <span class="badge bg-label-info">Marketing KPIs</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-custom-overview mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Bulan</th>
                        <th class="text-center">Total Prospek</th>
                        <th class="text-center">Provided (Sales)</th>
                        <th class="text-center">Quotation (Qty)</th>
                        <th class="text-end">Nominal Quotation</th>
                        <th class="text-center">PO (Qty)</th>
                        <th class="text-end">Realisasi PO</th>
                        <th class="text-center">Loss (Qty)</th>
                        <th class="text-center">Lead &rarr; Quote</th>
                        <th class="text-center pe-4">Quote &rarr; PO</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($monthlyMetrics as $m)
                        <tr>
                            <td class="ps-4 fw-bold text-dark">
                                <i class="mdi mdi-calendar-blank-outline me-1 text-muted"></i>
                                {{ $m['month_name'] }}
                            </td>
                            <td class="text-center fw-semibold">{{ number_format($m['prospects_total'], 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge bg-label-success">{{ $m['prospects_provided'] }}</span>
                                @if ($m['prospects_pending'] > 0)
                                    <span class="badge bg-label-warning ms-1" title="Pending">{{ $m['prospects_pending'] }}</span>
                                @endif
                            </td>
                            <td class="text-center fw-semibold text-info">{{ number_format($m['quotes_count'], 0, ',', '.') }}</td>
                            <td class="text-end text-muted">
                                {{ $m['quotes_nominal'] > 0 ? 'Rp ' . number_format($m['quotes_nominal'], 0, ',', '.') : '—' }}
                            </td>
                            <td class="text-center fw-bold text-success">{{ number_format($m['po_count'], 0, ',', '.') }}</td>
                            <td class="text-end fw-bold text-success">
                                {{ $m['po_nominal'] > 0 ? 'Rp ' . number_format($m['po_nominal'], 0, ',', '.') : '—' }}
                            </td>
                            <td class="text-center text-danger">
                                {{ $m['loss_count'] > 0 ? $m['loss_count'] : '—' }}
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $m['lead_to_quote_rate'] >= 50 ? 'bg-label-success' : 'bg-label-secondary' }}">
                                    {{ $m['lead_to_quote_rate'] }}%
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <span class="badge {{ $m['quote_to_po_rate'] >= 30 ? 'bg-label-success' : ($m['quote_to_po_rate'] > 0 ? 'bg-label-warning' : 'bg-label-secondary') }}">
                                    {{ $m['quote_to_po_rate'] }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td class="ps-4 text-dark fs-6">TOTAL / RATA-RATA</td>
                        <td class="text-center text-dark fs-6">{{ number_format($totalProspects, 0, ',', '.') }}</td>
                        <td class="text-center text-dark fs-6">{{ number_format($totalProvided, 0, ',', '.') }}</td>
                        <td class="text-center text-info fs-6">{{ number_format($totalQuoteCount, 0, ',', '.') }}</td>
                        <td class="text-end text-info fs-6">Rp {{ number_format($totalQuoteNominal, 0, ',', '.') }}</td>
                        <td class="text-center text-success fs-6">{{ number_format($totalPoCount, 0, ',', '.') }}</td>
                        <td class="text-end text-success fs-6">Rp {{ number_format($totalPoNominal, 0, ',', '.') }}</td>
                        <td class="text-center text-danger fs-6">{{ number_format($totalLossCount, 0, ',', '.') }}</td>
                        <td class="text-center fs-6">
                            <span class="badge bg-label-primary font-weight-bold">{{ $overallLeadToQuoteRate }}%</span>
                        </td>
                        <td class="text-center pe-4 fs-6">
                            <span class="badge bg-label-success font-weight-bold">{{ $overallQuoteToPoRate }}%</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Bottom Section: Lead Sources & Sales Handover Matrix -->
    <div class="row g-4">
        <!-- Lead Acquisition Sources & Category Breakdown -->
        <div class="col-12 col-lg-5">
            <div class="card clean-card h-100">
                <div class="card-header border-bottom py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="mdi mdi-chart-donut me-2 text-primary"></i>Sumber Leads & Kategori
                    </h5>
                    <small class="text-muted">Kanal akuisisi prospek yang masuk</small>
                </div>
                <div class="card-body p-4">
                    <!-- By Source -->
                    <h6 class="fw-bold text-dark mb-3">Distribusi Sumber Kanal (Source)</h6>
                    @if ($mktProspectBySource->isNotEmpty())
                        <div class="list-group list-group-flush mb-4">
                            @php
                                $sourceColors = ['primary', 'success', 'info', 'warning', 'secondary', 'dark'];
                                $ci = 0;
                            @endphp
                            @foreach ($mktProspectBySource as $src)
                                @php
                                    $pct = $totalProspects > 0 ? round(($src->total / $totalProspects) * 100, 1) : 0;
                                    $color = $sourceColors[$ci % count($sourceColors)];
                                    $ci++;
                                @endphp
                                <div class="list-group-item px-0 py-2 border-0">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-semibold text-dark">{{ $src->source }}</span>
                                        <span class="badge bg-label-{{ $color }}">{{ $src->total }} Leads ({{ $pct }}%)</span>
                                    </div>
                                    <div class="progress progress-slim bg-label-secondary">
                                        <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">Belum ada data sumber prospek</div>
                    @endif

                    <hr class="my-3">

                    <!-- By Category -->
                    <h6 class="fw-bold text-dark mb-3">Kategori Kebutuhan Prospek</h6>
                    @if ($mktProspectByCategory->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($mktProspectByCategory as $cat)
                                <span class="badge bg-label-primary px-3 py-2 fs-6">
                                    {{ $cat->category }}: <strong>{{ $cat->total }}</strong>
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-2">Belum ada data kategori</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sales Handover & Conversion Matrix -->
        <div class="col-12 col-lg-7">
            <div class="card clean-card h-100">
                <div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="mdi mdi-account-switch-outline me-2 text-primary"></i>Distribusi & Konversi Sales
                        </h5>
                        <small class="text-muted">Performa tim sales yang menindaklanjuti leads marketing ini</small>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-custom-overview mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Sales Person</th>
                                <th class="text-center">Leads Diterima</th>
                                <th class="text-center">Quotation</th>
                                <th class="text-center">PO Closing</th>
                                <th class="text-end">Nominal PO</th>
                                <th class="text-center pe-4">Win Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($salesDistribution as $s)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ $s->sales_image ? url($s->sales_image) : asset('assets/img/avatars/1.png') }}"
                                                 alt="{{ $s->sales_name }}"
                                                 class="avatar-sales border">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $s->sales_name }}</div>
                                                <small class="text-muted">{{ $s->sales_role ?? 'Sales' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-semibold">{{ $s->leads_count }}</td>
                                    <td class="text-center text-info fw-semibold">{{ $s->quote_count }}</td>
                                    <td class="text-center text-success fw-bold">{{ $s->po_count }}</td>
                                    <td class="text-end text-success fw-bold">
                                        {{ $s->po_nominal > 0 ? 'Rp ' . number_format($s->po_nominal, 0, ',', '.') : '—' }}
                                    </td>
                                    <td class="text-center pe-4">
                                        <span class="badge {{ $s->conversion_rate >= 30 ? 'bg-label-success' : ($s->conversion_rate > 0 ? 'bg-label-primary' : 'bg-label-secondary') }}">
                                            {{ $s->conversion_rate }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        Belum ada alokasi leads ke sales pada periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    <script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart 1: Volume Trend (Prospects, Quotes, POs)
            var volumeChartEl = document.querySelector('#mktVolumeTrendChart');
            if (volumeChartEl) {
                var volumeOptions = {
                    series: [{
                        name: 'Leads Masuk (Prospect)',
                        data: @json($chartProspects ?? [])
                    }, {
                        name: 'Quotation Dibuat',
                        data: @json($chartQuotes ?? [])
                    }, {
                        name: 'PO Won / Closing',
                        data: @json($chartPOs ?? [])
                    }],
                    chart: {
                        type: 'bar',
                        height: 320,
                        toolbar: { show: false },
                        parentHeightOffset: 0
                    },
                    colors: ['#8592a3', '#666cff', '#28c76f'],
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '45%',
                            borderRadius: 4
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: { show: true, width: 2, colors: ['transparent'] },
                    xaxis: {
                        categories: @json($chartMonths ?? []),
                        axisBorder: { show: false }
                    },
                    yaxis: {
                        title: { text: 'Jumlah (Qty)' }
                    },
                    fill: { opacity: 1 },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + ' item';
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right'
                    },
                    grid: { borderColor: '#f1f1f1' }
                };
                var volumeChart = new ApexCharts(volumeChartEl, volumeOptions);
                volumeChart.render();
            }

            // Chart 2: Nominal Trend (Quotation vs PO)
            var nominalChartEl = document.querySelector('#mktNominalTrendChart');
            if (nominalChartEl) {
                var nominalOptions = {
                    series: [{
                        name: 'Nilai Penawaran (Quotation)',
                        data: @json($chartQuotesNominal ?? [])
                    }, {
                        name: 'Realisasi PO (Revenue)',
                        data: @json($chartPOsNominal ?? [])
                    }],
                    chart: {
                        type: 'area',
                        height: 320,
                        toolbar: { show: false },
                        zoom: { enabled: false }
                    },
                    colors: ['#666cff', '#28c76f'],
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.05,
                            stops: [0, 95, 100]
                        }
                    },
                    xaxis: {
                        categories: @json($chartMonths ?? []),
                        axisBorder: { show: false }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                if (val >= 1000000000) {
                                    return 'Rp ' + (val / 1000000000).toFixed(1) + ' M';
                                } else if (val >= 1000000) {
                                    return 'Rp ' + (val / 1000000).toFixed(0) + ' Jt';
                                }
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right'
                    },
                    grid: { borderColor: '#f1f1f1' }
                };
                var nominalChart = new ApexCharts(nominalChartEl, nominalOptions);
                nominalChart.render();
            }
        });
    </script>
@endpush
