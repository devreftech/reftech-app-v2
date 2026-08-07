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

        // Gabungkan lalu urutkan ulang berdasarkan poTotal, Team E-Commerce ikut rangking (tidak fix di bawah)
        $rows = array_merge($regularData, count($ecoData) ? [$ecoRow] : []);
        usort($rows, fn($a, $b) => $b['poTotal'] <=> $a['poTotal']);
    @endphp

    {{-- ===== HEADER ===== --}}
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-heading">Monthly Sales Report</h4>
            <span class="text-muted">{{ $bulanMap[$month] }} {{ $year }} &bull; All Sales</span>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            {{-- Prev --}}
            <a href="{{ route('report.monthly', [$prevYear, $prevMonth]) }}"
               class="btn btn-sm btn-outline-secondary waves-effect">
                <i class="mdi mdi-chevron-left"></i>
            </a>

            {{-- Dropdown Bulan --}}
            <div class="dropdown">
                <button type="button"
                    class="btn btn-sm btn-outline-secondary dropdown-toggle waves-effect"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    {{ $bulanMap[$month] }}
                </button>
                <ul class="dropdown-menu">
                    @for ($m = 1; $m <= 12; $m++)
                        <li>
                            <a class="dropdown-item waves-effect {{ $m == $month ? 'active' : '' }}"
                               href="{{ route('report.monthly', [$year, $m]) }}">
                                {{ $bulanMap[$m] }}
                            </a>
                        </li>
                    @endfor
                </ul>
            </div>

            {{-- Dropdown Tahun --}}
            <div class="dropdown">
                <button type="button"
                    class="btn btn-sm btn-outline-secondary dropdown-toggle waves-effect"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    {{ $year }}
                </button>
                <ul class="dropdown-menu">
                    @foreach ($yearList as $yr)
                        <li>
                            <a class="dropdown-item waves-effect {{ $yr == $year ? 'active' : '' }}"
                               href="{{ route('report.monthly', [$yr, $month]) }}">
                                {{ $yr }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Next --}}
            <a href="{{ route('report.monthly', [$nextYear, $nextMonth]) }}"
               class="btn btn-sm btn-outline-secondary waves-effect">
                <i class="mdi mdi-chevron-right"></i>
            </a>
        </div>
    </div>

    {{-- ===== SUMMARY CARDS ===== --}}
    @php
        $cards = [
            ['label' => 'Purchase Order',   'icon' => 'mdi-cart-plus',      'color' => 'success',
             'amount' => 'Rp ' . number_format($poTotal, 0, ',', '.'),      'sub' => $poCount . ' transactions'],
            ['label' => 'Active Quotation','icon' => 'mdi-cart-outline',   'color' => 'primary',
             'amount' => 'Rp ' . number_format($quoteTotal, 0, ',', '.'),   'sub' => $quoteCount . ' quotations'],
            ['label' => 'Loss',            'icon' => 'mdi-cart-minus',     'color' => 'danger',
             'amount' => 'Rp ' . number_format($lossTotal, 0, ',', '.'),    'sub' => $lossCount . ' transactions'],
            ['label' => 'Win Rate',        'icon' => 'mdi-trophy-outline', 'color' => $winColor,
             'amount' => $winRate . '%',   'sub' => $poCount . ' PO of ' . $quoteOnCount . ' quotations'],
            ['label' => 'Loss Rate',       'icon' => 'mdi-trending-down',  'color' => $lossColor,
             'amount' => $lossRate . '%',  'sub' => $lossCount . ' loss of ' . $quoteOnCount . ' quotations'],
        ];
    @endphp
    <div class="row mb-4 g-3">
        @foreach ($cards as $card)
            <div class="col-6 col-md-4 col-lg">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="avatar">
                                <div class="avatar-initial bg-label-{{ $card['color'] }} rounded">
                                    <i class="mdi {{ $card['icon'] }} mdi-24px"></i>
                                </div>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 fw-semibold text-heading" style="font-size:0.82rem">{{ $card['label'] }}</p>
                                <small class="text-muted">{{ $card['sub'] }}</small>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-0 text-{{ $card['color'] }}">{{ $card['amount'] }}</h4>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ===== TABEL PER SALES ===== --}}
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div>
                <h5 class="mb-0">Sales Performance — {{ $bulanMap[$month] }} {{ $year }}</h5>
                <small class="text-muted">New Leads · DC · CRM · Quotation · PO · Loss · Target Achievement</small>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Sales</th>
                        <th class="text-center">New Leads</th>
                        <th class="text-center">DC</th>
                        <th class="text-center">CRM</th>
                        <th class="text-center">Quote</th>
                        <th class="text-end">Quote Value</th>
                        <th class="text-center">PO</th>
                        <th class="text-end">PO Value</th>
                        <th class="text-center">Loss</th>
                        <th class="text-end pe-3">Achievement</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $i => $s)
                        @php
                            $isEco    = $s['id'] === 0;
                            $pct      = $s['target'] > 0 ? round(($s['poTotal'] / $s['target']) * 100, 1) : 0;
                            $pctColor = $pct >= 100 ? 'success' : ($pct >= 70 ? 'warning' : 'danger');
                        @endphp
                        <tr>
                            <td class="ps-3 text-muted" style="width:40px">
                                {{ $i + 1 }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($isEco)
                                        <div class="avatar avatar-sm">
                                            <div class="avatar-initial bg-label-secondary rounded" style="width:36px;height:36px;font-size:11px">EC</div>
                                        </div>
                                    @else
                                        <div class="avatar avatar-sm">
                                            <img src="{{ url('') . '/' . $s['image'] }}"
                                                 alt="{{ $s['name'] }}"
                                                 class="rounded-circle"
                                                 style="width:36px;height:36px;object-fit:cover">
                                        </div>
                                    @endif
                                    <div>
                                        <span class="fw-semibold">{{ $s['name'] }}</span>
                                        @if (($s['mktProspect'] ?? 0) > 0)
                                            @php
                                                $mktRateQ  = $s['mktProspect'] > 0 ? round(($s['mktQuote'] / $s['mktProspect']) * 100, 0) : 0;
                                                $mktRatePo = $s['mktQuote']    > 0 ? round(($s['mktPo']    / $s['mktQuote'])    * 100, 0) : 0;
                                            @endphp
                                            <div class="d-flex align-items-center gap-1 mt-1 flex-wrap">
                                                <span class="badge bg-label-secondary" style="font-size:0.7rem">
                                                    <i class="mdi mdi-handshake-outline"></i>
                                                    {{ $s['mktProspect'] }} prospect
                                                </span>
                                                <span class="text-muted" style="font-size:0.65rem">→{{ $mktRateQ }}%→</span>
                                                <span class="badge bg-label-primary" style="font-size:0.7rem">
                                                    {{ $s['mktQuote'] }} quote
                                                </span>
                                                <span class="text-muted" style="font-size:0.65rem">→{{ $mktRatePo }}%→</span>
                                                <span class="badge bg-label-success" style="font-size:0.7rem">
                                                    {{ $s['mktPo'] }} PO
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            {{-- New Leads --}}
                            <td class="text-center">
                                @if ($s['leads'] > 0)
                                    <span class="badge bg-label-success rounded-pill">{{ $s['leads'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            {{-- DC --}}
                            <td class="text-center">
                                <span class="badge bg-label-primary rounded-pill">{{ $s['dc'] }}</span>
                            </td>
                            {{-- CRM --}}
                            <td class="text-center">
                                <span class="badge bg-label-info rounded-pill">{{ $s['crm'] }}</span>
                            </td>
                            {{-- Quote --}}
                            <td class="text-center">{{ $s['quoteCount'] }}</td>
                            <td class="text-end">
                                @if ($s['quoteTotal'] > 0)
                                    <small class="text-muted">Rp {{ number_format($s['quoteTotal'], 0, ',', '.') }}</small>
                                @else
                                    <small class="text-muted">—</small>
                                @endif
                            </td>
                            {{-- PO --}}
                            <td class="text-center">
                                @if ($s['poCount'] > 0)
                                    <span class="badge bg-success rounded-pill">{{ $s['poCount'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if ($s['poTotal'] > 0)
                                    <span class="fw-semibold text-success">Rp {{ number_format($s['poTotal'], 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            {{-- Loss --}}
                            <td class="text-center">
                                @if ($s['lossCount'] > 0)
                                    <span class="badge bg-danger rounded-pill">{{ $s['lossCount'] }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            {{-- Pencapaian --}}
                            <td class="text-end pe-3">
                                <div class="d-flex align-items-center justify-content-end gap-2">
                                    <div class="progress" style="width:60px;height:6px">
                                        <div class="progress-bar bg-{{ $pctColor }}"
                                             style="width:{{ min($pct, 100) }}%"></div>
                                    </div>
                                    <span class="badge bg-label-{{ $pctColor }} rounded-pill" style="min-width:48px">
                                        {{ $pct }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="text-center py-4 text-muted">
                                No data available for this month.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                {{-- Footer total --}}
                @if (count($rows) > 0)
                    @php
                        $totalPO       = array_sum(array_column($rows, 'poTotal'));
                        $totalQuote    = array_sum(array_column($rows, 'quoteTotal'));
                        $totalLeads    = array_sum(array_column($rows, 'leads'));
                        $totalDC       = array_sum(array_column($rows, 'dc'));
                        $totalCRM      = array_sum(array_column($rows, 'crm'));
                        $totalProspect = array_sum(array_column($rows, 'prospectCount'));
                        $totalPOCnt    = array_sum(array_column($rows, 'poCount'));
                        $totalQCnt     = array_sum(array_column($rows, 'quoteCount'));
                        $totalLoss     = array_sum(array_column($rows, 'lossCount'));
                        $totalPct      = $totalTarget > 0 ? round(($totalPO / $totalTarget) * 100, 1) : 0;
                        $totPctColor   = $totalPct >= 100 ? 'success' : ($totalPct >= 70 ? 'warning' : 'danger');
                    @endphp
                    <tfoot class="table-light fw-semibold">
                        <tr>
                            <td colspan="2" class="ps-3">Total</td>
                            <td class="text-center">{{ $totalLeads }}</td>
                            <td class="text-center">{{ $totalDC }}</td>
                            <td class="text-center">{{ $totalCRM }}</td>
                            <td class="text-center">{{ $totalQCnt }}</td>
                            <td class="text-end"><small>Rp {{ number_format($totalQuote, 0, ',', '.') }}</small></td>
                            <td class="text-center">{{ $totalPOCnt }}</td>
                            <td class="text-end text-success">Rp {{ number_format($totalPO, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $totalLoss }}</td>
                            <td class="text-end pe-3">
                                <span class="badge bg-{{ $totPctColor }} rounded-pill">{{ $totalPct }}%</span>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- ===== MARKETING FUNNEL ===== --}}
    @php
        $prospectToQuote = $mktProspectCount > 0 ? round(($mktQuoteCount / $mktProspectCount) * 100, 1) : 0;
        $quoteToPoRate   = $mktQuoteCount   > 0 ? round(($mktPoCount   / $mktQuoteCount)   * 100, 1) : 0;
    @endphp
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Marketing Report</h5>
            <small class="text-muted">Marketing team contribution — {{ $bulanMap[$month] }} {{ $year }} · Funnel: Prospect → Quotation → PO</small>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-center justify-content-center">

                {{-- Prospect --}}
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-secondary h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-secondary rounded">
                                    <i class="mdi mdi-account-search-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1">{{ $mktProspectCount }}</h2>
                            <p class="mb-0 fw-semibold">Prospect</p>
                            <small class="text-muted">Submitted by marketing this month</small>
                        </div>
                    </div>
                </div>

                {{-- Arrow + rate --}}
                <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center">
                    <i class="mdi mdi-arrow-right mdi-36px text-muted d-none d-md-block"></i>
                    <i class="mdi mdi-arrow-down mdi-36px text-muted d-block d-md-none"></i>
                    <small class="badge bg-label-primary mt-1">{{ $prospectToQuote }}%</small>
                </div>

                {{-- Quotation --}}
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-primary h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-primary rounded">
                                    <i class="mdi mdi-file-document-outline mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1">{{ $mktQuoteCount }}</h2>
                            <p class="mb-0 fw-semibold">Quotation</p>
                            @if ($mktQuoteTotal > 0)
                                <small class="text-muted">Rp {{ number_format($mktQuoteTotal, 0, ',', '.') }}</small>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Arrow + rate --}}
                <div class="col-12 col-md-1 text-center d-flex flex-column align-items-center justify-content-center">
                    <i class="mdi mdi-arrow-right mdi-36px text-muted d-none d-md-block"></i>
                    <i class="mdi mdi-arrow-down mdi-36px text-muted d-block d-md-none"></i>
                    <small class="badge bg-label-success mt-1">{{ $quoteToPoRate }}%</small>
                </div>

                {{-- PO --}}
                <div class="col-12 col-md-3">
                    <div class="card border shadow-none bg-label-success h-100 text-center">
                        <div class="card-body py-4">
                            <div class="avatar mx-auto mb-3">
                                <div class="avatar-initial bg-success rounded">
                                    <i class="mdi mdi-cart-check mdi-24px"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold mb-1">{{ $mktPoCount }}</h2>
                            <p class="mb-0 fw-semibold">Purchase Order</p>
                            @if ($mktPoTotal > 0)
                                <small class="text-muted">Rp {{ number_format($mktPoTotal, 0, ',', '.') }}</small>
                            @else
                                <small class="text-muted">—</small>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===== STATUS PROSPECT ===== --}}
            <hr class="my-4">
            @php
                $statusPending   = $mktProspectByStatus->pending   ?? 0;
                $statusProvided  = $mktProspectByStatus->provided  ?? 0;
                $statusNoProvide = $mktProspectByStatus->no_provide ?? 0;
                $pctPending      = $mktProspectCount > 0 ? round(($statusPending   / $mktProspectCount) * 100, 1) : 0;
                $pctProvided     = $mktProspectCount > 0 ? round(($statusProvided  / $mktProspectCount) * 100, 1) : 0;
                $pctNoProvide    = $mktProspectCount > 0 ? round(($statusNoProvide / $mktProspectCount) * 100, 1) : 0;
            @endphp
            <p class="fw-semibold mb-3 text-heading">
                <i class="mdi mdi-clipboard-list-outline me-1"></i> Prospect Follow-up Status
            </p>
            <div class="row g-3 mb-2">
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-warning rounded">
                                <i class="mdi mdi-clock-outline mdi-24px"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Pending</span>
                                <span class="fw-bold text-warning">{{ $statusPending }}</span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-warning" style="width:{{ $pctPending }}%"></div>
                            </div>
                            <small class="text-muted">{{ $pctPending }}% not yet followed up</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-success rounded">
                                <i class="mdi mdi-check-circle-outline mdi-24px"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">Provided</span>
                                <span class="fw-bold text-success">{{ $statusProvided }}</span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-success" style="width:{{ $pctProvided }}%"></div>
                            </div>
                            <small class="text-muted">{{ $pctProvided }}% forwarded to sales</small>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded border">
                        <div class="avatar flex-shrink-0">
                            <div class="avatar-initial bg-label-danger rounded">
                                <i class="mdi mdi-close-circle-outline mdi-24px"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <span class="fw-semibold">No Provide</span>
                                <span class="fw-bold text-danger">{{ $statusNoProvide }}</span>
                            </div>
                            <div class="progress mt-1" style="height:5px">
                                <div class="progress-bar bg-danger" style="width:{{ $pctNoProvide }}%"></div>
                            </div>
                            <small class="text-muted">{{ $pctNoProvide }}% not continued</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Loss dari marketing leads --}}
            @if ($mktLossCount > 0)
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mt-2 mb-0" role="alert">
                    <i class="mdi mdi-alert-outline"></i>
                    <span>
                        <strong>{{ $mktLossCount }} loss quotation(s)</strong> from marketing leads this month
                        @if ($mktLossTotal > 0)
                            — worth <strong>Rp {{ number_format($mktLossTotal, 0, ',', '.') }}</strong>
                        @endif
                    </span>
                </div>
            @endif

            {{-- ===== PER MARKETING PERSON ===== --}}
            @if ($mktPerPerson->isNotEmpty())
                <hr class="my-4">
                <p class="fw-semibold mb-3 text-heading">
                    <i class="mdi mdi-account-group-outline me-1"></i> Per Marketing Person
                </p>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Marketing</th>
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
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar avatar-sm">
                                                <img src="{{ url('') . '/' . $p->image }}"
                                                     alt="{{ $p->name }}"
                                                     class="rounded-circle"
                                                     style="width:36px;height:36px;object-fit:cover">
                                            </div>
                                            <span class="fw-semibold">{{ $p->name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-secondary rounded-pill">{{ $p->total }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-success rounded-pill">{{ $p->provided }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-warning rounded-pill">{{ $p->pending }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-label-danger rounded-pill">{{ $p->no_provide }}</span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            <div class="progress" style="width:60px;height:6px">
                                                <div class="progress-bar bg-{{ $rateColor }}"
                                                     style="width:{{ min($provideRate, 100) }}%"></div>
                                            </div>
                                            <span class="badge bg-label-{{ $rateColor }} rounded-pill" style="min-width:48px">
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

            {{-- Tiga kolom: Sumber, Kategori & Area --}}
            @if ($mktProspectBySource->isNotEmpty() || $mktProspectByCategory->isNotEmpty() || $mktProspectByArea->isNotEmpty())
                <hr class="my-4">
                <div class="row g-4">

                    {{-- Sumber --}}
                    @if ($mktProspectBySource->isNotEmpty())
                        <div class="col-12 col-lg-4">
                            <p class="fw-semibold mb-3 text-heading">
                                <i class="mdi mdi-source-branch me-1"></i> Prospect Source
                            </p>
                            @php $maxSrc = $mktProspectBySource->max('total'); @endphp
                            <div class="d-flex flex-column gap-3">
                                @foreach ($mktProspectBySource as $src)
                                    @php
                                        $s        = $sourceIcons[$src->source] ?? $sourceIcons['Other'];
                                        $pct      = $maxSrc > 0 ? round(($src->total / $maxSrc) * 100) : 0;
                                        $ofTotal  = $mktProspectCount > 0 ? round(($src->total / $mktProspectCount) * 100, 1) : 0;
                                        $isWebDom = $src->source === 'Website' && $mktProspectByDomain->isNotEmpty();
                                    @endphp
                                    <div>
                                        <div class="d-flex align-items-center gap-3"
                                             @if ($isWebDom)
                                                 role="button" data-bs-toggle="collapse"
                                                 data-bs-target="#collapseWebsiteDomainMonthly"
                                                 aria-expanded="false" aria-controls="collapseWebsiteDomainMonthly"
                                                 style="cursor:pointer"
                                             @endif>
                                            <div class="avatar avatar-sm flex-shrink-0">
                                                <div class="avatar-initial bg-label-{{ $s['color'] }} rounded">
                                                    <i class="mdi {{ $s['icon'] }}"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="fw-semibold" style="font-size:0.85rem">
                                                        {{ $src->source }}
                                                        @if ($isWebDom)
                                                            <i class="mdi mdi-chevron-down toggle-chevron text-muted" style="font-size:0.9rem"></i>
                                                        @endif
                                                    </span>
                                                    <span class="text-muted" style="font-size:0.82rem">
                                                        {{ $src->total }} <small>({{ $ofTotal }}%)</small>
                                                    </span>
                                                </div>
                                                <div class="progress" style="height:6px">
                                                    <div class="progress-bar bg-{{ $s['color'] }}" style="width:{{ $pct }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($isWebDom)
                                            @php
                                                $maxDomain   = $mktProspectByDomain->max('total');
                                                $domainTotal = $mktProspectByDomain->sum('total');
                                            @endphp
                                            <div class="collapse" id="collapseWebsiteDomainMonthly">
                                                <div class="d-flex flex-column gap-2 mt-2 ps-5">
                                                    @foreach ($mktProspectByDomain as $dom)
                                                        @php
                                                            $dPct = $maxDomain > 0 ? round(($dom->total / $maxDomain) * 100) : 0;
                                                            $dOfT = $domainTotal > 0 ? round(($dom->total / $domainTotal) * 100, 1) : 0;
                                                        @endphp
                                                        <div>
                                                            <div class="d-flex justify-content-between mb-1">
                                                                <span class="text-muted" style="font-size:0.76rem">{{ $dom->domain }}</span>
                                                                <span class="text-muted" style="font-size:0.72rem">
                                                                    {{ $dom->total }} <small>({{ $dOfT }}%)</small>
                                                                </span>
                                                            </div>
                                                            <div class="progress" style="height:4px">
                                                                <div class="progress-bar bg-primary" style="width:{{ $dPct }}%"></div>
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
                    @endif

                    {{-- Kategori --}}
                    @if ($mktProspectByCategory->isNotEmpty())
                        <div class="col-12 col-lg-4">
                            <p class="fw-semibold mb-3 text-heading">
                                <i class="mdi mdi-tag-multiple-outline me-1"></i> Prospect Category
                            </p>
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
                                            <div class="avatar-initial bg-label-{{ $c['color'] }} rounded">
                                                <i class="mdi {{ $c['icon'] }}"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="fw-semibold" style="font-size:0.85rem">{{ $cat->category }}</span>
                                                <span class="text-muted" style="font-size:0.82rem">
                                                    {{ $cat->total }} <small>({{ $ofTotal }}%)</small>
                                                </span>
                                            </div>
                                            <div class="progress" style="height:6px">
                                                <div class="progress-bar bg-{{ $c['color'] }}" style="width:{{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Area --}}
                    @if ($mktProspectByArea->isNotEmpty())
                        <div class="col-12 col-lg-4">
                            <p class="fw-semibold mb-3 text-heading">
                                <i class="mdi mdi-map-marker-outline me-1"></i> Prospect Area
                                <small class="text-muted fw-normal">({{ $mktProspectByArea->count() }} areas)</small>
                            </p>
                            @php $maxArea = $mktProspectByArea->max('total'); @endphp
                            <div class="d-flex flex-column gap-3" id="area-list">
                                @foreach ($mktProspectByArea as $i => $ar)
                                    @php
                                        $pct     = $maxArea > 0 ? round(($ar->total / $maxArea) * 100) : 0;
                                        $ofTotal = $mktProspectCount > 0 ? round(($ar->total / $mktProspectCount) * 100, 1) : 0;
                                    @endphp
                                    <div class="d-flex align-items-center gap-3 area-item {{ $i >= 10 ? 'd-none' : '' }}"
                                         data-index="{{ $i }}">
                                        <div class="avatar avatar-sm flex-shrink-0">
                                            <div class="avatar-initial bg-label-primary rounded">
                                                <i class="mdi mdi-map-marker-outline"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="fw-semibold" style="font-size:0.85rem">{{ $ar->area }}</span>
                                                <span class="text-muted" style="font-size:0.82rem">
                                                    {{ $ar->total }} <small>({{ $ofTotal }}%)</small>
                                                </span>
                                            </div>
                                            <div class="progress" style="height:6px">
                                                <div class="progress-bar bg-primary" style="width:{{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if ($mktProspectByArea->count() > 10)
                                <button type="button" id="btn-load-more-area"
                                    class="btn btn-sm btn-outline-primary waves-effect mt-3 w-100">
                                    <i class="mdi mdi-chevron-down me-1"></i>
                                    Show {{ $mktProspectByArea->count() - 10 }} more areas
                                </button>
                            @endif
                        </div>
                    @endif

                </div>
            @endif

        </div>
    </div>
@push('before-style')
<style>
    [data-bs-toggle="collapse"] .toggle-chevron { transition: transform .2s; }
    [data-bs-toggle="collapse"]:not(.collapsed) .toggle-chevron { transform: rotate(180deg); }
</style>
@endpush
@push('after-script')
<script>
    document.getElementById('btn-load-more-area')?.addEventListener('click', function () {
        document.querySelectorAll('.area-item.d-none').forEach(el => el.classList.remove('d-none'));
        this.remove();
    });
</script>
@endpush
@endsection
