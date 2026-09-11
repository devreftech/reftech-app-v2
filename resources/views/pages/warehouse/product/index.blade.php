@extends('layouts.sales.app')
@section('title', 'Katalog Produk')
@section('content')
    @if (Auth::user()->role == 'Sales')
        {{-- ── Hero Header Khusus Sales ─────────────────────────────── --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                    <span class="avatar avatar-sm bg-label-primary rounded p-1">
                        <i class="mdi mdi-cube-outline mdi-20px"></i>
                    </span>
                    Katalog Produk & Spare Part
                    <span class="badge bg-label-primary rounded-pill fs-7">Sales Portal</span>
                </h4>
                <p class="text-muted mb-0 small">
                    Pencarian cepat part number, cek ketersediaan stok fisik gudang Bandung (BDG) & Bekasi (BKS), serta info harga terbaru.
                </p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm shadow-none" id="btn-refresh-table" title="Segarkan Data Tabel">
                    <i class="mdi mdi-refresh me-1"></i> Refresh Data
                </button>
            </div>
        </div>

        {{-- ── KPI Quick Stat Cards Khusus Sales ─────────────────────── --}}
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

        {{-- ── Card Tabel Khusus Sales ───────────────────────────────── --}}
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
                            <th rowspan="2" class="text-center">Foto</th>
                            <th rowspan="2">Brand</th>
                            <th rowspan="2">Part Number</th>
                            <th rowspan="2">Deskripsi Item</th>
                            <th colspan="3" class="text-center border-bottom py-1">Ketersediaan Stok</th>
                            <th rowspan="2" class="text-center" style="min-width: 140px;">Harga Jual</th>
                            <th rowspan="2" class="text-center">Update Harga</th>
                        </tr>
                        <tr>
                            <th class="text-center px-2 py-1" style="font-size: 0.78rem;" title="Gudang Bandung">BDG</th>
                            <th class="text-center px-2 py-1" style="font-size: 0.78rem;" title="Gudang Bekasi">BKS</th>
                            <th class="text-center px-2 py-1" style="font-size: 0.78rem;" title="Pending / In-Transit">Pend</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    @else
        <h4 class="fw-bold py-3 mb-4">
            Product
        </h4>
        <div class="card mb-4">
            <div class="card-widget-separator-wrapper">
                <div class="card-body card-widget-separator">
                    <div class="row gy-4 gy-sm-1">
                        <div class="col-sm-6 col-lg-3">
                            <div
                                class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                                <div>
                                    <p class="mb-2">SKU</p>
                                    <h4 class="mb-2">{{ $commodity }}</h4>
                                    <p class="mb-0"><span class="badge rounded-pill bg-label-success"></span></p>
                                </div>
                                <div class="avatar me-sm-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="mdi mdi-home-outline mdi-24px"></i>
                                    </span>
                                </div>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none me-4">
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div
                                class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                                <div>
                                    <p class="mb-2">Equivalent</p>
                                    <h4 class="mb-2">{{ $sproduct }}</h4>
                                    <p class="mb-0"><span class="badge rounded-pill bg-label-success"></span></p>
                                </div>
                                <div class="avatar me-lg-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="mdi mdi-laptop mdi-24px"></i>
                                    </span>
                                </div>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none">
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div
                                class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                                <div>
                                    <p class="mb-2">Asset</p>
                                    <h4 class="mb-2">Rp {{ number_format($asset, '0', ',', '.') }}</h4>
                                    <p class="mb-0"><span class="badge rounded-pill bg-label-success"></span></p>
                                </div>
                                <div class="avatar me-sm-4">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="mdi mdi-wallet-giftcard mdi-24px"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="mb-2">Revenue</p>
                                    <h4 class="mb-2">Rp {{ number_format($revenue, '0', ',', '.') }}</h4>
                                    <p class="mb-0"><span class="badge rounded-pill bg-label-danger"></span></p>
                                </div>
                                <div class="avatar">
                                    <span class="avatar-initial rounded bg-label-secondary">
                                        <i class="mdi mdi-currency-usd mdi-24px"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-product table table-bordered">
                    <thead class="table-light text-center align-middle">
                        <tr>
                            <th rowspan="2"></th>
                            <th rowspan="2">ID</th>
                            <th rowspan="2">SKU</th>
                            <th rowspan="2">Brand</th>
                            <th rowspan="2">Part Number</th>
                            <th rowspan="2">Desc</th>
                            <th colspan="3" class="border-bottom">Stock</th>
                            <th rowspan="2">Price</th>
                        </tr>
                        <tr>
                            <th>BDG</th>
                            <th>BKS</th>
                            <th>Pend</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    @endif
    @include('components.modal.warehouse.product.form')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <style>
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
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product.js?v={{ filemtime(public_path('assets/includes/table-product.js')) }}"></script>
    <script src="{{ asset('assets') }}/includes/table-product-sales.js?v={{ filemtime(public_path('assets/includes/table-product-sales.js')) }}"></script>
@endpush
