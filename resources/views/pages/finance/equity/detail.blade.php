@extends('layouts.sales.app')
@section('title', 'Equity Statement Detail - ' . $startString . ' s/d ' . $endString)

@section('content')
    @php
        $periodYear = $year ?? \Carbon\Carbon::parse($startDate)->year;
        $currentProfit = @$month ? ($labaBulanIni ?? 0) : ($labaTahunIni ?? 0);
        $priveVal = $prive ?? 0;
        
        if (@$month) {
            $ekuitas = 250000000 + ($labaTahunTahun ?? 0) - $priveVal - ($labaBulanIni ?? 0);
            $totalekuitas = $ekuitas + ($labaBulanIni ?? 0);
            $sebelumnya = ($labaTahunTahun ?? 0) - ($labaBulanIni ?? 0);
            $periodeLabel = 'Bulan ' . \Carbon\Carbon::create($periodYear, $month, 1)->translatedFormat('F Y');
        } else {
            $ekuitas = 250000000 + ($labaTahunTahun ?? 0) - $priveVal - ($labaTahunIni ?? 0);
            $totalekuitas = $ekuitas + ($labaTahunIni ?? 0);
            $sebelumnya = ($labaTahunTahun ?? 0) - ($labaTahunIni ?? 0);
            $periodeLabel = 'Tahun ' . $periodYear;
        }
        $netMovement = $currentProfit - $priveVal;
        $growthPct = $ekuitas > 0 ? (($totalekuitas - $ekuitas) / $ekuitas) * 100 : 0;

        $printUrl = @$month
            ? route('expense-equity.print-bulan', [$periodYear, str_pad($month, 2, '0', STR_PAD_LEFT)])
            : route('expense-equity.print-tahun', [$periodYear]);
    @endphp

    {{-- Top Header Action Bar --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center py-3 mb-3 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 fs-6">
                    <li class="breadcrumb-item"><a href="{{ route('finance.statement.index') }}">Finance</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('finance.statement.index') }}?tab=equity#tab-equity">Statement</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Perubahan Modal (Equity Statement)</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-chart-donut text-primary"></i> Laporan Perubahan Modal (Equity Statement)
                <span class="badge bg-label-primary fs-6">{{ $startString }} &ndash; {{ $endString }}</span>
            </h4>
            <p class="text-muted mb-0 small">
                Rincian pergerakan modal pemilik, akumulasi laba ditahan, hasil usaha periode berjalan, dan posisi modal akhir PT. Reftech Jaya Optima.
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
                               href="{{ route('expense-equity.detail-bulan', [$periodYear, str_pad($m, 2, '0', STR_PAD_LEFT)]) }}">
                                <span>Bulan {{ $monthName }}</span>
                                @if($isCurrent) <i class="mdi mdi-check small"></i> @endif
                            </a>
                        </li>
                    @endfor
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item fw-semibold {{ !@$month ? 'active fw-bold' : '' }}" href="{{ route('expense-equity.detail-tahun', [$periodYear]) }}">
                            <i class="mdi mdi-calendar-blank-outline me-1"></i> Seluruh Tahun {{ $periodYear }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Back Button --}}
            <a href="{{ route('finance.statement.index') }}?tab=equity#tab-equity" class="btn btn-label-secondary btn-sm shadow-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>

            {{-- Print Button --}}
            <a href="{{ $printUrl }}" target="_blank" class="btn btn-primary btn-sm shadow-sm">
                <i class="mdi mdi-printer-outline me-1"></i> Cetak / Print A4
            </a>
        </div>
    </div>

    {{-- 4 Executive KPI Metrics Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Modal Awal --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%); border-left: 4px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Modal Awal Periode</span>
                        <div class="avatar avatar-sm bg-label-primary rounded">
                            <i class="mdi mdi-calendar-start fs-5 text-primary"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-primary mb-1">Rp {{ number_format($ekuitas, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        Modal Disetor + Laba Ditahan Awal
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Penambahan Laba Bersih --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border-left: 4px solid #71dd37 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Laba Bersih Berjalan</span>
                        <div class="avatar avatar-sm bg-label-success rounded">
                            <i class="mdi mdi-trending-up fs-5 text-success"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-success mb-1">+ Rp {{ number_format($currentProfit, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        Kontribusi Laba Bersih {{ $periodeLabel }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Pengambilan Prive --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #fff5f5 100%); border-left: 4px solid #ff3e1d !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Pengambilan Prive</span>
                        <div class="avatar avatar-sm bg-label-danger rounded">
                            <i class="mdi mdi-cash-refund fs-5 text-danger"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-danger mb-1">- Rp {{ number_format($priveVal, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        Penarikan Modal Keperluan Pribadi
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Modal Akhir --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #ffffff 0%, #f0fdfa 100%); border-left: 4px solid #03c3ec !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase">Modal Akhir (Ending)</span>
                        <div class="avatar avatar-sm bg-label-info rounded">
                            <i class="mdi mdi-shield-check-outline fs-5 text-info"></i>
                        </div>
                    </div>
                    <h5 class="fw-bolder text-dark mb-1">Rp {{ number_format($totalekuitas, 0, ',', '.') }}</h5>
                    <div class="text-muted small" style="font-size: 11px;">
                        <span class="badge {{ $growthPct >= 0 ? 'bg-label-success' : 'bg-label-danger' }} rounded-pill" style="font-size: 10px;">
                            {{ $growthPct >= 0 ? '+' : '' }}{{ number_format($growthPct, 1) }}% Growth
                        </span>
                        Hak Bersih Pemilik atas Aset
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Visual Bridge Card (Alur Perubahan Modal) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="mdi mdi-vector-polyline text-primary fs-5"></i>
                Jembatan Alur Perubahan Modal (Equity Flow Walkthrough)
            </h6>
            <span class="badge bg-label-primary fw-bold">Posisi {{ $periodeLabel }}</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-center justify-content-center text-center">
                {{-- Step 1: Modal Awal --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="p-3 rounded-3 border bg-light h-100">
                        <div class="text-muted small fw-bold text-uppercase mb-1">1. Modal Awal</div>
                        <h6 class="fw-bold text-primary mb-1">Rp {{ number_format($ekuitas, 0, ',', '.') }}</h6>
                        <small class="text-muted d-block" style="font-size: 11px;">Modal Disetor + Laba Ditahan</small>
                    </div>
                </div>

                {{-- Step 2: Laba Bersih --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="p-3 rounded-3 border bg-light h-100">
                        <div class="text-muted small fw-bold text-uppercase mb-1">2. (+) Laba Bersih</div>
                        <h6 class="fw-bold text-success mb-1">+ Rp {{ number_format($currentProfit, 0, ',', '.') }}</h6>
                        <small class="text-muted d-block" style="font-size: 11px;">Hasil Operasional {{ $periodeLabel }}</small>
                    </div>
                </div>

                {{-- Step 3: Prive --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="p-3 rounded-3 border bg-light h-100">
                        <div class="text-muted small fw-bold text-uppercase mb-1">3. (-) Penarikan Prive</div>
                        <h6 class="fw-bold text-danger mb-1">- Rp {{ number_format($priveVal, 0, ',', '.') }}</h6>
                        <small class="text-muted d-block" style="font-size: 11px;">Pengambilan Pribadi Pemilik</small>
                    </div>
                </div>

                {{-- Step 4: Modal Akhir --}}
                <div class="col-lg-3 col-sm-6">
                    <div class="p-3 rounded-3 border bg-primary text-white shadow-xs h-100">
                        <div class="text-white-50 small fw-bold text-uppercase mb-1">4. (=) Modal Akhir</div>
                        <h6 class="fw-bold text-white mb-1">Rp {{ number_format($totalekuitas, 0, ',', '.') }}</h6>
                        <small class="text-white-50 d-block" style="font-size: 11px;">Ending Equity per {{ $endString }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formal Document Report Card --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4 report-preview">
            @include('pages.finance.equity._report', ['isPrintMode' => false])
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body, table, th, td, h4, h5, h6, span, div, p {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }
    </style>
@endpush
