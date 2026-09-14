@extends('layouts.sales.app')
@section('title', 'Equity Statement (Perubahan Modal) - Finance ERP')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .finance-kpi-card {
            transition: all 0.25s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.07);
        }
        .finance-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px 0 rgba(67, 89, 113, 0.12);
        }
        .nav-tabs-finance .nav-link {
            font-weight: 600;
            color: #64748b;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 0.85rem 1.4rem;
            background: transparent;
            transition: all 0.2s ease;
            border-radius: 0;
            font-size: 0.92rem;
        }
        .nav-tabs-finance .nav-link:hover {
            color: #4f46e5;
            background: rgba(79, 70, 229, 0.04);
        }
        .nav-tabs-finance .nav-link.active {
            color: #4f46e5;
            border-bottom-color: #4f46e5;
            background: transparent;
            font-weight: 700;
        }
        .equity-bridge-card {
            background-color: #f8fafc;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
        }
        .dark-style .equity-bridge-card {
            background-color: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.08);
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Statement /</span> Equity Statement
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-chart-donut me-1 text-primary"></i> Laporan Perubahan Ekuitas (Perkembangan modal pemilik, penambahan laba bersih, &amp; penarikan prive)
            </p>
        </div>
    </div>

    {{-- Unified Statement Navigation Tabs --}}
    <ul class="nav nav-tabs nav-tabs-finance mb-4 border-bottom" role="tablist">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-income.index') }}">
                <i class="mdi mdi-book-open-outline me-1"></i> Income Statement (Laba Rugi)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-balance.index') }}">
                <i class="mdi mdi-scale-balance me-1"></i> Balance Statement (Neraca)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('expense-equity.index') }}">
                <i class="mdi mdi-chart-donut me-1"></i> Equity Statement (Perubahan Modal)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-cashflow.index') }}">
                <i class="mdi mdi-cash-sync me-1"></i> Cashflow Statement (Arus Kas)
            </a>
        </li>
    </ul>

    {{-- 4 Executive Finance KPI Cards (Perubahan Modal YTD) --}}
    <div class="row g-3 mb-4">
        {{-- KPI 1: Modal Awal --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Modal Awal Periode</span>
                        <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-calendar-start fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-primary mb-1">
                        Rp {{ number_format($modalAwalYear, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Saldo modal awal {{ $currentYear }}</span>
                        <span class="badge bg-label-primary fw-bold rounded-pill">Beginning</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 2: Tambahan Laba Bersih --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Penambahan Laba Bersih</span>
                        <div class="avatar avatar-sm bg-label-success rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-trending-up fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-success mb-1">
                        + Rp {{ number_format($labaBersihYear, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Net Profit YTD {{ $currentYear }}</span>
                        <span class="badge bg-label-success fw-bold rounded-pill">Net Income</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 3: Pengambilan Prive --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Pengambilan Prive (Drawings)</span>
                        <div class="avatar avatar-sm bg-label-danger rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cash-refund fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-danger mb-1">
                        - Rp {{ number_format($prive, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Penarikan modal pemilik</span>
                        <span class="badge bg-label-danger fw-bold rounded-pill">Drawings</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 4: Modal Akhir --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Modal Akhir (Ending Equity)</span>
                        <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-chart-donut fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-info mb-1">
                        Rp {{ number_format($modalAkhirYear, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Posisi ekuitas berjalan</span>
                        <span class="badge bg-label-info fw-bold rounded-pill">Ending</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Period Selector & Report Actions Toolbar (Single Row with Selections) --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body py-3 px-4">
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center gap-3">
                {{-- Left: Icon & Title in 1 clean line --}}
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-calendar-search text-primary fs-4"></i>
                    <div>
                        <h6 class="card-title mb-0 fw-bold text-dark">
                            Generator Laporan Equity Statement (Perubahan Modal)
                        </h6>
                        <small class="text-muted d-none d-sm-inline">Buka lembar rincian perhitungan atau cetak dokumen resmi perubahan ekuitas</small>
                    </div>
                </div>

                {{-- Right: All filters & actions in 1 neat row --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    {{-- Dropdown Bulan --}}
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-light text-muted">
                            <i class="mdi mdi-calendar-month-outline"></i>
                        </span>
                        <select id="equity-month-select" class="form-select form-select-sm" style="min-width: 140px;">
                            <option value="all">Semua Bulan (1 Thn)</option>
                            @php
                                $bulanOptions = [
                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
                                    '04' => 'April', '05' => 'Mei', '06' => 'Juni',
                                    '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
                                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                ];
                            @endphp
                            @foreach($bulanOptions as $mNum => $mName)
                                <option value="{{ $mNum }}" {{ (int)$mNum == (int)date('m') ? 'selected' : '' }}>
                                    {{ $mName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dropdown Tahun --}}
                    <div class="input-group input-group-sm" style="width: auto;">
                        <span class="input-group-text bg-light text-muted">
                            <i class="mdi mdi-calendar-range"></i>
                        </span>
                        <select id="equity-year-select" class="form-select form-select-sm" style="min-width: 105px;">
                            @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ $yr == $currentYear ? 'selected' : '' }}>
                                    Tahun {{ $yr }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Buka Ekuitas --}}
                    <button type="button" id="btn-view-equity" class="btn btn-primary btn-sm px-3 shadow-xs">
                        <i class="mdi mdi-eye-outline me-1"></i> Buka Ekuitas
                    </button>

                    {{-- Tombol Cetak PDF --}}
                    <button type="button" id="btn-print-equity" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                        <i class="mdi mdi-printer me-1"></i> Cetak PDF
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Visual Bridge Card (Alur Perubahan Ekuitas) --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="mdi mdi-vector-polyline text-primary fs-5"></i>
                Jembatan Alur Perubahan Modal (Equity Bridge Walkthrough)
            </h6>
            <span class="badge bg-label-primary">Standar PSAK Keuangan</span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 align-items-center justify-content-center text-center">
                {{-- Step 1: Modal Awal --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="equity-bridge-card p-3 h-100">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">1. Modal Awal</span>
                        <h5 class="fw-bold text-primary mb-1">Rp {{ number_format($modalAwalYear, 0, ',', '.') }}</h5>
                        <small class="text-muted">Saldo laba ditahan per 1 Jan {{ $currentYear }}</small>
                    </div>
                </div>

                {{-- Operator Plus --}}
                <div class="col-auto d-none d-md-block">
                    <div class="avatar avatar-sm bg-label-success rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-plus fs-4 text-success"></i>
                    </div>
                </div>

                {{-- Step 2: Laba Bersih --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="equity-bridge-card p-3 h-100 border-success-subtle">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">2. Laba Bersih</span>
                        <h5 class="fw-bold text-success mb-1">+ Rp {{ number_format($labaBersihYear, 0, ',', '.') }}</h5>
                        <small class="text-muted">Net Income berjalan YTD {{ $currentYear }}</small>
                    </div>
                </div>

                {{-- Operator Minus --}}
                <div class="col-auto d-none d-md-block">
                    <div class="avatar avatar-sm bg-label-danger rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-minus fs-4 text-danger"></i>
                    </div>
                </div>

                {{-- Step 3: Prive --}}
                <div class="col-md-2 col-sm-6 col-12">
                    <div class="equity-bridge-card p-3 h-100 border-danger-subtle">
                        <span class="text-muted small fw-semibold text-uppercase d-block mb-1">3. Prive</span>
                        <h5 class="fw-bold text-danger mb-1">- Rp {{ number_format($prive, 0, ',', '.') }}</h5>
                        <small class="text-muted">Penarikan modal</small>
                    </div>
                </div>

                {{-- Operator Equal --}}
                <div class="col-auto d-none d-md-block">
                    <div class="avatar avatar-sm bg-label-info rounded-circle d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-equal fs-4 text-info"></i>
                    </div>
                </div>

                {{-- Step 4: Modal Akhir --}}
                <div class="col-md-3 col-sm-6 col-12">
                    <div class="equity-bridge-card p-3 h-100 border-primary bg-primary-subtle">
                        <span class="text-primary small fw-bold text-uppercase d-block mb-1">4. Modal Akhir</span>
                        <h5 class="fw-bold text-primary mb-1">Rp {{ number_format($modalAkhirYear, 0, ',', '.') }}</h5>
                        <small class="text-primary">Posisi ekuitas bersih perusahaan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script>
        $(function() {
            $('#btn-view-equity').on('click', function() {
                var m = $('#equity-month-select').val();
                var y = $('#equity-year-select').val();
                if (m && m !== 'all') {
                    window.location.href = '/equity-detail/' + y + '/' + m;
                } else {
                    window.location.href = '/equity-detail/' + y;
                }
            });

            $('#btn-print-equity').on('click', function() {
                var m = $('#equity-month-select').val();
                var y = $('#equity-year-select').val();
                if (m && m !== 'all') {
                    window.open('/equity-print/' + m + '/' + y, '_blank');
                } else {
                    window.open('/equity-print/' + y, '_blank');
                }
            });
        });
    </script>
@endpush
