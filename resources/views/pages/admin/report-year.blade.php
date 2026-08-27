@extends('layouts.sales.app')
@section('title', 'Annual Sales Report — ' . $year)
@section('content')
    @php
        $fullPercent = $totalTarget > 0 ? round(($poTotal / ($totalTarget * 12)) * 100, 1) : 0;
        $pctColor    = $fullPercent >= 100 ? 'success' : ($fullPercent >= 80 ? 'warning' : 'danger');

        $bulanMap = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
        ];

        // Team E-Commerce grouping (ID 16 & 23)
        $ecommerceIds     = [16, 23];
        $ecommerceMembers = array_values(array_filter($data, fn($s) => in_array($s['id'], $ecommerceIds)));
        $regularSales     = array_values(array_filter($data, fn($s) => !in_array($s['id'], $ecommerceIds)));
        $teamTarget       = array_sum(array_column($ecommerceMembers, 'target'));
        $teamJumlah       = [];
        for ($m = 1; $m <= 12; $m++) {
            $teamJumlah[$m] = array_sum(array_map(fn($s) => $s['jumlah'][$m] ?? 0, $ecommerceMembers));
        }
        $teamTotal      = array_sum($teamJumlah);
        $teamMainMember = collect($ecommerceMembers)->firstWhere('id', 16);
        $teamImage      = $teamMainMember ? $teamMainMember['image'] : '';

        $winRate        = $quoteOnCount > 0 ? round(($poCount   / $quoteOnCount) * 100, 1) : 0;
        $lossRate       = $quoteOnCount > 0 ? round(($lossCount / $quoteOnCount) * 100, 1) : 0;
        $mktContrib     = $poTotal > 0 ? round(($poTotalSupport / $poTotal) * 100, 1) : 0;
        $winColor       = $winRate  >= 50 ? 'success' : ($winRate  >= 30 ? 'warning' : 'danger');
        $lossColor      = $lossRate <= 20 ? 'success' : ($lossRate <= 40 ? 'warning' : 'danger');
        $mktColor       = $mktContrib >= 30 ? 'success' : ($mktContrib >= 15 ? 'warning' : 'secondary');
    @endphp

    {{-- ===== HEADER & HERO BANNER ===== --}}
    <div class="card border-0 shadow-sm mb-4 report-hero-card">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                {{-- Kiri: Judul & Target Achievement --}}
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-label-primary px-3 py-1 rounded-pill fw-semibold">
                            <i class="mdi mdi-calendar-text me-1"></i> Annual Report
                        </span>
                        <span class="text-muted fw-medium">{{ $year }}</span>
                        <span class="text-muted">•</span>
                        <span class="text-muted small">January – December (Full Year)</span>
                    </div>

                    <h3 class="fw-bold text-heading mb-2">Annual Sales Overview — {{ $year }}</h3>

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $pctColor }} px-3 py-1 rounded-pill fw-semibold">
                                <i class="mdi mdi-target me-1"></i> {{ $fullPercent }}% Target Met
                            </span>
                            <span class="text-muted small">
                                <strong>Rp {{ number_format($poTotal, 0, ',', '.') }}</strong>
                                @if($totalTarget > 0)
                                    <span class="text-muted"> / Rp {{ number_format($totalTarget * 12, 0, ',', '.') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Link Semester + Pilih Tahun + Shortcut Monthly --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Link Semester 1, Semester 2 & Full Semester --}}
                    <div class="btn-group shadow-xs" role="group">
                        @if ($reportS1)
                            <a href="{{ route('report.semester', $reportS1->id) }}"
                               class="btn btn-outline-secondary waves-effect fw-semibold px-3">
                                <i class="mdi mdi-numeric-1-circle-outline me-1"></i> Semester 1
                            </a>
                        @endif
                        @if ($reportS2)
                            <a href="{{ route('report.semester', $reportS2->id) }}"
                               class="btn btn-outline-secondary waves-effect fw-semibold px-3">
                                <i class="mdi mdi-numeric-2-circle-outline me-1"></i> Semester 2
                            </a>
                        @endif
                        <a href="{{ route('report.year', $year) }}"
                           class="btn btn-primary waves-effect fw-semibold px-3">
                            <i class="mdi mdi-calendar-range-outline me-1"></i> Full Semester
                        </a>
                    </div>

                    {{-- Dropdown Pilih Tahun --}}
                    <div class="dropdown shadow-xs">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle waves-effect fw-semibold"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-calendar-range me-1 text-primary"></i> {{ $year }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 1055;">
                            @foreach ($yearList as $yr)
                                <li>
                                    <a class="dropdown-item waves-effect d-flex align-items-center justify-content-between {{ $yr == $year ? 'active' : '' }}"
                                        href="{{ route('report.year', $yr) }}">
                                        <span>{{ $yr }}</span>
                                        @if ($yr == $year)
                                            <i class="mdi mdi-check ms-2"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Quick Action: Monthly Report --}}
                    <a href="{{ route('report.monthly', [$year, 1]) }}" class="btn btn-primary waves-effect shadow-sm">
                        <i class="mdi mdi-calendar-month-outline me-1"></i> Monthly Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== EXECUTIVE KPI SUMMARY CARDS ===== --}}
    @php
        $yearCards = [
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
                'subtext'     => $mktContrib . '% dari total PO tahunan',
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
        @foreach ($yearCards as $card)
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

    {{-- ===== GRAFIK PENJUALAN PER SEMESTER (S1 & S2) ===== --}}
    @php
        $s1Labels = ['Jan','Feb','Mar','Apr','May','Jun'];
        $s2Labels = ['Jul','Aug','Sep','Oct','Nov','Dec'];
        $s1Totals = [];
        $s2Totals = [];
        for ($m = 1; $m <= 6; $m++) {
            $s1Totals[] = array_sum(array_column(array_map(fn($s) => ['v' => $s['jumlah'][$m]], $data), 'v'));
        }
        for ($m = 7; $m <= 12; $m++) {
            $s2Totals[] = array_sum(array_column(array_map(fn($s) => ['v' => $s['jumlah'][$m]], $data), 'v'));
        }
        $s1TargetLine = $totalTarget > 0 ? array_fill(0, 6, $totalTarget) : array_fill(0, 6, 0);
        $s2TargetLine = $totalTarget > 0 ? array_fill(0, 6, $totalTarget) : array_fill(0, 6, 0);

        $totalS1All = array_sum($s1Totals);
        $pctS1All   = $totalTarget > 0 ? round(($totalS1All / ($totalTarget * 6)) * 100, 1) : 0;
        $badgeS1    = $pctS1All >= 100 ? 'success' : ($pctS1All >= 80 ? 'warning' : 'danger');

        $totalS2All = array_sum($s2Totals);
        $pctS2All   = $totalTarget > 0 ? round(($totalS2All / ($totalTarget * 6)) * 100, 1) : 0;
        $badgeS2    = $pctS2All >= 100 ? 'success' : ($pctS2All >= 80 ? 'warning' : 'danger');
    @endphp

    <div class="row g-4 mb-4">
        {{-- Chart Semester 1 --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1 text-heading fw-bold">
                            <i class="mdi mdi-numeric-1-box me-1 text-primary"></i>Semester 1 Trend
                        </h5>
                        <small class="text-muted">Jan – Jun {{ $year }}</small>
                    </div>
                    <span class="badge bg-label-{{ $badgeS1 }} px-3 py-1 rounded-pill font-monospace fw-semibold">
                        Rp {{ number_format($totalS1All, 0, ',', '.') }} | {{ $pctS1All }}%
                    </span>
                </div>
                <div class="card-body p-3">
                    <div id="chartS1"></div>
                </div>
            </div>
        </div>

        {{-- Chart Semester 2 --}}
        <div class="col-12 col-lg-6">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1 text-heading fw-bold">
                            <i class="mdi mdi-numeric-2-box me-1 text-primary"></i>Semester 2 Trend
                        </h5>
                        <small class="text-muted">Jul – Dec {{ $year }}</small>
                    </div>
                    <span class="badge bg-label-{{ $badgeS2 }} px-3 py-1 rounded-pill font-monospace fw-semibold">
                        Rp {{ number_format($totalS2All, 0, ',', '.') }} | {{ $pctS2All }}%
                    </span>
                </div>
                <div class="card-body p-3">
                    <div id="chartS2"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TABEL SEMESTER 1 ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h5 class="mb-1 text-heading fw-bold">
                    <i class="mdi mdi-podium-gold me-2 text-warning"></i>Semester 1 Performance Breakdown
                </h5>
                <small class="text-muted">Januari – Juni {{ $year }} &bull; Realisasi per Bulan & Pencapaian Target</small>
            </div>
            <span class="badge bg-{{ $badgeS1 }} rounded-pill px-3 py-2 fs-6 fw-semibold shadow-xs">
                Total S1: Rp {{ number_format($totalS1All, 0, ',', '.') }} &nbsp;|&nbsp; {{ $pctS1All }}%
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 report-sales-table">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="min-width: 220px;">Sales Person</th>
                        @for ($m = 1; $m <= 6; $m++)
                            <th class="text-center" style="min-width: 105px;">{{ $bulanMap[$m] }}</th>
                        @endfor
                        <th class="text-end" style="min-width: 140px;">Total S1</th>
                        <th class="text-center" style="min-width: 110px;">% Target</th>
                        <th class="text-center pe-4" style="min-width: 70px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $s1Rows = [];
                        foreach ($regularSales as $sale) {
                            $t = 0;
                            for ($m = 1; $m <= 6; $m++) $t += $sale['jumlah'][$m];
                            $p = $sale['target'] > 0 ? round(($t / ($sale['target'] * 6)) * 100, 1) : 0;
                            $s1Rows[] = ['isTeam' => false, 'sale' => $sale, 'total' => $t, 'pct' => $p];
                        }
                        if (count($ecommerceMembers) > 0) {
                            $t = 0;
                            for ($m = 1; $m <= 6; $m++) $t += $teamJumlah[$m];
                            $p = $teamTarget > 0 ? round(($t / ($teamTarget * 6)) * 100, 1) : 0;
                            $s1Rows[] = ['isTeam' => true, 'total' => $t, 'pct' => $p];
                        }
                        usort($s1Rows, fn($a, $b) => $b['pct'] <=> $a['pct']);
                    @endphp

                    @foreach ($s1Rows as $idx => $row)
                        @if ($row['isTeam'])
                            @php
                                $teamS1Total = $row['total'];
                                $teamS1Pct   = $row['pct'];
                                $teamS1Lbl   = $teamS1Pct >= 100 ? 'success' : ($teamS1Pct >= 80 ? 'warning' : 'danger');
                            @endphp
                            <tr class="table-warning-subtle">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-md flex-shrink-0">
                                            <div class="avatar-initial bg-label-warning rounded-circle shadow-xs fw-bold">
                                                <i class="mdi mdi-shopping-outline fs-5"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-heading d-block">Team E-Commerce</span>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach ($ecommerceMembers as $member)
                                                    <span class="badge bg-label-secondary small px-1 py-0">{{ $member['name'] }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                @for ($m = 1; $m <= 6; $m++)
                                    @php
                                        $nomS1m  = $teamJumlah[$m];
                                        $pctMoS1 = $teamTarget > 0 ? round(($nomS1m / $teamTarget) * 100, 1) : 0;
                                        $barS1   = min($pctMoS1, 100);
                                        $clrS1   = $pctMoS1 >= 100 ? 'success' : ($pctMoS1 >= 80 ? 'warning' : 'danger');
                                    @endphp
                                    <td class="text-center">
                                        <span class="text-heading small fw-semibold d-block">
                                            {{ $nomS1m > 0 ? number_format($nomS1m, 0, ',', '.') : '—' }}
                                        </span>
                                        @if ($nomS1m > 0)
                                            <div class="d-flex align-items-center gap-1 mt-1 px-1">
                                                <div class="progress flex-grow-1" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ $clrS1 }}" style="width: {{ $barS1 }}%"></div>
                                                </div>
                                                <small class="text-{{ $clrS1 }} fw-semibold font-monospace" style="font-size: 0.68rem;">{{ $pctMoS1 }}%</small>
                                            </div>
                                        @endif
                                    </td>
                                @endfor

                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($teamS1Total, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $teamS1Lbl }} rounded-pill px-3 py-1 font-monospace">
                                        {{ $teamS1Pct }}%
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    @if ($reportS1)
                                        <div class="dropdown">
                                            <button class="btn btn-icon btn-sm btn-outline-primary waves-effect rounded-pill dropdown-toggle hide-arrow"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 1055;">
                                                @foreach ($ecommerceMembers as $member)
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('overview-sales.semester', [$reportS1->id, $member['id']]) }}">
                                                            <i class="mdi mdi-account-outline me-1"></i> {{ $member['name'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @else
                            @php
                                $sale    = $row['sale'];
                                $totalS1 = $row['total'];
                                $pctS1   = $row['pct'];
                                $lblS1   = $pctS1 >= 100 ? 'success' : ($pctS1 >= 80 ? 'warning' : 'danger');

                                // Rank badge
                                $rankIcon = '';
                                if ($idx === 0) {
                                    $rankIcon = '🥇';
                                } elseif ($idx === 1) {
                                    $rankIcon = '🥈';
                                } elseif ($idx === 2) {
                                    $rankIcon = '🥉';
                                }
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-md flex-shrink-0 position-relative">
                                            <img src="{{ url('') . '/' . $sale['image'] }}"
                                                 alt="{{ $sale['name'] }}"
                                                 class="rounded-circle shadow-xs object-fit-cover"
                                                 onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/1.png') }}'">
                                            @if ($rankIcon)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-white text-dark shadow-xs" style="font-size: 0.75rem; padding: 2px;">
                                                    {{ $rankIcon }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="fw-bold text-heading">{{ $sale['name'] }}</span>
                                    </div>
                                </td>

                                @for ($m = 1; $m <= 6; $m++)
                                    @php
                                        $nomS1m  = $sale['jumlah'][$m];
                                        $pctMoS1 = $sale['target'] > 0 ? round(($nomS1m / $sale['target']) * 100, 1) : 0;
                                        $barS1   = min($pctMoS1, 100);
                                        $clrS1   = $pctMoS1 >= 100 ? 'success' : ($pctMoS1 >= 80 ? 'warning' : 'danger');
                                    @endphp
                                    <td class="text-center">
                                        <span class="text-heading small fw-semibold d-block">
                                            {{ $nomS1m > 0 ? number_format($nomS1m, 0, ',', '.') : '—' }}
                                        </span>
                                        @if ($nomS1m > 0)
                                            <div class="d-flex align-items-center gap-1 mt-1 px-1">
                                                <div class="progress flex-grow-1" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ $clrS1 }}" style="width: {{ $barS1 }}%"></div>
                                                </div>
                                                <small class="text-{{ $clrS1 }} fw-semibold font-monospace" style="font-size: 0.68rem;">{{ $pctMoS1 }}%</small>
                                            </div>
                                        @endif
                                    </td>
                                @endfor

                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($totalS1, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $lblS1 }} rounded-pill px-3 py-1 font-monospace">
                                        {{ $pctS1 }}%
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    @if ($reportS1)
                                        <a href="{{ route('overview-sales.semester', [$reportS1->id, $sale['id']]) }}"
                                           class="btn btn-icon btn-sm btn-outline-primary waves-effect rounded-pill"
                                           data-bs-toggle="tooltip" title="View S1 Details">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    {{-- Support PIC Row --}}
                    @php
                        $supportS1 = 0;
                        for ($m = 1; $m <= 6; $m++) $supportS1 += $dataSupport[$m];
                    @endphp
                    <tr class="table-light">
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-md flex-shrink-0">
                                    <img src="{{ url('') . '/' . $support->image }}"
                                         alt="{{ $support->name }}"
                                         class="rounded-circle shadow-xs object-fit-cover"
                                         onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/1.png') }}'">
                                </div>
                                <div>
                                    <span class="fw-bold text-heading">{{ $support->name }}</span>
                                    <span class="badge bg-label-info ms-1">Marketing Support</span>
                                </div>
                            </div>
                        </td>
                        @for ($m = 1; $m <= 6; $m++)
                            <td class="text-center text-muted small fw-medium">
                                {{ $dataSupport[$m] > 0 ? number_format($dataSupport[$m], 0, ',', '.') : '—' }}
                            </td>
                        @endfor
                        <td class="text-end fw-bold text-heading">
                            Rp {{ number_format($supportS1, 0, ',', '.') }}
                        </td>
                        <td class="text-center text-muted">—</td>
                        <td class="text-center pe-4">
                            @if ($reportS1)
                                <a href="{{ route('overview-sales.semester', [$reportS1->id, $support->id]) }}"
                                   class="btn btn-icon btn-sm btn-outline-primary waves-effect rounded-pill"
                                   data-bs-toggle="tooltip" title="View Support S1 Details">
                                    <i class="mdi mdi-eye-outline"></i>
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                </tbody>

                {{-- S1 Table Footer --}}
                <tfoot class="table-light fw-bold report-table-foot">
                    <tr>
                        <td class="ps-4 py-3 text-heading">
                            <i class="mdi mdi-sigma me-1 text-primary"></i> Total Semester 1
                        </td>
                        @for ($m = 1; $m <= 6; $m++)
                            <td class="text-center">{{ number_format($s1Totals[$m - 1], 0, ',', '.') }}</td>
                        @endfor
                        <td class="text-end text-success fs-6">Rp {{ number_format($totalS1All, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $badgeS1 }} rounded-pill px-3 py-1 font-monospace">
                                {{ $pctS1All }}%
                            </span>
                        </td>
                        <td class="pe-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ===== TABEL SEMESTER 2 ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h5 class="mb-1 text-heading fw-bold">
                    <i class="mdi mdi-podium-gold me-2 text-warning"></i>Semester 2 Performance Breakdown
                </h5>
                <small class="text-muted">Juli – Desember {{ $year }} &bull; Realisasi per Bulan & Pencapaian Target</small>
            </div>
            <span class="badge bg-{{ $badgeS2 }} rounded-pill px-3 py-2 fs-6 fw-semibold shadow-xs">
                Total S2: Rp {{ number_format($totalS2All, 0, ',', '.') }} &nbsp;|&nbsp; {{ $pctS2All }}%
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 report-sales-table">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4" style="min-width: 220px;">Sales Person</th>
                        @for ($m = 7; $m <= 12; $m++)
                            <th class="text-center" style="min-width: 105px;">{{ $bulanMap[$m] }}</th>
                        @endfor
                        <th class="text-end" style="min-width: 140px;">Total S2</th>
                        <th class="text-center" style="min-width: 110px;">% Target</th>
                        <th class="text-center pe-4" style="min-width: 70px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $s2Rows = [];
                        foreach ($regularSales as $sale) {
                            $t = 0;
                            for ($m = 7; $m <= 12; $m++) $t += $sale['jumlah'][$m];
                            $p = $sale['target'] > 0 ? round(($t / ($sale['target'] * 6)) * 100, 1) : 0;
                            $s2Rows[] = ['isTeam' => false, 'sale' => $sale, 'total' => $t, 'pct' => $p];
                        }
                        if (count($ecommerceMembers) > 0) {
                            $t = 0;
                            for ($m = 7; $m <= 12; $m++) $t += $teamJumlah[$m];
                            $p = $teamTarget > 0 ? round(($t / ($teamTarget * 6)) * 100, 1) : 0;
                            $s2Rows[] = ['isTeam' => true, 'total' => $t, 'pct' => $p];
                        }
                        usort($s2Rows, fn($a, $b) => $b['pct'] <=> $a['pct']);
                    @endphp

                    @foreach ($s2Rows as $idx => $row)
                        @if ($row['isTeam'])
                            @php
                                $teamS2Total = $row['total'];
                                $teamS2Pct   = $row['pct'];
                                $teamS2Lbl   = $teamS2Pct >= 100 ? 'success' : ($teamS2Pct >= 80 ? 'warning' : 'danger');
                            @endphp
                            <tr class="table-warning-subtle">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-md flex-shrink-0">
                                            <div class="avatar-initial bg-label-warning rounded-circle shadow-xs fw-bold">
                                                <i class="mdi mdi-shopping-outline fs-5"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-heading d-block">Team E-Commerce</span>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach ($ecommerceMembers as $member)
                                                    <span class="badge bg-label-secondary small px-1 py-0">{{ $member['name'] }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                @for ($m = 7; $m <= 12; $m++)
                                    @php
                                        $nomS2m  = $teamJumlah[$m];
                                        $pctMoS2 = $teamTarget > 0 ? round(($nomS2m / $teamTarget) * 100, 1) : 0;
                                        $barS2   = min($pctMoS2, 100);
                                        $clrS2   = $pctMoS2 >= 100 ? 'success' : ($pctMoS2 >= 80 ? 'warning' : 'danger');
                                    @endphp
                                    <td class="text-center">
                                        <span class="text-heading small fw-semibold d-block">
                                            {{ $nomS2m > 0 ? number_format($nomS2m, 0, ',', '.') : '—' }}
                                        </span>
                                        @if ($nomS2m > 0)
                                            <div class="d-flex align-items-center gap-1 mt-1 px-1">
                                                <div class="progress flex-grow-1" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ $clrS2 }}" style="width: {{ $barS2 }}%"></div>
                                                </div>
                                                <small class="text-{{ $clrS2 }} fw-semibold font-monospace" style="font-size: 0.68rem;">{{ $pctMoS2 }}%</small>
                                            </div>
                                        @endif
                                    </td>
                                @endfor

                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($teamS2Total, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $teamS2Lbl }} rounded-pill px-3 py-1 font-monospace">
                                        {{ $teamS2Pct }}%
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    @if ($reportS2)
                                        <div class="dropdown">
                                            <button class="btn btn-icon btn-sm btn-outline-primary waves-effect rounded-pill dropdown-toggle hide-arrow"
                                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="mdi mdi-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 1055;">
                                                @foreach ($ecommerceMembers as $member)
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('overview-sales.semester', [$reportS2->id, $member['id']]) }}">
                                                            <i class="mdi mdi-account-outline me-1"></i> {{ $member['name'] }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @else
                            @php
                                $sale    = $row['sale'];
                                $totalS2 = $row['total'];
                                $pctS2   = $row['pct'];
                                $lblS2   = $pctS2 >= 100 ? 'success' : ($pctS2 >= 80 ? 'warning' : 'danger');

                                $rankIcon = '';
                                if ($idx === 0) {
                                    $rankIcon = '🥇';
                                } elseif ($idx === 1) {
                                    $rankIcon = '🥈';
                                } elseif ($idx === 2) {
                                    $rankIcon = '🥉';
                                }
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar avatar-md flex-shrink-0 position-relative">
                                            <img src="{{ url('') . '/' . $sale['image'] }}"
                                                 alt="{{ $sale['name'] }}"
                                                 class="rounded-circle shadow-xs object-fit-cover"
                                                 onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/1.png') }}'">
                                            @if ($rankIcon)
                                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-white text-dark shadow-xs" style="font-size: 0.75rem; padding: 2px;">
                                                    {{ $rankIcon }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="fw-bold text-heading">{{ $sale['name'] }}</span>
                                    </div>
                                </td>

                                @for ($m = 7; $m <= 12; $m++)
                                    @php
                                        $nomS2m  = $sale['jumlah'][$m];
                                        $pctMoS2 = $sale['target'] > 0 ? round(($nomS2m / $sale['target']) * 100, 1) : 0;
                                        $barS2   = min($pctMoS2, 100);
                                        $clrS2   = $pctMoS2 >= 100 ? 'success' : ($pctMoS2 >= 80 ? 'warning' : 'danger');
                                    @endphp
                                    <td class="text-center">
                                        <span class="text-heading small fw-semibold d-block">
                                            {{ $nomS2m > 0 ? number_format($nomS2m, 0, ',', '.') : '—' }}
                                        </span>
                                        @if ($nomS2m > 0)
                                            <div class="d-flex align-items-center gap-1 mt-1 px-1">
                                                <div class="progress flex-grow-1" style="height: 4px;">
                                                    <div class="progress-bar bg-{{ $clrS2 }}" style="width: {{ $barS2 }}%"></div>
                                                </div>
                                                <small class="text-{{ $clrS2 }} fw-semibold font-monospace" style="font-size: 0.68rem;">{{ $pctMoS2 }}%</small>
                                            </div>
                                        @endif
                                    </td>
                                @endfor

                                <td class="text-end fw-bold text-success">
                                    Rp {{ number_format($totalS2, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-label-{{ $lblS2 }} rounded-pill px-3 py-1 font-monospace">
                                        {{ $pctS2 }}%
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    @if ($reportS2)
                                        <a href="{{ route('overview-sales.semester', [$reportS2->id, $sale['id']]) }}"
                                           class="btn btn-icon btn-sm btn-outline-primary waves-effect rounded-pill"
                                           data-bs-toggle="tooltip" title="View S2 Details">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach

                    {{-- Support PIC Row --}}
                    @php
                        $supportS2 = 0;
                        for ($m = 7; $m <= 12; $m++) $supportS2 += $dataSupport[$m];
                    @endphp
                    <tr class="table-light">
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar avatar-md flex-shrink-0">
                                    <img src="{{ url('') . '/' . $support->image }}"
                                         alt="{{ $support->name }}"
                                         class="rounded-circle shadow-xs object-fit-cover"
                                         onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/1.png') }}'">
                                </div>
                                <div>
                                    <span class="fw-bold text-heading">{{ $support->name }}</span>
                                    <span class="badge bg-label-info ms-1">Marketing Support</span>
                                </div>
                            </div>
                        </td>
                        @for ($m = 7; $m <= 12; $m++)
                            <td class="text-center text-muted small fw-medium">
                                {{ $dataSupport[$m] > 0 ? number_format($dataSupport[$m], 0, ',', '.') : '—' }}
                            </td>
                        @endfor
                        <td class="text-end fw-bold text-heading">
                            Rp {{ number_format($supportS2, 0, ',', '.') }}
                        </td>
                        <td class="text-center text-muted">—</td>
                        <td class="text-center pe-4">
                            @if ($reportS2)
                                <a href="{{ route('overview-sales.semester', [$reportS2->id, $support->id]) }}"
                                   class="btn btn-icon btn-sm btn-outline-primary waves-effect rounded-pill"
                                   data-bs-toggle="tooltip" title="View Support S2 Details">
                                    <i class="mdi mdi-eye-outline"></i>
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                </tbody>

                {{-- S2 Table Footer --}}
                <tfoot class="table-light fw-bold report-table-foot">
                    <tr>
                        <td class="ps-4 py-3 text-heading">
                            <i class="mdi mdi-sigma me-1 text-primary"></i> Total Semester 2
                        </td>
                        @for ($m = 7; $m <= 12; $m++)
                            <td class="text-center">{{ number_format($s2Totals[$m - 7], 0, ',', '.') }}</td>
                        @endfor
                        <td class="text-end text-success fs-6">Rp {{ number_format($totalS2All, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge bg-{{ $badgeS2 }} rounded-pill px-3 py-1 font-monospace">
                                {{ $pctS2All }}%
                            </span>
                        </td>
                        <td class="pe-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
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

        /* Leaderboard Table */
        .report-sales-table thead th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: var(--bs-secondary);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }
        .report-sales-table tbody tr {
            transition: background-color 0.15s ease;
        }
        .report-table-foot {
            border-top: 2px solid rgba(0, 0, 0, 0.12) !important;
            background-color: rgba(var(--bs-primary-rgb), 0.03) !important;
        }

        /* Dark Mode Adjustments */
        .dark-style .report-hero-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.01) 100%);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }
        .dark-style .report-kpi-card {
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark-style .report-sales-table thead th {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }
        .dark-style .report-table-foot {
            border-top-color: rgba(255, 255, 255, 0.12) !important;
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
                if (val >= 1_000_000_000) return 'Rp ' + (val / 1_000_000_000).toFixed(1) + 'M';
                if (val >= 1_000_000)     return 'Rp ' + (val / 1_000_000).toFixed(1) + 'jt';
                return 'Rp ' + val.toLocaleString('id-ID');
            };

            const baseOptions = (labels, salesData, targetData) => ({
                chart: {
                    type: 'bar',
                    height: 290,
                    toolbar: { show: false },
                    parentHeightOffset: 0,
                },
                series: [
                    { name: 'Total Penjualan', type: 'bar',  data: salesData },
                    { name: 'Target Bulanan',  type: 'line', data: targetData },
                ],
                colors: ['#696cff', '#ff4c51'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '45%',
                    },
                },
                stroke: {
                    width: [0, 2],
                    curve: 'smooth',
                    dashArray: [0, 5],
                },
                markers: {
                    size: [0, 4],
                    strokeWidth: 2,
                    colors: [cardColor],
                    strokeColors: '#ff4c51',
                },
                dataLabels: {
                    enabled: true,
                    enabledOnSeries: [0],
                    formatter: function (val, opts) {
                        if (val === 0) return '';
                        const target = targetData[opts.dataPointIndex];
                        if (!target || target === 0) return '';
                        const pct = Math.round((val / target) * 100);
                        return pct + '%';
                    },
                    style: {
                        fontSize: '10px',
                        fontWeight: 'bold',
                        colors: ['#fff'],
                    },
                    background: { enabled: false },
                    offsetY: -18,
                },
                legend: {
                    show: true,
                    position: 'top',
                    horizontalAlign: 'right',
                    labels: { colors: labelColor },
                },
                xaxis: {
                    categories: labels,
                    labels: {
                        style: { colors: labelColor, fontSize: '12px' },
                    },
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
                    padding: { top: -10, bottom: -5 },
                },
                tooltip: {
                    y: { formatter: val => 'Rp ' + val.toLocaleString('id-ID') },
                },
            });

            const s1Data     = @json($s1Totals);
            const s2Data     = @json($s2Totals);
            const s1Target   = @json($s1TargetLine);
            const s2Target   = @json($s2TargetLine);
            const s1Labels   = @json($s1Labels);
            const s2Labels   = @json($s2Labels);

            const chartS1El = document.querySelector('#chartS1');
            if (chartS1El) {
                new ApexCharts(chartS1El, baseOptions(s1Labels, s1Data, s1Target)).render();
            }
            const chartS2El = document.querySelector('#chartS2');
            if (chartS2El) {
                new ApexCharts(chartS2El, baseOptions(s2Labels, s2Data, s2Target)).render();
            }
        })();
    </script>
@endpush
