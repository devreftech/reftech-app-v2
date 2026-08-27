@extends('layouts.sales.app')
@section('title', 'Executive Sales Report - ' . ($salesUser->name ?? 'Sales'))
@section('content')
    @php
        $user = $salesUser ?? Auth::user();
        $lastDetail = $user->detail->last();
        $userArea = $lastDetail->area ?? ($user->latestRole->area ?? 'Sales Area');
        $targetTotal = $target->total ?? 0;
        $salesPercentage = $targetTotal > 0 ? round(($amountSales / $targetTotal) * 100, 1) : 0;
        $salesColor = $salesPercentage >= 100 ? 'success' : ($salesPercentage >= 80 ? 'warning' : 'danger');
        $today = \Carbon\Carbon::now();
    @endphp

    <!-- Executive Header Card with Filter Options -->
    <div class="card clean-card mb-4 overflow-hidden position-relative">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-label-primary fs-6 px-3 py-2">
                            <i class="mdi mdi-calendar-text me-1"></i> Periode Laporan
                        </span>
                        <span class="text-muted fw-semibold fs-5">
                            {{ \Carbon\Carbon::createFromDate($yearNow, $monthNow, 1)->locale('id')->translatedFormat('F Y') }}
                        </span>
                        @if (Auth::user()->role == 'Admin')
                            <span class="badge bg-label-warning rounded-pill px-3 py-1">
                                <i class="mdi mdi-shield-account-outline me-1"></i> Admin View
                            </span>
                        @endif
                    </div>
                    <h4 class="fw-bold mb-0 text-dark">Executive Sales Performance Report</h4>
                    <small class="text-muted">
                        Laporan Performa & Operational KPI &mdash; <strong>{{ $user->name }}</strong> ({{ $userArea }})
                    </small>
                </div>

                <!-- Filter Options in 1 Single Bar: Sales Dropdown (if admin) | Prev | Bulan | Tahun | Next -->
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <form action="{{ url('/reports') }}" method="GET" id="filterReportForm" class="d-flex align-items-center flex-wrap mb-0 gap-2">
                        @if (Auth::user()->role == 'Admin' && isset($salesList))
                            <div class="input-group input-group-sm flex-nowrap" style="min-width: 210px;">
                                <span class="input-group-text bg-label-primary border-primary text-primary fw-semibold">
                                    <i class="mdi mdi-account-tie me-1"></i> Sales
                                </span>
                                <select name="sales" class="form-select border-primary text-primary fw-semibold" onchange="document.getElementById('filterReportForm').submit()">
                                    @foreach ($salesList as $s)
                                        <option value="{{ $s->id }}" {{ $s->id == ($salesId ?? $user->id) ? 'selected' : '' }}>
                                            {{ $s->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="input-group input-group-sm flex-nowrap">
                            <!-- Prev Month Button -->
                            <a href="{{ url('/reports') }}?month={{ $prevMonth }}&year={{ $prevYear }}{{ Auth::user()->role == 'Admin' && isset($salesId) ? '&sales=' . $salesId : '' }}" 
                               class="btn btn-outline-primary waves-effect" 
                               data-bs-toggle="tooltip" title="Bulan Sebelumnya">
                                <i class="mdi mdi-chevron-left me-1"></i> Prev
                            </a>

                            <!-- Month Select Dropdown -->
                            <select name="month" class="form-select border-primary text-primary fw-semibold" style="min-width: 125px;" onchange="document.getElementById('filterReportForm').submit()">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $m == $monthNow ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::createFromDate(2026, $m, 1)->locale('id')->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>

                            <!-- Year Select Dropdown -->
                            <select name="year" class="form-select border-primary text-primary fw-semibold" style="min-width: 95px;" onchange="document.getElementById('filterReportForm').submit()">
                                @foreach ($yearsList as $y)
                                    <option value="{{ $y }}" {{ $y == $yearNow ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Next Month Button -->
                            <a href="{{ url('/reports') }}?month={{ $nextMonth }}&year={{ $nextYear }}{{ Auth::user()->role == 'Admin' && isset($salesId) ? '&sales=' . $salesId : '' }}" 
                               class="btn btn-outline-primary waves-effect" 
                               data-bs-toggle="tooltip" title="Bulan Berikutnya">
                                Next <i class="mdi mdi-chevron-right ms-1"></i>
                            </a>
                        </div>

                        @if ($monthNow != now()->month || $yearNow != now()->year)
                            <a href="{{ url('/reports') }}{{ Auth::user()->role == 'Admin' && isset($salesId) ? '?sales=' . $salesId : '' }}" class="btn btn-sm btn-outline-secondary waves-effect" data-bs-toggle="tooltip" title="Kembali ke Bulan Sekarang">
                                <i class="mdi mdi-reload me-1"></i> Sekarang
                            </a>
                        @endif

                        @if (Auth::user()->role == 'Admin')
                            <a href="{{ url('/overview') }}" class="btn btn-sm btn-label-secondary waves-effect">
                                <i class="mdi mdi-arrow-left me-1"></i> Overview
                            </a>
                        @endif
                    </form>
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
                        Target Bulanan & Pencapaian Sales &mdash; <strong>{{ \Carbon\Carbon::createFromDate($yearNow, $monthNow, 1)->locale('id')->translatedFormat('F Y') }}</strong>
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
                                <div class="p-3 border rounded-3 bg-body-tertiary h-100 transition-all hover-shadow">
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
                                <div class="p-3 border rounded-3 bg-body-tertiary h-100 transition-all hover-shadow">
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
                                <div class="p-3 border rounded-3 bg-body-tertiary h-100 transition-all hover-shadow">
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
                                    <div class="p-3 border rounded-3 bg-body-tertiary h-100 transition-all hover-shadow">
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
                            $pQuote = $tQuote > 0 ? round(($totalQuote / $tQuote) * 100) : 0;
                        @endphp
                        <div class="col-6 col-md-4">
                            <a href="#activities" class="text-decoration-none">
                                <div class="p-3 border rounded-3 bg-body-tertiary h-100 transition-all hover-shadow">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-label-info p-2 rounded-circle">
                                            <i class="mdi mdi-file-document-outline fs-5"></i>
                                        </span>
                                        <span class="badge bg-label-info rounded-pill fs-tiny">{{ $pQuote }}%</span>
                                    </div>
                                    <small class="text-muted d-block fw-semibold" style="font-size: 0.78rem;">Quotation</small>
                                    <div class="d-flex align-items-baseline gap-1 mt-1">
                                        <h5 class="mb-0 fw-bold text-dark">{{ $totalQuote }}</h5>
                                        <small class="text-muted" style="font-size: 0.7rem;">/ {{ $tQuote }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Purchase Order (PO) -->
                        <div class="col-6 col-md-4">
                            <a href="#po" class="text-decoration-none">
                                <div class="p-3 border rounded-3 bg-success-subtle border-success-subtle h-100 transition-all hover-shadow">
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

        <!-- Right Column: Financial Realization Big Card -->
        <div class="col-12 col-xl-5">
            <div class="card clean-card h-100 bg-primary text-white position-relative overflow-hidden">
                <div class="position-absolute end-0 bottom-0 opacity-10 me-n3 mb-n3">
                    <i class="mdi mdi-wallet-outline" style="font-size: 11rem; line-height: 1;"></i>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between position-relative z-1">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="badge bg-white text-primary fw-bold px-3 py-2 rounded-pill">
                                <i class="mdi mdi-currency-usd me-1"></i> Realisasi Sales
                            </span>
                            <span class="badge bg-white bg-opacity-25 text-white fw-bold px-3 py-1 rounded-pill">
                                {{ \Carbon\Carbon::createFromDate($yearNow, $monthNow, 1)->locale('id')->translatedFormat('F Y') }}
                            </span>
                        </div>
                        <p class="mb-1 text-white-50 small fw-semibold">TOTAL CLOSING (PO RECEIVED)</p>
                        <h2 class="text-white fw-bold mb-3 display-6">
                            Rp {{ number_format($amountSales, 0, ',', '.') }}
                        </h2>
                    </div>

                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small text-white-50">Persentase Target Bulanan</span>
                            <span class="fw-bold text-white fs-6">{{ $salesPercentage }}%</span>
                        </div>
                        <div class="progress progress-white mb-3" style="height: 10px; background-color: rgba(255,255,255,0.25);">
                            <div class="progress-bar bg-white shadow-sm" role="progressbar" 
                                style="width: {{ min($salesPercentage, 100) }}%;" 
                                aria-valuenow="{{ $salesPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <div class="row pt-2 border-top border-white border-opacity-25 text-white-50 small g-2">
                            <div class="col-6">
                                <div>Hot Prospect (80%):</div>
                                <div class="text-white fw-semibold">Rp {{ number_format($amountProspect, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <div>Pipeline Quote Aktif:</div>
                                <div class="text-white fw-semibold">Rp {{ number_format($amountQuote, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Operational Activities Table Card -->
    <div class="card clean-card mb-4" id="activities">
        <div class="card-header d-flex align-items-center justify-content-between pb-2">
            <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                <i class="mdi mdi-calendar-range-outline text-primary fs-4"></i> Rincian Matrix Aktivitas Mingguan (Weekly Breakdown)
            </h5>
            <small class="text-muted">Distribusi data per minggu dalam bulan terpilih</small>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 200px;">Jenis Aktivitas</th>
                        @foreach ($dataDc as $week)
                            <th class="text-center">Minggu {{ $week['week'] }}</th>
                        @endforeach
                        <th class="text-center fw-bold">Total Capaian</th>
                        <th class="text-center fw-bold" style="width: 140px;">Pencapaian (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- New Leads Row -->
                    <tr>
                        <td class="fw-semibold text-dark">
                            <span class="badge bg-label-secondary p-1 rounded me-2"><i class="mdi mdi-account-plus"></i></span>
                            New Leads
                        </td>
                        @php
                            $totalLeadsFullWeek = 0;
                        @endphp
                        @foreach ($dataLeads as $week)
                            <td class="text-center font-monospace">{{ $week['total'] }}</td>
                            @php
                                $totalLeadsFullWeek += $week['total'];
                            @endphp
                        @endforeach
                        <td class="text-center fw-bold text-dark">{{ $totalLeadsFullWeek }}</td>
                        <td class="text-center">
                            @php
                                $targetL = $target->leads ?? 0;
                                $denomL = is_array($dataLeads) && count($dataLeads) > 4 ? ($targetL + $targetL / 4) : $targetL;
                                $pctL = $denomL > 0 ? round(($totalLeadsFullWeek / $denomL) * 100) : 0;
                                $colorL = $pctL >= 100 ? 'success' : ($pctL >= 80 ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-label-{{ $colorL }} rounded-pill px-3 py-1 fw-bold">{{ $pctL }}%</span>
                        </td>
                    </tr>

                    <!-- Daily Call Row -->
                    <tr>
                        <td class="fw-semibold text-dark">
                            <span class="badge bg-label-info p-1 rounded me-2"><i class="mdi mdi-phone-outline"></i></span>
                            Daily Call
                        </td>
                        @php
                            $totalDcFullWeek = 0;
                        @endphp
                        @foreach ($dataDc as $week)
                            <td class="text-center font-monospace">{{ $week['total'] }}</td>
                            @php
                                $totalDcFullWeek += $week['total'];
                            @endphp
                        @endforeach
                        <td class="text-center fw-bold text-dark">{{ $totalDcFullWeek }}</td>
                        <td class="text-center">
                            @php
                                $targetD = $target->dc ?? 0;
                                $denomD = is_array($dataDc) && count($dataDc) > 4 ? ($targetD + $targetD / 4) : $targetD;
                                $pctD = $denomD > 0 ? round(($totalDcFullWeek / $denomD) * 100) : 0;
                                $colorD = $pctD >= 100 ? 'success' : ($pctD >= 80 ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-label-{{ $colorD }} rounded-pill px-3 py-1 fw-bold">{{ $pctD }}%</span>
                        </td>
                    </tr>

                    <!-- CRM Row -->
                    <tr>
                        <td class="fw-semibold text-dark">
                            <span class="badge bg-label-primary p-1 rounded me-2"><i class="mdi mdi-account-multiple-outline"></i></span>
                            Customer Relationship Management (CRM)
                        </td>
                        @php
                            $totalCrmFullWeek = 0;
                        @endphp
                        @foreach ($dataCRM as $week)
                            <td class="text-center font-monospace">{{ $week['total'] }}</td>
                            @php
                                $totalCrmFullWeek += $week['total'];
                            @endphp
                        @endforeach
                        <td class="text-center fw-bold text-dark">{{ $totalCrmFullWeek }}</td>
                        <td class="text-center">
                            @php
                                $targetC = $target->crm ?? 0;
                                $denomC = is_array($dataCRM) && count($dataCRM) > 4 ? ($targetC + $targetC / 4) : $targetC;
                                $pctC = $denomC > 0 ? round(($totalCrmFullWeek / $denomC) * 100) : 0;
                                $colorC = $pctC >= 100 ? 'success' : ($pctC >= 80 ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-label-{{ $colorC }} rounded-pill px-3 py-1 fw-bold">{{ $pctC }}%</span>
                        </td>
                    </tr>

                    <!-- Visit Row -->
                    @if (in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat']))
                        <tr>
                            <td class="fw-semibold text-dark">
                                <span class="badge bg-label-warning p-1 rounded me-2"><i class="mdi mdi-map-marker-outline"></i></span>
                                Customer Visit
                            </td>
                            @php
                                $totalVisitFullWeek = 0;
                            @endphp
                            @foreach ($dataVisit as $week)
                                <td class="text-center font-monospace">{{ $week['total'] }}</td>
                                @php
                                    $totalVisitFullWeek += $week['total'];
                                @endphp
                            @endforeach
                            <td class="text-center fw-bold text-dark">{{ $totalVisitFullWeek }}</td>
                            <td class="text-center">
                                @php
                                    $targetV = $target->visit ?? 0;
                                    $denomV = is_array($dataVisit) && count($dataVisit) > 4 ? ($targetV + $targetV / 4) : $targetV;
                                    $pctV = $denomV > 0 ? round(($totalVisitFullWeek / $denomV) * 100) : 0;
                                    $colorV = $pctV >= 100 ? 'success' : ($pctV >= 80 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-label-{{ $colorV }} rounded-pill px-3 py-1 fw-bold">{{ $pctV }}%</span>
                            </td>
                        </tr>
                    @endif

                    <!-- Quotation Row -->
                    <tr>
                        <td class="fw-semibold text-dark">
                            <span class="badge bg-label-info p-1 rounded me-2"><i class="mdi mdi-file-document-outline"></i></span>
                            Quotation Issued
                        </td>
                        @php
                            $totalQuoteFullWeek = 0;
                        @endphp
                        @foreach ($dataQuote as $week)
                            <td class="text-center font-monospace">{{ $week['total'] }}</td>
                            @php
                                $totalQuoteFullWeek += $week['total'];
                            @endphp
                        @endforeach
                        <td class="text-center fw-bold text-dark">{{ $totalQuoteFullWeek }}</td>
                        <td class="text-center">
                            @php
                                $targetQ = $target->quote ?? 0;
                                $denomQ = is_array($dataQuote) && count($dataQuote) > 4 ? ($targetQ + $targetQ / 4) : $targetQ;
                                $pctQ = $denomQ > 0 ? round(($totalQuoteFullWeek / $denomQ) * 100) : 0;
                                $colorQ = $pctQ >= 100 ? 'success' : ($pctQ >= 80 ? 'warning' : 'danger');
                            @endphp
                            <span class="badge bg-label-{{ $colorQ }} rounded-pill px-3 py-1 fw-bold">{{ $pctQ }}%</span>
                        </td>
                    </tr>

                    <!-- Purchase Order Row -->
                    <tr>
                        <td class="fw-semibold text-dark">
                            <span class="badge bg-label-success p-1 rounded me-2"><i class="mdi mdi-cart-plus"></i></span>
                            Purchase Order (PO)
                        </td>
                        @php
                            $totalPoFullWeek = 0;
                        @endphp
                        @foreach ($dataPo as $week)
                            <td class="text-center font-monospace">{{ $week['total'] }}</td>
                            @php
                                $totalPoFullWeek += $week['total'];
                            @endphp
                        @endforeach
                        <td class="text-center fw-bold text-success">{{ $totalPoFullWeek }}</td>
                        <td class="text-center">
                            <span class="badge bg-label-success rounded-pill px-3 py-1 fw-bold">Active</span>
                        </td>
                    </tr>
                </tbody>
            </table>
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
                        <th class="fw-bold text-end">Paid (Accounting Confirmed)</th>
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
        .hover-shadow:hover {
            box-shadow: 0 4px 12px 0 rgba(67, 89, 113, 0.12);
            transform: translateY(-2px);
        }
        .transition-all {
            transition: all 0.2s ease-in-out;
        }
        .fs-tiny {
            font-size: 0.7rem;
        }
        @media (min-width: 768px) {
            .border-start-md {
                border-left: 1px solid rgba(67, 89, 113, 0.12) !important;
            }
        }
    </style>
@endpush
