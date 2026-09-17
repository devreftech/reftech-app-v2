@extends('layouts.sales.app')
@section('title', 'Katalog Produk')
@section('content')
    @php
        $showTransaksi = Auth::check() && in_array(strtolower(Auth::user()->role), ['developer', 'admin']);
        $isSales = Auth::check() && strtolower(Auth::user()->role) === 'sales';
    @endphp
    {{-- ── Hero Header ───────────────────────────────────────────── --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <span class="avatar avatar-sm bg-label-primary rounded p-1">
                    <i class="mdi mdi-cube-outline mdi-20px"></i>
                </span>
                Katalog Produk &amp; Spare Part
                <span class="badge bg-label-primary rounded-pill fs-7">
                    @if (Auth::user()->role === 'Sales')
                        Sales Portal
                    @else
                        {{ Auth::user()->role }}
                    @endif
                </span>
            </h4>
            <p class="text-muted mb-0 small">
                Pencarian cepat SKU &amp; part number, ketersediaan stok fisik gudang Bandung (BDG) &amp; Bekasi (BKS), serta info harga jual.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if (in_array(Auth::user()->role, ['Admin', 'developer', 'Logistic', 'Finance Manager', 'Finance', 'Accounting']))
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-export-variant me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" id="btn-export-excel">
                                <i class="mdi mdi-file-excel-outline text-success me-2"></i>Export Excel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" id="btn-export-csv">
                                <i class="mdi mdi-file-document-outline text-info me-2"></i>Export CSV
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" id="btn-export-print">
                                <i class="mdi mdi-printer-outline text-secondary me-2"></i>Print Table
                            </a>
                        </li>
                    </ul>
                </div>
            @endif

            @if (in_array(Auth::user()->role, ['Admin', 'developer', 'Logistic']))
                <button type="button" class="btn btn-primary btn-sm shadow-none" data-bs-toggle="modal" data-bs-target="#createProduct">
                    <i class="mdi mdi-plus me-1"></i> Tambah Produk
                </button>
            @endif

            <button type="button" class="btn btn-outline-primary btn-sm shadow-none" id="btn-refresh-table" title="Segarkan Data Tabel">
                <i class="mdi mdi-refresh me-1"></i> Refresh Data
            </button>
        </div>
    </div>

    {{-- ── KPI Quick Stat Cards (Clickable Filter) ───────────────── --}}
    <div class="row g-3 mb-4" id="sales-product-stats">
        <div class="col-6 col-md-3">
            <div class="card card-hover shadow-sm border-0 stat-card active-filter" data-filter="all" title="Klik untuk menampilkan semua produk">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-medium d-block mb-1">Total Produk / PN</span>
                            <h3 class="fw-bold mb-0 text-primary" id="stat-total-products">-</h3>
                        </div>
                        <div class="avatar avatar-md bg-label-primary rounded p-2">
                            <i class="mdi mdi-format-list-bulleted mdi-24px"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-1 border-top small text-muted d-flex align-items-center">
                        <span class="badge badge-dot bg-primary me-1"></span> Semua katalog suku cadang
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-hover shadow-sm border-0 stat-card" data-filter="ready" title="Klik untuk filter hanya produk ready stock">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-medium d-block mb-1">Ready Stock (Fisik)</span>
                            <h3 class="fw-bold mb-0 text-success" id="stat-ready-stock">-</h3>
                        </div>
                        <div class="avatar avatar-md bg-label-success rounded p-2">
                            <i class="mdi mdi-check-circle-outline mdi-24px"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-1 border-top small text-muted d-flex align-items-center">
                        <span class="badge badge-dot bg-success me-1"></span> Stok BDG atau BKS &gt; 0
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-hover shadow-sm border-0 stat-card" data-filter="genuine" title="Klik untuk filter Genuine parts">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-medium d-block mb-1">Genuine Parts (G)</span>
                            <h3 class="fw-bold mb-0 text-info" id="stat-genuine">-</h3>
                        </div>
                        <div class="avatar avatar-md bg-label-info rounded p-2">
                            <i class="mdi mdi-shield-check-outline mdi-24px"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-1 border-top small text-muted d-flex align-items-center">
                        <span class="badge badge-dot bg-info me-1"></span> Asli OEM / Pabrikan
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card card-hover shadow-sm border-0 stat-card" data-filter="replacement" title="Klik untuk filter Replacement parts">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-medium d-block mb-1">Replacement (R)</span>
                            <h3 class="fw-bold mb-0 text-warning" id="stat-replacement">-</h3>
                        </div>
                        <div class="avatar avatar-md bg-label-warning rounded p-2">
                            <i class="mdi mdi-swap-horizontal mdi-24px"></i>
                        </div>
                    </div>
                    <div class="mt-2 pt-1 border-top small text-muted d-flex align-items-center">
                        <span class="badge badge-dot bg-warning me-1"></span> Ekuivalen berkualitas
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Strip Informasi Finansial & Master (Untuk Admin, Developer, Logistic, Finance, Accounting) ── --}}
    @if (Auth::user()->role !== 'Sales')
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-success small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Total Estimasi Asset</span>
                            <h4 class="fw-bold mb-0 text-success">Rp {{ number_format($asset, 0, ',', '.') }}</h4>
                        </div>
                        <div class="avatar avatar-md bg-label-success rounded p-2">
                            <i class="mdi mdi-wallet-giftcard mdi-24px"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 pt-1 border-top d-block">Nilai total modal produk &amp; spare part</small>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-primary small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Estimasi Omset / Revenue</span>
                            <h4 class="fw-bold mb-0 text-primary">Rp {{ number_format($revenue, 0, ',', '.') }}</h4>
                        </div>
                        <div class="avatar avatar-md bg-label-primary rounded p-2">
                            <i class="mdi mdi-currency-usd mdi-24px"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 pt-1 border-top d-block">Estimasi omset stok &times; harga price list</small>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm p-3 bg-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-info small fw-semibold text-uppercase d-block mb-1" style="font-size: 11px;">Katalog SKU &amp; Varian Part</span>
                            <h4 class="fw-bold mb-0 text-info">{{ number_format($commodity, 0, ',', '.') }} <span class="fs-6 fw-normal text-muted">SKU / {{ number_format($sproduct, 0, ',', '.') }} Equivalent</span></h4>
                        </div>
                        <div class="avatar avatar-md bg-label-info rounded p-2">
                            <i class="mdi mdi-layers-outline mdi-24px"></i>
                        </div>
                    </div>
                    <small class="text-muted mt-2 pt-1 border-top d-block">Item terdaftar pada master katalog</small>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Card Tabel Data Modern ───────────────────────────────── --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-transparent py-3 border-bottom d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-semibold text-muted small text-uppercase tracking-wider me-1">
                    <i class="mdi mdi-filter-variant me-1"></i>Filter Cepat:
                </span>
                <div class="btn-group btn-group-sm" role="group" id="sales-filter-btn-group">
                    <button type="button" class="btn btn-outline-primary active" data-filter="all">
                        <i class="mdi mdi-view-grid-outline me-1"></i>Semua
                    </button>
                    <button type="button" class="btn btn-outline-success" data-filter="ready">
                        <i class="mdi mdi-check-circle-outline me-1"></i>Ready Stock
                    </button>
                    <button type="button" class="btn btn-outline-info" data-filter="genuine">
                        <i class="mdi mdi-shield-check-outline me-1"></i>Genuine (G)
                    </button>
                    <button type="button" class="btn btn-outline-warning" data-filter="replacement">
                        <i class="mdi mdi-swap-horizontal me-1"></i>Replacement (R)
                    </button>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-secondary py-2 px-3 fw-normal" id="table-filtered-info">
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memuat data...
                </span>
            </div>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-product-sales table table-hover">
                <thead class="table-light align-middle">
                    <tr>
                        <th rowspan="2"></th>
                        <th rowspan="2">ID</th>
                        @if (!$isSales)
                            <th rowspan="2" style="min-width: 95px;">
                                SKU
                                <i class="mdi mdi-information-outline text-muted fs-7 align-middle ms-1"
                                   style="cursor: pointer;"
                                   data-bs-toggle="tooltip"
                                   data-bs-html="true"
                                   data-bs-custom-class="tooltip-hpp"
                                   data-bs-placement="top"
                                   data-bs-title="&lt;div class='text-start py-1 px-1' style='min-width: 240px;'&gt;&lt;div class='fw-bold text-white mb-2 border-bottom border-light border-opacity-25 pb-1 d-flex align-items-center'&gt;&lt;i class='mdi mdi-calculator-variant-outline text-warning me-1'&gt;&lt;/i&gt;Keterangan HPP Produk&lt;/div&gt;&lt;div class='mb-2'&gt;&lt;div class='fw-bold text-warning fs-8'&gt;AVG HPP&lt;/div&gt;&lt;div class='text-white-50 small' style='font-size: 0.72rem; line-height: 1.25;'&gt;Total harga pembelian dibagi stok saat itu&lt;/div&gt;&lt;/div&gt;&lt;div class='pt-2 border-top border-light border-opacity-10'&gt;&lt;div class='fw-bold text-info fs-8'&gt;LAST HPP&lt;/div&gt;&lt;div class='text-white-50 small' style='font-size: 0.72rem; line-height: 1.25;'&gt;Harga pembelian terakhir&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;"></i>
                            </th>
                        @endif
                        <th rowspan="2" style="min-width: 90px;" class="text-nowrap">Brand</th>
                        <th rowspan="2" style="min-width: 140px;">Part Number</th>
                        <th rowspan="2">Desc</th>
                        <th colspan="3" class="text-center border-bottom py-1">Stock</th>
                        <th rowspan="2" style="min-width: 130px;" class="text-end">Price</th>
                        @if ($isSales)
                            <th rowspan="2" style="min-width: 120px;" class="text-center">Update Price</th>
                        @endif
                        @if ($showTransaksi)
                            <th rowspan="2" style="min-width: 95px;" class="text-center">Transaksi</th>
                        @endif
                        <th rowspan="2" style="min-width: 60px;" class="text-center">Aksi</th>
                    </tr>
                    <tr>
                        <th class="text-center px-2 py-1" style="font-size: 0.78rem;" title="Gudang Bandung">BDG</th>
                        <th class="text-center px-2 py-1" style="font-size: 0.78rem;" title="Gudang Bekasi">BKS</th>
                        <th class="text-center px-2 py-1" style="font-size: 0.78rem;" title="Keep Stock">KEEP</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    @include('components.modal.warehouse.product.form')
