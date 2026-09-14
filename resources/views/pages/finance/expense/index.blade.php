@extends('layouts.sales.app')
@section('title', 'Operational Expenses - Finance ERP')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
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
        .filter-toolbar {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
        }
        .dark-style .filter-toolbar {
            background-color: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.1);
        }
        .table th {
            font-size: 11.5px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700 !important;
            background-color: #f8fafc !important;
            color: #475569 !important;
        }
        .dark-style .table th {
            background-color: rgba(255, 255, 255, 0.04) !important;
            color: #cbd5e1 !important;
        }
        .posting-summary-card {
            background-color: #f8fafc;
            border-left: 4px solid #4f46e5;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance /</span> Operational Expenses
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-cash-multiple me-1 text-primary"></i> Manajemen beban operasional perusahaan (Bank, Kas Umum, Penyesuaian Stok, &amp; Ongkir Ekspedisi)
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('finance.expense-budget.index') }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-chart-timeline-variant me-1"></i> Monitoring Budget
            </a>
            <a href="{{ route('expense.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Catat Expense Bank
            </a>
            <a href="{{ route('expense-umum.create') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-cash-register me-1"></i> Catat Kas Umum
            </a>
            <a href="{{ route('expense-inventory.create') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-package-variant-closed me-1"></i> Buat Inventory Adj
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Instant Switching Tab Navigation (No Page Reload) --}}
    <ul class="nav nav-tabs nav-tabs-finance mb-4 border-bottom" id="expenseTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-bank-btn" data-bs-toggle="tab" data-bs-target="#tab-expense-bank" type="button" role="tab" aria-controls="tab-expense-bank" aria-selected="true">
                <i class="mdi mdi-bank-transfer me-1"></i> Expense Bank
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-umum-btn" data-bs-toggle="tab" data-bs-target="#tab-expense-umum" type="button" role="tab" aria-controls="tab-expense-umum" aria-selected="false">
                <i class="mdi mdi-cash-register me-1"></i> Expense Kas Umum
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-inventory-btn" data-bs-toggle="tab" data-bs-target="#tab-expense-inventory" type="button" role="tab" aria-controls="tab-expense-inventory" aria-selected="false">
                <i class="mdi mdi-package-variant-closed me-1"></i> Inventory Adjustment
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-ongkir-btn" data-bs-toggle="tab" data-bs-target="#tab-expense-ongkir" type="button" role="tab" aria-controls="tab-expense-ongkir" aria-selected="false">
                <i class="mdi mdi-truck-delivery-outline me-1"></i> Ongkir Logistik
                @if($pendingCount > 0)
                    <span class="badge bg-warning rounded-pill ms-1" style="font-size: 11px;">{{ $pendingCount }}</span>
                @endif
            </button>
        </li>
    </ul>

    {{-- Tab Content (Dynamic SPA Panes) --}}
    <div class="tab-content p-0 border-0 shadow-none bg-transparent" id="expenseTabContent">

        {{-- =================================================================== --}}
        {{-- TAB 1: EXPENSE BANK --}}
        {{-- =================================================================== --}}
        <div class="tab-pane fade show active" id="tab-expense-bank" role="tabpanel" aria-labelledby="tab-bank-btn">
            {{-- 4 Executive Finance KPI Cards --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Pengeluaran Bulan Ini</span>
                                <div class="avatar avatar-sm bg-label-danger rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-cash-minus fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-danger mb-1">
                                Rp {{ number_format($totalThisMonth, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">{{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                                <span class="badge bg-label-danger fw-bold rounded-pill">{{ $countThisMonth }} Voucher</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Akumulasi YTD {{ $selectedYear }}</span>
                                <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-calendar-check-outline fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-primary mb-1">
                                Rp {{ number_format($totalThisYear, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Total Tahun {{ $selectedYear }}</span>
                                <span class="badge bg-label-primary fw-bold rounded-pill">{{ $countThisYear }} Transaksi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Rata-rata per Voucher</span>
                                <div class="avatar avatar-sm bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-chart-line fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">
                                Rp {{ number_format($avgVoucher, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Ukuran tiket bulan ini</span>
                                <span class="badge bg-label-warning text-dark fw-bold rounded-pill">Avg Size</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Rekening Sumber Dana</span>
                                <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-bank-outline fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-info mb-1">
                                {{ count($banks) }} Rekening
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Akun kas &amp; bank aktif</span>
                                <span class="badge bg-label-info fw-bold rounded-pill">Bank Kas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datatable Card Bank --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="mdi mdi-receipt-text-outline text-primary fs-5"></i>
                                Daftar Voucher Pengeluaran Bank
                            </h6>
                            <small class="text-muted">Data transaksi mutasi debet kas bank yang telah diotorisasi</small>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <select id="filter-expense-year" class="form-select form-select-sm" style="width: 125px;">
                                <option value="" selected>Semua Tahun</option>
                                @foreach($availableYears as $yr)
                                    <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                                @endforeach
                            </select>

                            <select id="filter-expense-month" class="form-select form-select-sm" style="width: 135px;">
                                <option value="" selected>Semua Bulan</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">
                                        {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>

                            <select id="filter-expense-bank" class="form-select form-select-sm" style="width: 160px;">
                                <option value="">Semua Bank</option>
                                @foreach($banks as $b)
                                    <option value="{{ $b->id }}" {{ $selectedBank == $b->id ? 'selected' : '' }}>
                                        {{ $b->bank }} ({{ $b->no_rek }})
                                    </option>
                                @endforeach
                            </select>

                            <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                <i class="mdi mdi-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-expense-data table table-hover border-bottom mb-0">
                        <thead>
                            <tr>
                                <th style="min-width: 170px;">No. Voucher</th>
                                <th style="min-width: 110px;">Tanggal</th>
                                <th style="min-width: 190px;">Sumber Rekening Bank</th>
                                <th style="min-width: 230px;">Keperluan / Memo</th>
                                <th style="min-width: 140px;">Ref. Cek / Inv</th>
                                <th class="text-end" style="min-width: 140px;">Nominal (IDR)</th>
                                <th class="text-center" style="width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- =================================================================== --}}
        {{-- TAB 2: EXPENSE KAS UMUM --}}
        {{-- =================================================================== --}}
        <div class="tab-pane fade" id="tab-expense-umum" role="tabpanel" aria-labelledby="tab-umum-btn">
            {{-- 4 Executive Finance KPI Cards Kas Umum --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Akumulasi Kas Keluar</span>
                                <div class="avatar avatar-sm bg-label-danger rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-cash-minus fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-danger mb-1">
                                Rp {{ number_format($totalKasUmum, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Total Beban Kas Umum</span>
                                <span class="badge bg-label-danger fw-bold rounded-pill">Total Biaya</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Total Transaksi Kas</span>
                                <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-receipt-text-outline fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-primary mb-1">
                                {{ number_format($countKasUmum, 0, ',', '.') }} Transaksi
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Voucher Kas Keluar</span>
                                <span class="badge bg-label-primary fw-bold rounded-pill">Voucher</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Pengeluaran Bulan Ini</span>
                                <div class="avatar avatar-sm bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-calendar-month-outline fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-warning mb-1">
                                Rp {{ number_format($currentMonthKas, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">{{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                                <span class="badge bg-label-warning text-dark fw-bold rounded-pill">{{ $currentMonthCount }} Transaksi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Rata-rata per Transaksi</span>
                                <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-chart-areaspline fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-info mb-1">
                                Rp {{ number_format($avgKasUmum, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Ukuran per transaksi</span>
                                <span class="badge bg-label-info fw-bold rounded-pill">Avg Size</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datatable Card Kas Umum --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="mdi mdi-cash-register text-primary fs-5"></i>
                                Daftar Buku Kas &amp; Pengeluaran Umum
                            </h6>
                            <small class="text-muted">Pencatatan pengeluaran operasional non-bank dan jurnal kas harian</small>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <select id="filter-kas-year" class="form-select form-select-sm" style="width: 125px;">
                                <option value="all" selected>Semua Tahun</option>
                                @foreach($availableYearsKas as $yr)
                                    <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                                @endforeach
                            </select>

                            <select id="filter-kas-month" class="form-select form-select-sm" style="width: 135px;">
                                <option value="all" selected>Semua Bulan</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">
                                        {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>

                            <button type="button" id="btn-reset-kas-filter" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                <i class="mdi mdi-refresh"></i>
                            </button>

                            <a href="{{ route('expense-umum.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Catat Kas Umum
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-expense-umum table table-hover border-bottom mb-0">
                        <thead>
                            <tr>
                                <th style="min-width: 150px;">No. Invoice Kas</th>
                                <th style="min-width: 160px;">No. Voucher</th>
                                <th style="min-width: 110px;">Tanggal</th>
                                <th style="min-width: 250px;">Keperluan / Memo Pengeluaran</th>
                                <th style="min-width: 180px;">Akun Beban (COA)</th>
                                <th class="text-end" style="min-width: 140px;">Nominal (IDR)</th>
                                <th class="text-center" style="width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- =================================================================== --}}
        {{-- TAB 3: INVENTORY ADJUSTMENT --}}
        {{-- =================================================================== --}}
        <div class="tab-pane fade" id="tab-expense-inventory" role="tabpanel" aria-labelledby="tab-inventory-btn">
            {{-- 4 Executive Finance KPI Cards Inventory Adj --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Total Nilai Penyesuaian</span>
                                <div class="avatar avatar-sm bg-label-danger rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-cash-remove fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-danger mb-1">
                                Rp {{ number_format($totalAdjValue, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Total Beban HPP Kerusakan/Hilang</span>
                                <span class="badge bg-label-danger fw-bold rounded-pill">Total HPP Loss</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Frekuensi Penyesuaian</span>
                                <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-clipboard-text-clock fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-primary mb-1">
                                {{ number_format($totalAdjCount, 0, ',', '.') }} Kali
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Transaksi Opname/Penyesuaian</span>
                                <span class="badge bg-label-primary fw-bold rounded-pill">Adjustment</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Total Unit Terdampak</span>
                                <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-package-variant fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-info mb-1">
                                {{ number_format($totalItemsAdjusted, 0, ',', '.') }} Pcs
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Kuantitas fisik unit/part</span>
                                <span class="badge bg-label-info fw-bold rounded-pill">Barang Fisik</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Penyesuaian Bulan Ini</span>
                                <div class="avatar avatar-sm bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-calendar-alert fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-warning mb-1">
                                Rp {{ number_format($currentMonthValue, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">{{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                                <span class="badge bg-label-warning text-dark fw-bold rounded-pill">Bulan Ini</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datatable Card Inventory Adj --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="mdi mdi-clipboard-text-outline text-primary fs-5"></i>
                                Riwayat Penyesuaian Stok (Inventory Adjustment)
                            </h6>
                            <small class="text-muted">Rekonsiliasi selisih stok gudang, barang rusak, dan write-off HPP</small>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <select id="filter-inventory-year" class="form-select form-select-sm" style="width: 125px;">
                                <option value="all" selected>Semua Tahun</option>
                                @foreach($availableYearsInventory as $yr)
                                    <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                                @endforeach
                            </select>

                            <select id="filter-inventory-month" class="form-select form-select-sm" style="width: 135px;">
                                <option value="all" selected>Semua Bulan</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}">
                                        {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>

                            <button type="button" id="btn-reset-inventory-filter" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                <i class="mdi mdi-refresh"></i>
                            </button>

                            <a href="{{ route('expense-inventory.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                                <i class="mdi mdi-plus-circle-outline me-1"></i> Buat Penyesuaian Baru
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-expense-inventory table table-hover border-bottom mb-0">
                        <thead>
                            <tr>
                                <th style="min-width: 250px;">Item / Produk Pengganti</th>
                                <th style="min-width: 150px;">No. Invoice / Ref</th>
                                <th style="min-width: 110px;">Tanggal</th>
                                <th style="min-width: 100px;">Gudang</th>
                                <th class="text-center" style="min-width: 80px;">Qty Fisik</th>
                                <th class="text-end" style="min-width: 140px;">Nilai Penyesuaian HPP</th>
                                <th class="text-center" style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- =================================================================== --}}
        {{-- TAB 4: ONGKIR LOGISTIK --}}
        {{-- =================================================================== --}}
        <div class="tab-pane fade" id="tab-expense-ongkir" role="tabpanel" aria-labelledby="tab-ongkir-btn">
            {{-- 4 Executive Finance KPI Cards Ongkir --}}
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Total Resi Logistik</span>
                                <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-truck-delivery fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-primary mb-1">
                                {{ number_format($totalResi, 0, ',', '.') }} Resi
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Pengiriman Charged</span>
                                <span class="badge bg-label-primary fw-bold rounded-pill">Total Logistik</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Menunggu Posting</span>
                                <div class="avatar avatar-sm bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-clock-alert-outline fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-warning mb-1">
                                Rp {{ number_format($pendingCost, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Belum Masuk Finance</span>
                                <span class="badge bg-label-warning text-dark fw-bold rounded-pill">{{ $pendingCount }} Pending Resi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Sudah Diposting Finance</span>
                                <div class="avatar avatar-sm bg-label-success rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-check-decagram-outline fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-success mb-1">
                                Rp {{ number_format($postedCost, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Tercatat di Beban Buku</span>
                                <span class="badge bg-label-success fw-bold rounded-pill">{{ $postedCount }} Posted Resi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Rasio Terposting</span>
                                <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-percent-outline fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-info mb-1">
                                {{ $totalResi > 0 ? round(($postedCount / $totalResi) * 100) : 0 }}%
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Efektivitas verifikasi logistik</span>
                                <span class="badge bg-label-info fw-bold rounded-pill">Progress</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datatable Card Ongkir --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                        <div>
                            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="mdi mdi-truck-fast-outline text-primary fs-5"></i>
                                Rekap Biaya Ongkos Kirim Logistik
                            </h6>
                            <small class="text-muted">Daftar resi pengiriman barang pending PO yang dibebankan kepada perusahaan</small>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <select id="filter-ongkir-status" class="form-select form-select-sm" style="width: 170px;">
                                <option value="" selected>Semua Status</option>
                                <option value="pending">Menunggu Posting</option>
                                <option value="posted">Sudah Diposting</option>
                            </select>

                            <select id="filter-ongkir-kurir" class="form-select form-select-sm" style="width: 160px;">
                                <option value="" selected>Semua Ekspedisi</option>
                                @foreach($couriers as $kr)
                                    <option value="{{ $kr }}">{{ $kr }}</option>
                                @endforeach
                            </select>

                            <button type="button" id="btn-reset-ongkir-filter" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                <i class="mdi mdi-refresh"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-expense-ongkir table table-hover border-bottom mb-0">
                        <thead>
                            <tr>
                                <th style="min-width: 170px;">No. Resi / AWB</th>
                                <th style="min-width: 130px;">Ekspedisi</th>
                                <th style="min-width: 160px;">Ref. PO / Pending</th>
                                <th style="min-width: 120px;">Tanggal Kirim</th>
                                <th class="text-end" style="min-width: 140px;">Biaya Ongkir (IDR)</th>
                                <th class="text-center" style="min-width: 140px;">Status Finance</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Modal Post Ongkir ke Finance --}}
<form id="formPostOngkir" method="POST" action="">
    @csrf
    <div class="modal fade" id="postOngkirModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary py-3 text-white">
                    <h5 class="modal-title text-white d-flex align-items-center">
                        <i class="mdi mdi-bank-transfer me-2"></i> Posting Ongkir ke Finance
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 mb-3 posting-summary-card">
                        <div class="d-flex align-items-center mb-1">
                            <i class="mdi mdi-information-outline text-primary me-2 fs-5"></i>
                            <span class="fw-bold text-dark small text-uppercase">Rincian Dokumen Logistik</span>
                        </div>
                        <p class="mb-0 text-dark small fw-medium" id="ongkir-info-text"></p>
                    </div>

                    <div class="form-floating form-floating-outline mb-3">
                        <select class="form-select" id="id_bank" name="id_bank" required>
                            <option value="">---- Pilih Sumber Rekening Kas/Bank ----</option>
                            @foreach ($banks as $b)
                                <option value="{{ $b->id }}">{{ $b->bank }} - {{ $b->no_rek }} (Saldo: Rp {{ number_format($b->saldo, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                        <label for="id_bank">Sumber Kas / Rekening Bank</label>
                    </div>

                    <div class="form-floating form-floating-outline mb-3">
                        <select class="form-select" id="id_account" name="id_account" required>
                            <option value="">---- Pilih Akun Beban (COA) ----</option>
                            @foreach ($account as $a)
                                <option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }} ({{ $a->category }})</option>
                            @endforeach
                        </select>
                        <label for="id_account">Akun Alokasi Biaya (COA)</label>
                    </div>

                    <div class="form-floating form-floating-outline mb-2">
                        <input type="text" class="form-control" id="memo" name="memo" placeholder="Memo Transaksi">
                        <label for="memo">Keterangan / Memo Pembayaran</label>
                    </div>
                    <small class="text-muted d-block">Saldo rekening bank akan otomatis terpotong dan voucher pengeluaran akan dibuat.</small>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Otorisasi &amp; Posting
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    {{-- Load Datatables Handlers for all 4 Tabs --}}
    <script src="{{ asset('assets') }}/includes/table-expense-data.js?v={{ file_exists(public_path('assets/includes/table-expense-data.js')) ? filemtime(public_path('assets/includes/table-expense-data.js')) : time() }}"></script>
    <script src="{{ asset('assets') }}/includes/table-expense-umum-data.js?v={{ file_exists(public_path('assets/includes/table-expense-umum-data.js')) ? filemtime(public_path('assets/includes/table-expense-umum-data.js')) : time() }}"></script>
    <script src="{{ asset('assets') }}/includes/table-expense-inventory.js?v={{ file_exists(public_path('assets/includes/table-expense-inventory.js')) ? filemtime(public_path('assets/includes/table-expense-inventory.js')) : time() }}"></script>
    <script src="{{ asset('assets') }}/includes/table-expense-ongkir.js?v={{ file_exists(public_path('assets/includes/table-expense-ongkir.js')) ? filemtime(public_path('assets/includes/table-expense-ongkir.js')) : time() }}"></script>

    {{-- Seamless SPA Tab Switching & Action Delegations --}}
    <script>
        $(function() {
            // Adjust DataTables column width whenever a tab is switched
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                var targetPaneId = $(e.target).data('bs-target');
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();

                // Update URL hash smoothly without reloading the page
                if (history.replaceState) {
                    history.replaceState(null, null, targetPaneId);
                } else {
                    location.hash = targetPaneId;
                }
            });

            // Activate tab from URL hash on page load (e.g., #tab-expense-umum)
            var currentHash = window.location.hash;
            if (currentHash) {
                var activeBtn = $('button[data-bs-target="' + currentHash + '"]');
                if (activeBtn.length) {
                    activeBtn.tab('show');
                }
            }

            // Post Ongkir Modal Opener
            $(document).on('click', '.btn-post-ongkir', function() {
                var id = $(this).data('id');
                var kurir = $(this).data('kurir');
                var po = $(this).data('po');
                var cost = $(this).data('cost');
                var resi = $(this).data('resi');

                $('#formPostOngkir').attr('action', '{{ url('expense-ongkir/post') }}/' + id);
                $('#ongkir-info-text').html(
                    'Resi: <span class="fw-bold text-primary font-monospace">' + resi + '</span> | ' +
                    'Kurir: <span class="fw-bold">' + kurir + '</span> | ' +
                    'Pending PO: <span class="fw-bold">#' + po + '</span> | ' +
                    'Biaya: <span class="fw-bold text-danger">Rp ' + Number(cost).toLocaleString('id-ID') + '</span>'
                );
                $('#memo').val('Pembayaran Ongkir ' + kurir + ' (Resi: ' + resi + ' - Pending PO #' + po + ')');
                $('#postOngkirModal').modal('show');
            });

            // Delete Kas Umum Transaction
            $(document).on('click', '.delete-expense-umum', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: "Hapus Transaksi Kas Umum?",
                    text: "Voucher pengeluaran kas ini akan dihapus permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal",
                    customClass: {
                        confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ url('expense') }}/' + id,
                            type: 'POST',
                            data: {
                                '_method': 'DELETE',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil Dihapus!",
                                    text: "Transaksi kas umum telah dihapus.",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $('.datatable-expense-umum').DataTable().ajax.reload(null, false);
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan sistem saat menghapus data kas!'
                                });
                            }
                        });
                    }
                });
            });

            // Cancel / Delete Inventory Adjustment
            $(document).on('click', '.delete-expense-inventory', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                Swal.fire({
                    title: "Batalkan Penyesuaian Stok?",
                    text: "Stok barang fisik gudang akan dikembalikan ke kondisi sebelum penyesuaian!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, Batalkan & Kembalikan Stok!",
                    cancelButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                        cancelButton: "btn btn-label-secondary waves-effect",
                    },
                    buttonsStyling: false,
                }).then(function(result) {
                    if (result.value) {
                        $.ajax({
                            url: '{{ url('expense-inventory') }}/' + id,
                            type: 'POST',
                            data: {
                                '_method': 'DELETE',
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response == 1) {
                                    Swal.fire({
                                        icon: "success",
                                        title: "Berhasil Dibatalkan!",
                                        text: "Data penyesuaian dihapus dan stok gudang telah dikembalikan.",
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    $('.datatable-expense-inventory').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: 'Gagal membatalkan penyesuaian stok!'
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Terjadi kesalahan sistem saat membatalkan data!'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
