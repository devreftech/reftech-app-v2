@extends('layouts.sales.app')
@section('title', 'Sales Overview — Semester ' . $report->semester . ' ' . $report->year)
@section('content')
    @php
        $fullPercent   = $totalTarget > 0 ? round(($poTotal / ($totalTarget * 6)) * 100, 1) : 0;
        $pctColor      = $fullPercent >= 100 ? 'success' : ($fullPercent >= 80 ? 'warning' : 'danger');
        $semesterLabel = $report->semester == 1 ? 'January – June' : 'July – December';
        $s1Report      = $semester->where('year', $report->year)->where('semester', 1)->first();
        $s2Report      = $semester->where('year', $report->year)->where('semester', 2)->first();

        $winRate       = $quoteOnCount > 0 ? round(($poCount   / $quoteOnCount) * 100, 1) : 0;
        $lossRate      = $quoteOnCount > 0 ? round(($lossCount / $quoteOnCount) * 100, 1) : 0;
        $mktContrib    = $poTotal > 0 ? round(($poTotalSupport / $poTotal) * 100, 1) : 0;
        $winColor      = $winRate  >= 50 ? 'success' : ($winRate  >= 30 ? 'warning' : 'danger');
        $lossColor     = $lossRate <= 20 ? 'success' : ($lossRate <= 40 ? 'warning' : 'danger');
        $mktColor      = $mktContrib >= 30 ? 'success' : ($mktContrib >= 15 ? 'warning' : 'secondary');

        $bulanMap = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
    @endphp

    {{-- ===== HEADER & HERO BANNER ===== --}}
    <div class="card border-0 shadow-sm mb-4 report-hero-card">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                {{-- Kiri: Judul & Target Achievement --}}
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-label-primary px-3 py-1 rounded-pill fw-semibold">
                            <i class="mdi mdi-chart-areaspline me-1"></i> Semester {{ $report->semester }}
                        </span>
                        <span class="text-muted fw-medium">{{ $report->year }}</span>
                        <span class="text-muted">•</span>
                        <span class="text-muted small">{{ $semesterLabel }}</span>
                    </div>

                    <h3 class="fw-bold text-heading mb-2">Semester {{ $report->semester }} Sales & Marketing Overview</h3>

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $pctColor }} px-3 py-1 rounded-pill fw-semibold">
                                <i class="mdi mdi-target me-1"></i> {{ $fullPercent }}% Target Met
                            </span>
                            <span class="text-muted small">
                                <strong>Rp {{ number_format($poTotal, 0, ',', '.') }}</strong>
                                @if($totalTarget > 0)
                                    <span class="text-muted"> / Rp {{ number_format($totalTarget * 6, 0, ',', '.') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Toggle Semester + Pilih Tahun + Shortcut --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Semester 1 / Semester 2 / Full Semester Switcher --}}
                    <div class="btn-group shadow-xs" role="group">
                        @if ($s1Report)
                            <a href="{{ route('report.semester', $s1Report->id) }}"
                               class="btn {{ $report->semester == 1 ? 'btn-primary' : 'btn-outline-secondary' }} waves-effect fw-semibold px-3">
                                <i class="mdi mdi-numeric-1-circle-outline me-1"></i> Semester 1
                            </a>
                        @endif
                        @if ($s2Report)
                            <a href="{{ route('report.semester', $s2Report->id) }}"
                               class="btn {{ $report->semester == 2 ? 'btn-primary' : 'btn-outline-secondary' }} waves-effect fw-semibold px-3">
                                <i class="mdi mdi-numeric-2-circle-outline me-1"></i> Semester 2
                            </a>
                        @endif
                        <a href="{{ route('report.year', $report->year) }}"
                           class="btn btn-outline-secondary waves-effect fw-semibold px-3">
                            <i class="mdi mdi-calendar-range-outline me-1"></i> Full Semester
                        </a>
                    </div>

                    {{-- Dropdown Tahun --}}
                    <div class="dropdown shadow-xs">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle waves-effect fw-semibold"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-calendar-range me-1 text-primary"></i> {{ $report->year }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 1055;">
                            @foreach ($semester->pluck('year')->unique()->sortDesc() as $yr)
                                <li>
                                    <a class="dropdown-item waves-effect d-flex align-items-center justify-content-between {{ $yr == $report->year ? 'active' : '' }}"
                                        href="{{ route('report.year', $yr) }}">
                                        <span>{{ $yr }}</span>
                                        @if ($yr == $report->year)
                                            <i class="mdi mdi-check ms-2"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Quick Action: Monthly Report --}}
                    <a href="{{ route('report.monthly', [$report->year, 1]) }}" class="btn btn-primary waves-effect shadow-sm">
                        <i class="mdi mdi-calendar-month-outline me-1"></i> Monthly Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== EXECUTIVE KPI SUMMARY CARDS ===== --}}
    @php
        $cards = [
            [
                'title'       => 'Purchase Order',
                'amount'      => 'Rp ' . number_format($poTotal, 0, ',', '.'),
                'subtext'     => $poCount . ' Transaksi Closed',
                'badge'       => 'Realized Revenue',
                'icon'        => 'mdi-cart-check',
                'theme'       => 'success',
                'borderClass' => 'border-start-success',
            ],
            [
                'title'       => 'Total Quotation',
                'amount'      => 'Rp ' . number_format($quoteOnTotal, 0, ',', '.'),
                'subtext'     => $quoteOnCount . ' Total Penawaran',
                'badge'       => 'Total Pipeline',
                'icon'        => 'mdi-cart-outline',
                'theme'       => 'primary',
                'borderClass' => 'border-start-primary',
            ],
            [
                'title'       => 'Active Quotations',
                'amount'      => 'Rp ' . number_format($quoteTotal, 0, ',', '.'),
                'subtext'     => $quoteCount . ' Penawaran Aktif',
                'badge'       => 'In Progress',
                'icon'        => 'mdi-file-document-outline',
                'theme'       => 'info',
                'borderClass' => 'border-start-info',
            ],
            [
                'title'       => 'Loss Quotations',
                'amount'      => 'Rp ' . number_format($lossTotal, 0, ',', '.'),
                'subtext'     => $lossCount . ' Transaksi Loss',
                'badge'       => 'Unrealized',
                'icon'        => 'mdi-cart-minus',
                'theme'       => 'danger',
                'borderClass' => 'border-start-danger',
            ],
            [
                'title'       => 'Conversion Rate',
                'amount'      => $winRate . '%',
                'subtext'     => $poCount . ' PO dari ' . $quoteOnCount . ' Quote',
                'badge'       => 'Win Rate',
                'icon'        => 'mdi-trophy-outline',
                'theme'       => $winColor,
                'borderClass' => 'border-start-' . $winColor,
                'progress'    => min($winRate, 100),
            ],
            [
                'title'       => 'Loss Rate',
                'amount'      => $lossRate . '%',
                'subtext'     => $lossCount . ' Loss dari ' . $quoteOnCount . ' Quote',
                'badge'       => 'Loss Ratio',
                'icon'        => 'mdi-trending-down',
                'theme'       => $lossColor,
                'borderClass' => 'border-start-' . $lossColor,
                'progress'    => min($lossRate, 100),
            ],
            [
                'title'       => 'Marketing Contribution',
                'amount'      => 'Rp ' . number_format($poTotalSupport, 0, ',', '.'),
                'subtext'     => $mktContrib . '% dari total semester PO',
                'badge'       => 'Marketing PO',
                'icon'        => 'mdi-handshake-outline',
                'theme'       => $mktColor,
                'borderClass' => 'border-start-' . $mktColor,
                'progress'    => min($mktContrib, 100),
            ],
            [
                'title'       => 'Marketing Quotation',
                'amount'      => 'Rp ' . number_format($quoteTotalSupport, 0, ',', '.'),
                'subtext'     => $quoteCountSupport . ' Penawaran Marketing',
                'badge'       => 'Marketing Quote',
                'icon'        => 'mdi-file-percent-outline',
                'theme'       => 'secondary',
                'borderClass' => 'border-start-secondary',
            ],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach ($cards as $card)
            <div class="col-12 col-sm-6 col-md-4 col-xl-3">
                <div class="card h-100 border-0 shadow-sm report-kpi-card {{ $card['borderClass'] }}">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-label-{{ $card['theme'] }} rounded-3 shadow-xs">
                                    <i class="mdi {{ $card['icon'] }} fs-4"></i>
                                </div>
                            </div>
                            <span class="badge bg-label-{{ $card['theme'] }} rounded-pill small px-2 py-1">
                                {{ $card['badge'] }}
                            </span>
                        </div>

                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">{{ $card['title'] }}</span>
                            <h4 class="fw-bold mb-1 text-{{ $card['theme'] }} kpi-value">{{ $card['amount'] }}</h4>
                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-muted">{{ $card['subtext'] }}</small>
                            </div>

                            @if (isset($card['progress']))
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-{{ $card['theme'] }}"
                                         role="progressbar"
                                         style="width: {{ $card['progress'] }}%"
                                         aria-valuenow="{{ $card['progress'] }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ===== GRAFIK PENJUALAN PER BULAN ===== --}}
    @php
        $bulanLabelShort = [
            1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',
            7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec',
        ];
        $startMonthChart   = $report->semester == 1 ? 1 : 7;
        $ecommerceIdsChart = [16, 23];

        $chartLabels   = [];
        $chartSeries   = [];
        $combinedTotal = array_fill(0, 6, 0);

        for ($i = 0; $i < 6; $i++) {
            $chartLabels[] = $bulanLabelShort[$startMonthChart + $i];
        }

        // Regular sales
        foreach (array_filter($data, fn($s) => !in_array($s['id'], $ecommerceIdsChart)) as $s) {
            $monthlyData = [];
            for ($i = 0; $i < 6; $i++) {
                $val = $s['jumlah'][$i]['total'] ?? 0;
                $monthlyData[] = $val;
                $combinedTotal[$i] += $val;
            }
            $chartSeries[] = ['name' => $s['name'], 'data' => $monthlyData];
        }

        // E-Commerce team digabung
        $ecoMembers = array_filter($data, fn($s) => in_array($s['id'], $ecommerceIdsChart));
        if (count($ecoMembers) > 0) {
            $ecoData = array_fill(0, 6, 0);
            foreach ($ecoMembers as $m) {
                for ($i = 0; $i < 6; $i++) {
                    $val = $m['jumlah'][$i]['total'] ?? 0;
                    $ecoData[$i] += $val;
                    $combinedTotal[$i] += $val;
                }
            }
            $chartSeries[] = ['name' => 'E-Commerce', 'data' => array_values($ecoData)];
        }

        $chartTargetLine = array_fill(0, 6, $totalTarget);

        $pctByMonth = [];
        foreach (array_values($combinedTotal) as $total) {
            $pctByMonth[] = $totalTarget > 0 ? round($total / $totalTarget * 100) : 0;
        }

        $totalSemester = array_sum($combinedTotal);
        $pctSemester   = $totalTarget > 0 ? round(($totalSemester / ($totalTarget * 6)) * 100, 1) : 0;
        $badgeSem      = $pctSemester >= 100 ? 'success' : ($pctSemester >= 80 ? 'warning' : 'danger');
    @endphp

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h5 class="mb-1 text-heading fw-bold">
                    <i class="mdi mdi-chart-timeline-variant me-2 text-primary"></i>Monthly Sales Trend vs Target
                </h5>
                <small class="text-muted">
                    {{ $semesterLabel }} {{ $report->year }} &bull; Distribusi Realisasi Penjualan Bulanan
                </small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-{{ $badgeSem }} fs-6 px-3 py-2 rounded-pill fw-semibold">
                    <i class="mdi mdi-cash-multiple me-1"></i> Rp {{ number_format($totalSemester, 0, ',', '.') }} &nbsp;|&nbsp; {{ $pctSemester }}%
                </span>
            </div>
        </div>
        <div class="card-body p-4">
            <div id="chartPenjualanSemester"></div>
            <div class="d-flex mt-2 pt-2 border-top" style="padding-left: 65px; padding-right: 15px;">
                @foreach($pctByMonth as $idx => $pct)
                    @php
                        $badgePct = $pct >= 100 ? 'success' : ($pct >= 80 ? 'warning' : 'danger');
                    @endphp
                    <div class="flex-fill text-center">
                        <span class="text-muted small d-block mb-1 font-monospace">{{ $chartLabels[$idx] }}</span>
                        <span class="badge rounded-pill bg-{{ $badgePct }} px-2 py-1 shadow-xs fw-semibold">{{ $pct }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== TABS: SALES TEAM VS MARKETING ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="nav-segment-container d-inline-flex p-1 rounded-pill bg-light border">
                <button class="btn btn-sm rounded-pill fw-semibold px-4 py-2 segment-tab-btn active"
                        id="tab-sales-btn" data-bs-toggle="tab" data-bs-target="#tab-sales"
                        type="button" role="tab">
                    <i class="mdi mdi-account-group-outline me-1"></i> Sales Team Performance
                </button>
                <button class="btn btn-sm rounded-pill fw-semibold px-4 py-2 segment-tab-btn"
                        id="tab-marketing-btn" data-bs-toggle="tab" data-bs-target="#tab-marketing"
                        type="button" role="tab">
                    <i class="mdi mdi-bullseye-arrow me-1"></i> Marketing Department
                    @if ($smktProspectCount > 0)
                        <span class="badge bg-primary ms-1 rounded-pill">{{ $smktProspectCount }}</span>
                    @endif
                </button>
            </div>
            <small class="text-muted d-none d-md-block">
                Semester {{ $report->semester }} &bull; {{ $report->year }}
            </small>
        </div>

        <div class="tab-content p-0">

            {{-- ===== TAB: SALES TEAM ===== --}}
            <div class="tab-pane fade show active p-4" id="tab-sales" role="tabpanel">
                @php
                    $ecommerceIds = [16, 23];
                    $ecommerceMembers = array_values(array_filter($data, fn($s) => in_array($s['id'], $ecommerceIds)));
                    $regularSales     = array_values(array_filter($data, fn($s) => !in_array($s['id'], $ecommerceIds)));

                    // Gabungkan data bulanan Tim E-Commerce
                    $teamTotal       = array_sum(array_column($ecommerceMembers, 'total'));
                    $teamTarget      = array_sum(array_column($ecommerceMembers, 'target'));
                    $teamMktProspect = array_sum(array_column($ecommerceMembers, 'mktProspect'));
                    $teamMktQuote    = array_sum(array_column($ecommerceMembers, 'mktQuote'));
                    $teamMktPo       = array_sum(array_column($ecommerceMembers, 'mktPo'));
                    $teamJumlah      = [];
                    foreach ($ecommerceMembers as $member) {
                        foreach ($member['jumlah'] as $j) {
                            $bulan = $j['bulan'];
                            if (!isset($teamJumlah[$bulan])) $teamJumlah[$bulan] = 0;
                            $teamJumlah[$bulan] += $j['total'];
                        }
                    }
                    ksort($teamJumlah);
                    $teamJumlahArr = [];
                    foreach ($teamJumlah as $b => $t) {
                        $teamJumlahArr[] = ['bulan' => $b, 'total' => $t];
                    }
                @endphp

                <div class="row g-4">
                    {{-- Regular Sales Cards --}}
                    @foreach ($regularSales as $sale)
                        @php
                            $semTargetTotal = $sale['target'] * 6;
                            $semSalesPct    = $semTargetTotal > 0 ? round(($sale['total'] / $semTargetTotal) * 100, 1) : 0;
                            $semBadgeColor  = $semSalesPct >= 100 ? 'success' : ($semSalesPct >= 80 ? 'warning' : 'danger');
                        @endphp
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card h-100 border rounded-3 p-3 shadow-none report-sales-card d-flex flex-column justify-content-between">
                                <div>
                                    {{-- Header Sales Card --}}
                                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                                        <div class="avatar avatar-lg flex-shrink-0">
                                            <img src="{{ url('') . '/' . $sale['image'] }}"
                                                 alt="{{ $sale['name'] }}"
                                                 class="rounded-circle shadow-xs object-fit-cover"
                                                 onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/1.png') }}'">
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                <h6 class="fw-bold text-heading mb-0 text-truncate">{{ $sale['name'] }}</h6>
                                                <span class="badge bg-label-{{ $semBadgeColor }} rounded-pill font-monospace">
                                                    {{ $semSalesPct }}%
                                                </span>
                                            </div>
                                            <h5 class="fw-bold text-success mb-1">Rp {{ number_format($sale['total'], 0, ',', '.') }}</h5>

                                            @if (($sale['mktProspect'] ?? 0) > 0)
                                                @php
                                                    $sMktRateQ  = $sale['mktProspect'] > 0 ? round(($sale['mktQuote'] / $sale['mktProspect']) * 100, 0) : 0;
                                                    $sMktRatePo = $sale['mktQuote']    > 0 ? round(($sale['mktPo']    / $sale['mktQuote'])    * 100, 0) : 0;
                                                @endphp
                                                <div class="funnel-inline-chip d-inline-flex align-items-center gap-1 mt-1">
                                                    <span class="badge bg-label-secondary px-2 py-0" style="font-size: 0.68rem;">
                                                        {{ $sale['mktProspect'] }} prospect
                                                    </span>
                                                    <i class="mdi mdi-chevron-right text-muted micro-arrow"></i>
                                                    <span class="badge bg-label-primary px-2 py-0" style="font-size: 0.68rem;">
                                                        {{ $sale['mktQuote'] }} quote
                                                    </span>
                                                    <i class="mdi mdi-chevron-right text-muted micro-arrow"></i>
                                                    <span class="badge bg-label-success px-2 py-0" style="font-size: 0.68rem;">
                                                        {{ $sale['mktPo'] }} PO
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Monthly Breakdown Rows --}}
                                    <div class="d-flex flex-column gap-2 mb-3">
                                        @foreach ($sale['jumlah'] as $item)
                                            @php
                                                $persenanSales = $sale['target'] > 0 ? round(($item['total'] / $sale['target']) * 100, 1) : 0;
                                                $label = $persenanSales >= 100 ? 'success' : ($persenanSales >= 80 ? 'warning' : 'danger');
                                            @endphp
                                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light-subtle">
                                                <span class="fw-semibold text-muted small" style="min-width: 75px;">
                                                    {{ $bulanMap[$item['bulan']] }}
                                                </span>
                                                <span class="fw-bold text-heading small">
                                                    Rp {{ number_format($item['total'], 0, ',', '.') }}
                                                </span>
                                                <span class="badge bg-label-{{ $label }} rounded-pill font-monospace" style="min-width: 48px;">
                                                    {{ $persenanSales }}%
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Action Button --}}
                                <a href="{{ route('overview-sales.semester', [$report->id, $sale['id']]) }}"
                                   class="btn btn-outline-primary waves-effect w-100 rounded-pill">
                                    <i class="mdi mdi-eye-outline me-1"></i> View Sales Details
                                </a>
                            </div>
                        </div>
                    @endforeach

                    {{-- Team E-Commerce Card --}}
                    @if (count($ecommerceMembers) > 0)
                        @php
                            $semTeamTarget = $teamTarget * 6;
                            $semTeamPct    = $semTeamTarget > 0 ? round(($teamTotal / $semTeamTarget) * 100, 1) : 0;
                            $teamBadgeColor= $semTeamPct >= 100 ? 'success' : ($semTeamPct >= 80 ? 'warning' : 'danger');
                            $mainMember    = collect($ecommerceMembers)->firstWhere('id', 16);
                        @endphp
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card h-100 border border-warning rounded-3 p-3 shadow-none report-sales-card d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                                        <div class="avatar avatar-lg flex-shrink-0">
                                            <div class="avatar-initial bg-label-warning rounded-circle shadow-xs fw-bold">
                                                <i class="mdi mdi-shopping-outline fs-3"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                <h6 class="fw-bold text-heading mb-0">Team E-Commerce</h6>
                                                <span class="badge bg-label-{{ $teamBadgeColor }} rounded-pill font-monospace">
                                                    {{ $semTeamPct }}%
                                                </span>
                                            </div>
                                            <h5 class="fw-bold text-warning mb-1">Rp {{ number_format($teamTotal, 0, ',', '.') }}</h5>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach ($ecommerceMembers as $member)
                                                    <span class="badge bg-label-secondary small px-2 py-0">{{ $member['name'] }}</span>
                                                @endforeach
                                            </div>

                                            @if ($teamMktProspect > 0)
                                                @php
                                                    $tMktRateQ  = $teamMktProspect > 0 ? round(($teamMktQuote / $teamMktProspect) * 100, 0) : 0;
                                                    $tMktRatePo = $teamMktQuote    > 0 ? round(($teamMktPo    / $teamMktQuote)    * 100, 0) : 0;
                                                @endphp
                                                <div class="funnel-inline-chip d-inline-flex align-items-center gap-1 mt-2">
                                                    <span class="badge bg-label-secondary px-2 py-0" style="font-size: 0.68rem;">
                                                        {{ $teamMktProspect }} prospect
                                                    </span>
                                                    <i class="mdi mdi-chevron-right text-muted micro-arrow"></i>
                                                    <span class="badge bg-label-primary px-2 py-0" style="font-size: 0.68rem;">
                                                        {{ $teamMktQuote }} quote
                                                    </span>
                                                    <i class="mdi mdi-chevron-right text-muted micro-arrow"></i>
                                                    <span class="badge bg-label-success px-2 py-0" style="font-size: 0.68rem;">
                                                        {{ $teamMktPo }} PO
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Team Monthly Breakdown --}}
                                    <div class="d-flex flex-column gap-2 mb-3">
                                        @foreach ($teamJumlahArr as $item)
                                            @php
                                                $pct = $teamTarget > 0 ? round(($item['total'] / $teamTarget) * 100, 1) : 0;
                                                $lbl = $pct >= 100 ? 'success' : ($pct >= 80 ? 'warning' : 'danger');
                                            @endphp
                                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light-subtle">
                                                <span class="fw-semibold text-muted small" style="min-width: 75px;">
                                                    {{ $bulanMap[$item['bulan']] }}
                                                </span>
                                                <span class="fw-bold text-heading small">
                                                    Rp {{ number_format($item['total'], 0, ',', '.') }}
                                                </span>
                                                <span class="badge bg-label-{{ $lbl }} rounded-pill font-monospace" style="min-width: 48px;">
                                                    {{ $pct }}%
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="text-center text-muted small py-2 bg-light-subtle rounded">
                                    <i class="mdi mdi-account-multiple-outline me-1"></i> E-Commerce Joint Performance
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Support PIC Card --}}
                    @if ($support)
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card h-100 border border-success rounded-3 p-3 shadow-none report-sales-card d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                                        <div class="avatar avatar-lg flex-shrink-0">
                                            <img src="{{ url('') . '/' . $support->image }}"
                                                 alt="{{ $support->name }}"
                                                 class="rounded-circle shadow-xs object-fit-cover"
                                                 onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/1.png') }}'">
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <div class="d-flex align-items-center justify-content-between gap-1 mb-1">
                                                <h6 class="fw-bold text-heading mb-0">{{ $support->name }}</h6>
                                                <span class="badge bg-label-success rounded-pill">Support PIC</span>
                                            </div>
                                            <h5 class="fw-bold text-success mb-1">Rp {{ number_format($poTotalSupport, 0, ',', '.') }}</h5>
                                            <small class="text-muted">Marketing Support Contributed PO</small>
                                        </div>
                                    </div>

                                    {{-- Support Monthly Breakdown --}}
                                    <div class="d-flex flex-column gap-2 mb-3">
                                        @foreach ($dataSupport as $item)
                                            <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light-subtle">
                                                <span class="fw-semibold text-muted small" style="min-width: 75px;">
                                                    {{ $bulanMap[$item->bulan] }}
                                                </span>
                                                <span class="fw-bold text-heading small">
                                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                                </span>
                                                <span class="badge bg-label-primary rounded-pill font-monospace">Realized</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <a href="{{ route('overview-sales.semester', [$report->id, $support->id]) }}"
                                   class="btn btn-outline-success waves-effect w-100 rounded-pill">
                                    <i class="mdi mdi-eye-outline me-1"></i> View Support Details
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ===== TAB: MARKETING ===== --}}
            <div class="tab-pane fade p-4" id="tab-marketing" role="tabpanel">
                @php
                    $smktProspectToQuote = $smktProspectCount > 0 ? round(($smktQuoteCount / $smktProspectCount) * 100, 1) : 0;
                    $smktQuoteToPo       = $smktQuoteCount   > 0 ? round(($smktPoCount   / $smktQuoteCount)   * 100, 1) : 0;
                    $smktStatusPending   = $smktProspectByStatus->pending   ?? 0;
                    $smktStatusProvided  = $smktProspectByStatus->provided  ?? 0;
                    $smktStatusNoProvide = $smktProspectByStatus->no_provide ?? 0;
                    $smktPctPending      = $smktProspectCount > 0 ? round(($smktStatusPending   / $smktProspectCount) * 100, 1) : 0;
                    $smktPctProvided     = $smktProspectCount > 0 ? round(($smktStatusProvided  / $smktProspectCount) * 100, 1) : 0;
                    $smktPctNoProvide    = $smktProspectCount > 0 ? round(($smktStatusNoProvide / $smktProspectCount) * 100, 1) : 0;

                    $smktSourceIcons = [
                        'IG'          => ['icon' => 'mdi-instagram',        'color' => 'danger'],
                        'Instagram'   => ['icon' => 'mdi-instagram',        'color' => 'danger'],
                        'WhatsApp'    => ['icon' => 'mdi-whatsapp',         'color' => 'success'],
                        'LinkedIn'    => ['icon' => 'mdi-linkedin',         'color' => 'info'],
                        'Website'     => ['icon' => 'mdi-web',              'color' => 'primary'],
                        'Indotrading' => ['icon' => 'mdi-store-outline',    'color' => 'warning'],
                        'Tokopedia'   => ['icon' => 'mdi-shopping-outline', 'color' => 'success'],
                        'OLX'         => ['icon' => 'mdi-tag-outline',      'color' => 'warning'],
                        'Google'      => ['icon' => 'mdi-google',           'color' => 'danger'],
                        'Google Ads'  => ['icon' => 'mdi-google',           'color' => 'danger'],
                        'Meta Ads'    => ['icon' => 'mdi-facebook',         'color' => 'primary'],
                        'Facebook'    => ['icon' => 'mdi-facebook',         'color' => 'primary'],
                        'Other'       => ['icon' => 'mdi-help-circle-outline','color' => 'secondary'],
                    ];
                    $smktCategoryIcons = [
                        'Service Compressor'   => ['icon' => 'mdi-wrench-outline',         'color' => 'primary'],
                        'Rental Compressor'    => ['icon' => 'mdi-calendar-clock-outline', 'color' => 'info'],
                        'Sparepart Compressor' => ['icon' => 'mdi-cog-outline',            'color' => 'warning'],
                        'Instalasi Piping'     => ['icon' => 'mdi-pipe',                   'color' => 'secondary'],
                        'Air Audit'            => ['icon' => 'mdi-clipboard-check-outline','color' => 'success'],
                        'Fire System'          => ['icon' => 'mdi-fire-extinguisher',      'color' => 'danger'],
                        'HVAC System'          => ['icon' => 'mdi-air-conditioner',        'color' => 'info'],
                        'Unit Baru/Second'     => ['icon' => 'mdi-package-variant-closed', 'color' => 'success'],
                        'Uncategorized'        => ['icon' => 'mdi-help-circle-outline',    'color' => 'secondary'],
                    ];
                @endphp

                {{-- Connected Funnel Pipeline Cards --}}
                <div class="row g-3 align-items-center justify-content-center mb-4">
                    {{-- Step 1: Prospect --}}
                    <div class="col-12 col-md-3">
                        <div class="card border border-label-secondary bg-label-secondary h-100 text-center report-funnel-box shadow-none">
                            <div class="card-body py-4">
                                <div class="avatar avatar-lg mx-auto mb-3">
                                    <div class="avatar-initial bg-secondary rounded-circle shadow-xs">
                                        <i class="mdi mdi-account-search-outline fs-3"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bold mb-1 text-heading">{{ $smktProspectCount }}</h2>
                                <p class="mb-0 fw-semibold text-muted text-uppercase small">Prospect Generated</p>
                                <small class="text-muted">Total leads masuk semester ini</small>
                            </div>
                        </div>
                    </div>

                    {{-- Connector 1 --}}
                    <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center py-2">
                        <div class="funnel-connector-pill">
                            <i class="mdi mdi-arrow-right fs-3 text-primary d-none d-md-block"></i>
                            <i class="mdi mdi-arrow-down fs-3 text-primary d-block d-md-none"></i>
                        </div>
                        <span class="badge bg-primary rounded-pill shadow-xs px-2 py-1 mt-1 font-monospace">
                            {{ $smktProspectToQuote }}%
                        </span>
                        <small class="text-muted" style="font-size: 0.68rem;">Konversi Quote</small>
                    </div>

                    {{-- Step 2: Quotation --}}
                    <div class="col-12 col-md-3">
                        <div class="card border border-label-primary bg-label-primary h-100 text-center report-funnel-box shadow-none">
                            <div class="card-body py-4">
                                <div class="avatar avatar-lg mx-auto mb-3">
                                    <div class="avatar-initial bg-primary rounded-circle shadow-xs">
                                        <i class="mdi mdi-file-document-outline fs-3"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bold mb-1 text-primary">{{ $smktQuoteCount }}</h2>
                                <p class="mb-0 fw-semibold text-primary text-uppercase small">Quotation Created</p>
                                @if ($smktQuoteTotal > 0)
                                    <span class="badge bg-white text-primary fw-bold shadow-xs mt-1">
                                        Rp {{ number_format($smktQuoteTotal, 0, ',', '.') }}
                                    </span>
                                @else
                                    <small class="text-muted">—</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Connector 2 --}}
                    <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center py-2">
                        <div class="funnel-connector-pill">
                            <i class="mdi mdi-arrow-right fs-3 text-success d-none d-md-block"></i>
                            <i class="mdi mdi-arrow-down fs-3 text-success d-block d-md-none"></i>
                        </div>
                        <span class="badge bg-success rounded-pill shadow-xs px-2 py-1 mt-1 font-monospace">
                            {{ $smktQuoteToPo }}%
                        </span>
                        <small class="text-muted" style="font-size: 0.68rem;">Closing PO</small>
                    </div>

                    {{-- Step 3: Purchase Order --}}
                    <div class="col-12 col-md-3">
                        <div class="card border border-label-success bg-label-success h-100 text-center report-funnel-box shadow-none">
                            <div class="card-body py-4">
                                <div class="avatar avatar-lg mx-auto mb-3">
                                    <div class="avatar-initial bg-success rounded-circle shadow-xs">
                                        <i class="mdi mdi-cart-check fs-3"></i>
                                    </div>
                                </div>
                                <h2 class="fw-bold mb-1 text-success">{{ $smktPoCount }}</h2>
                                <p class="mb-0 fw-semibold text-success text-uppercase small">Purchase Order (PO)</p>
                                @if ($smktPoTotal > 0)
                                    <span class="badge bg-white text-success fw-bold shadow-xs mt-1">
                                        Rp {{ number_format($smktPoTotal, 0, ',', '.') }}
                                    </span>
                                @else
                                    <small class="text-muted">—</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Prospect Follow-up --}}
                <div class="pt-2">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-heading mb-0">
                            <i class="mdi mdi-clipboard-text-clock-outline me-2 text-warning"></i>Prospect Follow-up Status
                        </h6>
                        <span class="text-muted small">Status tindak lanjut prospect semester ini</span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 border bg-light report-status-card">
                                <div class="avatar avatar-md flex-shrink-0">
                                    <div class="avatar-initial bg-label-warning rounded-3 shadow-xs">
                                        <i class="mdi mdi-clock-outline fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-heading">Pending Follow-up</span>
                                        <span class="fw-bold text-warning fs-5">{{ $smktStatusPending }}</span>
                                    </div>
                                    <div class="progress mb-1" style="height: 6px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $smktPctPending }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $smktPctPending }}% belum diproses</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 border bg-light report-status-card">
                                <div class="avatar avatar-md flex-shrink-0">
                                    <div class="avatar-initial bg-label-success rounded-3 shadow-xs">
                                        <i class="mdi mdi-check-circle-outline fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-heading">Provided to Sales</span>
                                        <span class="fw-bold text-success fs-5">{{ $smktStatusProvided }}</span>
                                    </div>
                                    <div class="progress mb-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $smktPctProvided }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $smktPctProvided }}% diteruskan ke sales</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <div class="d-flex align-items-center gap-3 p-3 rounded-3 border bg-light report-status-card">
                                <div class="avatar avatar-md flex-shrink-0">
                                    <div class="avatar-initial bg-label-danger rounded-3 shadow-xs">
                                        <i class="mdi mdi-close-circle-outline fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-bold text-heading">No Provide</span>
                                        <span class="fw-bold text-danger fs-5">{{ $smktStatusNoProvide }}</span>
                                    </div>
                                    <div class="progress mb-1" style="height: 6px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $smktPctNoProvide }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $smktPctNoProvide }}% tidak dilanjutkan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Loss Callout Banner --}}
                @if ($smktLossCount > 0)
                    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-3 p-3 mt-4 mb-0 rounded-3" role="alert">
                        <div class="avatar avatar-sm flex-shrink-0">
                            <div class="avatar-initial bg-danger text-white rounded-circle">
                                <i class="mdi mdi-alert-circle-outline fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <span class="fw-bold d-block">Marketing Leads Loss Summary</span>
                            <span class="small">
                                Terdapat <strong>{{ $smktLossCount }} penawaran loss</strong> dari leads marketing pada semester ini
                                @if ($smktLossTotal > 0)
                                    &mdash; senilai <strong>Rp {{ number_format($smktLossTotal, 0, ',', '.') }}</strong>
                                @endif
                            </span>
                        </div>
                    </div>
                @endif

                {{-- Per Marketing Person --}}
                @if ($smktPerPerson->isNotEmpty())
                    <hr class="my-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-bold text-heading mb-0">
                            <i class="mdi mdi-account-group-outline me-2 text-primary"></i>Marketing Support Performance
                        </h6>
                        <small class="text-muted">{{ $smktPerPerson->count() }} personel marketing</small>
                    </div>

                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 py-3">Marketing Personel</th>
                                    <th class="text-center">Total Prospect</th>
                                    <th class="text-center">Provided</th>
                                    <th class="text-center">Pending</th>
                                    <th class="text-center">No Provide</th>
                                    <th class="text-end pe-3">Provide Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($smktPerPerson as $p)
                                    @php
                                        $smktProvideRate = $p->total > 0 ? round(($p->provided / $p->total) * 100, 1) : 0;
                                        $smktRateColor   = $smktProvideRate >= 70 ? 'success' : ($smktProvideRate >= 40 ? 'warning' : 'danger');
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar avatar-sm flex-shrink-0">
                                                    <img src="{{ url('') . '/' . $p->image }}"
                                                         alt="{{ $p->name }}"
                                                         class="rounded-circle shadow-xs object-fit-cover"
                                                         onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/1.png') }}'">
                                                </div>
                                                <span class="fw-bold text-heading">{{ $p->name }}</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-secondary rounded-pill fw-semibold">{{ $p->total }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-success rounded-pill fw-semibold">{{ $p->provided }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-warning rounded-pill fw-semibold">{{ $p->pending }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-label-danger rounded-pill fw-semibold">{{ $p->no_provide }}</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="d-flex align-items-center justify-content-end gap-2">
                                                <div class="progress" style="width: 70px; height: 6px;">
                                                    <div class="progress-bar bg-{{ $smktRateColor }}"
                                                         role="progressbar"
                                                         style="width: {{ min($smktProvideRate, 100) }}%"></div>
                                                </div>
                                                <span class="badge bg-label-{{ $smktRateColor }} rounded-pill" style="min-width: 50px;">
                                                    {{ $smktProvideRate }}%
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Sumber & Kategori --}}
                @if ($smktProspectBySource->isNotEmpty() || $smktProspectByCategory->isNotEmpty())
                    <hr class="my-4">
                    <div class="row g-4">
                        {{-- 1. Prospect Source --}}
                        @if ($smktProspectBySource->isNotEmpty())
                            @php $smktMaxSource = $smktProspectBySource->max('total'); @endphp
                            <div class="col-12 col-lg-6">
                                <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fw-bold text-heading mb-0">
                                            <i class="mdi mdi-source-branch me-1 text-primary"></i> Prospect Source
                                        </h6>
                                        <span class="badge bg-label-primary rounded-pill">{{ $smktProspectBySource->count() }} Sources</span>
                                    </div>

                                    <div class="d-flex flex-column gap-3">
                                        @foreach ($smktProspectBySource as $src)
                                            @php
                                                $si       = $smktSourceIcons[$src->source] ?? $smktSourceIcons['Other'];
                                                $pct      = $smktMaxSource > 0 ? round(($src->total / $smktMaxSource) * 100) : 0;
                                                $ofT      = $smktProspectCount > 0 ? round(($src->total / $smktProspectCount) * 100, 1) : 0;
                                                $isWebDom = $src->source === 'Website' && $smktProspectByDomain->isNotEmpty();
                                            @endphp
                                            <div class="source-item">
                                                <div class="d-flex align-items-center gap-3 {{ $isWebDom ? 'cursor-pointer' : '' }}"
                                                     @if ($isWebDom)
                                                         role="button" data-bs-toggle="collapse"
                                                         data-bs-target="#collapseWebsiteDomainAdmin"
                                                         aria-expanded="false" aria-controls="collapseWebsiteDomainAdmin"
                                                     @endif>
                                                    <div class="avatar avatar-sm flex-shrink-0">
                                                        <div class="avatar-initial bg-label-{{ $si['color'] }} rounded-3 shadow-xs">
                                                            <i class="mdi {{ $si['icon'] }} fs-5"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <span class="fw-semibold text-heading small">
                                                                {{ $src->source }}
                                                                @if ($isWebDom)
                                                                    <i class="mdi mdi-chevron-down toggle-chevron text-muted ms-1"></i>
                                                                @endif
                                                            </span>
                                                            <span class="text-muted small">
                                                                <strong>{{ $src->total }}</strong> <small>({{ $ofT }}%)</small>
                                                            </span>
                                                        </div>
                                                        <div class="progress" style="height: 6px;">
                                                            <div class="progress-bar bg-{{ $si['color'] }}" style="width: {{ $pct }}%"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Website Domain Drilldown --}}
                                                @if ($isWebDom)
                                                    @php
                                                        $smktMaxDomain   = $smktProspectByDomain->max('total');
                                                        $smktDomainTotal = $smktProspectByDomain->sum('total');
                                                    @endphp
                                                    <div class="collapse mt-2 ps-4" id="collapseWebsiteDomainAdmin">
                                                        <div class="p-2 rounded border bg-white shadow-xs d-flex flex-column gap-2">
                                                            @foreach ($smktProspectByDomain as $dom)
                                                                @php
                                                                    $dPct = $smktMaxDomain > 0 ? round(($dom->total / $smktMaxDomain) * 100) : 0;
                                                                    $dOfT = $smktDomainTotal > 0 ? round(($dom->total / $smktDomainTotal) * 100, 1) : 0;
                                                                @endphp
                                                                <div>
                                                                    <div class="d-flex justify-content-between mb-1">
                                                                        <span class="text-muted" style="font-size: 0.76rem;">{{ $dom->domain }}</span>
                                                                        <span class="text-muted" style="font-size: 0.72rem;">
                                                                            <strong>{{ $dom->total }}</strong> <small>({{ $dOfT }}%)</small>
                                                                        </span>
                                                                    </div>
                                                                    <div class="progress" style="height: 4px;">
                                                                        <div class="progress-bar bg-primary" style="width: {{ $dPct }}%"></div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- 2. Prospect Category --}}
                        @if ($smktProspectByCategory->isNotEmpty())
                            @php $smktMaxCategory = $smktProspectByCategory->max('total'); @endphp
                            <div class="col-12 col-lg-6">
                                <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="fw-bold text-heading mb-0">
                                            <i class="mdi mdi-tag-multiple-outline me-1 text-primary"></i> Prospect Category
                                        </h6>
                                        <span class="badge bg-label-info rounded-pill">{{ $smktProspectByCategory->count() }} Categories</span>
                                    </div>

                                    <div class="d-flex flex-column gap-3">
                                        @foreach ($smktProspectByCategory as $cat)
                                            @php
                                                $ci   = $smktCategoryIcons[$cat->category] ?? $smktCategoryIcons['Uncategorized'];
                                                $pct  = $smktMaxCategory > 0 ? round(($cat->total / $smktMaxCategory) * 100) : 0;
                                                $ofT  = $smktProspectCount > 0 ? round(($cat->total / $smktProspectCount) * 100, 1) : 0;
                                            @endphp
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar avatar-sm flex-shrink-0">
                                                    <div class="avatar-initial bg-label-{{ $ci['color'] }} rounded-3 shadow-xs">
                                                        <i class="mdi {{ $ci['icon'] }} fs-5"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <span class="fw-semibold text-heading small">{{ $cat->category }}</span>
                                                        <span class="text-muted small">
                                                            <strong>{{ $cat->total }}</strong> <small>({{ $ofT }}%)</small>
                                                        </span>
                                                    </div>
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-{{ $ci['color'] }}" style="width: {{ $pct }}%"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </div>

    @foreach ($data as $sale)
        @include('components.modal.overview.report')
    @endforeach
