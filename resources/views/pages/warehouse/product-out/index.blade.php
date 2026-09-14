@extends('layouts.sales.app')
@section('title', 'Product Out (Barang Keluar)')

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y p-0">
        {{-- Hero Page Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 font-12">
                        <li class="breadcrumb-item"><a href="javascript:void(0);" class="text-muted">Warehouse</a></li>
                        <li class="breadcrumb-item text-muted">Pengeluaran</li>
                        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Product Out</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-1 text-dark" style="font-family: 'Inter', sans-serif;">
                    <i class="mdi mdi-truck-delivery-outline text-primary me-2 fs-4"></i>Pengeluaran Barang (Product Out)
                </h4>
                <p class="text-muted mb-0 small">
                    Kelola riwayat surat jalan barang keluar, monitoring pengiriman part &amp; unit, dan verifikasi dokumen pengiriman ke pelanggan.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('product-out.create') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm px-3 py-2">
                    <i class="mdi mdi-plus fs-5 me-1"></i>
                    <span class="fw-semibold">Input Barang Keluar</span>
                </a>
            </div>
        </div>

        {{-- KPI Stat Cards Row --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 kpi-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted font-11 text-uppercase fw-bold" style="letter-spacing: .5px;">Total Dokumen BK</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="mdi mdi-file-document-multiple-outline fs-5"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-0 text-heading">{{ number_format($totalDoc ?? 0) }}</h4>
                        <div class="font-11 text-muted mt-1">
                            <span class="text-primary fw-semibold"><i class="mdi mdi-check-circle-outline me-1"></i>Semua riwayat</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 kpi-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted font-11 text-uppercase fw-bold" style="letter-spacing: .5px;">Dokumen Bulan Ini</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-info">
                                    <i class="mdi mdi-calendar-month-outline fs-5"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-0 text-heading">{{ number_format($thisMonthDoc ?? 0) }}</h4>
                        <div class="font-11 text-muted mt-1">
                            <span class="text-info fw-semibold">{{ now()->translatedFormat('F Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 kpi-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted font-11 text-uppercase fw-bold" style="letter-spacing: .5px;">Total Qty Bulan Ini</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-success">
                                    <i class="mdi mdi-package-variant-closed fs-5"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-0 text-heading">{{ number_format($totalQtyThisMonth ?? 0) }} <span class="font-13 text-muted fw-normal">pcs</span></h4>
                        <div class="font-11 text-muted mt-1">
                            <span class="text-success fw-semibold"><i class="mdi mdi-truck-fast-outline me-1"></i>Fisik terdistribusi</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 kpi-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted font-11 text-uppercase fw-bold" style="letter-spacing: .5px;">Customer Dilayani</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle bg-label-warning">
                                    <i class="mdi mdi-domain fs-5"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="fw-bold mb-0 text-heading">{{ number_format($totalClients ?? 0) }}</h4>
                        <div class="font-11 text-muted mt-1">
                            <span class="text-warning fw-semibold"><i class="mdi mdi-account-group-outline me-1"></i>Total relasi klien</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table Card --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark font-16">
                        <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i>Daftar Surat Jalan &amp; Barang Keluar
                    </h5>
                    <p class="text-muted font-12 mb-0 mt-1">Klik kolom item untuk membuka slide-over rincian dokumen &amp; barang keluar.</p>
                </div>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-product-out table table-hover">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>No. Dokumen BK</th>
                            <th>No. Invoice / SJ</th>
                            <th>No. PO Customer</th>
                            <th>Customer / Klien</th>
                            <th>Rincian Barang</th>
                            <th>Catatan</th>
                            <th class="text-center">Qty</th>
                            <th>Tanggal Keluar</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        {{-- Sliding Quick Preview Drawer (Offcanvas) for Product Out with Blur Effect --}}
        <div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="productOutOffcanvas" aria-labelledby="productOutOffcanvasLabel" style="width: 540px; max-width: 92vw;">
            <div class="offcanvas-header bg-label-primary border-bottom py-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary text-white font-11 rounded-pill" id="bkDrawerDocCode">000-P/BK/00/0000</span>
                        <span class="badge bg-label-success rounded-pill px-3 py-1 font-11 fw-semibold">
                            <i class="mdi mdi-check-circle-outline me-1"></i>Terkirim / Selesai
                        </span>
                    </div>
                    <h5 class="offcanvas-title fw-bold text-heading mb-0" id="productOutOffcanvasLabel">Rincian Dokumen Barang Keluar</h5>
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-4">
                {{-- Document Summary Card --}}
                <div class="card border mb-3 bg-light">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                            <div>
                                <span class="text-muted font-11 d-block text-uppercase fw-bold">Customer / Klien:</span>
                                <h6 class="fw-bold text-heading font-14 mb-0" id="bkDrawerClient">-</h6>
                            </div>
                            <div class="text-end">
                                <span class="text-muted font-11 d-block text-uppercase fw-bold">No. Invoice / SJ:</span>
                                <span class="fw-bold text-primary font-13 font-monospace" id="bkDrawerInvoice">-</span>
                            </div>
                        </div>

                        <div class="row g-2 font-12">
                            <div class="col-6">
                                <span class="text-muted d-block font-11">No. PO Customer:</span>
                                <strong class="text-heading font-monospace" id="bkDrawerPo">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Tanggal Pengeluaran:</span>
                                <strong class="text-heading" id="bkDrawerDate">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Ekspedisi / Shipping:</span>
                                <strong class="text-heading" id="bkDrawerShipping">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Keterangan / Vers:</span>
                                <strong class="text-heading" id="bkDrawerNote">-</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ordered / Shipped Items Table --}}
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="font-11 text-muted text-uppercase fw-bold mb-0">Daftar Barang Keluar (<span id="bkDrawerItemCount">0</span> jenis)</label>
                        <span class="badge bg-label-primary font-11" id="bkDrawerTotalPcs">0 item</span>
                    </div>
                    <div class="table-responsive border rounded-3 bg-white">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr class="font-11 text-muted text-uppercase">
                                    <th style="width: 30px;" class="text-center">#</th>
                                    <th>Nama Barang &amp; Spesifikasi</th>
                                    <th class="text-end" style="width: 110px;">Kuantitas</th>
                                </tr>
                            </thead>
                            <tbody id="bkDrawerItemsTbody" class="font-12">
                                {{-- Dynamically populated --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="offcanvas-footer border-top p-3 d-flex align-items-center justify-content-between gap-2">
                <a href="#" id="bkDrawerDetailBtn" class="btn btn-primary d-flex align-items-center gap-1 shadow-xs">
                    <i class="mdi mdi-printer-outline me-1"></i> Buka Surat Jalan / Detail
                </a>
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">
                    Tutup
                </button>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />

    <style>
        /* Typography & General Styling */
        body, h4, h5, h6, table, .card-title, .dataTables_wrapper, .form-label {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        .kpi-stat-card {
            border: 1px solid rgba(24, 28, 33, 0.08) !important;
            transition: all 0.2s ease;
        }
        .kpi-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(24, 28, 33, 0.08) !important;
        }

        /* Item Preview Trigger Button */
        .btn-item-preview {
            background: #f4f5f9;
            border: 1px solid #e2e5ec;
            color: #435971;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-item-preview:hover {
            background: #696cff;
            border-color: #696cff;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(105, 108, 255, 0.25);
        }
        .btn-item-preview:hover i,
        .btn-item-preview:hover span {
            color: #ffffff !important;
        }

        /* Glassmorphism Backdrop Blur for Offcanvas Drawer */
        .offcanvas-backdrop {
            transition: opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1), backdrop-filter 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .offcanvas-backdrop.show {
            backdrop-filter: blur(8px) saturate(160%) !important;
            -webkit-backdrop-filter: blur(8px) saturate(160%) !important;
            background-color: rgba(15, 23, 42, 0.5) !important;
            opacity: 1 !important;
        }

        /* Slide-over Drawer Elevation */
        #productOutOffcanvas {
            box-shadow: -15px 0 45px rgba(15, 23, 42, 0.3) !important;
            border-left: 1px solid rgba(0, 0, 0, 0.08) !important;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        /* Modern Table Header */
        .table thead th {
            background-color: #fcfcfd !important;
            color: #5D596C !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 0.74rem !important;
            letter-spacing: 0.04em;
            border-bottom: 1px solid rgba(24, 28, 33, 0.08) !important;
            padding: 12px 14px !important;
        }

        .table tbody td {
            padding: 12px 14px !important;
            color: #333333 !important;
            font-size: 0.85rem !important;
            border-bottom: 1px solid rgba(24, 28, 33, 0.05) !important;
            vertical-align: middle !important;
        }

        /* Filter Search Box */
        .dataTables_filter input {
            border: 1px solid rgba(24, 28, 33, 0.12) !important;
            border-radius: 6px !important;
            padding: 5px 12px !important;
            font-size: 0.84rem !important;
            outline: none !important;
        }
        .dataTables_filter input:focus {
            border-color: #7367F0 !important;
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
    <script src="{{ asset('assets') }}/includes/table-product-out.js?v={{ time() }}"></script>
@endpush
