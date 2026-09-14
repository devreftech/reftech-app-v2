@extends('layouts.sales.app')
@section('title', 'Laporan Laba Rugi - ' . $startString . ' s/d ' . $endString)
@section('content')
    @php
        $periodYear = \Carbon\Carbon::parse($startDate)->year;
        $printUrl = $month
            ? route('expense-income.print-bulan', [str_pad($month, 2, '0', STR_PAD_LEFT), $periodYear])
            : route('expense-income.print-tahun', [$periodYear]);
    @endphp

    {{-- Top Header Action Bar --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center py-3 mb-3 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 fs-6">
                    <li class="breadcrumb-item"><a href="{{ route('finance.statement.index') }}">Finance</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('finance.statement.index') }}?tab=income#tab-income">Statement</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Laba Rugi (Income Statement)</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-book-open-outline text-primary"></i> Laporan Laba Rugi Komprehensif
                <span class="badge bg-label-primary fs-6">{{ $startString }} &ndash; {{ $endString }}</span>
            </h4>
            <p class="text-muted mb-0 small">
                Rincian pendapatan penjualan, beban pokok barang (HPP), beban operasional, dan laba bersih PT. Reftech Jaya Optima.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Period Quick Jump --}}
            <div class="dropdown">
                <button class="btn btn-label-secondary btn-sm dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-calendar-range me-1"></i> Ganti Periode
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="max-height: 320px; overflow-y: auto;">
                    <li class="dropdown-header text-uppercase small fw-bold">Tahun {{ $periodYear }}</li>
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $monthName = \Carbon\Carbon::create($periodYear, $m, 1)->translatedFormat('F');
                            $isCurrent = (int)$month === $m;
                        @endphp
                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center {{ $isCurrent ? 'active fw-bold' : '' }}" 
                               href="{{ route('expense-income.detail-bulan', [str_pad($m, 2, '0', STR_PAD_LEFT), $periodYear]) }}">
                                <span>Bulan {{ $monthName }}</span>
                                @if($isCurrent) <i class="mdi mdi-check small"></i> @endif
                            </a>
                        </li>
                    @endfor
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item fw-semibold" href="{{ route('expense-income.detail-tahun', [$periodYear]) }}">
                            <i class="mdi mdi-calendar-blank-outline me-1"></i> Seluruh Tahun {{ $periodYear }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Back Button --}}
            <a href="{{ route('finance.statement.index') }}?tab=income#tab-income" class="btn btn-label-secondary btn-sm shadow-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>

            {{-- Print Button --}}
            <a href="{{ $printUrl }}" target="_blank" class="btn btn-primary btn-sm shadow-sm">
                <i class="mdi mdi-printer-outline me-1"></i> Cetak / Print A4
            </a>
        </div>
    </div>

    {{-- Executive KPI Metrics Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Revenue --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%); border-left: 4px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Pendapatan Usaha</span>
                        <div class="avatar avatar-sm bg-label-primary rounded">
                            <i class="mdi mdi-chart-timeline-variant-shimmer fs-5 text-primary"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-primary mb-1">Rp {{ number_format($poSum ?? 0, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        Realisasi PO Penjualan Periode Ini
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: HPP --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #fffbeb 100%); border-left: 4px solid #ffab00 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Beban Pokok (HPP)</span>
                        <div class="avatar avatar-sm bg-label-warning rounded">
                            <i class="mdi mdi-package-variant-closed fs-5 text-warning"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-dark mb-1">Rp {{ number_format($modalSum ?? 0, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        Harga Modal Unit Serial Produk
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Gross Profit --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%); border-left: 4px solid #03c3ec !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Laba Kotor</span>
                        <div class="avatar avatar-sm bg-label-info rounded">
                            <i class="mdi mdi-finance fs-5 text-info"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-info mb-1">Rp {{ number_format($subtotal ?? 0, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        <span class="fw-bold text-info">Gross Margin: {{ $grossMarginPct }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Net Profit --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, {{ $total >= 0 ? '#f0fdf4' : '#fef2f2' }} 100%); border-left: 4px solid {{ $total >= 0 ? '#71dd37' : '#ff3e1d' }} !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Laba Bersih (Net)</span>
                        <div class="avatar avatar-sm {{ $total >= 0 ? 'bg-label-success' : 'bg-label-danger' }} rounded">
                            <i class="mdi {{ $total >= 0 ? 'mdi-trending-up text-success' : 'mdi-trending-down text-danger' }} fs-5"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder {{ $total >= 0 ? 'text-success' : 'text-danger' }} mb-1">
                        {{ $total < 0 ? '- ' : '' }}Rp {{ number_format(abs($total ?? 0), 0, ',', '.') }}
                    </h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        <span class="fw-bold {{ $total >= 0 ? 'text-success' : 'text-danger' }}">Net Margin: {{ $netMarginPct }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Financial Statement Card --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <span class="fw-bold text-dark d-flex align-items-center gap-2">
                <i class="mdi mdi-file-document-outline text-primary fs-5"></i> Laporan Posisi Laba Rugi Komprehensif
            </span>
            <span class="badge bg-label-primary rounded-pill">{{ $startString }} &ndash; {{ $endString }}</span>
        </div>
        <div class="card-body p-0">
            <style>
                .bs-table {
                    width: 100%;
                    border-collapse: collapse;
                    font-size: 13px;
                }
                .bs-table td {
                    padding: 6px 14px;
                    vertical-align: middle;
                    line-height: 1.4;
                }
                .bs-sec-header td {
                    background-color: #f1f5f9;
                    font-weight: 700;
                    color: #1e293b;
                    font-size: 12px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    padding-top: 8px;
                    padding-bottom: 8px;
                    border-top: 1px solid #e2e8f0;
                    border-bottom: 1px solid #cbd5e1;
                }
                .bs-group-header td {
                    font-weight: 600;
                    color: #334155;
                    font-size: 12px;
                    background-color: #fafbfd;
                    padding-top: 6px;
                    padding-bottom: 5px;
                    border-top: 1px dashed #edf2f7;
                }
                .bs-row-item td {
                    color: #475569;
                    border-bottom: 1px solid #f8fafc;
                }
                .bs-row-item:hover td {
                    background-color: #f8faff;
                }
                .bs-row-subtotal td {
                    background-color: #f8fafc;
                    border-top: 1px dashed #cbd5e1;
                    border-bottom: 1px solid #e2e8f0;
                    color: #1e293b;
                    font-size: 12.5px;
                    padding-top: 6px;
                    padding-bottom: 6px;
                }
                .bs-row-highlight td {
                    background-color: #f0f7ff;
                    border-top: 2px solid #3b82f6;
                    border-bottom: 2px solid #3b82f6;
                    color: #1e40af !important;
                    font-size: 13px;
                    font-weight: 700;
                    padding-top: 8px;
                    padding-bottom: 8px;
                }
                .bs-row-highlight td * {
                    color: #1e40af !important;
                }
                .bs-row-grand-total td {
                    background-color: #f1f5f9 !important;
                    color: #0f172a !important;
                    font-weight: 800 !important;
                    border-top: 2px solid #0f172a !important;
                    border-bottom: 4px double #0f172a !important;
                    padding-top: 10px !important;
                    padding-bottom: 10px !important;
                }
                .bs-row-grand-total td * {
                    color: #0f172a !important;
                }
                .bs-amount {
                    font-variant-numeric: tabular-nums;
                    white-space: nowrap;
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, monospace;
                    text-align: right;
                }
            </style>

            <table class="table table-hover bs-table mb-0">
                <colgroup>
                    <col style="width: 70%;">
                    <col style="width: 30%;">
                </colgroup>
                <tbody>
                    {{-- A. PENDAPATAN USAHA --}}
                    <tr class="bs-sec-header">
                        <td colspan="2">
                            <i class="mdi mdi-cash-plus me-1 text-primary"></i> A. PENDAPATAN USAHA (REVENUES)
                        </td>
                    </tr>
                    <tr class="bs-row-item">
                        <td class="ps-4">
                            <span class="d-inline-flex align-items-center gap-1">
                                <i class="mdi mdi-file-document-outline text-muted small"></i> Penjualan Produk &amp; Unit PO
                            </span>
                        </td>
                        <td class="bs-amount fw-medium">{{ number_format($poSum ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bs-row-item">
                        <td class="ps-4 text-muted">
                            <span class="d-inline-flex align-items-center gap-1">
                                <i class="mdi mdi-tag-outline text-muted small"></i> Potongan Penjualan &amp; Retur
                            </span>
                        </td>
                        <td class="bs-amount text-muted">0</td>
                    </tr>
                    <tr class="bs-row-subtotal">
                        <td class="ps-3 fw-semibold">Jumlah Pendapatan Usaha Bersih</td>
                        <td class="bs-amount fw-semibold text-primary">{{ number_format($poSum ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    {{-- B. BEBAN POKOK PENJUALAN --}}
                    <tr class="bs-sec-header">
                        <td colspan="2">
                            <i class="mdi mdi-cart-arrow-down me-1 text-warning"></i> B. BEBAN POKOK PENJUALAN (COST OF GOODS SOLD)
                        </td>
                    </tr>
                    <tr class="bs-row-item">
                        <td class="ps-4">
                            <span class="d-inline-flex align-items-center gap-1">
                                <i class="mdi mdi-barcode text-muted small"></i> Beban Pokok Barang Terjual (HPP Serial Unit)
                            </span>
                        </td>
                        <td class="bs-amount text-danger">- {{ number_format($modalSum ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="bs-row-subtotal">
                        <td class="ps-3 fw-semibold text-danger">Jumlah Beban Pokok Penjualan (HPP)</td>
                        <td class="bs-amount fw-semibold text-danger">- {{ number_format($modalSum ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    {{-- LABA KOTOR --}}
                    <tr class="bs-row-highlight">
                        <td class="fw-bold">
                            <i class="mdi mdi-chart-bell-curve me-1 text-primary"></i> LABA KOTOR (GROSS PROFIT)
                            <span class="badge bg-label-primary rounded-pill ms-2">Gross Margin: {{ $grossMarginPct }}%</span>
                        </td>
                        <td class="bs-amount fw-bold fs-6 text-primary">{{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>

                    {{-- C. BEBAN OPERASIONAL --}}
                    <tr class="bs-sec-header">
                        <td colspan="2">
                            <i class="mdi mdi-office-building-cog me-1 text-info"></i> C. BEBAN OPERASIONAL (OPERATING EXPENSES)
                        </td>
                    </tr>
                    @forelse ($allExpense as $item)
                        <tr class="bs-row-item">
                            <td class="ps-4">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-chevron-right text-muted small"></i> {{ optional($item->account)->name ?? $item->description ?? 'Beban Operasional' }}
                                </span>
                            </td>
                            <td class="bs-amount text-danger">- {{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr class="bs-row-item">
                            <td class="ps-4 text-muted">- Tidak ada beban operasional tercatat pada periode ini -</td>
                            <td class="bs-amount text-muted">0</td>
                        </tr>
                    @endforelse
                    <tr class="bs-row-subtotal">
                        <td class="ps-3 fw-semibold text-danger">Jumlah Beban Operasional</td>
                        <td class="bs-amount fw-semibold text-danger">- {{ number_format($expenseSum ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    {{-- LABA OPERASIONAL --}}
                    <tr class="bs-row-subtotal" style="background-color: #edf2f7; border-top: 1.5px solid #cbd5e1; border-bottom: 1.5px solid #cbd5e1;">
                        <td class="fw-bold text-dark ps-3">
                            <i class="mdi mdi-equal-box me-1 text-secondary"></i> LABA / (RUGI) OPERASIONAL (OPERATING INCOME)
                        </td>
                        <td class="bs-amount fw-bold {{ $operatingProfit >= 0 ? 'text-dark' : 'text-danger' }}">
                            {{ $operatingProfit < 0 ? '- ' : '' }}{{ number_format(abs($operatingProfit), 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- D. PENDAPATAN & BEBAN LAIN-LAIN --}}
                    <tr class="bs-sec-header">
                        <td colspan="2">
                            <i class="mdi mdi-swap-horizontal me-1 text-secondary"></i> D. PENDAPATAN &amp; BEBAN LAIN-LAIN (OTHER INCOME &amp; CHARGES)
                        </td>
                    </tr>
                    <tr class="bs-group-header">
                        <td colspan="2">1. Pendapatan Lain-lain (Other Income)</td>
                    </tr>
                    @forelse ($allIncome as $item)
                        <tr class="bs-row-item">
                            <td class="ps-4">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-plus-circle-outline text-success small"></i> {{ $item->description }}
                                </span>
                            </td>
                            <td class="bs-amount text-success">{{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr class="bs-row-item">
                            <td class="ps-4 text-muted">- Tidak ada pendapatan lain-lain -</td>
                            <td class="bs-amount text-muted">0</td>
                        </tr>
                    @endforelse
                    <tr class="bs-row-subtotal">
                        <td class="ps-3 fw-semibold">Jumlah Pendapatan Lain-lain</td>
                        <td class="bs-amount fw-semibold text-success">{{ number_format($incomeSum ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    <tr class="bs-group-header">
                        <td colspan="2">2. Beban Lain-lain (Other Charges)</td>
                    </tr>
                    @forelse ($allCharge as $item)
                        <tr class="bs-row-item">
                            <td class="ps-4">
                                <span class="d-inline-flex align-items-center gap-1">
                                    <i class="mdi mdi-minus-circle-outline text-danger small"></i> {{ $item->description }}
                                </span>
                            </td>
                            <td class="bs-amount text-danger">- {{ number_format($item->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr class="bs-row-item">
                            <td class="ps-4 text-muted">- Tidak ada beban lain-lain -</td>
                            <td class="bs-amount text-muted">0</td>
                        </tr>
                    @endforelse
                    <tr class="bs-row-subtotal">
                        <td class="ps-3 fw-semibold text-danger">Jumlah Beban Lain-lain</td>
                        <td class="bs-amount fw-semibold text-danger">- {{ number_format($chargeSum ?? 0, 0, ',', '.') }}</td>
                    </tr>

                    <tr class="bs-row-subtotal" style="border-top: 1px solid #cbd5e1;">
                        <td class="ps-3 fw-semibold">Total Pendapatan (Beban) Lain-lain Bersih</td>
                        <td class="bs-amount fw-semibold {{ $incomeCharge >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $incomeCharge < 0 ? '- ' : '' }}{{ number_format(abs($incomeCharge), 0, ',', '.') }}
                        </td>
                    </tr>

                    {{-- GRAND TOTAL: LABA (RUGI) BERSIH --}}
                    <tr class="bs-row-grand-total">
                        <td class="fw-bolder fs-6">
                            <i class="mdi mdi-check-decagram me-1 text-primary"></i> JUMLAH TOTAL LABA / (RUGI) BERSIH
                            <span class="badge bg-label-success rounded-pill ms-2 fs-6">Net Margin: {{ $netMarginPct }}%</span>
                        </td>
                        <td class="bs-amount fw-bolder fs-5">
                            {{ $total < 0 ? '- ' : '' }}Rp {{ number_format(abs($total), 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
