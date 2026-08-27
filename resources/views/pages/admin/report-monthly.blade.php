@extends('layouts.sales.app')
@section('title', 'Monthly Sales Report')
@section('content')
    @php
        $bulanMap = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
        ];
        $prevMonth = $month == 1 ? 12 : $month - 1;
        $prevYear  = $month == 1 ? $year - 1 : $year;
        $nextMonth = $month == 12 ? 1 : $month + 1;
        $nextYear  = $month == 12 ? $year + 1 : $year;
        $winRate   = $quoteOnCount > 0 ? round(($poCount / $quoteOnCount) * 100, 1) : 0;
        $lossRate  = $quoteOnCount > 0 ? round(($lossCount / $quoteOnCount) * 100, 1) : 0;
        $winColor  = $winRate  >= 50 ? 'success' : ($winRate  >= 30 ? 'warning' : 'danger');
        $lossColor = $lossRate <= 20 ? 'success' : ($lossRate <= 40 ? 'warning' : 'danger');

        // Pisahkan E-Commerce & regular
        $ecommerceIds = [16, 23];
        $ecoData      = array_filter($data, fn($s) => in_array($s['id'], $ecommerceIds));
        $regularData  = array_values(array_filter($data, fn($s) => !in_array($s['id'], $ecommerceIds)));

        // Gabungkan metrik Tim E-Commerce
        $ecoRow = [
            'id'            => 0,
            'name'          => 'Team E-Commerce',
            'image'         => null,
            'leads'         => array_sum(array_column($ecoData, 'leads')),
            'dc'            => array_sum(array_column($ecoData, 'dc')),
            'crm'           => array_sum(array_column($ecoData, 'crm')),
            'quoteCount'    => array_sum(array_column($ecoData, 'quoteCount')),
            'quoteTotal'    => array_sum(array_column($ecoData, 'quoteTotal')),
            'prospectCount' => array_sum(array_column($ecoData, 'prospectCount')),
            'poCount'       => array_sum(array_column($ecoData, 'poCount')),
            'poTotal'       => array_sum(array_column($ecoData, 'poTotal')),
            'lossCount'     => array_sum(array_column($ecoData, 'lossCount')),
            'target'        => array_sum(array_column($ecoData, 'target')),
            'mktProspect'   => array_sum(array_column($ecoData, 'mktProspect')),
            'mktQuote'      => array_sum(array_column($ecoData, 'mktQuote')),
            'mktPo'         => array_sum(array_column($ecoData, 'mktPo')),
        ];

        // Gabungkan lalu urutkan ulang berdasarkan poTotal
        $rows = array_merge($regularData, count($ecoData) ? [$ecoRow] : []);
        usort($rows, fn($a, $b) => $b['poTotal'] <=> $a['poTotal']);

        $totalPOAll       = array_sum(array_column($rows, 'poTotal'));
        $totalAchievement = $totalTarget > 0 ? round(($totalPOAll / $totalTarget) * 100, 1) : 0;
        $totalAchColor    = $totalAchievement >= 100 ? 'success' : ($totalAchievement >= 70 ? 'warning' : 'danger');
    @endphp

    {{-- ===== HEADER & HERO BANNER ===== --}}
    <div class="card border-0 shadow-sm mb-4 report-hero-card">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                {{-- Kiri: Judul & Target Achievement --}}
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                        <span class="badge bg-label-primary px-3 py-1 rounded-pill fw-semibold">
                            <i class="mdi mdi-chart-line me-1"></i> Monthly Report
                        </span>
                        <span class="text-muted fw-medium">{{ $bulanMap[$month] }} {{ $year }}</span>
                        <span class="text-muted">•</span>
                        <span class="text-muted small">All Sales & Marketing Division</span>
                    </div>

                    <h3 class="fw-bold text-heading mb-2">Monthly Sales & Marketing Overview</h3>

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-{{ $totalAchColor }} px-3 py-1 rounded-pill fw-semibold">
                                <i class="mdi mdi-target me-1"></i> {{ $totalAchievement }}% Target Met
                            </span>
                            <span class="text-muted small">
                                <strong>Rp {{ number_format($totalPOAll, 0, ',', '.') }}</strong>
                                @if($totalTarget > 0)
                                    <span class="text-muted"> / Rp {{ number_format($totalTarget, 0, ',', '.') }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Navigasi Bulan & Tahun + Shortcut --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <div class="btn-group shadow-xs" role="group">
                        {{-- Prev Month --}}
                        <a href="{{ route('report.monthly', [$prevYear, $prevMonth]) }}"
                           class="btn btn-outline-secondary waves-effect px-2"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="Previous Month ({{ $bulanMap[$prevMonth] }})">
                            <i class="mdi mdi-chevron-left fs-5"></i>
                        </a>

                        {{-- Dropdown Bulan --}}
                        <div class="btn-group" role="group">
                            <button type="button"
                                class="btn btn-outline-secondary dropdown-toggle waves-effect fw-semibold"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-calendar-month-outline me-1 text-primary"></i>
                                {{ $bulanMap[$month] }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 1055;">
                                @for ($m = 1; $m <= 12; $m++)
                                    <li>
                                        <a class="dropdown-item waves-effect d-flex align-items-center justify-content-between {{ $m == $month ? 'active' : '' }}"
                                           href="{{ route('report.monthly', [$year, $m]) }}">
                                            <span>{{ $bulanMap[$m] }}</span>
                                            @if ($m == $month)
                                                <i class="mdi mdi-check ms-2"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endfor
                            </ul>
                        </div>

                        {{-- Dropdown Tahun --}}
                        <div class="btn-group" role="group">
                            <button type="button"
                                class="btn btn-outline-secondary dropdown-toggle waves-effect fw-semibold"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-calendar-range me-1 text-primary"></i>
                                {{ $year }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="z-index: 1055;">
                                @foreach ($yearList as $yr)
                                    <li>
                                        <a class="dropdown-item waves-effect d-flex align-items-center justify-content-between {{ $yr == $year ? 'active' : '' }}"
                                           href="{{ route('report.monthly', [$yr, $month]) }}">
                                            <span>{{ $yr }}</span>
                                            @if ($yr == $year)
                                                <i class="mdi mdi-check ms-2"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Next Month --}}
                        <a href="{{ route('report.monthly', [$nextYear, $nextMonth]) }}"
                           class="btn btn-outline-secondary waves-effect px-2"
                           data-bs-toggle="tooltip" data-bs-placement="top" title="Next Month ({{ $bulanMap[$nextMonth] }})">
                            <i class="mdi mdi-chevron-right fs-5"></i>
                        </a>
                    </div>

                    {{-- Quick Action: Annual Report --}}
                    <a href="{{ route('report.year', $year) }}" class="btn btn-primary waves-effect shadow-sm">
                        <i class="mdi mdi-chart-box-outline me-1"></i> Annual Report
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== EXECUTIVE KPI SUMMARY CARDS ===== --}}
    @php
        $kpiCards = [
            [
                'title'       => 'Purchase Order',
                'amount'      => 'Rp ' . number_format($poTotal, 0, ',', '.'),
                'subtext'     => $poCount . ' Transaksi',
                'badge'       => 'Realized Revenue',
                'icon'        => 'mdi-cart-check',
                'theme'       => 'success',
                'borderClass' => 'border-start-success',
            ],
            [
                'title'       => 'Active Quotations',
                'amount'      => 'Rp ' . number_format($quoteTotal, 0, ',', '.'),
                'subtext'     => $quoteCount . ' Penawaran',
                'badge'       => 'Pipeline Value',
                'icon'        => 'mdi-file-document-outline',
                'theme'       => 'primary',
                'borderClass' => 'border-start-primary',
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
                'title'       => 'Win Rate',
                'amount'      => $winRate . '%',
                'subtext'     => $poCount . ' PO dari ' . $quoteOnCount . ' Quote',
                'badge'       => 'Closing Efficiency',
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
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach ($kpiCards as $kpi)
            <div class="col-12 col-sm-6 col-lg">
                <div class="card h-100 border-0 shadow-sm report-kpi-card {{ $kpi['borderClass'] }}">
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div class="avatar avatar-md">
                                <div class="avatar-initial bg-label-{{ $kpi['theme'] }} rounded-3 shadow-xs">
                                    <i class="mdi {{ $kpi['icon'] }} fs-4"></i>
                                </div>
                            </div>
                            <span class="badge bg-label-{{ $kpi['theme'] }} rounded-pill small px-2 py-1">
                                {{ $kpi['badge'] }}
                            </span>
                        </div>

                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">{{ $kpi['title'] }}</span>
                            <h4 class="fw-bold mb-1 text-{{ $kpi['theme'] }} kpi-value">{{ $kpi['amount'] }}</h4>
                            <div class="d-flex align-items-center justify-content-between">
                                <small class="text-muted">{{ $kpi['subtext'] }}</small>
                            </div>

                            @if (isset($kpi['progress']))
                                <div class="progress mt-2" style="height: 4px;">
                                    <div class="progress-bar bg-{{ $kpi['theme'] }}"
                                         role="progressbar"
                                         style="width: {{ $kpi['progress'] }}%"
                                         aria-valuenow="{{ $kpi['progress'] }}"
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

    {{-- ===== TABEL PER SALES ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent py-3 border-bottom d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h5 class="mb-1 text-heading fw-bold">
                    <i class="mdi mdi-podium-gold me-2 text-warning"></i>Sales Performance Leaderboard
                </h5>
                <small class="text-muted">
                    Periode: {{ $bulanMap[$month] }} {{ $year }} &bull; Aktivitas, Penawaran, Realisasi PO & Pencapaian Target
                </small>
            </div>

            {{-- Live Search Filter --}}
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-merge input-group-sm shadow-xs" style="max-width: 260px;">
                    <span class="input-group-text bg-transparent border-end-0">
                        <i class="mdi mdi-magnify text-muted"></i>
                    </span>
                    <input type="text" id="salesSearchInput" class="form-control border-start-0 ps-0" placeholder="Search sales name..." autocomplete="off">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 report-sales-table" id="salesTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 text-center" style="width: 50px;">#</th>
                        <th style="min-width: 220px;">Sales Person</th>
                        <th class="text-center" style="min-width: 90px;">New Leads</th>
                        <th class="text-center" style="min-width: 80px;">DC</th>
                        <th class="text-center" style="min-width: 80px;">CRM</th>
                        <th class="text-center" style="min-width: 80px;">Quote</th>
                        <th class="text-end" style="min-width: 140px;">Quote Value</th>
                        <th class="text-center" style="min-width: 80px;">PO</th>
                        <th class="text-end" style="min-width: 150px;">PO Value</th>
                        <th class="text-center" style="min-width: 80px;">Loss</th>
                        <th class="text-end pe-4" style="min-width: 170px;">Achievement</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $i => $s)
                        @php
                            $isEco    = $s['id'] === 0;
                            $pct      = $s['target'] > 0 ? round(($s['poTotal'] / $s['target']) * 100, 1) : 0;
                            $pctColor = $pct >= 100 ? 'success' : ($pct >= 70 ? 'warning' : 'danger');

                            // Rank Badge Styling
                            $rankBadge = '';
                            if ($i === 0) {
                                $rankBadge = '<span class="badge bg-warning text-white rounded-circle rank-badge shadow-xs" title="1st Place"><i class="mdi mdi-trophy"></i></span>';
                            } elseif ($i === 1) {
                                $rankBadge = '<span class="badge bg-secondary text-white rounded-circle rank-badge shadow-xs" title="2nd Place">2</span>';
                            } elseif ($i === 2) {
                                $rankBadge = '<span class="badge bg-label-warning text-dark rounded-circle rank-badge shadow-xs" title="3rd Place">3</span>';
                            } else {
                                $rankBadge = '<span class="text-muted fw-semibold">' . ($i + 1) . '</span>';
                            }
                        @endphp
                        <tr class="sales-row" data-name="{{ strtolower($s['name']) }}">
                            {{-- Rank --}}
                            <td class="ps-4 text-center">
                                {!! $rankBadge !!}
                            </td>

                            {{-- Sales Profile --}}
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if ($isEco)
                                        <div class="avatar avatar-md flex-shrink-0">
                                            <div class="avatar-initial bg-label-info rounded-circle shadow-xs fw-bold">
                                                <i class="mdi mdi-shopping-outline fs-5"></i>
                                            </div>
                                        </div>
                                    @else
                                        <div class="avatar avatar-md flex-shrink-0">
                                            <img src="{{ url('') . '/' . $s['image'] }}"
                                                 alt="{{ $s['name'] }}"
                                                 class="rounded-circle shadow-xs object-fit-cover"
                                                 onerror="this.onerror=null; this.src='{{ asset('assets/img/avatars/1.png') }}'">
                                        </div>
                                    @endif

                                    <div class="sales-info">
                                        <span class="fw-bold text-heading d-block">{{ $s['name'] }}</span>
                                        
                                        @if (($s['mktProspect'] ?? 0) > 0)
                                            @php
                                                $mktRateQ  = $s['mktProspect'] > 0 ? round(($s['mktQuote'] / $s['mktProspect']) * 100, 0) : 0;
                                                $mktRatePo = $s['mktQuote']    > 0 ? round(($s['mktPo']    / $s['mktQuote'])    * 100, 0) : 0;
                                            @endphp
                                            <div class="funnel-inline-chip mt-1 d-inline-flex align-items-center gap-1">
                                                <span class="badge bg-label-secondary px-2 py-0" style="font-size: 0.7rem;">
                                                    <i class="mdi mdi-account-search-outline me-1"></i>{{ $s['mktProspect'] }}
                                                </span>
                                                <i class="mdi mdi-chevron-right text-muted micro-arrow"></i>
                                                <span class="badge bg-label-primary px-2 py-0" style="font-size: 0.7rem;">
                                                    <i class="mdi mdi-file-document-outline me-1"></i>{{ $s['mktQuote'] }}
                                                </span>
                                                <i class="mdi mdi-chevron-right text-muted micro-arrow"></i>
                                                <span class="badge bg-label-success px-2 py-0" style="font-size: 0.7rem;">
                                                    <i class="mdi mdi-cart-check me-1"></i>{{ $s['mktPo'] }} PO
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- New Leads --}}
                            <td class="text-center">
                                @if ($s['leads'] > 0)
                                    <span class="badge bg-label-success rounded-pill fw-semibold">{{ $s['leads'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- DC --}}
                            <td class="text-center">
                                @if ($s['dc'] > 0)
                                    <span class="badge bg-label-primary rounded-pill fw-semibold">{{ $s['dc'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- CRM --}}
                            <td class="text-center">
                                @if ($s['crm'] > 0)
                                    <span class="badge bg-label-info rounded-pill fw-semibold">{{ $s['crm'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Quote Count --}}
                            <td class="text-center">
                                @if ($s['quoteCount'] > 0)
                                    <span class="fw-semibold">{{ $s['quoteCount'] }}</span>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>

                            {{-- Quote Value --}}
                            <td class="text-end">
                                @if ($s['quoteTotal'] > 0)
                                    <span class="text-muted small fw-medium">Rp {{ number_format($s['quoteTotal'], 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- PO Count --}}
                            <td class="text-center">
                                @if ($s['poCount'] > 0)
                                    <span class="badge bg-success rounded-pill px-2 py-1 fw-bold">{{ $s['poCount'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- PO Value --}}
                            <td class="text-end">
                                @if ($s['poTotal'] > 0)
                                    <span class="fw-bold text-success">Rp {{ number_format($s['poTotal'], 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Loss Count --}}
                            <td class="text-center">
                                @if ($s['lossCount'] > 0)
                                    <span class="badge bg-label-danger rounded-pill fw-semibold">{{ $s['lossCount'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Achievement Progress --}}
                            <td class="text-end pe-4">
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress report-progress-bar" style="width: 70px; height: 6px;">
                                            <div class="progress-bar bg-{{ $pctColor }}"
                                                 role="progressbar"
                                                 style="width: {{ min($pct, 100) }}%"
                                                 aria-valuenow="{{ $pct }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100"></div>
                                        </div>
                                        <span class="badge bg-label-{{ $pctColor }} rounded-pill" style="min-width: 52px;">
                                            {{ $pct }}%
                                        </span>
                                    </div>
                                    @if ($s['target'] > 0)
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            Target: Rp {{ number_format($s['target'], 0, ',', '.') }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="noDataRow">
                            <td colspan="11" class="text-center py-5 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="mdi mdi-database-off-outline fs-1 text-muted mb-2"></i>
                                    <span class="fw-semibold">No sales data available for this month.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Footer Summary Total --}}
                @if (count($rows) > 0)
                    @php
                        $totalLeads    = array_sum(array_column($rows, 'leads'));
                        $totalDC       = array_sum(array_column($rows, 'dc'));
                        $totalCRM      = array_sum(array_column($rows, 'crm'));
                        $totalQCnt     = array_sum(array_column($rows, 'quoteCount'));
                        $totalQuote    = array_sum(array_column($rows, 'quoteTotal'));
                        $totalPOCnt    = array_sum(array_column($rows, 'poCount'));
                        $totalPO       = array_sum(array_column($rows, 'poTotal'));
                        $totalLoss     = array_sum(array_column($rows, 'lossCount'));
                    @endphp
                    <tfoot class="table-light fw-bold report-table-foot">
                        <tr>
                            <td colspan="2" class="ps-4 py-3 text-heading">
                                <i class="mdi mdi-sigma me-1 text-primary"></i> Grand Total Summary
                            </td>
                            <td class="text-center">{{ $totalLeads }}</td>
                            <td class="text-center">{{ $totalDC }}</td>
                            <td class="text-center">{{ $totalCRM }}</td>
                            <td class="text-center">{{ $totalQCnt }}</td>
                            <td class="text-end text-muted">Rp {{ number_format($totalQuote, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge bg-success rounded-pill px-2">{{ $totalPOCnt }}</span>
                            </td>
                            <td class="text-end text-success fs-6">Rp {{ number_format($totalPO, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <span class="badge bg-label-danger rounded-pill px-2">{{ $totalLoss }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge bg-{{ $totalAchColor }} rounded-pill px-3 py-1 fs-6">
                                    {{ $totalAchievement }}%
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ===== MARKETING FUNNEL & ANALYTICS SECTION ===== --}}
    @php
        $prospectToQuote = $mktProspectCount > 0 ? round(($mktQuoteCount / $mktProspectCount) * 100, 1) : 0;
        $quoteToPoRate   = $mktQuoteCount   > 0 ? round(($mktPoCount   / $mktQuoteCount)   * 100, 1) : 0;
    @endphp
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent py-3 border-bottom">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="mb-1 text-heading fw-bold">
                        <i class="mdi mdi-bullseye-arrow me-2 text-primary"></i>Marketing Funnel & Acquisition Report
                    </h5>
                    <small class="text-muted">
                        Kontribusi Tim Marketing &mdash; {{ $bulanMap[$month] }} {{ $year }} &bull; Pipeline Konversi: Prospect &rarr; Quotation &rarr; PO
                    </small>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
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
                            <h2 class="fw-bold mb-1 text-heading">{{ $mktProspectCount }}</h2>
                            <p class="mb-0 fw-semibold text-muted text-uppercase small">Prospect Generated</p>
                            <small class="text-muted">Total leads masuk marketing</small>
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
                        {{ $prospectToQuote }}%
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
                            <h2 class="fw-bold mb-1 text-primary">{{ $mktQuoteCount }}</h2>
                            <p class="mb-0 fw-semibold text-primary text-uppercase small">Quotation Created</p>
                            @if ($mktQuoteTotal > 0)
                                <span class="badge bg-white text-primary fw-bold shadow-xs mt-1">
                                    Rp {{ number_format($mktQuoteTotal, 0, ',', '.') }}
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
                        {{ $quoteToPoRate }}%
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
                            <h2 class="fw-bold mb-1 text-success">{{ $mktPoCount }}</h2>
                            <p class="mb-0 fw-semibold text-success text-uppercase small">Purchase Order (PO)</p>
                            @if ($mktPoTotal > 0)
                                <span class="badge bg-white text-success fw-bold shadow-xs mt-1">
                                    Rp {{ number_format($mktPoTotal, 0, ',', '.') }}
                                </span>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===== STATUS PROSPECT FOLLOW-UP ===== --}}
            <div class="pt-2">
                @php
                    $statusPending   = $mktProspectByStatus->pending   ?? 0;
                    $statusProvided  = $mktProspectByStatus->provided  ?? 0;
                    $statusNoProvide = $mktProspectByStatus->no_provide ?? 0;
                    $pctPending      = $mktProspectCount > 0 ? round(($statusPending   / $mktProspectCount) * 100, 1) : 0;
                    $pctProvided     = $mktProspectCount > 0 ? round(($statusProvided  / $mktProspectCount) * 100, 1) : 0;
                    $pctNoProvide    = $mktProspectCount > 0 ? round(($statusNoProvide / $mktProspectCount) * 100, 1) : 0;
                @endphp

                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-heading mb-0">
                        <i class="mdi mdi-clipboard-text-clock-outline me-2 text-warning"></i>Prospect Follow-up Status
                    </h6>
                    <span class="text-muted small">Status tindak lanjut prospect bulan ini</span>
                </div>

                <div class="row g-3">
                    {{-- Status: Pending --}}
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
                                    <span class="fw-bold text-warning fs-5">{{ $statusPending }}</span>
                                </div>
                                <div class="progress mb-1" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $pctPending }}%"></div>
                                </div>
                                <small class="text-muted">{{ $pctPending }}% belum diproses</small>
                            </div>
                        </div>
                    </div>

                    {{-- Status: Provided --}}
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
                                    <span class="fw-bold text-success fs-5">{{ $statusProvided }}</span>
                                </div>
                                <div class="progress mb-1" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $pctProvided }}%"></div>
                                </div>
                                <small class="text-muted">{{ $pctProvided }}% diteruskan ke sales</small>
                            </div>
                        </div>
                    </div>

                    {{-- Status: No Provide --}}
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
                                    <span class="fw-bold text-danger fs-5">{{ $statusNoProvide }}</span>
                                </div>
                                <div class="progress mb-1" style="height: 6px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $pctNoProvide }}%"></div>
                                </div>
                                <small class="text-muted">{{ $pctNoProvide }}% tidak dilanjutkan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Loss Callout Banner --}}
            @if ($mktLossCount > 0)
                <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-3 p-3 mt-4 mb-0 rounded-3" role="alert">
                    <div class="avatar avatar-sm flex-shrink-0">
                        <div class="avatar-initial bg-danger text-white rounded-circle">
                            <i class="mdi mdi-alert-circle-outline fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <span class="fw-bold d-block">Marketing Leads Loss Summary</span>
                        <span class="small">
                            Terdapat <strong>{{ $mktLossCount }} penawaran loss</strong> dari leads marketing pada bulan ini
                            @if ($mktLossTotal > 0)
                                &mdash; senilai <strong>Rp {{ number_format($mktLossTotal, 0, ',', '.') }}</strong>
                            @endif
                        </span>
                    </div>
                </div>
            @endif

            {{-- ===== PER MARKETING PERSON ===== --}}
            @if ($mktPerPerson->isNotEmpty())
                <hr class="my-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-heading mb-0">
                        <i class="mdi mdi-account-group-outline me-2 text-primary"></i>Marketing Support Performance
                    </h6>
                    <small class="text-muted">{{ $mktPerPerson->count() }} personel marketing</small>
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
                            @foreach ($mktPerPerson as $p)
                                @php
                                    $provideRate = $p->total > 0 ? round(($p->provided / $p->total) * 100, 1) : 0;
                                    $rateColor   = $provideRate >= 70 ? 'success' : ($provideRate >= 40 ? 'warning' : 'danger');
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
                                                <div class="progress-bar bg-{{ $rateColor }}"
                                                     role="progressbar"
                                                     style="width: {{ min($provideRate, 100) }}%"></div>
                                            </div>
                                            <span class="badge bg-label-{{ $rateColor }} rounded-pill" style="min-width: 50px;">
                                                {{ $provideRate }}%
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @php
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
                $categoryIcons = [
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

            {{-- ===== 3-COLUMN BREAKDOWN: SOURCE, CATEGORY, AREA ===== --}}
            @if ($mktProspectBySource->isNotEmpty() || $mktProspectByCategory->isNotEmpty() || $mktProspectByArea->isNotEmpty())
                <hr class="my-4">
                <div class="row g-4">

                    {{-- 1. Prospect Source --}}
                    @if ($mktProspectBySource->isNotEmpty())
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fw-bold text-heading mb-0">
                                        <i class="mdi mdi-source-branch me-1 text-primary"></i> Prospect Source
                                    </h6>
                                    <span class="badge bg-label-primary rounded-pill">{{ $mktProspectBySource->count() }} Sources</span>
                                </div>

                                @php $maxSrc = $mktProspectBySource->max('total'); @endphp
                                <div class="d-flex flex-column gap-3">
                                    @foreach ($mktProspectBySource as $src)
                                        @php
                                            $s        = $sourceIcons[$src->source] ?? $sourceIcons['Other'];
                                            $pct      = $maxSrc > 0 ? round(($src->total / $maxSrc) * 100) : 0;
                                            $ofTotal  = $mktProspectCount > 0 ? round(($src->total / $mktProspectCount) * 100, 1) : 0;
                                            $isWebDom = $src->source === 'Website' && $mktProspectByDomain->isNotEmpty();
                                        @endphp
                                        <div class="source-item">
                                            <div class="d-flex align-items-center gap-3 {{ $isWebDom ? 'cursor-pointer' : '' }}"
                                                 @if ($isWebDom)
                                                     role="button" data-bs-toggle="collapse"
                                                     data-bs-target="#collapseWebsiteDomainMonthly"
                                                     aria-expanded="false" aria-controls="collapseWebsiteDomainMonthly"
                                                 @endif>
                                                <div class="avatar avatar-sm flex-shrink-0">
                                                    <div class="avatar-initial bg-label-{{ $s['color'] }} rounded-3 shadow-xs">
                                                        <i class="mdi {{ $s['icon'] }} fs-5"></i>
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
                                                            <strong>{{ $src->total }}</strong> <small>({{ $ofTotal }}%)</small>
                                                        </span>
                                                    </div>
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-{{ $s['color'] }}" style="width: {{ $pct }}%"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Drilldown Website Domain --}}
                                            @if ($isWebDom)
                                                @php
                                                    $maxDomain   = $mktProspectByDomain->max('total');
                                                    $domainTotal = $mktProspectByDomain->sum('total');
                                                @endphp
                                                <div class="collapse mt-2 ps-4" id="collapseWebsiteDomainMonthly">
                                                    <div class="p-2 rounded border bg-white shadow-xs d-flex flex-column gap-2">
                                                        @foreach ($mktProspectByDomain as $dom)
                                                            @php
                                                                $dPct = $maxDomain > 0 ? round(($dom->total / $maxDomain) * 100) : 0;
                                                                $dOfT = $domainTotal > 0 ? round(($dom->total / $domainTotal) * 100, 1) : 0;
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
                    @if ($mktProspectByCategory->isNotEmpty())
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fw-bold text-heading mb-0">
                                        <i class="mdi mdi-tag-multiple-outline me-1 text-primary"></i> Prospect Category
                                    </h6>
                                    <span class="badge bg-label-info rounded-pill">{{ $mktProspectByCategory->count() }} Categories</span>
                                </div>

                                @php $maxCat = $mktProspectByCategory->max('total'); @endphp
                                <div class="d-flex flex-column gap-3">
                                    @foreach ($mktProspectByCategory as $cat)
                                        @php
                                            $c       = $categoryIcons[$cat->category] ?? $categoryIcons['Uncategorized'];
                                            $pct     = $maxCat > 0 ? round(($cat->total / $maxCat) * 100) : 0;
                                            $ofTotal = $mktProspectCount > 0 ? round(($cat->total / $mktProspectCount) * 100, 1) : 0;
                                        @endphp
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar avatar-sm flex-shrink-0">
                                                <div class="avatar-initial bg-label-{{ $c['color'] }} rounded-3 shadow-xs">
                                                    <i class="mdi {{ $c['icon'] }} fs-5"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-semibold text-heading small">{{ $cat->category }}</span>
                                                    <span class="text-muted small">
                                                        <strong>{{ $cat->total }}</strong> <small>({{ $ofTotal }}%)</small>
                                                    </span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $c['color'] }}" style="width: {{ $pct }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 3. Prospect Area --}}
                    @if ($mktProspectByArea->isNotEmpty())
                        <div class="col-12 col-lg-4">
                            <div class="card h-100 border rounded-3 p-3 bg-light-subtle">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fw-bold text-heading mb-0">
                                        <i class="mdi mdi-map-marker-outline me-1 text-primary"></i> Prospect Area
                                    </h6>
                                    <span class="badge bg-label-warning rounded-pill">{{ $mktProspectByArea->count() }} Areas</span>
                                </div>

                                @php $maxArea = $mktProspectByArea->max('total'); @endphp
                                <div class="d-flex flex-column gap-3" id="area-list">
                                    @foreach ($mktProspectByArea as $i => $ar)
                                        @php
                                            $pct     = $maxArea > 0 ? round(($ar->total / $maxArea) * 100) : 0;
                                            $ofTotal = $mktProspectCount > 0 ? round(($ar->total / $mktProspectCount) * 100, 1) : 0;
                                        @endphp
                                        <div class="d-flex align-items-center gap-3 area-item {{ $i >= 8 ? 'd-none' : '' }}" data-index="{{ $i }}">
                                            <div class="avatar avatar-sm flex-shrink-0">
                                                <div class="avatar-initial bg-label-primary rounded-3 shadow-xs">
                                                    <i class="mdi mdi-map-marker-outline fs-5"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-semibold text-heading small">{{ $ar->area }}</span>
                                                    <span class="text-muted small">
                                                        <strong>{{ $ar->total }}</strong> <small>({{ $ofTotal }}%)</small>
                                                    </span>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-primary" style="width: {{ $pct }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if ($mktProspectByArea->count() > 8)
                                    <button type="button" id="btn-load-more-area"
                                        class="btn btn-sm btn-outline-primary waves-effect mt-3 w-100">
                                        <i class="mdi mdi-chevron-down me-1"></i>
                                        Show {{ $mktProspectByArea->count() - 8 }} more areas
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
            @endif

        </div>
    </div>

@push('before-style')
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
    .border-start-primary { border-left: 4px solid var(--bs-primary) !important; }
    .border-start-success { border-left: 4px solid var(--bs-success) !important; }
    .border-start-warning { border-left: 4px solid var(--bs-warning) !important; }
    .border-start-danger  { border-left: 4px solid var(--bs-danger) !important; }

    .kpi-value {
        font-size: 1.35rem;
        letter-spacing: -0.5px;
    }

    /* Sales Leaderboard Table */
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

    .rank-badge {
        width: 26px;
        height: 26px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.78rem;
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

    /* Funnel Box Style */
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

    /* Chevron rotation for collapsible items */
    [data-bs-toggle="collapse"] .toggle-chevron {
        transition: transform 0.2s ease;
    }
    [data-bs-toggle="collapse"]:not(.collapsed) .toggle-chevron {
        transform: rotate(180deg);
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

@push('after-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize tooltips if bootstrap is loaded
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Live Search Filter for Sales Table
        const searchInput = document.getElementById('salesSearchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                const rows = document.querySelectorAll('.sales-row');
                let matchCount = 0;

                rows.forEach(function (row) {
                    const name = row.getAttribute('data-name') || '';
                    if (name.includes(query)) {
                        row.style.display = '';
                        matchCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const noDataRow = document.getElementById('noDataRow');
                if (noDataRow) {
                    noDataRow.style.display = matchCount === 0 ? '' : 'none';
                }
            });
        }

        // Load More Areas Toggle
        const btnLoadMoreArea = document.getElementById('btn-load-more-area');
        if (btnLoadMoreArea) {
            btnLoadMoreArea.addEventListener('click', function () {
                document.querySelectorAll('.area-item.d-none').forEach(function (el) {
                    el.classList.remove('d-none');
                });
                this.remove();
            });
        }
    });
</script>
@endpush
@endsection