@endsection()

@push('after-style')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <style>
        body,
        .layout-wrapper,
        .content-wrapper,
        .content-wrapper *,
        .modal,
        .modal *,
        .tooltip,
        .tooltip *,
        .dropdown-menu,
        .dropdown-menu * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }

        .stat-card {
            cursor: pointer;
            transition: all 0.22s ease-in-out;
            border: 2px solid transparent !important;
            border-radius: 0.5rem;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1.25rem rgba(67, 89, 113, 0.12) !important;
        }
        .stat-card.active-filter {
            border-color: #666cff !important;
            box-shadow: 0 0.25rem 0.75rem rgba(102, 108, 255, 0.18) !important;
            background-color: rgba(102, 108, 255, 0.02);
        }
        .datatable-product-sales thead th {
            font-size: 0.8125rem;
            letter-spacing: 0.3px;
        }
        .datatable-product-sales tbody tr {
            transition: background-color 0.15s ease;
        }
        .datatable-product-sales tbody tr:hover {
            background-color: rgba(102, 108, 255, 0.035);
        }
        .btn-copy-pn {
            opacity: 0.5;
            transition: opacity 0.15s ease;
        }
        .btn-copy-pn:hover {
            opacity: 1;
        }

        /* Custom styled tooltip for HPP */
        .tooltip-hpp .tooltip-inner {
            max-width: 320px;
            background-color: #1f2430 !important;
            color: #f3f4f6 !important;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 10px 14px;
            text-align: left;
            font-size: 0.8125rem;
            backdrop-filter: blur(8px);
        }
        .tooltip-hpp.bs-tooltip-top .tooltip-arrow::before {
            border-top-color: #1f2430 !important;
        }
        .tooltip-hpp.bs-tooltip-bottom .tooltip-arrow::before {
            border-bottom-color: #1f2430 !important;
        }
        .tooltip-hpp.bs-tooltip-start .tooltip-arrow::before {
            border-left-color: #1f2430 !important;
        }
        .tooltip-hpp.bs-tooltip-end .tooltip-arrow::before {
            border-right-color: #1f2430 !important;
        }
        .sku-hpp-trigger {
            cursor: pointer;
            transition: color 0.15s ease;
        }
        .sku-hpp-trigger:hover {
            color: #4f52e6 !important;
            text-decoration: underline !important;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script>
        window.isDeveloper = @json($showTransaksi);
        window.showTransaksi = @json($showTransaksi);
        window.isSales = @json($isSales);
    </script>
    <script src="{{ asset('assets') }}/includes/table-product-sales.js?v={{ time() }}"></script>
@endpush
