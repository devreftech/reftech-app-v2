{{--
    Card "Rekap KPI Mingguan" (gaya sheet Excel Sales Manager).
    Dipakai di:
      - pages/admin/overview/kpi.blade.php (halaman detail-overview)
      - components/modal/overview.blade.php (dimuat via AJAX di modal Info dashboard admin)

    Parameter:
      $weeklyKpi  => hasil OverviewService::buildWeeklyKpi()
      $monthLabel => label bulan, mis. "Agustus 2026" (opsional)
--}}
@php
    $monthLabel = $monthLabel ?? '';
    $rekapMeta = [
        'newleads'    => ['accent' => 'primary', 'icon' => 'mdi-account-multiple-plus-outline'],
        'crm'         => ['accent' => 'success', 'icon' => 'mdi-account-heart-outline'],
        'indotrading' => ['accent' => 'warning', 'icon' => 'mdi-bullhorn-outline'],
    ];
    $rekapGrand = 0;
    foreach ($weeklyKpi as $sec) {
        foreach ($sec['rows'] as $r) {
            $rekapGrand += $r['data']['total'] ?? 0;
        }
    }
@endphp

@once
    <style>
        /* ===== Rekap KPI Mingguan ===== */
        .rekap-kpi-card {
            border-radius: 16px;
        }
        .rekap-kpi-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #696cff 0%, #9d9fff 100%);
            color: #fff;
            font-size: 22px;
            box-shadow: 0 6px 16px rgba(105, 108, 255, 0.35);
            flex-shrink: 0;
        }
        .rekap-kpi-scroll {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            overflow: auto;
        }
        .rekap-kpi-table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
            min-width: 560px;
            margin-bottom: 0;
            table-layout: fixed;
        }
        .rekap-kpi-table thead th {
            background: #f5f5f9;
            color: #6c7383;
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            text-align: center;
            padding: 12px 10px;
            border: none;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 3;
        }
        .rekap-kpi-table thead th:first-child {
            text-align: left;
            padding-left: 16px;
        }
        .rekap-kpi-table tbody td {
            padding: 12px 10px;
            text-align: center;
            vertical-align: middle;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            font-size: 0.875rem;
            color: #566a7f;
        }
        .rekap-kpi-table thead th:not(:first-child):not(.rekap-total-col) {
            /* kolom W1-W5 membagi rata sisa lebar tabel (table-layout: fixed) */
            width: auto;
            min-width: 56px;
        }
        .rekap-kpi-table thead th:first-child,
        .rekap-kpi-table tbody td:first-child {
            /* kolom "Data Monitoring" dibatasi supaya tidak terlalu lebar di layar besar */
            width: 320px;
        }
        .rekap-kpi-table tbody td:first-child {
            text-align: left;
            padding-left: 16px;
            white-space: normal;
            line-height: 1.35;
        }
        .rekap-total-col {
            width: 84px;
            background: rgba(0, 0, 0, 0.015);
        }
        .rekap-group-row td {
            padding-top: 13px;
            padding-bottom: 13px;
            border-top: 2px solid rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }
        .rekap-group-row.acc-primary td { background: rgba(105, 108, 255, 0.1); }
        .rekap-group-row.acc-success td { background: rgba(113, 221, 55, 0.12); }
        .rekap-group-row.acc-warning td { background: rgba(255, 171, 0, 0.13); }
        .rekap-group-row .rekap-group-label {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            color: #435971;
            vertical-align: middle;
        }
        .rekap-group-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 9px;
            margin-right: 10px;
            font-size: 16px;
            vertical-align: middle;
        }
        .rekap-data-row {
            transition: background 0.15s ease;
        }
        .rekap-data-row:hover {
            background: rgba(105, 108, 255, 0.04);
        }
        .rekap-row-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #c7c9d9;
            margin-right: 10px;
            vertical-align: middle;
        }
        .rekap-data-row.acc-primary .rekap-row-dot { background: #696cff; }
        .rekap-data-row.acc-success .rekap-row-dot { background: #71dd37; }
        .rekap-data-row.acc-warning .rekap-row-dot { background: #ffab00; }
        .rekap-data-row.acc-primary { --rekap-bar: #696cff; }
        .rekap-data-row.acc-success { --rekap-bar: #71dd37; }
        .rekap-data-row.acc-warning { --rekap-bar: #ffab00; }
        .rekap-week-cell {
            position: relative;
        }
        .rekap-week-val {
            font-weight: 600;
            color: #566a7f;
        }
        .rekap-week-cell.is-zero .rekap-week-val {
            color: #b7bac6;
            font-weight: 500;
        }
        .rekap-week-cell.is-peak .rekap-week-val {
            color: #435971;
            font-weight: 700;
        }
        .rekap-week-bar {
            position: absolute;
            left: 14px;
            right: 14px;
            bottom: 5px;
            height: 3px;
            border-radius: 3px;
            background: rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .rekap-week-bar::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--w, 0%);
            background: var(--rekap-bar, #696cff);
            border-radius: 3px;
            opacity: 0.55;
        }
        .rekap-week-cell.is-peak .rekap-week-bar::after {
            opacity: 1;
        }
        .rekap-total-badge {
            display: inline-block;
            min-width: 36px;
            padding: 4px 10px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.85rem;
        }
        .rekap-review-row td {
            background: repeating-linear-gradient(45deg, #fafafb, #fafafb 10px, #f4f4f7 10px, #f4f4f7 20px);
        }
        .rekap-legend-swatch {
            display: inline-block;
            width: 26px;
            height: 6px;
            border-radius: 3px;
            background: #696cff;
            opacity: 0.55;
        }
    </style>
@endonce

<div class="card mb-0 border-0 shadow-sm rekap-kpi-card">
    <div class="card-header bg-transparent border-0 pt-4 pb-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rekap-kpi-icon">
                <i class="mdi mdi-calendar-week-outline"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold">Rekap KPI Mingguan</h5>
                <span class="text-muted small">Breakdown performa sales per minggu @if ($monthLabel) &mdash; {{ $monthLabel }} @endif</span>
            </div>
        </div>
        <div class="text-end">
            <div class="text-muted small text-uppercase fw-semibold" style="letter-spacing:.04em;">Total Aktivitas</div>
            <div class="h4 mb-0 fw-bold text-primary">{{ number_format($rekapGrand, 0, ',', '.') }}</div>
        </div>
    </div>

    <div class="card-body px-4 pb-4 pt-0">
        <div class="rekap-kpi-scroll">
            <table class="table rekap-kpi-table">
                <thead>
                    <tr>
                        <th class="text-start">Data Monitoring</th>
                        <th title="Minggu 1">W1</th>
                        <th title="Minggu 2">W2</th>
                        <th title="Minggu 3">W3</th>
                        <th title="Minggu 4">W4</th>
                        <th title="Minggu 5">W5</th>
                        <th class="rekap-total-col">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weeklyKpi as $key => $section)
                        @php
                            $accent = $rekapMeta[$key]['accent'] ?? 'secondary';
                            $sectionIcon = $rekapMeta[$key]['icon'] ?? 'mdi-folder-outline';
                            $sectionTotal = 0;
                            foreach ($section['rows'] as $r) {
                                $sectionTotal += $r['data']['total'] ?? 0;
                            }
                        @endphp
                        <tr class="rekap-group-row acc-{{ $accent }}">
                            <td class="text-start">
                                <span class="rekap-group-chip bg-label-{{ $accent }}">
                                    <i class="mdi {{ $sectionIcon }}"></i>
                                </span>
                                <span class="rekap-group-label text-uppercase">{{ $section['label'] }}</span>
                            </td>
                            <td colspan="5"></td>
                            <td class="rekap-total-col">
                                <span class="fw-bold text-{{ $accent }}">{{ $sectionTotal }}</span>
                            </td>
                        </tr>

                        @foreach ($section['rows'] as $row)
                            @if (!empty($row['review']))
                                <tr class="rekap-review-row">
                                    <td class="text-start">
                                        <span class="rekap-row-dot"></span>
                                        {{ $row['name'] }}
                                        <span class="badge bg-label-warning ms-2">
                                            <i class="mdi mdi-progress-clock me-1"></i>on Review
                                        </span>
                                    </td>
                                    <td colspan="6" class="text-muted fst-italic small">
                                        Menunggu definisi metrik dari Sales Manager
                                    </td>
                                </tr>
                            @else
                                @php
                                    $weekVals = [
                                        $row['data'][1], $row['data'][2], $row['data'][3],
                                        $row['data'][4], $row['data'][5],
                                    ];
                                    $peak = max($weekVals);
                                @endphp
                                <tr class="rekap-data-row acc-{{ $accent }}">
                                    <td class="text-start">
                                        <span class="rekap-row-dot"></span>
                                        {{ $row['name'] }}
                                    </td>
                                    @foreach ($weekVals as $val)
                                        <td class="rekap-week-cell @if ($val === 0) is-zero @endif @if ($peak > 0 && $val === $peak) is-peak @endif">
                                            <span class="rekap-week-val">{{ $val }}</span>
                                            <span class="rekap-week-bar" style="--w: {{ $peak > 0 ? round($val / $peak * 100) : 0 }}%"></span>
                                        </td>
                                    @endforeach
                                    <td class="rekap-total-col">
                                        <span class="rekap-total-badge bg-label-{{ $accent }}">{{ $row['data']['total'] }}</span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-3 mt-3 pt-3 border-top">
            <span class="d-inline-flex align-items-center gap-2 small text-muted">
                <span class="rekap-legend-swatch"></span> Bar = kontribusi minggu, minggu tertinggi diberi warna penuh
            </span>
            <span class="d-inline-flex align-items-center gap-1 small text-muted">
                <i class="mdi mdi-information-outline"></i>
                Aktivitas &amp; quotation memakai minggu input sales; PO diturunkan dari tanggal PO. Quotation non-smart tidak dihitung.
            </span>
        </div>
    </div>
</div>