@endsection

@push('before-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/apex-charts/apex-charts.css" />
    <style>
        /* Hero Card Style */
        .report-hero-card {
            background: linear-gradient(135deg, rgba(var(--bs-primary-rgb), 0.04) 0%, rgba(var(--bs-primary-rgb), 0.01) 100%);
            border: 1px solid rgba(var(--bs-primary-rgb), 0.12) !important;
            border-radius: 14px;
        }

        /* KPI Summary Cards */
        .report-kpi-card {
            border-radius: 12px;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.06);
        }
        .report-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
        }
        .border-start-primary   { border-left: 4px solid var(--bs-primary) !important; }
        .border-start-success   { border-left: 4px solid var(--bs-success) !important; }
        .border-start-warning   { border-left: 4px solid var(--bs-warning) !important; }
        .border-start-danger    { border-left: 4px solid var(--bs-danger) !important; }
        .border-start-info      { border-left: 4px solid var(--bs-info) !important; }
        .border-start-secondary { border-left: 4px solid var(--bs-secondary) !important; }

        .kpi-value {
            font-size: 1.3rem;
            letter-spacing: -0.5px;
        }

        /* Segment Navigation Tabs */
        .nav-segment-container {
            border: 1px solid rgba(105, 108, 255, 0.15);
        }
        .segment-tab-btn {
            color: #6d6b77;
            background: transparent;
            border: none;
            transition: all 0.2s ease;
        }
        .segment-tab-btn.active,
        .segment-tab-btn:focus {
            color: #696cff;
            background: #fff;
            box-shadow: 0 2px 8px rgba(105, 108, 255, 0.2);
        }
        .segment-tab-btn:hover:not(.active) {
            background: rgba(255, 255, 255, 0.6);
            color: #696cff;
        }

        /* Sales Cards */
        .report-sales-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .report-sales-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .funnel-inline-chip {
            border-radius: 20px;
            background: rgba(0, 0, 0, 0.03);
            padding: 2px 4px;
        }
        .micro-arrow {
            font-size: 0.75rem;
            margin: 0 -2px;
        }

        /* Funnel Pipeline Box */
        .report-funnel-box {
            border-radius: 14px;
            transition: transform 0.2s ease;
        }
        .report-funnel-box:hover {
            transform: translateY(-2px);
        }

        .funnel-connector-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.03);
        }

        .report-status-card {
            transition: all 0.2s ease;
        }
        .report-status-card:hover {
            border-color: var(--bs-primary) !important;
            background-color: #fff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        /* Collapsible Chevron */
        [data-bs-toggle="collapse"] .toggle-chevron { transition: transform .2s; }
        [data-bs-toggle="collapse"]:not(.collapsed) .toggle-chevron { transform: rotate(180deg); }

        /* Dark Mode */
        .dark-style .report-hero-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.01) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
        .dark-style .report-kpi-card {
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark-style .segment-tab-btn.active {
            background: #2f3349;
            color: #7367f0;
        }
        .dark-style .report-sales-card {
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark-style .report-status-card {
            background-color: rgba(255, 255, 255, 0.03) !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
        }
        .dark-style .report-status-card:hover {
            background-color: rgba(255, 255, 255, 0.06) !important;
        }
        .dark-style .funnel-inline-chip {
            background: rgba(255, 255, 255, 0.06);
        }
        .dark-style .bg-light-subtle {
            background-color: rgba(255, 255, 255, 0.02) !important;
        }
    </style>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/vendor/libs/apex-charts/apexcharts.js"></script>
    <script>
        (function () {
            const isDark       = document.documentElement.classList.contains('dark-style');
            const labelColor   = isDark ? '#a8aaae' : '#6d6b77';
            const borderColor  = isDark ? '#404152' : '#dbdade';
            const cardColor    = isDark ? '#2f3349' : '#fff';

            const formatRp = val => {
                if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'B';
                if (val >= 1_000_000)     return 'Rp ' + (val / 1_000_000).toFixed(1) + 'M';
                return 'Rp ' + val.toLocaleString('id-ID');
            };

            const salesSeries = @json($chartSeries);
            const targetLine  = @json($chartTargetLine);
            const labels      = @json($chartLabels);

            const tooltipColors = [
                '#696cff','#03c3ec','#71dd37','#ffab00','#ff3e1d',
                '#26c6da','#8c57ff','#20c997',
            ];

            const combinedData = @json(array_values($combinedTotal));

            const allSeries = [
                { name: 'Total Penjualan', type: 'bar', data: combinedData },
                { name: 'Target Bulanan',  type: 'line', data: targetLine },
            ];

            const barPrimary  = '#696cff';
            const targetColor = '#ff4c51';
            const chartColors = [barPrimary, targetColor];

            const chartEl = document.querySelector('#chartPenjualanSemester');
            if (!chartEl) return;

            new ApexCharts(chartEl, {
                chart: {
                    type: 'bar',
                    height: 340,
                    toolbar: { show: false },
                    parentHeightOffset: 0,
                },
                series: allSeries,
                plotOptions: {
                    bar: { borderRadius: 6, columnWidth: '45%' },
                },
                dataLabels: { enabled: false },
                stroke: {
                    width: allSeries.map((s, i) => i === allSeries.length - 1 ? 3 : 0),
                    curve: 'smooth',
                    dashArray: allSeries.map((s, i) => i === allSeries.length - 1 ? 5 : 0),
                },
                markers: {
                    size: allSeries.map((s, i) => i === allSeries.length - 1 ? 5 : 0),
                    strokeWidth: 2,
                    colors: [cardColor],
                    strokeColors: targetColor,
                },
                colors: chartColors,
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: labelColor },
                },
                xaxis: {
                    categories: labels,
                    labels: { style: { colors: labelColor, fontSize: '13px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    labels: {
                        formatter: formatRp,
                        style: { colors: labelColor, fontSize: '11px' },
                    },
                },
                grid: {
                    borderColor,
                    strokeDashArray: 5,
                    padding: { top: 10, bottom: -5 },
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    custom: function({ series: apexSeries, dataPointIndex, w }) {
                        const month      = labels[dataPointIndex];
                        const grandTotal = combinedData[dataPointIndex] || 0;
                        const targetVal  = targetLine[dataPointIndex]   || 0;

                        let rows = '';
                        salesSeries.forEach((s, i) => {
                            const val = s.data[dataPointIndex] || 0;
                            if (val <= 0) return;
                            const color = tooltipColors[i] ?? '#696cff';
                            rows += `<div style="display:flex;align-items:center;gap:8px;padding:3px 0">
                                        <span style="width:10px;height:10px;border-radius:50%;background:${color};flex-shrink:0"></span>
                                        <span style="flex:1;color:#6d6b77;font-size:12px">${s.name}</span>
                                        <span style="font-size:12px;font-weight:600">Rp ${val.toLocaleString('id-ID')}</span>
                                     </div>`;
                        });

                        const pct      = targetVal > 0 ? Math.round(grandTotal / targetVal * 100) : 0;
                        const pctColor = pct >= 100 ? '#71dd37' : pct >= 80 ? '#ffab00' : '#ff4c51';

                        const totalRow = `<div style="display:flex;align-items:center;gap:8px;padding:4px 0;border-top:1px solid #dbdade;margin-top:4px">
                                              <span style="width:10px;height:10px;border-radius:2px;background:#444;flex-shrink:0"></span>
                                              <span style="flex:1;font-weight:600;font-size:12px">Total Penjualan</span>
                                              <span style="font-weight:700;font-size:12px">Rp ${grandTotal.toLocaleString('id-ID')}</span>
                                              <span style="margin-left:6px;padding:1px 6px;border-radius:10px;background:${pctColor};color:#fff;font-size:11px;font-weight:700">${pct}%</span>
                                          </div>`;

                        const targetRow = `<div style="display:flex;align-items:center;gap:8px;padding:3px 0">
                                               <span style="width:10px;height:2px;background:${targetColor};flex-shrink:0;display:inline-block"></span>
                                               <span style="flex:1;color:#6d6b77;font-size:12px">Target Bulanan</span>
                                               <span style="font-size:12px">Rp ${targetVal.toLocaleString('id-ID')}</span>
                                           </div>`;

                        return `<div style="padding:10px 12px;min-width:230px;font-family:inherit">
                                    <div style="font-weight:700;border-bottom:1px solid #dbdade;padding-bottom:6px;margin-bottom:4px">${month}</div>
                                    ${rows}
                                    ${totalRow}
                                    ${targetRow}
                                </div>`;
                    },
                },
            }).render();
        })();
    </script>
    <script>
        document.querySelectorAll('.segment-tab-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const target = document.querySelector(this.dataset.bsTarget);
                if (!target) return;

                document.querySelectorAll('.tab-pane').forEach(p => {
                    p.classList.remove('show', 'active');
                });
                document.querySelectorAll('.segment-tab-btn').forEach(b => {
                    b.classList.remove('active');
                });

                this.classList.add('active');
                target.classList.add('show', 'active');
            });
        });
    </script>
@endpush
