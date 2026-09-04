@extends('layouts.sales.app')
@section('title', 'Executive Sales Report - ' . ($user->name ?? 'Sales') . ' (' . ($dates ?? $date) . ')')

@section('content')
    @php
        $lastDetail = $user->detail ? $user->detail->last() : null;
        $userArea = $lastDetail ? $lastDetail->area : ($user->area ?? 'Sales Area');
        $targetTotal = (isset($target->total) && $target->total > 0) ? (float) $target->total : 0;
        $salesPercentage = $targetTotal > 0 ? round((($amountSales ?? 0) / $targetTotal) * 100, 1) : 0;
        $salesColor = $salesPercentage >= 100 ? 'success' : ($salesPercentage >= 80 ? 'warning' : 'danger');

        // Calculate Total Paid (Accounting Confirmed) for POs this month
        $paidRegular = $quotation->sum(function($q) {
            return $q->payment ? $q->payment->filter(fn($p) => (int)$p->level === 1 || !empty($p->date_confirm))->sum('amount') : 0;
        });
        $paidUnit = $unitQuotationPO->sum(function($uq) {
            return $uq->payments ? $uq->payments->filter(fn($p) => (int)$p->level === 1 || !empty($p->date_confirm))->sum('amount') : 0;
        });
        $amountPaidSales = $paidRegular + $paidUnit;
        $paidPercentage = ($amountSales ?? 0) > 0 ? round(($amountPaidSales / $amountSales) * 100, 1) : 0;

        try {
            $parsedDate = \Carbon\Carbon::createFromFormat('d-m-Y', '01-' . ($dates ?? $date));
        } catch (\Exception $e) {
            $parsedDate = \Carbon\Carbon::now();
        }
        $month = $parsedDate->month;
        $year = $parsedDate->year;
        $prevCarbon = (clone $parsedDate)->subMonth();
        $nextCarbon = (clone $parsedDate)->addMonth();
        $prevMonthDate = $prevCarbon->format('m-Y');
        $nextMonthDate = $nextCarbon->format('m-Y');
        $dateFormatted = sprintf('%02d-%04d', $month, $year);

        $currentYear = \Carbon\Carbon::now()->year;
        $yearsList = range(max($currentYear + 1, $year), 2022);
    @endphp

    <!-- Executive Header Card with Filter Options -->
    <div class="card clean-card mb-4 overflow-hidden position-relative">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                        <span class="badge bg-label-primary rounded-pill px-3 py-1 fw-semibold">
                            <i class="mdi mdi-calendar-text me-1"></i> Periode {{ $parsedDate->locale('id')->translatedFormat('F Y') }}
                        </span>
                        @if (Auth::user()->role == 'Admin')
                            <span class="badge bg-label-warning rounded-pill px-3 py-1">
                                <i class="mdi mdi-shield-account-outline me-1"></i> Admin Overview
                            </span>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-0 text-dark">Executive Sales Performance Report</h4>
                    <small class="text-muted">
                        Laporan Performa & Operational KPI &mdash; <strong>{{ $user->name }}</strong> ({{ $userArea }})
                    </small>
                </div>

                <!-- Clean & Modern Filter Toolbar -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="d-flex align-items-center flex-wrap mb-0 gap-2">
                        <!-- Segmented Date Navigator Pill Group -->
                        <div class="d-inline-flex align-items-center bg-body-tertiary border rounded-pill p-1">
                            <!-- Prev Month Button -->
                            <a href="{{ url('/detail-overview/' . $user->id . '/' . $prevMonthDate) }}" 
                               class="btn btn-icon btn-sm btn-label-secondary rounded-circle shadow-none" 
                               style="width: 28px; height: 28px;"
                               data-bs-toggle="tooltip" title="Bulan Sebelumnya ({{ $prevCarbon->locale('id')->translatedFormat('M Y') }})">
                                <i class="mdi mdi-chevron-left fs-5"></i>
                            </a>

                            <!-- Month Select -->
                            <select class="form-select form-select-sm border-0 bg-transparent fw-semibold text-dark text-center px-1" 
                                    style="min-width: 110px; font-size: 0.82rem; cursor: pointer;" 
                                    onchange="var ym = String(this.value).padStart(2, '0') + '-{{ $year }}'; window.location.href = '{{ url('/detail-overview/' . $user->id) }}/' + ym;">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->locale('id')->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>

                            <span class="text-muted opacity-25">|</span>

                            <!-- Year Select -->
                            <select class="form-select form-select-sm border-0 bg-transparent fw-semibold text-dark text-center px-1" 
                                    style="min-width: 78px; font-size: 0.82rem; cursor: pointer;" 
                                    onchange="var ym = '{{ sprintf('%02d', $month) }}-' + this.value; window.location.href = '{{ url('/detail-overview/' . $user->id) }}/' + ym;">
                                @foreach ($yearsList as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Next Month Button -->
                            <a href="{{ url('/detail-overview/' . $user->id . '/' . $nextMonthDate) }}" 
                               class="btn btn-icon btn-sm btn-label-secondary rounded-circle shadow-none" 
                               style="width: 28px; height: 28px;"
                               data-bs-toggle="tooltip" title="Bulan Berikutnya ({{ $nextCarbon->locale('id')->translatedFormat('M Y') }})">
                                <i class="mdi mdi-chevron-right fs-5"></i>
                            </a>
                        </div>

                        <!-- Return to Current Month Button -->
                        @if ($month != now()->month || $year != now()->year)
                            <a href="{{ url('/detail-overview/' . $user->id . '/' . now()->format('m-Y')) }}" 
                               class="btn btn-sm btn-label-primary rounded-pill px-3 fw-semibold" 
                               data-bs-toggle="tooltip" title="Kembali ke Bulan Sekarang">
                                <i class="mdi mdi-calendar-today me-1"></i> Bulan Ini
                            </a>
                        @endif

                        <a href="{{ url('/reports') }}?month={{ $month }}&year={{ $year }}&sales={{ $user->id }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                            <i class="mdi mdi-file-chart-outline me-1"></i> Laporan Sales
                        </a>

                        <a href="{{ route('overview.semester', $user->id) }}" class="btn btn-sm btn-label-secondary rounded-pill px-3 fw-semibold">
                            <i class="mdi mdi-arrow-left me-1"></i> Overview
                        </a>
                    </div>
                </div>
            </div>

            <hr class="my-3 text-muted opacity-25">

            <!-- Sales Target & Profile Info Row -->
            <div class="row align-items-center gy-3 pt-1">
                <div class="col-12 col-md-auto text-center text-md-start">
                    <div class="position-relative d-inline-block">
                        @if ($user->image)
                            <img src="{{ url('') . '/' . $user->image }}" alt="{{ $user->name }}"
                                class="rounded-circle border border-3 border-primary shadow-xs"
                                style="width: 68px; height: 68px; object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-label-primary d-flex align-items-center justify-content-center border border-3 border-primary shadow-xs"
                                style="width: 68px; height: 68px; font-size: 24px; font-weight: bold;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-2 border-white rounded-circle">
                            <span class="visually-hidden">Active</span>
                        </span>
                    </div>
                </div>
                <div class="col-12 col-md">
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1 justify-content-center justify-content-md-start">
                        <h5 class="mb-0 fw-bold text-dark">{{ $user->name }}</h5>
                        <span class="badge bg-label-primary rounded-pill px-3 py-1">
                            <i class="mdi mdi-map-marker-outline me-1"></i> {{ $userArea }}
                        </span>
                    </div>
                    <p class="text-muted mb-0 small text-center text-md-start">
                        Target Bulanan & Pencapaian Sales &mdash; <strong>{{ $parsedDate->locale('id')->translatedFormat('F Y') }}</strong>
                    </p>
                </div>
                <div class="col-12 col-md-auto text-center text-md-end border-start-md ps-md-4">
                    <small class="text-muted fw-semibold d-block mb-1">Target Sales Bulanan</small>
                    <h4 class="text-primary fw-bold mb-0">Rp {{ number_format($targetTotal, 0, ',', '.') }}</h4>
                    <span class="badge bg-label-{{ $salesColor }} rounded-pill px-3 py-1 mt-1">
                        Pencapaian: {{ $salesPercentage }}%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4 mb-4">
        <!-- Left Column: KPI Operational Micro Cards -->
        <div class="col-12 col-xl-7">
            <div class="card clean-card h-100">
                <div class="card-header d-flex align-items-center justify-content-between pb-2">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="mdi mdi-chart-box-outline text-primary fs-4"></i> Operational KPI Summary
                    </h5>
                    <small class="text-muted">Target Performa Bulan Ini</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- New Leads -->
                        @php
                            $tLeads = $target->leads ?? 0;
                            $pLeads = $tLeads > 0 ? round(($totalLeads / $tLeads) * 100) : 0;
                        @endphp
                        <div class="col-6 col-md-4">
                            <a href="#activities" class="text-decoration-none">
                                <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-label-secondary p-2 rounded-circle">
                                            <i class="mdi mdi-account-multiple-plus-outline fs-5"></i>
                                        </span>
                                        <span class="badge bg-label-secondary rounded-pill fs-tiny">{{ $pLeads }}%</span>
                                    </div>
                                    <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">New Leads</small>
                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                        <h5 class="mb-0 fw-bold text-dark">{{ $totalLeads }}</h5>
                                        <small class="text-muted" style="font-size: 0.7rem;">/ {{ $tLeads }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Daily Call -->
                        @php
                            $tDc = $target->dc ?? 0;
                            $pDc = $tDc > 0 ? round(($totalDC / $tDc) * 100) : 0;
                        @endphp
                        <div class="col-6 col-md-4">
                            <a href="#activities" class="text-decoration-none">
                                <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-label-info p-2 rounded-circle">
                                            <i class="mdi mdi-phone-outline fs-5"></i>
                                        </span>
                                        <span class="badge bg-label-info rounded-pill fs-tiny">{{ $pDc }}%</span>
                                    </div>
                                    <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Daily Call</small>
                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                        <h5 class="mb-0 fw-bold text-dark">{{ $totalDC }}</h5>
                                        <small class="text-muted" style="font-size: 0.7rem;">/ {{ $tDc }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- CRM -->
                        @php
                            $tCrm = $target->crm ?? 0;
                            $pCrm = $tCrm > 0 ? round(($totalCRM / $tCrm) * 100) : 0;
                        @endphp
                        <div class="col-6 col-md-4">
                            <a href="#activities" class="text-decoration-none">
                                <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-label-primary p-2 rounded-circle">
                                            <i class="mdi mdi-account-multiple-outline fs-5"></i>
                                        </span>
                                        <span class="badge bg-label-primary rounded-pill fs-tiny">{{ $pCrm }}%</span>
                                    </div>
                                    <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">CRM</small>
                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                        <h5 class="mb-0 fw-bold text-dark">{{ $totalCRM }}</h5>
                                        <small class="text-muted" style="font-size: 0.7rem;">/ {{ $tCrm }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Visit (if applicable) -->
                        @if (in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat']))
                            @php
                                $tVisit = $target->visit ?? 0;
                                $pVisit = $tVisit > 0 ? round(($totalVisit / $tVisit) * 100) : 0;
                            @endphp
                            <div class="col-6 col-md-4">
                                <a href="#activities" class="text-decoration-none">
                                    <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-label-warning p-2 rounded-circle">
                                                <i class="mdi mdi-map-marker-outline fs-5"></i>
                                            </span>
                                            <span class="badge bg-label-warning rounded-pill fs-tiny">{{ $pVisit }}%</span>
                                        </div>
                                        <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Customer Visit</small>
                                        <div class="d-flex align-items-baseline gap-1 mt-1">
                                            <h5 class="mb-0 fw-bold text-dark">{{ $totalVisit }}</h5>
                                            <small class="text-muted" style="font-size: 0.7rem;">/ {{ $tVisit }}</small>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif

                        <!-- Quotation -->
                        @php
                            $tQuote = $target->quote ?? 0;
                            $pQuote = $tQuote > 0 ? round((($totalQuoteAll ?? $totalQuote) / $tQuote) * 100) : 0;
                        @endphp
                        <div class="col-6 col-md-4">
                            <a href="#pipeline" class="text-decoration-none">
                                <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-label-info p-2 rounded-circle">
                                            <i class="mdi mdi-file-document-outline fs-5"></i>
                                        </span>
                                        <span class="badge bg-label-info rounded-pill fs-tiny">{{ $pQuote }}%</span>
                                    </div>
                                    <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Quotation</small>
                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                        <h5 class="mb-0 fw-bold text-dark">{{ $totalQuoteAll ?? $totalQuote }}</h5>
                                        <small class="text-muted" style="font-size: 0.7rem;">/ {{ $tQuote }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Purchase Order (PO) -->
                        <div class="col-6 col-md-4">
                            <a href="#po" class="text-decoration-none">
                                <div class="p-3 border rounded-3 bg-success-subtle border-success-subtle h-100">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-success text-white p-2 rounded-circle">
                                            <i class="mdi mdi-cart-plus fs-5"></i>
                                        </span>
                                        <span class="badge bg-success text-white rounded-pill fs-tiny">Active</span>
                                    </div>
                                    <small class="text-success-emphasis d-block fw-semibold" style="font-size: 0.78rem;">Purchase Order</small>
                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                        <h5 class="mb-0 fw-bold text-success">{{ $totalPO }}</h5>
                                        <small class="text-muted" style="font-size: 0.7rem;">deals closed</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Financial Realization Card (Clean & Modern SaaS Dashboard Style) -->
        <div class="col-12 col-xl-5">
            <div class="card clean-card h-100 d-flex flex-column justify-content-between">
                <!-- Card Header -->
                <div class="card-header pb-2">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-label-primary p-2 rounded-3">
                                <i class="mdi mdi-currency-usd fs-5"></i>
                            </span>
                            <div>
                                <h5 class="card-title mb-0 fw-bold text-dark">Realisasi Sales</h5>
                                <small class="text-muted">Periode {{ $parsedDate->locale('id')->translatedFormat('F Y') }}</small>
                            </div>
                        </div>
                        <div>
                            @if ($salesPercentage >= 100)
                                <span class="badge bg-label-success rounded-pill px-3 py-1 fw-bold">
                                    <i class="mdi mdi-check-circle-outline me-1"></i>{{ $salesPercentage }}% Target
                                </span>
                            @elseif ($salesPercentage >= 80)
                                <span class="badge bg-label-warning rounded-pill px-3 py-1 fw-bold">
                                    <i class="mdi mdi-fire me-1"></i>{{ $salesPercentage }}% On Track
                                </span>
                            @else
                                <span class="badge bg-label-primary rounded-pill px-3 py-1 fw-bold">
                                    {{ $salesPercentage }}% Target
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="card-body py-3 d-flex flex-column justify-content-between">
                    <!-- Hero Revenue Display & Paid Status -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <small class="text-muted fw-semibold text-uppercase" style="font-size: 0.73rem; letter-spacing: 0.5px;">
                                Total Closing (PO Received)
                            </small>
                            <span class="badge bg-label-success rounded-pill px-2 py-0 fw-bold" style="font-size: 0.72rem;">
                                <i class="mdi mdi-cash-check me-1"></i>Terbayar: {{ $paidPercentage }}%
                            </span>
                        </div>
                        <div class="d-flex align-items-baseline justify-content-between flex-wrap gap-2">
                            <h2 class="mb-0 fw-bold text-dark" style="letter-spacing: -0.5px;">
                                Rp {{ number_format($amountSales, 0, ',', '.') }}
                            </h2>
                        </div>
                        
                        <!-- Financial Sub-Metrics (Paid, Target, Gap) -->
                        <div class="d-flex align-items-center gap-2 text-muted small mt-2 flex-wrap">
                            <div class="d-inline-flex align-items-center gap-1">
                                <span class="badge bg-label-success px-2 py-1 rounded fw-semibold text-dark" style="font-size: 0.73rem;">
                                    <i class="mdi mdi-check-circle-outline text-success me-1"></i>Sudah Dibayar: <strong>Rp {{ number_format($amountPaidSales, 0, ',', '.') }}</strong>
                                </span>
                            </div>
                            <span class="opacity-50">•</span>
                            <div>
                                <span class="opacity-75">Target:</span> 
                                <span class="fw-semibold text-dark">Rp {{ number_format($targetTotal, 0, ',', '.') }}</span>
                            </div>
                            <span class="opacity-50">•</span>
                            <div>
                                @if ($amountSales >= $targetTotal && $targetTotal > 0)
                                    <span class="badge bg-label-success rounded-pill px-2 py-0 fw-bold" style="font-size: 0.72rem;">
                                        +Rp {{ number_format($amountSales - $targetTotal, 0, ',', '.') }} (Surplus)
                                    </span>
                                @else
                                    <span class="opacity-75">Sisa:</span> 
                                    <span class="fw-semibold text-danger">Rp {{ number_format(max(0, $targetTotal - $amountSales), 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Clean Progress Bar -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <small class="text-muted fw-semibold">Pencapaian Target Bulanan</small>
                            <small class="fw-bold text-dark">{{ $salesPercentage }}%</small>
                        </div>
                        <div class="progress" style="height: 8px; background-color: rgba(67, 89, 113, 0.08); border-radius: 6px;">
                            <div class="progress-bar bg-{{ $salesColor }} rounded-pill" role="progressbar" 
                                style="width: {{ min($salesPercentage, 100) }}%;" 
                                aria-valuenow="{{ $salesPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <!-- Pipeline Sub-Cards -->
                    <div class="row g-2">
                        <!-- Hot Prospect (80%) -->
                        <div class="col-6">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <small class="text-muted fw-semibold" style="font-size: 0.75rem;">Hot Prospect (80%)</small>
                                    <span class="badge bg-label-danger p-1 rounded-circle">
                                        <i class="mdi mdi-fire fs-tiny"></i>
                                    </span>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.92rem;">
                                    Rp {{ number_format($amountProspect, 0, ',', '.') }}
                                </h6>
                                <small class="text-muted" style="font-size: 0.7rem;">Potensi closing segera</small>
                            </div>
                        </div>

                        <!-- Pipeline Quote Aktif -->
                        <div class="col-6">
                            <div class="p-3 border rounded-3 bg-body-tertiary h-100">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <small class="text-muted fw-semibold" style="font-size: 0.75rem;">Pipeline Quote Aktif</small>
                                    <span class="badge bg-label-info p-1 rounded-circle">
                                        <i class="mdi mdi-file-document-outline fs-tiny"></i>
                                    </span>
                                </div>
                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.92rem;">
                                    Rp {{ number_format($amountQuoteActive ?? $amountQuote, 0, ',', '.') }}
                                </h6>
                                <small class="text-muted" style="font-size: 0.7rem;">Penawaran aktif</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Footer Action Strip -->
                <div class="card-footer py-2 px-4 border-top d-flex align-items-center justify-content-between bg-body-tertiary rounded-bottom flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2 text-muted small">
                        <span class="d-inline-flex align-items-center gap-1">
                            <i class="mdi mdi-cart-check text-success fs-5"></i>
                            <strong class="text-dark">{{ $totalPO }}</strong> Deals Closed
                        </span>
                        <span class="opacity-50">•</span>
                        <span class="text-success fw-semibold">
                            <i class="mdi mdi-cash-check me-1"></i>Terbayar: Rp {{ number_format($amountPaidSales, 0, ',', '.') }}
                        </span>
                    </div>
                    <a href="#po" class="btn btn-xs btn-label-primary rounded-pill fw-semibold">
                        Rincian PO <i class="mdi mdi-chevron-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 1: Rekap KPI Mingguan (gaya sheet Excel Sales Manager) --}}
    <div class="mb-4" id="weekly-kpi">
        @include('components.overview.weekly-kpi-card', [
            'weeklyKpi' => $weeklyKpi,
            'monthLabel' => $parsedDate->locale('id')->translatedFormat('F Y'),
            'salesId' => $user->id,
            'dateFormatted' => $parsedDate->format('m-Y'),
        ])
    </div>

    {{-- Commercial Pipeline (Quotation, Purchase Order & Lost) --}}
    <div class="card clean-card mb-4 overflow-hidden" id="pipeline">
        <div class="card-header border-bottom bg-white p-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-file-chart-outline text-primary mdi-24px"></i>
                <h5 class="card-title mb-0 fw-bold">Commercial Pipeline (Quotation, Purchase Order & Lost)</h5>
            </div>
            <ul class="nav nav-pills nav-pills-custom flex-wrap gap-2" id="pipelineTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active d-flex align-items-center gap-2" id="quote-all-tab" data-bs-toggle="tab" data-bs-target="#tab-quote-all" type="button" role="tab" aria-controls="tab-quote-all" aria-selected="true">
                        <i class="mdi mdi-file-document-multiple-outline"></i>
                        <span>Semua Quotation</span>
                        <span class="badge rounded-pill bg-label-primary">{{ $totalQuoteAll }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="quote-active-tab" data-bs-toggle="tab" data-bs-target="#tab-quote-active" type="button" role="tab" aria-controls="tab-quote-active" aria-selected="false">
                        <i class="mdi mdi-clock-outline"></i>
                        <span>Quotation Active</span>
                        <span class="badge rounded-pill bg-label-warning">{{ $totalQuoteActive }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="po-tab" data-bs-toggle="tab" data-bs-target="#tab-po" type="button" role="tab" aria-controls="tab-po" aria-selected="false">
                        <i class="mdi mdi-cart-check"></i>
                        <span>Purchase Order</span>
                        <span class="badge rounded-pill bg-label-success">{{ $totalPO }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link d-flex align-items-center gap-2" id="loss-tab" data-bs-toggle="tab" data-bs-target="#tab-loss" type="button" role="tab" aria-controls="tab-loss" aria-selected="false">
                        <i class="mdi mdi-cart-minus"></i>
                        <span>Lost Quotation</span>
                        <span class="badge rounded-pill bg-label-danger">{{ $totalLoss }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content p-0" id="pipelineTabsContent">
                {{-- Tab 1: Quotations (Semua Status) --}}
                <div class="tab-pane fade show active" id="tab-quote-all" role="tabpanel" aria-labelledby="quote-all-tab">
                    <div class="card-datatable table-responsive">
                        <table class="datatable-overview-quotation table table-hover border-top" id="tableOverviewQuotation" data-url="{{ url('/db/overview/quotation/' . $user->id . '/' . $dateFormatted) }}">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th class="text-nowrap">Date Quotation</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Tab 2: Quotations Active (Belum Loss & Belum PO) --}}
                <div class="tab-pane fade" id="tab-quote-active" role="tabpanel" aria-labelledby="quote-active-tab">
                    <div class="card-datatable table-responsive">
                        <table class="datatable-overview-quotation-active table table-hover border-top" id="tableOverviewQuotationActive" data-url="{{ url('/db/overview/quotation-active/' . $user->id . '/' . $dateFormatted) }}">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th class="text-nowrap">Date Quotation</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Tab 3: Purchase Orders --}}
                <div class="tab-pane fade" id="tab-po" role="tabpanel" aria-labelledby="po-tab">
                    <div class="card-datatable table-responsive">
                        <table class="datatable-overview-po table table-hover border-top" id="tableOverviewPo" data-url="{{ url('/db/overview/po/' . $user->id . '/' . $dateFormatted) }}">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Description</th>
                                    <th class="text-nowrap">Date PO</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Tab 4: Lost Quotations --}}
                <div class="tab-pane fade" id="tab-loss" role="tabpanel" aria-labelledby="loss-tab">
                    <div class="card-datatable table-responsive">
                        <table class="datatable-overview-loss table table-hover border-top" id="tableOverviewLoss" data-url="{{ url('/db/overview/loss/' . $user->id . '/' . $dateFormatted) }}">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Description</th>
                                    <th class="text-nowrap">Date</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Purchase Order Records Table Card -->
    <div class="card clean-card mb-4" id="po">
        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-file-document-check text-success fs-4"></i> Rincian Closing Purchase Order (PO Received)
            </h5>
            <span class="badge bg-label-success px-3 py-1 rounded-pill fw-bold">
                {{ count($quotation) + count($unitQuotationPO) }} PO Total
            </span>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold">No. Quotation</th>
                        <th class="fw-bold">Perusahaan / Customer</th>
                        <th class="fw-bold">Judul Penawaran</th>
                        <th class="fw-bold">Tanggal PO</th>
                        <th class="fw-bold text-end">Nilai Nett (Rp)</th>
                        <th class="fw-bold text-end">PAID</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @php
                        $totalP = 0;
                        $totalPaid = 0;
                    @endphp
                    @forelse ($quotation as $quote)
                        @php
                            $totalQ = $quote['nett'] ?? 0;
                            $totalP += $totalQ;
                            $paidPayments = $quote->payment ? $quote->payment->filter(fn($p) => (int)$p->level === 1 || !empty($p->date_confirm)) : collect();
                            $paidAmount = $paidPayments->sum('amount');
                            $totalPaid += $paidAmount;
                            $paidPct = $totalQ > 0 ? min(100, round(($paidAmount / $totalQ) * 100)) : ($paidAmount > 0 ? 100 : 0);
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('quotation.show', $quote->id) }}" class="fw-bold text-primary">
                                    {{ $quote->no_quote }}
                                </a>
                            </td>
                            <td>{{ $quote->pic->client->company ?? '-' }}</td>
                            <td>{{ $quote->title }}</td>
                            <td>
                                <span class="badge bg-label-secondary">
                                    {{ \Carbon\Carbon::parse($quote->po_date ?? $quote->estimated_date)->format('d-m-Y') }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($totalQ, 0, ',', '.') }}</td>
                            <td class="text-end">
                                @if ($paidAmount > 0)
                                    <div class="fw-bold text-success">Rp {{ number_format($paidAmount, 0, ',', '.') }}</div>
                                    @if ($paidPct >= 100)
                                        <span class="badge bg-label-success rounded-pill px-2 py-0" style="font-size: 0.7rem;">
                                            <i class="mdi mdi-check-circle-outline me-1"></i>Lunas (100%)
                                        </span>
                                    @else
                                        <span class="badge bg-label-warning rounded-pill px-2 py-0" style="font-size: 0.7rem;">
                                            {{ $paidPct }}% Paid
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-label-secondary rounded-pill px-2 py-1 text-muted" style="font-size: 0.7rem;">
                                        Rp 0 (Unpaid)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    @forelse ($unitQuotationPO as $uq)
                        @php
                            $uqNett  = $uq->total - $uq->tax_amount;
                            $totalP += $uqNett;
                            $uqDate  = $uq->po_received ?? $uq->statusHistory->first()?->created_at;
                            $paidPayments = $uq->payments ? $uq->payments->filter(fn($p) => (int)$p->level === 1 || !empty($p->date_confirm)) : collect();
                            $paidAmount = $paidPayments->sum('amount');
                            $totalPaid += $paidAmount;
                            $paidPct = $uqNett > 0 ? min(100, round(($paidAmount / $uqNett) * 100)) : ($paidAmount > 0 ? 100 : 0);
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('unit-quotation.show', $uq->id) }}" class="fw-bold text-primary">
                                    {{ $uq->no_quote }}
                                </a>
                                <span class="badge bg-label-info ms-1">Smart</span>
                            </td>
                            <td>{{ $uq->client?->company ?? '-' }}</td>
                            <td>{{ $uq->title ?? '-' }}</td>
                            <td>
                                <span class="badge bg-label-secondary">
                                    {{ $uqDate ? \Carbon\Carbon::parse($uqDate)->format('d-m-Y') : '-' }}
                                </span>
                            </td>
                            <td class="text-end fw-semibold text-dark">Rp {{ number_format($uqNett, 0, ',', '.') }}</td>
                            <td class="text-end">
                                @if ($paidAmount > 0)
                                    <div class="fw-bold text-success">Rp {{ number_format($paidAmount, 0, ',', '.') }}</div>
                                    @if ($paidPct >= 100)
                                        <span class="badge bg-label-success rounded-pill px-2 py-0" style="font-size: 0.7rem;">
                                            <i class="mdi mdi-check-circle-outline me-1"></i>Lunas (100%)
                                        </span>
                                    @else
                                        <span class="badge bg-label-warning rounded-pill px-2 py-0" style="font-size: 0.7rem;">
                                            {{ $paidPct }}% Paid
                                        </span>
                                    @endif
                                @else
                                    <span class="badge bg-label-secondary rounded-pill px-2 py-1 text-muted" style="font-size: 0.7rem;">
                                        Rp 0 (Unpaid)
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    @if (count($quotation) == 0 && count($unitQuotationPO) == 0)
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Tidak ada data Purchase Order (PO) closing pada periode ini.
                            </td>
                        </tr>
                    @else
                        <tr class="bg-body-tertiary">
                            <td colspan="3" class="border-0"></td>
                            <td class="fw-bold text-dark fs-6">Total:</td>
                            <td class="text-end fw-bold text-dark fs-6">Rp {{ number_format($totalP, 0, ',', '.') }}</td>
                            <td class="text-end fw-bold text-success fs-5">Rp {{ number_format($totalPaid, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <style>
        .clean-card {
            border: 0;
            border-radius: 12px;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.08);
            background: #ffffff;
        }
        .fs-tiny {
            font-size: 0.7rem;
        }
        @media (min-width: 768px) {
            .border-start-md {
                border-left: 1px solid rgba(67, 89, 113, 0.12) !important;
            }
        }
        .nav-pills-custom .nav-link {
            border-radius: 8px;
            padding: 10px 18px;
            font-weight: 500;
            color: #566a7f;
            transition: all 0.2s ease;
            background: #ffffff;
            border: 1px solid #e0e4e8;
        }
        .nav-pills-custom .nav-link:hover {
            background: rgba(105, 108, 255, 0.08);
            color: #696cff;
            border-color: #696cff;
        }
        .nav-pills-custom .nav-link.active {
            background: #696cff;
            color: #ffffff;
            border-color: #696cff;
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
        }
        .nav-pills-custom .nav-link.active .badge {
            background: rgba(255, 255, 255, 0.25) !important;
            color: #ffffff !important;
        }
        .dt-control-btn {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background: rgba(105, 108, 255, 0.08);
            color: #696cff;
            border: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .dt-control-btn:hover {
            background: #696cff;
            color: #ffffff;
        }
        tr.shown .dt-control-btn {
            background: rgba(255, 62, 29, 0.1);
            color: #ff3e1d;
        }
        tr.shown .dt-control-btn:hover {
            background: #ff3e1d;
            color: #ffffff;
        }
        .minimal-feed-item {
            background: #ffffff;
            border: 1px solid #edf0f2;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }
        .minimal-feed-item:last-child {
            margin-bottom: 0;
        }
        .minimal-feed-item:hover {
            background: #fafbfe;
            border-color: #dbe0ea;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .minimal-feed-item.feed-success {
            border-left: 4px solid #71dd37;
        }
        .minimal-feed-item.feed-danger {
            border-left: 4px solid #ff3e1d;
        }
        .minimal-feed-avatar {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .minimal-note-text {
            color: #566a7f;
            font-size: 0.84rem;
            line-height: 1.45;
        }
    </style>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/includes/table-overview-quotation.js?v={{ time() }}"></script>
    <script src="{{ asset('assets') }}/includes/table-overview-quotation-active.js?v={{ time() }}"></script>
    <script src="{{ asset('assets') }}/includes/table-overview-po.js?v={{ time() }}"></script>
    <script src="{{ asset('assets') }}/includes/table-overview-loss.js?v={{ time() }}"></script>
    <script>
        $(document).ready(function() {
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                if ($.fn.dataTable) {
                    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                }
            });
        });
    </script>
@endpush
