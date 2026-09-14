@extends('layouts.sales.app')
@section('title', 'Financial Statements - Finance ERP')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
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
        .income-filter-card {
            background: #ffffff;
            border: 1px solid rgba(67, 89, 113, 0.1) !important;
            border-left: 4px solid #696cff !important;
            box-shadow: 0 2px 8px 0 rgba(67, 89, 113, 0.08) !important;
            transition: all 0.25s ease;
        }
        .income-filter-card:hover {
            box-shadow: 0 4px 14px 0 rgba(67, 89, 113, 0.12) !important;
        }
        .dark-style .income-filter-card {
            background: #2b2c40;
            border-color: rgba(255, 255, 255, 0.08) !important;
            border-left-color: #696cff !important;
        }
        .income-filter-pill {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.65rem;
            padding: 0.45rem 0.65rem;
            transition: all 0.2s ease-in-out;
        }
        .income-filter-pill:hover,
        .income-filter-pill:focus-within {
            background: #ffffff;
            border-color: #696cff;
            box-shadow: 0 2px 8px rgba(105, 108, 255, 0.12);
        }
        .dark-style .income-filter-pill {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .dark-style .income-filter-pill:hover,
        .dark-style .income-filter-pill:focus-within {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
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
        .balance-item-row {
            padding: 0.65rem 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s ease;
        }
        .balance-item-row:hover {
            background-color: #f8fafc;
        }
        .dark-style .balance-item-row:hover {
            background-color: rgba(255, 255, 255, 0.03);
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
        .cashflow-table th {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .cashflow-table td {
            vertical-align: middle;
            font-size: 0.88rem;
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance /</span> Statement
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-book-open-outline me-1 text-primary"></i> Laporan Keuangan Komprehensif (Laba Rugi, Neraca, Perubahan Modal, &amp; Arus Kas)
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createStatement">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Catat Pendapatan / Beban Lain
            </button>
            <a href="{{ route('fixed.index') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-domain me-1"></i> Aset Tetap
            </a>
            <a href="{{ route('report.project_profitability') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-chart-box-outline me-1"></i> Laba Rugi Proyek
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
    <ul class="nav nav-tabs nav-tabs-finance mb-4 border-bottom" id="statementTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ ($activeTab ?? 'income') == 'income' ? 'active' : '' }}" id="tab-income-btn" data-bs-toggle="tab" data-bs-target="#tab-income" type="button" role="tab" aria-controls="tab-income" aria-selected="{{ ($activeTab ?? 'income') == 'income' ? 'true' : 'false' }}">
                <i class="mdi mdi-book-open-outline me-1"></i> Income Statement (Laba Rugi)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ ($activeTab ?? '') == 'balance' ? 'active' : '' }}" id="tab-balance-btn" data-bs-toggle="tab" data-bs-target="#tab-balance" type="button" role="tab" aria-controls="tab-balance" aria-selected="{{ ($activeTab ?? '') == 'balance' ? 'true' : 'false' }}">
                <i class="mdi mdi-scale-balance me-1"></i> Balance Statement (Neraca)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ ($activeTab ?? '') == 'equity' ? 'active' : '' }}" id="tab-equity-btn" data-bs-toggle="tab" data-bs-target="#tab-equity" type="button" role="tab" aria-controls="tab-equity" aria-selected="{{ ($activeTab ?? '') == 'equity' ? 'true' : 'false' }}">
                <i class="mdi mdi-chart-donut me-1"></i> Equity Statement (Perubahan Modal)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ ($activeTab ?? '') == 'cashflow' ? 'active' : '' }}" id="tab-cashflow-btn" data-bs-toggle="tab" data-bs-target="#tab-cashflow" type="button" role="tab" aria-controls="tab-cashflow" aria-selected="{{ ($activeTab ?? '') == 'cashflow' ? 'true' : 'false' }}">
                <i class="mdi mdi-cash-sync me-1"></i> Cashflow Statement (Arus Kas)
            </button>
        </li>
    </ul>

    {{-- Tab Content Panes (Instant SPA style) --}}
    <div class="tab-content p-0 border-0 shadow-none bg-transparent" id="statementTabContent">

        {{-- =================================================================== --}}
        {{-- TAB 1: INCOME STATEMENT (LABA RUGI) --}}
        {{-- =================================================================== --}}
        <div class="tab-pane fade {{ ($activeTab ?? 'income') == 'income' ? 'show active' : '' }}" id="tab-income" role="tabpanel" aria-labelledby="tab-income-btn">
            
            {{-- 4 Executive Finance KPI Cards (Periode Terpilih / YTD) --}}
            <div class="row g-3 mb-4">
                {{-- KPI 1: Pendapatan Usaha --}}
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Pendapatan Usaha (Revenue)</span>
                                <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-chart-timeline-variant-shimmer fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-primary mb-1" id="kpi-income-revenue">
                                Rp {{ number_format($incomeData['poSum'] ?? ($poYear ?? 0), 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted" id="kpi-income-period">Periode {{ $incomeData['periodLabel'] ?? 'YTD ' . $currentYear }}</span>
                                <span class="badge bg-label-primary fw-bold rounded-pill">Top Line</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 2: HPP --}}
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Beban Pokok Penjualan (HPP)</span>
                                <div class="avatar avatar-sm bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-package-variant-closed fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-dark mb-1" id="kpi-income-cogs">
                                Rp {{ number_format($incomeData['modalSum'] ?? ($modalYear ?? 0), 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Harga modal unit serial PO</span>
                                <span class="badge bg-label-warning text-dark fw-bold rounded-pill">COGS</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 3: Gross Profit --}}
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Laba Kotor (Gross Profit)</span>
                                <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-finance fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-info mb-1" id="kpi-income-gross">
                                Rp {{ number_format($incomeData['subtotal'] ?? ($grossProfitYear ?? 0), 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Gross Margin Ratio</span>
                                <span class="badge bg-label-info fw-bold rounded-pill" id="kpi-income-gross-margin">{{ $incomeData['grossMarginPct'] ?? ($grossMarginPct ?? 0) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 4: Net Profit --}}
                @php
                    $activeNet = $incomeData['total'] ?? ($netProfitYear ?? 0);
                    $activeNetMargin = $incomeData['netMarginPct'] ?? ($netMarginPct ?? 0);
                @endphp
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Laba Bersih Berjalan (Net)</span>
                                <div class="avatar avatar-sm {{ $activeNet >= 0 ? 'bg-label-success' : 'bg-label-danger' }} rounded-3 d-flex align-items-center justify-content-center" id="kpi-income-net-avatar">
                                    <i class="mdi {{ $activeNet >= 0 ? 'mdi-trending-up' : 'mdi-trending-down' }} fs-4" id="kpi-income-net-icon"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold {{ $activeNet >= 0 ? 'text-success' : 'text-danger' }} mb-1" id="kpi-income-net">
                                {{ $activeNet < 0 ? '- ' : '' }}Rp {{ number_format(abs($activeNet), 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Net Margin Ratio</span>
                                <span class="badge {{ $activeNet >= 0 ? 'bg-label-success' : 'bg-label-danger' }} fw-bold rounded-pill" id="kpi-income-net-margin">
                                    {{ $activeNetMargin }}% Net
                                </span>
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
                            <i class="mdi mdi-printer-eye text-primary fs-4"></i>
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    Cetak Laporan Resmi Income Statement (Laba Rugi)
                                </h6>
                                <small class="text-muted d-none d-sm-inline">Pilih periode untuk melihat rincian laba rugi atau mencetak dokumen resmi</small>
                            </div>
                        </div>

                        {{-- Right: All filters & actions in 1 neat row --}}
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            {{-- Dropdown Bulan --}}
                            <div class="input-group input-group-sm" style="width: auto;">
                                <span class="input-group-text bg-light text-muted">
                                    <i class="mdi mdi-calendar-month-outline"></i>
                                </span>
                                <select id="income-month-select" class="form-select form-select-sm" style="min-width: 140px;">
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
                                        <option value="{{ $mNum }}" {{ (int)$mNum == (int)($selectedMonth ?? $currentMonth) ? 'selected' : '' }}>
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
                                <select id="income-year-select" class="form-select form-select-sm" style="min-width: 105px;">
                                    @foreach($years as $yr)
                                        <option value="{{ $yr }}" {{ $yr == ($selectedYear ?? $currentYear) ? 'selected' : '' }}>
                                            Tahun {{ $yr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tombol Buka Laba Rugi --}}
                            <button type="button" id="btn-view-income" class="btn btn-primary btn-sm px-3 shadow-xs">
                                <i class="mdi mdi-eye-outline me-1"></i> Buka Laba Rugi
                            </button>

                            {{-- Tombol Cetak PDF --}}
                            <button type="button" id="btn-print-income" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                <i class="mdi mdi-printer me-1"></i> Cetak PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Datatable Card: Pendapatan / Beban Lain-Lain --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="mdi mdi-format-list-bulleted-square text-primary fs-5"></i>
                                Daftar Pendapatan &amp; Beban Lain-Lain (Other Income &amp; Expenses)
                            </h6>
                            <small class="text-muted">Pendapatan dan biaya non-operasional (bunga bank, selisih kurs, pendapatan lain)</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-label-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createStatement">
                                <i class="mdi mdi-plus me-1"></i> Tambah Transaksi
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-income-stat table table-hover border-bottom mb-0">
                        <thead>
                            <tr>
                                <th style="min-width: 130px;">Tanggal</th>
                                <th style="min-width: 250px;">Keterangan / Deskripsi</th>
                                <th style="min-width: 160px;">Tipe Pos Akun</th>
                                <th class="text-end" style="min-width: 150px;">Nominal (IDR)</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- =================================================================== --}}
        {{-- TAB 2: BALANCE STATEMENT (NERACA) --}}
        {{-- =================================================================== --}}
        <div class="tab-pane fade {{ ($activeTab ?? '') == 'balance' ? 'show active' : '' }}" id="tab-balance" role="tabpanel" aria-labelledby="tab-balance-btn">
            
            {{-- 4 Executive Finance KPI Cards (Aktiva vs Pasiva) --}}
            <div class="row g-3 mb-4">
                {{-- KPI 1: Total Aset --}}
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Total Aset (Aktiva)</span>
                                <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-shield-check-outline fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-primary mb-1">
                                Rp {{ number_format($totalAset, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Aset Lancar + Tetap</span>
                                <span class="badge bg-label-primary fw-bold rounded-pill">Total Aktiva</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 2: Aset Lancar --}}
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Aset Lancar (Current Asset)</span>
                                <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-cash-multiple fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-info mb-1">
                                Rp {{ number_format($asetLancar, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Kas + Piutang + Persediaan</span>
                                <span class="badge bg-label-info fw-bold rounded-pill">Likuid</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 3: Nilai Buku Aset Tetap Bersih --}}
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Aset Tetap Bersih (Net Fixed)</span>
                                <div class="avatar avatar-sm bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-domain fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-dark mb-1">
                                Rp {{ number_format($asetTetapBersih, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Setelah depresiasi penyusutan</span>
                                <span class="badge bg-label-warning text-dark fw-bold rounded-pill">Net Book</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 4: Total Ekuitas Modal --}}
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card finance-kpi-card h-100 rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Ekuitas &amp; Modal Bersih</span>
                                <div class="avatar avatar-sm bg-label-success rounded-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-scale-balance fs-4"></i>
                                </div>
                            </div>
                            <h4 class="fw-bold text-success mb-1">
                                Rp {{ number_format($totalEkuitas, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center justify-content-between small">
                                <span class="text-muted">Modal + Laba - Prive</span>
                                <span class="badge bg-label-success fw-bold rounded-pill">Equity</span>
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
                            <i class="mdi mdi-file-chart-outline text-primary fs-4"></i>
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    Generator Laporan Balance Statement (Neraca)
                                </h6>
                                <small class="text-muted d-none d-sm-inline">Pilih periode untuk melihat rincian neraca komprehensif atau mencetak dokumen resmi</small>
                            </div>
                        </div>

                        {{-- Right: All filters & actions in 1 neat row --}}
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            {{-- Dropdown Bulan --}}
                            <div class="input-group input-group-sm" style="width: auto;">
                                <span class="input-group-text bg-light text-muted">
                                    <i class="mdi mdi-calendar-month-outline"></i>
                                </span>
                                <select id="balance-month-select" class="form-select form-select-sm" style="min-width: 140px;">
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
                                        <option value="{{ $mNum }}" {{ (int)$mNum == (int)($selectedMonth ?? $currentMonth) ? 'selected' : '' }}>
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
                                <select id="balance-year-select" class="form-select form-select-sm" style="min-width: 105px;">
                                    @foreach($years as $yr)
                                        <option value="{{ $yr }}" {{ $yr == ($selectedYear ?? $currentYear) ? 'selected' : '' }}>
                                            Tahun {{ $yr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tombol Buka Neraca --}}
                            <button type="button" id="btn-view-balance" class="btn btn-primary btn-sm px-3 shadow-xs">
                                <i class="mdi mdi-eye-outline me-1"></i> Buka Neraca
                            </button>

                            {{-- Tombol Cetak PDF --}}
                            <button type="button" id="btn-print-balance" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                <i class="mdi mdi-printer me-1"></i> Cetak PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ringkasan Komposisi Neraca (Aktiva vs Pasiva / Ekuitas) --}}
            <div class="row g-3">
                {{-- Sisi Kiri: Rincian Aset (Aktiva) --}}
                <div class="col-lg-6 col-12">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="mdi mdi-wallet-outline text-primary fs-5"></i>
                                Rincian Komponen Aset (Aktiva)
                            </h6>
                            <span class="badge bg-label-primary fw-bold">Posisi {{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="balance-item-row d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-dark">Kas &amp; Saldo Rekening Bank (BCA)</span>
                                    <small class="d-block text-muted">Saldo kas cair di rekening bank operasional</small>
                                </div>
                                <span class="fw-bold text-dark">Rp {{ number_format($bankSaldo, 0, ',', '.') }}</span>
                            </div>

                            <div class="balance-item-row d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-dark">Piutang Usaha Belum Lunas (AR)</span>
                                    <small class="d-block text-muted">Tagihan invoice tempo yang belum dilunasi klien</small>
                                </div>
                                <span class="fw-bold text-dark">Rp {{ number_format($piutang, 0, ',', '.') }}</span>
                            </div>

                            <div class="balance-item-row d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-dark">Persediaan Barang Gudang (Inventory)</span>
                                    <small class="d-block text-muted">Nilai modal produk spare part &amp; unit stok gudang</small>
                                </div>
                                <span class="fw-bold text-dark">Rp {{ number_format($persediaan, 0, ',', '.') }}</span>
                            </div>

                            <div class="p-3 bg-light d-flex justify-content-between align-items-center border-bottom">
                                <span class="fw-bold text-dark">Subtotal Aset Lancar (Current Assets)</span>
                                <span class="fw-bold text-primary fs-6">Rp {{ number_format($asetLancar, 0, ',', '.') }}</span>
                            </div>

                            <div class="balance-item-row d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-dark">Harga Perolehan Aset Tetap</span>
                                    <small class="d-block text-muted">Total inventaris kendaraan, gedung, &amp; kantor</small>
                                </div>
                                <span class="fw-bold text-dark">Rp {{ number_format($totalFixed, 0, ',', '.') }}</span>
                            </div>

                            <div class="balance-item-row d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-danger">Akumulasi Penyusutan (Depresiasi)</span>
                                    <small class="d-block text-muted">Penyusutan aset tetap berjalan</small>
                                </div>
                                <span class="fw-bold text-danger">- Rp {{ number_format($penyusutan, 0, ',', '.') }}</span>
                            </div>

                            <div class="p-3 bg-light d-flex justify-content-between align-items-center border-bottom">
                                <span class="fw-bold text-dark">Nilai Buku Aset Tetap Bersih</span>
                                <span class="fw-bold text-dark fs-6">Rp {{ number_format($asetTetapBersih, 0, ',', '.') }}</span>
                            </div>

                            <div class="p-3 bg-label-primary d-flex justify-content-between align-items-center rounded-bottom">
                                <span class="fw-bold text-primary text-uppercase">Total Seluruh Nilai Aset (Aktiva)</span>
                                <span class="fw-bold text-primary fs-5">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sisi Kanan: Rincian Kewajiban & Ekuitas --}}
                <div class="col-lg-6 col-12">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="mdi mdi-scale-balance text-primary fs-5"></i>
                                Rincian Kewajiban &amp; Ekuitas (Pasiva)
                            </h6>
                            <span class="badge bg-label-success fw-bold">Posisi {{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="balance-item-row d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-dark">Utang Usaha &amp; Pengadaan (AP)</span>
                                    <small class="d-block text-muted">Kewajiban pembayaran supplier/vendor</small>
                                </div>
                                <span class="fw-bold text-dark">Rp 0</span>
                            </div>

                            <div class="balance-item-row d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-dark">Kewajiban Jangka Pendek Lainnya</span>
                                    <small class="d-block text-muted">Beban terakru &amp; kewajiban lancar</small>
                                </div>
                                <span class="fw-bold text-dark">Rp 0</span>
                            </div>

                            <div class="p-3 bg-light d-flex justify-content-between align-items-center border-bottom">
                                <span class="fw-bold text-dark">Total Kewajiban (Liabilities)</span>
                                <span class="fw-bold text-dark fs-6">Rp 0</span>
                            </div>

                            <div class="balance-item-row d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-dark">Modal Pemilik &amp; Laba Ditahan Lalu</span>
                                    <small class="d-block text-muted">Akumulasi modal awal dan laba tahun sebelumnya</small>
                                </div>
                                <span class="fw-bold text-dark">Rp {{ number_format($labaTahunLalu, 0, ',', '.') }}</span>
                            </div>

                            <div class="balance-item-row d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-success">Laba Bersih Tahun Berjalan</span>
                                    <small class="d-block text-muted">Hasil laba bersih operasional tahun {{ $currentYear }}</small>
                                </div>
                                <span class="fw-bold text-success">+ Rp {{ number_format($labaBulanIni, 0, ',', '.') }}</span>
                            </div>

                            <div class="balance-item-row d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold text-danger">Pengambilan Prive (Drawings)</span>
                                    <small class="d-block text-muted">Penarikan modal keperluan pribadi pemilik</small>
                                </div>
                                <span class="fw-bold text-danger">- Rp {{ number_format($prive, 0, ',', '.') }}</span>
                            </div>

                            <div class="p-3 bg-light d-flex justify-content-between align-items-center border-bottom">
                                <span class="fw-bold text-dark">Total Ekuitas Modal (Owner's Equity)</span>
                                <span class="fw-bold text-success fs-6">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</span>
                            </div>

                            <div class="p-3 bg-label-success d-flex justify-content-between align-items-center rounded-bottom">
                                <span class="fw-bold text-success text-uppercase">Total Pasiva (Kewajiban + Ekuitas)</span>
                                <span class="fw-bold text-success fs-5">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================================================================== --}}
        {{-- TAB 3: EQUITY STATEMENT (PERUBAHAN MODAL) --}}
        {{-- =================================================================== --}}
        <div class="tab-pane fade {{ ($activeTab ?? '') == 'equity' ? 'show active' : '' }}" id="tab-equity" role="tabpanel" aria-labelledby="tab-equity-btn">
            
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
                                        <option value="{{ $mNum }}" {{ (int)$mNum == (int)($selectedMonth ?? $currentMonth) ? 'selected' : '' }}>
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
                                        <option value="{{ $yr }}" {{ $yr == ($selectedYear ?? $currentYear) ? 'selected' : '' }}>
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
                    <span class="badge bg-label-primary fw-bold">Akumulasi YTD {{ $currentYear }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 text-center align-items-center">
                        <div class="col-md-3 col-6">
                            <div class="p-3 equity-bridge-card">
                                <small class="text-muted fw-semibold text-uppercase d-block mb-1">Modal Awal</small>
                                <h5 class="fw-bold text-primary mb-0">Rp {{ number_format($modalAwalYear, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 equity-bridge-card">
                                <small class="text-muted fw-semibold text-uppercase d-block mb-1">Penambahan Laba</small>
                                <h5 class="fw-bold text-success mb-0">+ Rp {{ number_format($labaBersihYear, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 equity-bridge-card">
                                <small class="text-muted fw-semibold text-uppercase d-block mb-1">Penarikan Prive</small>
                                <h5 class="fw-bold text-danger mb-0">- Rp {{ number_format($prive, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 bg-label-info rounded-3">
                                <small class="text-info fw-bold text-uppercase d-block mb-1">Modal Akhir Berjalan</small>
                                <h5 class="fw-bold text-info mb-0">Rp {{ number_format($modalAkhirYear, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- =================================================================== --}}
        {{-- TAB 4: CASHFLOW STATEMENT (ARUS KAS) --}}
        {{-- =================================================================== --}}
        <div class="tab-pane fade {{ ($activeTab ?? '') == 'cashflow' ? 'show active' : '' }}" id="tab-cashflow" role="tabpanel" aria-labelledby="tab-cashflow-btn">
            
            @php
                $netOperasiBulan = ($ringkasanBulan['kasMasuk'] ?? 0) - ($ringkasanBulan['kasKeluar'] ?? 0);
                $netOperasiTahun = ($ringkasanTahun['kasMasuk'] ?? 0) - ($ringkasanTahun['kasKeluar'] ?? 0);
                $netInvFinTahun = ($ringkasanTahun['netInvestasi'] ?? 0) + ($ringkasanTahun['netPendanaan'] ?? 0);
            @endphp

            {{-- 4 Executive Cashflow KPI Cards (YTD) --}}
            <div class="row g-3 mb-4">
                {{-- Kas Masuk Operasi --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card finance-kpi-card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-semibold text-muted small">Kas Masuk Operasi (YTD)</span>
                                <span class="avatar avatar-sm rounded bg-label-success">
                                    <i class="mdi mdi-cash-plus fs-5"></i>
                                </span>
                            </div>
                            <h4 class="mb-1 text-success fw-bold">Rp {{ number_format($ringkasanTahun['kasMasuk'], 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center text-muted small">
                                <span>Bulan ini: <strong class="text-dark">Rp {{ number_format($ringkasanBulan['kasMasuk'], 0, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kas Keluar Operasi --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card finance-kpi-card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-semibold text-muted small">Kas Keluar Operasi (YTD)</span>
                                <span class="avatar avatar-sm rounded bg-label-danger">
                                    <i class="mdi mdi-cash-minus fs-5"></i>
                                </span>
                            </div>
                            <h4 class="mb-1 text-danger fw-bold">Rp {{ number_format($ringkasanTahun['kasKeluar'], 0, ',', '.') }}</h4>
                            <div class="d-flex align-items-center text-muted small">
                                <span>Bulan ini: <strong class="text-dark">Rp {{ number_format($ringkasanBulan['kasKeluar'], 0, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Net Kas Operasi --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card finance-kpi-card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-semibold text-muted small">Net Arus Kas Operasi (OCF)</span>
                                <span class="avatar avatar-sm rounded bg-label-primary">
                                    <i class="mdi mdi-swap-horizontal-bold fs-5"></i>
                                </span>
                            </div>
                            <h4 class="mb-1 {{ $netOperasiTahun >= 0 ? 'text-primary' : 'text-danger' }} fw-bold">
                                Rp {{ number_format($netOperasiTahun, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center text-muted small">
                                <span class="badge {{ $netOperasiTahun >= 0 ? 'bg-label-success' : 'bg-label-danger' }} me-1">
                                    {{ $netOperasiTahun >= 0 ? 'Surplus Operasi' : 'Defisit Operasi' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Net Investasi & Pendanaan --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card finance-kpi-card h-100 border-0 shadow-sm rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-semibold text-muted small">Net Investasi &amp; Modal</span>
                                <span class="avatar avatar-sm rounded bg-label-info">
                                    <i class="mdi mdi-bank-transfer fs-5"></i>
                                </span>
                            </div>
                            <h4 class="mb-1 {{ $netInvFinTahun >= 0 ? 'text-dark' : 'text-danger' }} fw-bold">
                                Rp {{ number_format($netInvFinTahun, 0, ',', '.') }}
                            </h4>
                            <div class="d-flex align-items-center text-muted small">
                                <span>Inv: <strong>Rp {{ number_format($ringkasanTahun['netInvestasi'], 0, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Comparison: Bulan Berjalan vs Tahun Berjalan --}}
            <div class="row g-4 mb-4">
                {{-- Bulan Berjalan --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100 rounded-3">
                        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm rounded bg-label-primary">
                                    <i class="mdi mdi-calendar-month-outline fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Ringkasan Bulan Ini</h6>
                                    <small class="text-muted">{{ $ringkasanBulanLabel }}</small>
                                </div>
                            </div>
                            <span class="badge bg-label-secondary">Bulan Berjalan</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 cashflow-table">
                                    <tbody>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold text-dark"><i class="mdi mdi-cash-plus text-success me-2"></i>Kas Masuk Operasi</div>
                                                <small class="text-muted">Pelunasan Penjualan PO &amp; Pendapatan Lain</small>
                                            </td>
                                            <td class="text-end pe-4 fw-bold text-success fs-6">
                                                Rp {{ number_format($ringkasanBulan['kasMasuk'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold text-dark"><i class="mdi mdi-cash-minus text-danger me-2"></i>Kas Keluar Operasi</div>
                                                <small class="text-muted">Beban Operasional Kas &amp; Pengeluaran Umum</small>
                                            </td>
                                            <td class="text-end pe-4 fw-bold text-danger fs-6">
                                                (Rp {{ number_format($ringkasanBulan['kasKeluar'], 0, ',', '.') }})
                                            </td>
                                        </tr>
                                        <tr class="table-light">
                                            <td class="ps-4 fw-bold text-primary">
                                                <i class="mdi mdi-sigma me-2"></i>Net Arus Kas Aktivitas Operasi
                                            </td>
                                            <td class="text-end pe-4 fw-bold {{ $netOperasiBulan >= 0 ? 'text-primary' : 'text-danger' }} fs-6">
                                                Rp {{ number_format($netOperasiBulan, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold text-dark"><i class="mdi mdi-domain text-info me-2"></i>Net Aktivitas Investasi</div>
                                                <small class="text-muted">Pembelian / Penjualan Aset Tetap</small>
                                            </td>
                                            <td class="text-end pe-4 fw-bold {{ ($ringkasanBulan['netInvestasi'] ?? 0) >= 0 ? 'text-dark' : 'text-danger' }}">
                                                {{ ($ringkasanBulan['netInvestasi'] ?? 0) < 0 ? '(Rp ' . number_format(abs($ringkasanBulan['netInvestasi']), 0, ',', '.') . ')' : 'Rp ' . number_format($ringkasanBulan['netInvestasi'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold text-dark"><i class="mdi mdi-cash-refund text-warning me-2"></i>Net Aktivitas Pendanaan</div>
                                                <small class="text-muted">Penyetoran Modal / Penarikan Prive</small>
                                            </td>
                                            <td class="text-end pe-4 fw-bold {{ ($ringkasanBulan['netPendanaan'] ?? 0) >= 0 ? 'text-dark' : 'text-danger' }}">
                                                {{ ($ringkasanBulan['netPendanaan'] ?? 0) < 0 ? '(Rp ' . number_format(abs($ringkasanBulan['netPendanaan']), 0, ',', '.') . ')' : 'Rp ' . number_format($ringkasanBulan['netPendanaan'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tahun Berjalan --}}
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm h-100 rounded-3">
                        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm rounded bg-label-info">
                                    <i class="mdi mdi-calendar-range fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">Ringkasan Tahun Ini (YTD)</h6>
                                    <small class="text-muted">Tahun {{ $ringkasanTahunLabel }}</small>
                                </div>
                            </div>
                            <span class="badge bg-label-info">Tahun Berjalan</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 cashflow-table">
                                    <tbody>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold text-dark"><i class="mdi mdi-cash-plus text-success me-2"></i>Kas Masuk Operasi</div>
                                                <small class="text-muted">Pelunasan Penjualan PO &amp; Pendapatan Lain</small>
                                            </td>
                                            <td class="text-end pe-4 fw-bold text-success fs-6">
                                                Rp {{ number_format($ringkasanTahun['kasMasuk'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold text-dark"><i class="mdi mdi-cash-minus text-danger me-2"></i>Kas Keluar Operasi</div>
                                                <small class="text-muted">Beban Operasional Kas &amp; Pengeluaran Umum</small>
                                            </td>
                                            <td class="text-end pe-4 fw-bold text-danger fs-6">
                                                (Rp {{ number_format($ringkasanTahun['kasKeluar'], 0, ',', '.') }})
                                            </td>
                                        </tr>
                                        <tr class="table-light">
                                            <td class="ps-4 fw-bold text-primary">
                                                <i class="mdi mdi-sigma me-2"></i>Net Arus Kas Aktivitas Operasi
                                            </td>
                                            <td class="text-end pe-4 fw-bold {{ $netOperasiTahun >= 0 ? 'text-primary' : 'text-danger' }} fs-6">
                                                Rp {{ number_format($netOperasiTahun, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold text-dark"><i class="mdi mdi-domain text-info me-2"></i>Net Aktivitas Investasi</div>
                                                <small class="text-muted">Pembelian / Penjualan Aset Tetap</small>
                                            </td>
                                            <td class="text-end pe-4 fw-bold {{ ($ringkasanTahun['netInvestasi'] ?? 0) >= 0 ? 'text-dark' : 'text-danger' }}">
                                                {{ ($ringkasanTahun['netInvestasi'] ?? 0) < 0 ? '(Rp ' . number_format(abs($ringkasanTahun['netInvestasi']), 0, ',', '.') . ')' : 'Rp ' . number_format($ringkasanTahun['netInvestasi'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-semibold text-dark"><i class="mdi mdi-cash-refund text-warning me-2"></i>Net Aktivitas Pendanaan</div>
                                                <small class="text-muted">Penyetoran Modal / Penarikan Prive</small>
                                            </td>
                                            <td class="text-end pe-4 fw-bold {{ ($ringkasanTahun['netPendanaan'] ?? 0) >= 0 ? 'text-dark' : 'text-danger' }}">
                                                {{ ($ringkasanTahun['netPendanaan'] ?? 0) < 0 ? '(Rp ' . number_format(abs($ringkasanTahun['netPendanaan']), 0, ',', '.') . ')' : 'Rp ' . number_format($ringkasanTahun['netPendanaan'] ?? 0, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Generator Laporan Arus Kas Periode Lengkap --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                        {{-- Left: Icon & Title in 1 clean line --}}
                        <div class="d-flex align-items-center gap-2">
                            <i class="mdi mdi-calendar-search text-primary fs-4"></i>
                            <div>
                                <h6 class="card-title mb-0 fw-bold text-dark">
                                    Generator Laporan Cashflow Statement (Arus Kas)
                                </h6>
                                <small class="text-muted d-none d-sm-inline">Buka lembar rincian transaksi kas atau cetak dokumen resmi arus kas</small>
                            </div>
                        </div>

                        {{-- Right: All filters & actions in 1 neat row --}}
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            {{-- Dropdown Bulan --}}
                            <div class="input-group input-group-sm" style="width: auto;">
                                <span class="input-group-text bg-light text-muted">
                                    <i class="mdi mdi-calendar-month-outline"></i>
                                </span>
                                <select id="cashflow-month-select" class="form-select form-select-sm" style="min-width: 140px;">
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
                                        <option value="{{ $mNum }}" {{ (int)$mNum == (int)($selectedMonth ?? $currentMonth) ? 'selected' : '' }}>
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
                                <select id="cashflow-year-select" class="form-select form-select-sm" style="min-width: 105px;">
                                    @foreach($years as $yr)
                                        <option value="{{ $yr }}" {{ $yr == ($selectedYear ?? $currentYear) ? 'selected' : '' }}>
                                            Tahun {{ $yr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Tombol Buka Arus Kas --}}
                            <button type="button" id="btn-view-cashflow" class="btn btn-primary btn-sm px-3 shadow-xs">
                                <i class="mdi mdi-eye-outline me-1"></i> Buka Arus Kas
                            </button>

                            {{-- Tombol Cetak PDF --}}
                            <button type="button" id="btn-print-cashflow" class="btn btn-outline-primary btn-sm px-3 shadow-xs">
                                <i class="mdi mdi-printer me-1"></i> Cetak PDF
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Modal Tambah Pendapatan/Beban Lain --}}
    @include('components.modal.finance.income')
</div>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-income-data.js?v={{ file_exists(public_path('assets/includes/table-income-data.js')) ? filemtime(public_path('assets/includes/table-income-data.js')) : time() }}"></script>
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

            // Activate tab from URL hash (e.g. #tab-balance) or query param (?tab=balance)
            var currentHash = window.location.hash;
            var urlParams = new URLSearchParams(window.location.search);
            var tabParam = urlParams.get('tab');

            if (currentHash && $('button[data-bs-target="' + currentHash + '"]').length) {
                $('button[data-bs-target="' + currentHash + '"]').tab('show');
            } else if (tabParam && $('button[data-bs-target="#tab-' + tabParam + '"]').length) {
                $('button[data-bs-target="#tab-' + tabParam + '"]').tab('show');
            }

            // ==========================================
            // TAB 1: INCOME ACTIONS & DYNAMIC FILTER
            // ==========================================
            function updateIncomeKPI(year, month) {
                var url = '{{ route("finance.statement.income-kpi") }}?year=' + year + (month ? '&month=' + month : '');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(res) {
                        if (res.success) {
                            $('#kpi-income-revenue').text(res.poSumFormatted);
                            $('#kpi-income-cogs').text(res.modalSumFormatted);
                            $('#kpi-income-gross').text(res.grossProfitFormatted);
                            $('#kpi-income-gross-margin').text(res.grossMarginPct);
                            $('#kpi-income-net').text(res.netProfitFormatted);
                            $('#kpi-income-net-margin').text(res.netMarginPct);
                            $('#kpi-income-period').text('Periode ' + res.periodLabel);

                            if (res.isNetProfitPositive) {
                                $('#kpi-income-net').removeClass('text-danger').addClass('text-success');
                                $('#kpi-income-net-avatar').removeClass('bg-label-danger').addClass('bg-label-success');
                                $('#kpi-income-net-icon').removeClass('mdi-trending-down text-danger').addClass('mdi-trending-up text-success');
                                $('#kpi-income-net-margin').removeClass('bg-label-danger').addClass('bg-label-success');
                            } else {
                                $('#kpi-income-net').removeClass('text-success').addClass('text-danger');
                                $('#kpi-income-net-avatar').removeClass('bg-label-success').addClass('bg-label-danger');
                                $('#kpi-income-net-icon').removeClass('mdi-trending-up text-success').addClass('mdi-trending-down text-danger');
                                $('#kpi-income-net-margin').removeClass('bg-label-success').addClass('bg-label-danger');
                            }
                        }
                    }
                });
            }

            // ==========================================
            // TAB 1: INCOME ACTIONS & DYNAMIC FILTER (Selection Bulan & Tahun)
            // ==========================================
            function applyIncomeFilter() {
                var m = $('#income-month-select').val();
                var y = $('#income-year-select').val();
                if (m && m !== 'all') {
                    updateIncomeKPI(y, m);
                    if ($.fn.DataTable.isDataTable('.datatable-income-stat')) {
                        $('.datatable-income-stat').DataTable().column(0).search(m + '-' + y).draw();
                    }
                } else {
                    updateIncomeKPI(y, null);
                    if ($.fn.DataTable.isDataTable('.datatable-income-stat')) {
                        $('.datatable-income-stat').DataTable().column(0).search(y).draw();
                    }
                }
            }

            $('#income-month-select, #income-year-select').on('change', function() {
                applyIncomeFilter();
            });

            $('#btn-view-income').on('click', function() {
                var m = $('#income-month-select').val();
                var y = $('#income-year-select').val();
                if (m && m !== 'all') {
                    window.location.href = '/income-detail/' + m + '/' + y;
                } else {
                    window.location.href = '/income-detail/' + y;
                }
            });

            $('#btn-print-income').on('click', function() {
                var m = $('#income-month-select').val();
                var y = $('#income-year-select').val();
                if (m && m !== 'all') {
                    window.open('/income-print/' + m + '/' + y, '_blank');
                } else {
                    window.open('/income-print/' + y, '_blank');
                }
            });

            // ==========================================
            // TAB 2: BALANCE ACTIONS (Selection Bulan & Tahun)
            // ==========================================
            $('#btn-view-balance').on('click', function() {
                var m = $('#balance-month-select').val();
                var y = $('#balance-year-select').val();
                if (m && m !== 'all') {
                    window.location.href = '/balance-detail/' + y + '/' + m;
                } else {
                    window.location.href = '/balance-detail/' + y;
                }
            });

            $('#btn-print-balance').on('click', function() {
                var m = $('#balance-month-select').val();
                var y = $('#balance-year-select').val();
                if (m && m !== 'all') {
                    window.open('/balance-print/' + m + '/' + y, '_blank');
                } else {
                    window.open('/balance-print/' + y, '_blank');
                }
            });

            // ==========================================
            // TAB 3: EQUITY ACTIONS (Selection Bulan & Tahun)
            // ==========================================
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

            // ==========================================
            // TAB 4: CASHFLOW ACTIONS
            // ==========================================
            $('#btn-view-cashflow').on('click', function() {
                var m = $('#cashflow-month-select').val();
                var y = $('#cashflow-year-select').val();
                if (m && m !== 'all') {
                    window.location.href = '/cashflow-detail/' + y + '/' + m;
                } else {
                    window.location.href = '/cashflow-detail/' + y;
                }
            });

            $('#btn-print-cashflow').on('click', function() {
                var m = $('#cashflow-month-select').val();
                var y = $('#cashflow-year-select').val();
                if (m && m !== 'all') {
                    window.open('/cashflow-print/' + m + '/' + y, '_blank');
                } else {
                    window.open('/cashflow-print/' + y, '_blank');
                }
            });
        });
    </script>
@endpush
