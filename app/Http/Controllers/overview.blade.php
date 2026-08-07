@extends('layouts.sales.app')
@section('title', 'Year Reports')
@section('content')
    @if (in_array(Auth::user()->role, ['Sales', 'Admin', 'Super Admin']))
        @php
            $user = $user ?? Auth::user();
            $lastDetail = $user->detail->last();
            $userArea = $lastDetail->area ?? ($user->latestRole->area ?? 'Sales Area');
            $isAdminView = Auth::user()->role !== 'Sales';

            // Calculate total PO & Forecast for semester
            $semesterTotalPO = 0;
            $semesterTotalForecast = 0;
            $semesterTotalQuoteNominal = 0;
            $semesterTotalLeads = 0;
            $semesterTotalDC = 0;
            $semesterTotalCRM = 0;
            $semesterTotalVisit = 0;
            $semesterTotalQuote = 0;

            $chartMonths = [];
            $chartPOData = [];
            $chartQuoteNominalData = [];

            if (isset($getDC) && is_array($getDC)) {
                foreach ($getDC as $idx => $dcItem) {
                    $monthIdx = (int) $idx;
                    $poVal = (float) ($getTotalPO[$monthIdx]['total'] ?? 0);
                    $quoteNominalVal = (float) ($getTotalQuoteNominal[$monthIdx]['total'] ?? 0);

                    $semesterTotalPO += $poVal;
                    $semesterTotalForecast += (float) ($getTotalForecast[$monthIdx]['total'] ?? 0);
                    $semesterTotalQuoteNominal += $quoteNominalVal;
                    $semesterTotalLeads += $getLeads[$monthIdx]['total'] ?? 0;
                    $semesterTotalDC += $dcItem['total'] ?? 0;
                    $semesterTotalCRM += $getCRM[$monthIdx]['total'] ?? 0;
                    $semesterTotalVisit += $getVisit[$monthIdx]['total'] ?? 0;
                    $semesterTotalQuote += $getQuote[$monthIdx]['total'] ?? 0;

                    $chartMonths[] = $dcItem['month'];
                    $chartPOData[] = $poVal;
                    $chartQuoteNominalData[] = $quoteNominalVal;
                }
            }

            // $targett dari controller adalah target BULANAN. Untuk mode 'full' (S1+S2),
            // controller sudah mengalikan x2 (jadi setara 2 bulan), sehingga di sini cukup
            // dikalikan count($getDC)/2 (=6) supaya total jadi target 12 bulan yang benar.
            // Untuk mode semester biasa, dikalikan count($getDC) (=6 bulan) langsung.
            $targettPeriod = $targett * ($report->semester === 'full' ? count($getDC) / 2 : count($getDC));
            $salesPercentage = $targettPeriod > 0 ? round(($semesterTotalPO / $targettPeriod) * 100, 1) : 0;
            $salesColor = $salesPercentage >= 100 ? 'success' : ($salesPercentage >= 80 ? 'warning' : 'danger');
        @endphp

        <!-- Header Card with S1/S2 Toggle & Year Filter Dropdown -->
        <div class="card clean-card mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-label-primary fs-6 px-3 py-2">
                                <i class="mdi mdi-chart-areaspline me-1"></i>
                                {{ $report->semester == 'full' ? 'Full Year (S1+S2)' : 'Semester ' . $report->semester }}
                            </span>
                            <span class="text-muted fw-semibold fs-5">{{ $report->year }}</span>
                            <span class="text-muted">•</span>
                            <small class="text-muted fw-semibold">
                                {{ $report->semester == 'full' ? 'Januari – Desember (12 Bulan)' : ($report->semester == 1 ? 'Januari – Juni' : 'Juli – Desember') }}
                            </small>
                        </div>
                        <h4 class="fw-bold mb-0 text-dark">Executive Sales Overview</h4>
                        <small class="text-muted">
                            Laporan Performa Sales &mdash; <strong>{{ $user->name }}</strong> ({{ $userArea }})
                        </small>
                    </div>

                    <!-- Filter Options (Like Admin Report Page) -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <!-- Semester & Full Year Toggle Buttons -->
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
                                <i class="mdi mdi-calendar-blank-multiple me-1"></i> Full Year (S1+S2)
                            </a>
                        </div>

                        <!-- Year Filter Dropdown -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle waves-effect"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-calendar me-1"></i> {{ $report->year }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @foreach ($yearsList as $yr)
                                    @php
                                        if ($report->semester == 'full') {
                                            $targetUrl = $isAdminView
                                                ? url('/overview/full_' . $yr . '/' . $user->id)
                                                : url('/overview') . '?report_id=full_' . $yr;
                                        } else {
                                            $targetRep = $allReports->where('year', $yr)->where('semester', $report->semester)->first()
                                                ?? $allReports->where('year', $yr)->first();
                                            $targetUrl = $targetRep
                                                ? ($isAdminView ? url('/overview/' . $targetRep->id . '/' . $user->id) : url('/overview') . '?report_id=' . $targetRep->id)
                                                : '#';
                                        }
                                    @endphp
                                    <li>
                                        <a class="dropdown-item waves-effect {{ $yr == $report->year ? 'active' : '' }}"
                                           href="{{ $targetUrl }}">
                                            Tahun {{ $yr }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Semester Summary Metrics -->
        <div class="row gy-4 mb-4">
            <!-- Omset Sales PO -->
            <div class="col-12 col-md-4">
                <div class="card clean-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-success p-2 rounded-circle">
                                <i class="mdi mdi-cart-plus fs-4"></i>
                            </span>
                            <span class="badge bg-{{ $salesColor }} rounded-pill px-3 py-1">{{ $salesPercentage }}% Target</span>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase d-block" style="font-size: 0.75rem;">Total Sales (PO Received)</small>
                        <h3 class="fw-bold text-dark mb-1 mt-1">Rp {{ number_format($semesterTotalPO, 0, ',', '.') }}</h3>
                        <div class="progress mt-2" style="height: 6px; border-radius: 4px;">
                            <div class="progress-bar bg-{{ $salesColor }}" style="width: {{ min($salesPercentage, 100) }}%; border-radius: 4px;"></div>
                        </div>
                        <small class="text-muted mt-2 d-block fs-tiny">Target {{ $report->semester === 'full' ? 'Tahunan' : 'Semester' }}: Rp {{ number_format($targettPeriod, 0, ',', '.') }}</small>
                    </div>
                </div>
            </div>

            <!-- Total Quotation Card -->
            <div class="col-12 col-md-4">
                <div class="card clean-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-primary p-2 rounded-circle">
                                <i class="mdi mdi-email-multiple-outline fs-4"></i>
                            </span>
                            <span class="badge bg-label-primary rounded-pill px-3 py-1">Quotation</span>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase d-block" style="font-size: 0.75rem;">Total Quotation</small>
                        <h3 class="fw-bold text-primary mb-1 mt-1">Rp {{ number_format($semesterTotalQuoteNominal, 0, ',', '.') }}</h3>
                        <small class="text-muted d-block mt-2 fs-tiny">Akumulasi Penawaran Terbit 6 Bulan</small>
                    </div>
                </div>
            </div>

            <!-- Operational Cumulative Stats -->
            <div class="col-12 col-md-4">
                <div class="card clean-card h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-label-info p-2 rounded-circle">
                                <i class="mdi mdi-chart-timeline-variant fs-4"></i>
                            </span>
                            <span class="badge bg-label-info rounded-pill px-3 py-1">Aktivitas</span>
                        </div>
                        <small class="text-muted fw-semibold text-uppercase d-block" style="font-size: 0.75rem;">Total Aktivitas Sales</small>
                        <div class="row g-2 mt-1">
                            <div class="col-6">
                                <small class="text-muted d-block">Leads:</small>
                                <span class="fw-bold text-dark fs-6">{{ $semesterTotalLeads }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Daily Call:</small>
                                <span class="fw-bold text-dark fs-6">{{ $semesterTotalDC }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">CRM:</small>
                                <span class="fw-bold text-dark fs-6">{{ $semesterTotalCRM }}</span>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Quotation:</small>
                                <span class="fw-bold text-dark fs-6">{{ $semesterTotalQuote }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Revenue Trend Chart Card -->
        <div class="card clean-card mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                    <i class="mdi mdi-chart-bell-curve-cumulative text-primary fs-4"></i> Grafik Penjualan Sales & Nominal Quotation (Semester {{ $report->semester }} - {{ $report->year }})
                </h5>
                <span class="badge bg-label-primary rounded-pill px-3 py-1">Tren Bulanan</span>
            </div>
            <div class="card-body">
                <div id="salesOverviewTrendChart" style="min-height: 320px;"></div>
            </div>
        </div>

        <!-- Performance Table Section (Replaces Heavy Monthly Cards) -->
        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i class="mdi mdi-table-large text-primary fs-4"></i> Tabel Performa Sales Per Semester
        </h5>

        @if ($report->semester == 'full')
            <!-- Full Year: 2 Tables (Semester 1 & Semester 2) -->
            <div class="row gy-4 mb-4">
                @php
                    $semesters = [
                        1 => ['title' => 'Semester 1 Overview (Januari – Juni)', 'range' => [1, 6], 'badgeClass' => 'bg-primary'],
                        2 => ['title' => 'Semester 2 Overview (Juli – Desember)', 'range' => [7, 12], 'badgeClass' => 'bg-info']
                    ];
                @endphp
                @foreach ($semesters as $semNum => $semMeta)
                    <div class="col-12">
                        <div class="card clean-card mb-4">
                            <div class="card-header bg-body-tertiary border-bottom py-3 d-flex align-items-center justify-content-between">
                                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="mdi mdi-calendar-text text-primary"></i> {{ $semMeta['title'] }}
                                </h6>
                                <span class="badge {{ $semMeta['badgeClass'] }} rounded-pill px-3">Semester {{ $semNum }}</span>
                            </div>
                            <div class="table-responsive text-nowrap">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr class="text-uppercase" style="font-size: 0.72rem;">
                                            <th>Bulan</th>
                                            <th class="text-end">Sales PO</th>
                                            <th class="text-end">Nominal Quotation</th>
                                            <th class="text-center">Leads</th>
                                            <th class="text-center">Call</th>
                                            <th class="text-center">CRM</th>
                                            @if (in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat']))
                                                <th class="text-center">Visit</th>
                                            @endif
                                            <th class="text-center">Quote</th>
                                            <th class="text-center">PO</th>
                                            <th class="text-center">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $sPO = 0; $sFc = 0; $sLd = 0; $sDc = 0; $sCrm = 0; $sVis = 0; $sQt = 0; $sPoCnt = 0;
                                        @endphp
                                        @for ($item = $semMeta['range'][0]; $item <= $semMeta['range'][1]; $item++)
                                            @php
                                                $DC = $getDC[$item] ?? null;
                                                if (!$DC) continue;

                                                $monthPO = $getTotalPO[$item]['total'] ?? 0;
                                                $monthForecast = $getTotalQuoteNominal[$item]['total'] ?? 0;
                                                $monthLeads = $getLeads[$item]['total'] ?? 0;
                                                $monthDC = $DC['total'] ?? 0;
                                                $monthCRM = $getCRM[$item]['total'] ?? 0;
                                                $monthVisit = $getVisit[$item]['total'] ?? 0;
                                                $monthQuote = $getQuote[$item]['total'] ?? 0;
                                                $monthPOCount = $getPO[$item]['total'] ?? 0;

                                                $sPO += $monthPO;
                                                $sFc += $monthForecast;
                                                $sLd += $monthLeads;
                                                $sDc += $monthDC;
                                                $sCrm += $monthCRM;
                                                $sVis += $monthVisit;
                                                $sQt += $monthQuote;
                                                $sPoCnt += $monthPOCount;
                                            @endphp
                                            <tr>
                                                <td class="fw-semibold text-dark">{{ $DC['month'] }}</td>
                                                <td class="text-end fw-bold text-success">Rp {{ number_format($monthPO, 0, ',', '.') }}</td>
                                                <td class="text-end text-primary fw-semibold">Rp {{ number_format($monthForecast, 0, ',', '.') }}</td>
                                                <td class="text-center">{{ $monthLeads }}</td>
                                                <td class="text-center">{{ $monthDC }}</td>
                                                <td class="text-center">{{ $monthCRM }}</td>
                                                @if (in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat']))
                                                    <td class="text-center">{{ $monthVisit }}</td>
                                                @endif
                                                <td class="text-center">{{ $monthQuote }}</td>
                                                <td class="text-center fw-bold text-success">{{ $monthPOCount }}</td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-xs btn-primary rounded-pill px-2 py-1"
                                                        data-bs-toggle="modal" data-bs-target="#overviewPO{{ $DC['monthKey'] }}">
                                                        <i class="mdi mdi-eye-outline"></i>
                                                    </button>
                                                    @include('components.modal.overview.totalPo')
                                                </td>
                                            </tr>
                                        @endfor
                                    </tbody>
                                    <tfoot class="table-light fw-bold">
                                        <tr>
                                            <td>Total S{{ $semNum }}</td>
                                            <td class="text-end text-success">Rp {{ number_format($sPO, 0, ',', '.') }}</td>
                                            <td class="text-end text-primary">Rp {{ number_format($sFc, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $sLd }}</td>
                                            <td class="text-center">{{ $sDc }}</td>
                                            <td class="text-center">{{ $sCrm }}</td>
                                            @if (in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat']))
                                                <td class="text-center">{{ $sVis }}</td>
                                            @endif
                                            <td class="text-center">{{ $sQt }}</td>
                                            <td class="text-center text-success">{{ $sPoCnt }}</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Single Semester Table -->
            <div class="card clean-card mb-4">
                <div class="card-header bg-body-tertiary border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="mdi mdi-calendar-month text-primary"></i> Tabel Performa Bulanan &mdash; Semester {{ $report->semester }} ({{ $report->year }})
                    </h5>
                    <span class="badge bg-label-primary rounded-pill px-3 py-1">6 Bulan</span>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-uppercase" style="font-size: 0.72rem;">
                                <th>Bulan</th>
                                <th class="text-end">Total Sales (PO)</th>
                                <th class="text-end">Nominal Quotation</th>
                                <th class="text-center">Leads</th>
                                <th class="text-center">Daily Call</th>
                                <th class="text-center">CRM</th>
                                @if (in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat']))
                                    <th class="text-center">Visit</th>
                                @endif
                                <th class="text-center">Quotation</th>
                                <th class="text-center">PO Closed</th>
                                <th class="text-center">% Target</th>
                                <th class="text-center">Rincian PO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $sPO = 0; $sFc = 0; $sLd = 0; $sDc = 0; $sCrm = 0; $sVis = 0; $sQt = 0; $sPoCnt = 0;
                            @endphp
                            @foreach ($getDC as $monthKey => $DC)
                                @php
                                    $item = (int) $monthKey;
                                    $monthPO = $getTotalPO[$item]['total'] ?? 0;
                                    $monthForecast = $getTotalQuoteNominal[$item]['total'] ?? 0;
                                    $monthLeads = $getLeads[$item]['total'] ?? 0;
                                    $monthDC = $DC['total'] ?? 0;
                                    $monthCRM = $getCRM[$item]['total'] ?? 0;
                                    $monthVisit = $getVisit[$item]['total'] ?? 0;
                                    $monthQuote = $getQuote[$item]['total'] ?? 0;
                                    $monthPOCount = $getPO[$item]['total'] ?? 0;
                                    $monthPct = $targett > 0 ? round(($monthPO / $targett) * 100, 1) : 0;

                                    $sPO += $monthPO;
                                    $sFc += $monthForecast;
                                    $sLd += $monthLeads;
                                    $sDc += $monthDC;
                                    $sCrm += $monthCRM;
                                    $sVis += $monthVisit;
                                    $sQt += $monthQuote;
                                    $sPoCnt += $monthPOCount;
                                @endphp
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $DC['month'] }}</td>
                                    <td class="text-end fw-bold text-success">Rp {{ number_format($monthPO, 0, ',', '.') }}</td>
                                    <td class="text-end text-primary fw-semibold">Rp {{ number_format($monthForecast, 0, ',', '.') }}</td>
                                    <td class="text-center">{{ $monthLeads }}</td>
                                    <td class="text-center">{{ $monthDC }}</td>
                                    <td class="text-center">{{ $monthCRM }}</td>
                                    @if (in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat']))
                                        <td class="text-center">{{ $monthVisit }}</td>
                                    @endif
                                    <td class="text-center">{{ $monthQuote }}</td>
                                    <td class="text-center fw-bold text-success">{{ $monthPOCount }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-label-{{ $monthPct >= 100 ? 'success' : ($monthPct >= 80 ? 'warning' : 'danger') }} rounded-pill">
                                            {{ $monthPct }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-primary rounded-pill px-3 py-1"
                                            data-bs-toggle="modal" data-bs-target="#overviewPO{{ $DC['monthKey'] }}">
                                            <i class="mdi mdi-eye-outline me-1"></i> Rincian PO
                                        </button>
                                        @include('components.modal.overview.totalPo')
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>Total Semester {{ $report->semester }}</td>
                                <td class="text-end text-success">Rp {{ number_format($sPO, 0, ',', '.') }}</td>
                                <td class="text-end text-primary">Rp {{ number_format($sFc, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $sLd }}</td>
                                <td class="text-center">{{ $sDc }}</td>
                                <td class="text-center">{{ $sCrm }}</td>
                                @if (in_array($userArea, ['Bekasi', 'Jabodetabek', 'Jawa Barat']))
                                    <td class="text-center">{{ $sVis }}</td>
                                @endif
                                <td class="text-center">{{ $sQt }}</td>
                                <td class="text-center text-success">{{ $sPoCnt }}</td>
                                <td class="text-center">
                                    @php
                                        $semesterTargett = $targett * count($getDC);
                                        $totPct = $semesterTargett > 0 ? round(($sPO / $semesterTargett) * 100, 1) : 0;
                                    @endphp
                                    <span class="badge bg-{{ $totPct >= 100 ? 'success' : ($totPct >= 80 ? 'warning' : 'danger') }} rounded-pill">
                                        {{ $totPct }}%
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    @elseif(Auth::user()->role == 'Admin')
        <div class="row">
            @php
                $item = 0;
            @endphp
            @foreach ($sales as $sale)
                <div class="col-6 col-lg-4 mb-3">
                    <a href="{{ Route('overview.semester', $sale->id) }}" class="text-decoration-none text-black">
                        <div class="card">
                            <div class="row">
                                <div class="col-4">
                                    <img src="{{ url('') . '/' . $sale->image }}" alt="" srcset=""
                                        class="rounded-circle" style="width : 100%; height:100%;">
                                </div>
                                <div class="col-8 m-auto">
                                    @php
                                        $lastDetail = $sale->detail->last();
                                    @endphp
                                    <h3>{{ $sale->name }}</h3>
                                    <p>{{ $lastDetail->area ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                {{-- <div class="col-lg-6 mb-3">
                    <div class="card" data-id="{{ $item }}">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <h4 class="mb-2">{{ $sale->name }}'s Overview</h4>
                                <div class="dropdown">
                                    <button class="btn p-0" type="button" id="salesOverview" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        <i class="mdi mdi-dots-vertical mdi-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesOverview"
                                        style="">
                                        <a class="dropdown-item waves-effect" href="javascript:void(0);">Refresh</a>
                                        <a class="dropdown-item waves-effect" href="javascript:void(0);">Share</a>
                                        <a class="dropdown-item waves-effect" href="javascript:void(0);">Update</a>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0 fw-normal">Total Sales <span class="fs-4">Rp
                                        {{ number_format($totalPO[$item], 2, ',', '.') }}</span></h5>
                                @php
                                    $jumlah_target = [];
                                    foreach ($totalPO as $key => $value) {
                                        if (isset($targett[$key]) && $targett[$key] != 0) {
                                            $jumlah_target[$key] = ($value / $targett[$key]) * 100;
                                            $formatted_jumlah_target[$key] = number_format($jumlah_target[$key], 3);
                                        } else {
                                            $jumlah_target[$key] = 0;
                                        }
                                    }
                                @endphp
                                <div class="d-flex align-items-center text-success">
                                    <p class="mb-0"> {{ $formatted_jumlah_target[$item] }}%</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <h5 class="fw-normal">Forecast <span class="fs-4">Rp {{ $totalForecast[$item] }}</span>
                                </h5>
                                <div class="d-flex align-items-center text-success">
                                <p class="mb-0">+18%</p>
                                <i class="mdi mdi-chevron-up"></i>
                            </div>
                            </div>
                        </div>
                        <div class="card-body d-flex justify-content-between flex-wrap gap-3">
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-info rounded">
                                        <i class="mdi mdi-phone-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $filteredDC[$item] }}</h5>
                                    <small class="text-muted">Daily Call</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-primary rounded">
                                        <i class="mdi mdi-account-multiple-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $filteredCRM[$item] }}</h5>
                                    <small class="text-muted">CRM</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-warning rounded">
                                        <i class="mdi mdi-email-multiple-outline mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $filteredQuote[$item] }}</h5>
                                    <small class="text-muted">Quotation</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <div class="avatar">
                                    <div class="avatar-initial bg-label-success rounded">
                                        <i class="mdi mdi-cart-plus mdi-24px"></i>
                                    </div>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ $filteredLeads[$item] }}</h5>
                                    <small class="text-muted">Leads</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                @php
                    $item++;
                @endphp
            @endforeach
            <div class="col-6 col-lg-4 mb-3">
                <a href="{{ Route('overview.semester', $support->id) }}" class="text-decoration-none text-black">
                    <div class="card">
                        <div class="row">
                            <div class="col-4">
                                <img src="{{ url('') . '/' . $support->image }}" alt="" srcset=""
                                    class="rounded-circle" style="width : 100%; height:100%;">
                            </div>
                            <div class="col-8 m-auto">
                                {{-- @php
                                    $lastDetail = $sale->detail->last();
                                @endphp --}}
                                <h3>{{ $support->name }}</h3>
                                <p>Online</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    @endif
    @include('pages.warehouse.reports.form')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var chartEl = document.querySelector('#salesOverviewTrendChart');
            if (chartEl) {
                var options = {
                    series: [{
                        name: 'Total Sales (PO Received)',
                        data: @json($chartPOData ?? [])
                    }, {
                        name: 'Total Nominal Quotation Dibuat',
                        data: @json($chartQuoteNominalData ?? [])
                    }],
                    chart: {
                        type: 'area',
                        height: 320,
                        toolbar: { show: false },
                        zoom: { enabled: false }
                    },
                    colors: ['#28c76f', '#666cff'],
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
                    grid: { borderColor: '#f1f1f1' }
                };
                var chart = new ApexCharts(chartEl, options);
                chart.render();
            }
        });
    </script>
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-overview.js"></script>
@endpush
