@extends('layouts.sales.app')
@section('title', 'Monthly Marketing Report - ' . ($bulanMap[$month] ?? 'Month') . ' ' . $year)

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
            padding: 1.25rem 1rem;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.2s;
        }
        .funnel-step:hover {
            transform: translateY(-3px);
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
        .avatar-sales {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }
        .progress-slim {
            height: 6px;
            border-radius: 3px;
        }
        [data-bs-toggle="collapse"] .toggle-chevron { transition: transform .2s; }
        [data-bs-toggle="collapse"]:not(.collapsed) .toggle-chevron { transform: rotate(180deg); }
    </style>
@endpush

@section('content')
    @php
        $bulanMap = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $prevMonth = $month == 1 ? 12 : $month - 1;
        $prevYear  = $month == 1 ? $year - 1 : $year;
        $nextMonth = $month == 12 ? 1 : $month + 1;
        $nextYear  = $month == 12 ? $year + 1 : $year;

        $leadToQuoteRate = $mktProspectCount > 0 ? round(($mktQuoteCount / $mktProspectCount) * 100, 1) : 0;
        $quoteToPoRate   = $mktQuoteCount   > 0 ? round(($mktPoCount   / $mktQuoteCount)   * 100, 1) : 0;
        $overallWinRate  = $mktProspectCount > 0 ? round(($mktPoCount   / $mktProspectCount) * 100, 1) : 0;

        $statusPending   = $mktProspectByStatus->pending   ?? 0;
        $statusProvided  = $mktProspectByStatus->provided  ?? 0;
        $statusNoProvide = $mktProspectByStatus->no_provide ?? 0;
        $pctPending      = $mktProspectCount > 0 ? round(($statusPending   / $mktProspectCount) * 100, 1) : 0;
        $pctProvided     = $mktProspectCount > 0 ? round(($statusProvided  / $mktProspectCount) * 100, 1) : 0;
        $pctNoProvide    = $mktProspectCount > 0 ? round(($statusNoProvide / $mktProspectCount) * 100, 1) : 0;

        $sourceIcons = [
            'IG'          => ['icon' => 'mdi-instagram',          'color' => 'danger'],
            'Instagram'   => ['icon' => 'mdi-instagram',          'color' => 'danger'],
            'WhatsApp'    => ['icon' => 'mdi-whatsapp',           'color' => 'success'],
            'LinkedIn'    => ['icon' => 'mdi-linkedin',           'color' => 'info'],
            'Website'     => ['icon' => 'mdi-web',                'color' => 'primary'],
            'Indotrading' => ['icon' => 'mdi-store-outline',      'color' => 'warning'],
            'Tokopedia'   => ['icon' => 'mdi-shopping-outline',   'color' => 'success'],
            'OLX'         => ['icon' => 'mdi-tag-outline',        'color' => 'warning'],
            'Google'      => ['icon' => 'mdi-google',             'color' => 'danger'],
            'Google Ads'  => ['icon' => 'mdi-google',             'color' => 'danger'],
            'Meta Ads'    => ['icon' => 'mdi-facebook',           'color' => 'primary'],
            'Facebook'    => ['icon' => 'mdi-facebook',           'color' => 'primary'],
            'Other'       => ['icon' => 'mdi-help-circle-outline','color' => 'secondary'],
        ];
    @endphp

    {{-- ===== HEADER & DATE NAVIGATOR ===== --}}
    <div class="card clean-card mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="badge bg-label-primary fs-6 px-3 py-1">
                            <i class="mdi mdi-calendar-month-outline me-1"></i>
                            Monthly Report
                        </span>
                        <span class="text-muted fw-semibold fs-5">{{ $bulanMap[$month] }} {{ $year }}</span>
                        <span class="text-muted">•</span>
                        <small class="text-muted fw-semibold">Bulan Berjalan</small>
                    </div>
                    <h4 class="fw-bold mb-0 text-dark">Monthly Marketing Performance</h4>
                    <small class="text-muted">
                        Laporan Aktivitas & Akuisisi Leads &mdash; <strong>{{ Auth::user()->name }}</strong>
                    </small>
                </div>

                {{-- Date Navigator --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="d-inline-flex align-items-center bg-body-tertiary border rounded-pill p-1">
                        {{-- Prev Month --}}
                        <a href="{{ route('reports.support', ['year' => $prevYear, 'month' => $prevMonth]) }}"
                           class="btn btn-icon btn-sm btn-label-secondary rounded-circle shadow-none"
                           style="width: 30px; height: 30px;"
                           data-bs-toggle="tooltip" title="Bulan Sebelumnya ({{ $bulanMap[$prevMonth] }})">
                            <i class="mdi mdi-chevron-left fs-5"></i>
                        </a>

                        {{-- Month Dropdown --}}
                        <div class="dropdown">
                            <button type="button"
                                class="btn btn-sm border-0 bg-transparent fw-semibold text-dark dropdown-toggle py-0 px-2 shadow-none"
                                style="font-size: 0.85rem;"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $bulanMap[$month] }}
                            </button>
                            <ul class="dropdown-menu shadow-sm">
                                @for ($m = 1; $m <= 12; $m++)
                                    <li>
                                        <a class="dropdown-item waves-effect {{ $m == $month ? 'active' : '' }}"
                                           href="{{ route('reports.support', ['year' => $year, 'month' => $m]) }}">
                                            {{ $bulanMap[$m] }}
                                        </a>
                                    </li>
                                @endfor
                            </ul>
                        </div>

                        <span class="text-muted opacity-25">|</span>

                        {{-- Year Dropdown --}}
                        <div class="dropdown">
                            <button type="button"
                                class="btn btn-sm border-0 bg-transparent fw-semibold text-dark dropdown-toggle py-0 px-2 shadow-none"
                                style="font-size: 0.85rem;"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                {{ $year }}
                            </button>
                            <ul class="dropdown-menu shadow-sm">
                                @foreach ($yearList as $yr)
                                    <li>
                                        <a class="dropdown-item waves-effect {{ $yr == $year ? 'active' : '' }}"
                                           href="{{ route('reports.support', ['year' => $yr, 'month' => $month]) }}">
                                            {{ $yr }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Next Month --}}
                        <a href="{{ route('reports.support', ['year' => $nextYear, 'month' => $nextMonth]) }}"
                           class="btn btn-icon btn-sm btn-label-secondary rounded-circle shadow-none"
                           style="width: 30px; height: 30px;"
                           data-bs-toggle="tooltip" title="Bulan Berikutnya ({{ $bulanMap[$nextMonth] }})">
                            <i class="mdi mdi-chevron-right fs-5"></i>
                        </a>
                    </div>

                    {{-- Shortcut to Current Month --}}
                    @if ($month != now()->month || $year != now()->year)
                        <a href="{{ route('reports.support', ['year' => now()->year, 'month' => now()->month]) }}" 
                           class="btn btn-sm btn-label-primary rounded-pill px-3 fw-semibold" 
                           data-bs-toggle="tooltip" title="Kembali ke Bulan Sekarang">
                            <i class="mdi mdi-calendar-today me-1"></i> Bulan Sekarang
                        </a>
                    @endif

                    {{-- Annual Report / Overview Button --}}
                    @php
                        $annualOverviewUrl = in_array(Auth::user()->role, ['Support', 'Marketing'])
                            ? url('/overview?report_id=full_' . $year)
                            : url('/overview/full_' . $year . '/' . ($supportId ?? Auth::id()));
                    @endphp
                    <a href="{{ $annualOverviewUrl }}" 
                       class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold waves-effect" 
                       data-bs-toggle="tooltip" title="Lihat Laporan & Overview Tahunan {{ $year }}">
                        <i class="mdi mdi-chart-areaspline me-1"></i> Annual Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== 5 TOP EXECUTIVE METRIC CARDS ===== --}}
    <div class="row g-3 mb-4">
        {{-- 1. Leads Masuk --}}
        <div class="col-12 col-sm-6 col-xl">
            <div class="card clean-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small">Leads Masuk</span>
                        <div class="metric-icon-box bg-label-primary text-primary">
                            <i class="mdi mdi-account-search-outline fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark">{{ number_format($mktProspectCount, 0, ',', '.') }}</h3>
                    <div class="d-flex align-items-center gap-1 flex-wrap mt-2">
                        <span class="badge bg-label-success small" title="Provided to sales">
                            <i class="mdi mdi-check-circle-outline me-1"></i>{{ $statusProvided }} Provided
                        </span>
                        <span class="badge bg-label-warning small" title="Pending">
                            {{ $statusPending }} Pending
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Quotation Dibuat --}}
        <div class="col-12 col-sm-6 col-xl">
            <div class="card clean-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small">Quotation Diterbitkan</span>
                        <div class="metric-icon-box bg-label-info text-info">
                            <i class="mdi mdi-file-document-edit-outline fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-dark">{{ number_format($mktQuoteCount, 0, ',', '.') }}</h3>
                    <div class="text-info fw-semibold small mt-2">
                        Rp {{ number_format($mktQuoteTotal, 0, ',', '.') }}
                    </div>
                    <small class="text-muted">Konversi: <strong>{{ $leadToQuoteRate }}%</strong></small>
                </div>
            </div>
        </div>

        {{-- 3. PO Closing --}}
        <div class="col-12 col-sm-6 col-xl">
            <div class="card clean-card h-100" style="border-left: 4px solid #28c76f;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small">Closing PO (Revenue)</span>
                        <div class="metric-icon-box bg-label-success text-success">
                            <i class="mdi mdi-cart-check fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-success">{{ number_format($mktPoCount, 0, ',', '.') }} <small class="fs-6 text-muted">PO</small></h3>
                    <div class="text-success fw-bold small mt-2">
                        Rp {{ number_format($mktPoTotal, 0, ',', '.') }}
                    </div>
                    <small class="text-muted">Win Rate: <strong>{{ $quoteToPoRate }}%</strong></small>
                </div>
            </div>
        </div>

        {{-- 4. Total Win Rate --}}
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
                    <small class="text-muted">Rasio Prospek &rarr; PO</small>
                </div>
            </div>
        </div>

        {{-- 5. Loss Summary --}}
        <div class="col-12 col-sm-6 col-xl">
            <div class="card clean-card h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small">Quotation Loss</span>
                        <div class="metric-icon-box bg-label-danger text-danger">
                            <i class="mdi mdi-close-circle-outline fs-4"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-1 text-danger">{{ number_format($mktLossCount, 0, ',', '.') }}</h3>
                    <div class="text-danger small mt-2 fw-semibold">
                        Rp {{ number_format($mktLossTotal, 0, ',', '.') }}
                    </div>
                    <small class="text-muted">Evaluasi Kualitas Lead</small>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MONTHLY MARKETING CONVERSION FUNNEL ===== --}}
    <div class="card clean-card mb-4">
        <div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="mdi mdi-filter-variant me-2 text-primary"></i>Marketing Funnel Konversi Bulan Ini
                </h5>
                <small class="text-muted">Pipeline: Leads Masuk &rarr; Diterbitkan Penawaran &rarr; Realisasi Closing PO</small>
            </div>
            <span class="badge bg-label-primary px-3 py-1">{{ $bulanMap[$month] }} {{ $year }}</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-center justify-content-center">
                {{-- Step 1: Leads Masuk --}}
                <div class="col-12 col-md-3">
                    <div class="funnel-step bg-label-secondary border border-secondary border-opacity-25 h-100">
                        <div class="avatar mx-auto mb-2">
                            <div class="avatar-initial bg-secondary text-white rounded">
                                <i class="mdi mdi-account-group-outline fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0 text-dark">{{ number_format($mktProspectCount, 0, ',', '.') }}</h2>
                        <span class="fw-semibold text-muted d-block">1. Leads / Prospek</span>
                        <small class="text-muted">100% Inflow Leads Masuk</small>
                    </div>
                </div>

                {{-- Arrow 1 --}}
                <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center">
                    <i class="mdi mdi-arrow-right fs-1 text-muted d-none d-md-block"></i>
                    <i class="mdi mdi-arrow-down fs-1 text-muted d-block d-md-none"></i>
                    <span class="funnel-badge-rate bg-label-primary mt-1" title="Lead to Quote Rate">
                        {{ $leadToQuoteRate }}%
                    </span>
                </div>

                {{-- Step 2: Quotation --}}
                <div class="col-12 col-md-3">
                    <div class="funnel-step bg-label-primary border border-primary border-opacity-25 h-100">
                        <div class="avatar mx-auto mb-2">
                            <div class="avatar-initial bg-primary text-white rounded">
                                <i class="mdi mdi-file-document-outline fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0 text-primary">{{ number_format($mktQuoteCount, 0, ',', '.') }}</h2>
                        <span class="fw-semibold text-dark d-block">2. Penawaran (Quotation)</span>
                        <small class="text-muted">Rp {{ number_format($mktQuoteTotal, 0, ',', '.') }}</small>
                    </div>
                </div>

                {{-- Arrow 2 --}}
                <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center">
                    <i class="mdi mdi-arrow-right fs-1 text-muted d-none d-md-block"></i>
                    <i class="mdi mdi-arrow-down fs-1 text-muted d-block d-md-none"></i>
                    <span class="funnel-badge-rate bg-label-success mt-1" title="Quote to PO Win Rate">
                        {{ $quoteToPoRate }}%
                    </span>
                </div>

                {{-- Step 3: PO Closed --}}
                <div class="col-12 col-md-3">
                    <div class="funnel-step bg-label-success border border-success border-opacity-25 h-100">
                        <div class="avatar mx-auto mb-2">
                            <div class="avatar-initial bg-success text-white rounded">
                                <i class="mdi mdi-cart-check fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0 text-success">{{ number_format($mktPoCount, 0, ',', '.') }}</h2>
                        <span class="fw-semibold text-dark d-block">3. Closing PO (Won)</span>
                        <small class="text-success fw-bold">Rp {{ number_format($mktPoTotal, 0, ',', '.') }}</small>
                    </div>
                </div>
            </div>

            {{-- Status Follow-up Progress Cards --}}
            <hr class="my-4">
            <h6 class="fw-bold text-dark mb-3">
                <i class="mdi mdi-clipboard-list-outline me-1 text-primary"></i>Status Tindak Lanjut Prospek Bulan Ini
            </h6>
            <div class="row g-3">
                {{-- Provided --}}
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="mdi mdi-check-circle-outline fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold text-dark">Provided</span>
                                <span class="fw-bold text-success">{{ $statusProvided }}</span>
                            </div>
                            <div class="progress progress-slim mt-1">
                                <div class="progress-bar bg-success" style="width:{{ $pctProvided }}%"></div>
                            </div>
                            <small class="text-muted">{{ $pctProvided }}% dialokasikan ke sales</small>
                        </div>
                    </div>
                </div>

                {{-- Pending --}}
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="mdi mdi-clock-outline fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold text-dark">Pending</span>
                                <span class="fw-bold text-warning">{{ $statusPending }}</span>
                            </div>
                            <div class="progress progress-slim mt-1">
                                <div class="progress-bar bg-warning" style="width:{{ $pctPending }}%"></div>
                            </div>
                            <small class="text-muted">{{ $pctPending }}% belum diproses</small>
                        </div>
                    </div>
                </div>

                {{-- No Provide --}}
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-danger rounded">
                                <i class="mdi mdi-close-circle-outline fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold text-dark">No Provide</span>
                                <span class="fw-bold text-danger">{{ $statusNoProvide }}</span>
                            </div>
                            <div class="progress progress-slim mt-1">
                                <div class="progress-bar bg-danger" style="width:{{ $pctNoProvide }}%"></div>
                            </div>
                            <small class="text-muted">{{ $pctNoProvide }}% tidak diteruskan</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TWO COLUMNS: SOURCES & SALES HANDOVER ===== --}}
    <div class="row g-4 mb-4">
        {{-- Left: Sources & Categories --}}
        <div class="col-12 col-lg-5">
            <div class="card clean-card h-100">
                <div class="card-header border-bottom py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="mdi mdi-source-branch me-2 text-primary"></i>Sumber & Kanal Prospek
                    </h5>
                    <small class="text-muted">Kanal akuisisi prospek yang masuk bulan ini</small>
                </div>
                <div class="card-body p-4">
                    {{-- Sources list --}}
                    <h6 class="fw-bold text-dark mb-3">Distribusi Kanal Masuk</h6>
                    @if ($mktProspectBySource->isNotEmpty())
                        <div class="list-group list-group-flush mb-4">
                            @php $maxSrc = $mktProspectBySource->max('total'); @endphp
                            @foreach ($mktProspectBySource as $src)
                                @php
                                    $s        = $sourceIcons[$src->source] ?? $sourceIcons['Other'];
                                    $pct      = $maxSrc > 0 ? round(($src->total / $maxSrc) * 100) : 0;
                                    $ofTotal  = $mktProspectCount > 0 ? round(($src->total / $mktProspectCount) * 100, 1) : 0;
                                    $isWebDom = $src->source === 'Website' && $mktProspectByDomain->isNotEmpty();
                                @endphp
                                <div class="list-group-item px-0 py-2 border-0">
                                    <div class="d-flex justify-content-between align-items-center mb-1"
                                         @if ($isWebDom)
                                             role="button" data-bs-toggle="collapse"
                                             data-bs-target="#collapseWebsiteDomainSupport"
                                             aria-expanded="false" style="cursor:pointer"
                                         @endif>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-xs flex-shrink-0">
                                                <div class="avatar-initial bg-label-{{ $s['color'] }} rounded">
                                                    <i class="mdi {{ $s['icon'] }}"></i>
                                                </div>
                                            </div>
                                            <span class="fw-semibold text-dark small">
                                                {{ $src->source }}
                                                @if ($isWebDom)
                                                    <i class="mdi mdi-chevron-down toggle-chevron text-muted ms-1"></i>
                                                @endif
                                            </span>
                                        </div>
                                        <span class="badge bg-label-{{ $s['color'] }}">{{ $src->total }} Leads ({{ $ofTotal }}%)</span>
                                    </div>
                                    <div class="progress progress-slim bg-label-secondary">
                                        <div class="progress-bar bg-{{ $s['color'] }}" role="progressbar" style="width: {{ $pct }}%"></div>
                                    </div>

                                    {{-- Collapse Website Domains --}}
                                    @if ($isWebDom)
                                        <div class="collapse mt-2 ps-4" id="collapseWebsiteDomainSupport">
                                            <div class="p-2 bg-light rounded">
                                                @foreach ($mktProspectByDomain as $dom)
                                                    <div class="d-flex justify-content-between align-items-center py-1 small">
                                                        <span class="text-muted">{{ $dom->domain }}</span>
                                                        <span class="badge bg-label-primary">{{ $dom->total }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">Belum ada data sumber prospek bulan ini</div>
                    @endif

                    <hr class="my-3">

                    {{-- Categories --}}
                    <h6 class="fw-bold text-dark mb-3">Kategori Kebutuhan Calon Klien</h6>
                    @if ($mktProspectByCategory->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($mktProspectByCategory as $cat)
                                <span class="badge bg-label-primary px-3 py-2 fs-6">
                                    {{ $cat->category }}: <strong>{{ $cat->total }}</strong>
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-2">Belum ada data kategori bulan ini</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Sales Handover Matrix --}}
        <div class="col-12 col-lg-7">
            <div class="card clean-card h-100">
                <div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">
                            <i class="mdi mdi-account-switch-outline me-2 text-primary"></i>Distribusi & Konversi Sales
                        </h5>
                        <small class="text-muted">Performa tim sales yang menindaklanjuti leads marketing bulan ini</small>
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
                                        Belum ada alokasi leads ke sales pada bulan ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== RECENT LEADS IN THIS MONTH TABLE ===== --}}
    <div class="card clean-card mb-4">
        <div class="card-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="mdi mdi-table-large me-2 text-primary"></i>Daftar Prospek Masuk Bulan Ini
                </h5>
                <small class="text-muted">Leads yang diinput dan ditangani pada periode {{ $bulanMap[$month] }} {{ $year }}</small>
            </div>
            <a href="{{ route('prospect.index') }}" class="btn btn-sm btn-outline-primary waves-effect">
                <i class="mdi mdi-open-in-new me-1"></i> Buka Master Prospek
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-custom-overview mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Klien / Perusahaan</th>
                        <th>PIC</th>
                        <th>Kategori Kebutuhan</th>
                        <th>Sales Ditugaskan</th>
                        <th class="text-center pe-4">Status Provide</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentProspects as $rp)
                        <tr>
                            <td class="ps-4 text-muted small">
                                {{ \Carbon\Carbon::parse($rp->date)->translatedFormat('d M Y') }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $rp->pic->client->company ?? '—' }}</div>
                                <small class="text-muted">{{ $rp->pic->client->source ?? 'Direct' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $rp->pic->name ?? '—' }}</div>
                                <small class="text-muted">{{ $rp->pic->phone ?? '' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-label-info">{{ $rp->category ?? 'Umum' }}</span>
                            </td>
                            <td>
                                @if ($rp->sales)
                                    <span class="fw-semibold text-dark">{{ $rp->sales->name }}</span>
                                @else
                                    <span class="badge bg-label-secondary">Belum Dialokasikan</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                @if ($rp->provide === '1')
                                    <span class="badge bg-label-success">Provided</span>
                                @elseif ($rp->provide === '0')
                                    <span class="badge bg-label-danger">No Provide</span>
                                @else
                                    <span class="badge bg-label-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada data prospek yang diinput pada bulan ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
