@extends('layouts.sales.app')
@section('title', 'Cashflow Statement Detail - ' . $startString . ' s/d ' . $endString)

@section('content')
    @php
        $periodYear = $year ?? \Carbon\Carbon::parse($startDate)->year;
        $kasMasukOperasi = ($quotation ?? 0) + ($income ?? 0);
        $kasKeluarOperasi = ($expenseSum ?? 0) + ($outcome ?? 0);
        $netOperasiKas = $kasMasukOperasi - $kasKeluarOperasi;
        $kasBersihInvestasi = ($disposalProceeds ?? 0) - ($assetPurchase ?? 0);
        $kasBersihPendanaan = -($prive ?? 0);
        $totalNetPerubahanKas = $netOperasiKas + $kasBersihInvestasi + $kasBersihPendanaan;

        if (@$month) {
            $periodeLabel = 'Bulan ' . \Carbon\Carbon::create($periodYear, $month, 1)->translatedFormat('F Y');
            $printUrl = route('expense-cashflow.print-bulan', [$periodYear, str_pad($month, 2, '0', STR_PAD_LEFT)]);
        } else {
            $periodeLabel = 'Tahun ' . $periodYear;
            $printUrl = route('expense-cashflow.print-tahun', [$periodYear]);
        }
    @endphp

    {{-- Top Header Action Bar --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center py-3 mb-3 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 fs-6">
                    <li class="breadcrumb-item"><a href="{{ route('finance.statement.index') }}">Finance</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('finance.statement.index') }}?tab=cashflow#tab-cashflow">Statement</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Arus Kas (Cashflow Statement)</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-cash-sync text-primary"></i> Laporan Arus Kas (Cashflow Statement)
                <span class="badge bg-label-primary fs-6">{{ $startString }} &ndash; {{ $endString }}</span>
            </h4>
            <p class="text-muted mb-0 small">
                Analisis likuiditas dan pergerakan kas masuk dan kas keluar dari aktivitas operasi, investasi, dan pendanaan PT. Reftech Jaya Optima.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Quick Jump Period Dropdown --}}
            <div class="dropdown">
                <button class="btn btn-label-secondary btn-sm dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-calendar-range me-1"></i> Ganti Periode
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="max-height: 320px; overflow-y: auto;">
                    <li class="dropdown-header text-uppercase small fw-bold">Tahun {{ $periodYear }}</li>
                    @for($m = 1; $m <= 12; $m++)
                        @php
                            $monthName = \Carbon\Carbon::create($periodYear, $m, 1)->translatedFormat('F');
                            $isCurrent = (int)($month ?? 0) === $m;
                        @endphp
                        <li>
                            <a class="dropdown-item d-flex justify-content-between align-items-center {{ $isCurrent ? 'active fw-bold' : '' }}" 
                               href="{{ route('expense-cashflow.detail-bulan', [$periodYear, str_pad($m, 2, '0', STR_PAD_LEFT)]) }}">
                                <span>Bulan {{ $monthName }}</span>
                                @if($isCurrent) <i class="mdi mdi-check small"></i> @endif
                            </a>
                        </li>
                    @endfor
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item fw-semibold {{ !@$month ? 'active fw-bold' : '' }}" href="{{ route('expense-cashflow.detail-tahun', [$periodYear]) }}">
                            <i class="mdi mdi-calendar-blank-outline me-1"></i> Seluruh Tahun {{ $periodYear }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Back Button --}}
            <a href="{{ route('finance.statement.index') }}?tab=cashflow#tab-cashflow" class="btn btn-label-secondary btn-sm shadow-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>

            {{-- Print Button --}}
            <a href="{{ $printUrl }}" target="_blank" class="btn btn-primary btn-sm shadow-sm">
                <i class="mdi mdi-printer-outline me-1"></i> Cetak / Print A4
            </a>
        </div>
    </div>

    {{-- 4 Executive Cashflow KPI Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Kas Masuk Operasi --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border-left: 4px solid #71dd37 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Kas Masuk Operasi</span>
                        <div class="avatar avatar-sm bg-label-success rounded">
                            <i class="mdi mdi-cash-plus fs-5 text-success"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-success mb-1">Rp {{ number_format($kasMasukOperasi, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        Penjualan Selesai + Pendapatan Lain
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Kas Keluar Operasi --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #fff5f5 100%); border-left: 4px solid #ff3e1d !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Kas Keluar Operasi</span>
                        <div class="avatar avatar-sm bg-label-danger rounded">
                            <i class="mdi mdi-cash-minus fs-5 text-danger"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-danger mb-1">(Rp {{ number_format($kasKeluarOperasi, 0, ',', '.') }})</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        Pengeluaran Usaha &amp; Beban Operasional
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Net Kas Operasi (OCF) --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%); border-left: 4px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Net Kas Operasi (OCF)</span>
                        <div class="avatar avatar-sm bg-label-primary rounded">
                            <i class="mdi mdi-swap-horizontal-bold fs-5 text-primary"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder {{ $netOperasiKas >= 0 ? 'text-primary' : 'text-danger' }} mb-1">
                        {{ $netOperasiKas < 0 ? '(Rp ' . number_format(abs($netOperasiKas), 0, ',', '.') . ')' : 'Rp ' . number_format($netOperasiKas, 0, ',', '.') }}
                    </h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        <span class="badge {{ $netOperasiKas >= 0 ? 'bg-label-success' : 'bg-label-danger' }} rounded-pill" style="font-size: 10px;">
                            {{ $netOperasiKas >= 0 ? 'Surplus Operasional' : 'Defisit Operasional' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Net Perubahan Kas Bersih --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%); border-left: 4px solid #03c3ec !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Net Perubahan Kas</span>
                        <div class="avatar avatar-sm bg-label-info rounded">
                            <i class="mdi mdi-shield-check-outline fs-5 text-info"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder {{ $totalNetPerubahanKas >= 0 ? 'text-dark' : 'text-danger' }} mb-1">
                        {{ $totalNetPerubahanKas < 0 ? '(Rp ' . number_format(abs($totalNetPerubahanKas), 0, ',', '.') . ')' : 'Rp ' . number_format($totalNetPerubahanKas, 0, ',', '.') }}
                    </h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        Operasi + Investasi + Pendanaan
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Visual Cashflow Bridge Walkthrough Card --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="mdi mdi-vector-polyline text-primary fs-5"></i>
                Jembatan 3 Pilar Arus Kas (Cash Flow Bridge Walkthrough)
            </h6>
            <span class="badge bg-label-primary fw-bold">Posisi {{ $periodeLabel }}</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-center justify-content-center text-center">
                {{-- Step 1: Arus Kas Operasi --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="p-3 rounded-3 border bg-light h-100">
                        <div class="text-muted small fw-bold text-uppercase mb-1">1. Kas Operasi (OCF)</div>
                        <h6 class="fw-bold {{ $netOperasiKas >= 0 ? 'text-primary' : 'text-danger' }} mb-1">
                            {{ $netOperasiKas < 0 ? '(Rp ' . number_format(abs($netOperasiKas), 0, ',', '.') . ')' : 'Rp ' . number_format($netOperasiKas, 0, ',', '.') }}
                        </h6>
                        <small class="text-muted d-block" style="font-size: 11px;">Penerimaan &ndash; Pengeluaran Operasi</small>
                    </div>
                </div>

                {{-- Step 2: Arus Kas Investasi --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="p-3 rounded-3 border bg-light h-100">
                        <div class="text-muted small fw-bold text-uppercase mb-1">2. Kas Investasi</div>
                        <h6 class="fw-bold {{ $kasBersihInvestasi >= 0 ? 'text-dark' : 'text-danger' }} mb-1">
                            {{ $kasBersihInvestasi < 0 ? '(Rp ' . number_format(abs($kasBersihInvestasi), 0, ',', '.') . ')' : 'Rp ' . number_format($kasBersihInvestasi, 0, ',', '.') }}
                        </h6>
                        <small class="text-muted d-block" style="font-size: 11px;">Disposal vs Pembelian Aset Tetap</small>
                    </div>
                </div>

                {{-- Step 3: Arus Kas Pendanaan --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="p-3 rounded-3 border bg-light h-100">
                        <div class="text-muted small fw-bold text-uppercase mb-1">3. Kas Pendanaan</div>
                        <h6 class="fw-bold {{ $kasBersihPendanaan >= 0 ? 'text-dark' : 'text-danger' }} mb-1">
                            {{ $kasBersihPendanaan < 0 ? '(Rp ' . number_format(abs($kasBersihPendanaan), 0, ',', '.') . ')' : 'Rp ' . number_format($kasBersihPendanaan, 0, ',', '.') }}
                        </h6>
                        <small class="text-muted d-block" style="font-size: 11px;">Setoran Modal / Penarikan Prive</small>
                    </div>
                </div>

                {{-- Step 4: Net Perubahan Kas --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="p-3 rounded-3 border bg-primary text-white shadow-xs h-100">
                        <div class="text-white-50 small fw-bold text-uppercase mb-1">4. (=) Net Perubahan Kas</div>
                        <h6 class="fw-bold text-white mb-1">
                            {{ $totalNetPerubahanKas < 0 ? '(Rp ' . number_format(abs($totalNetPerubahanKas), 0, ',', '.') . ')' : 'Rp ' . number_format($totalNetPerubahanKas, 0, ',', '.') }}
                        </h6>
                        <small class="text-white-50 d-block" style="font-size: 11px;">Kenaikan / (Penurunan) Kas Bersih</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formal Document Report Card --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4 report-preview">
            @include('pages.finance.cashflow._report', ['isPrintMode' => false])
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body, table, th, td, h3, h4, h5, h6, span, div, p {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }
        .report-preview .table td, 
        .report-preview .table th {
            padding-top: 0.45rem;
            padding-bottom: 0.45rem;
        }
    </style>
@endpush
