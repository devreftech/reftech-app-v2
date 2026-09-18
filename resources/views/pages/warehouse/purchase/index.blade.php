@extends('layouts.sales.app')
@section('title', 'Purchase Request (Pengadaan Barang) - Reftech ERP')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />

    <style>
        /* Modern Typography & Polish */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
        }

        /* Hero Banner Card */
        .pr-hero-card {
            background: linear-gradient(135deg, #1e2640 0%, #2a3558 100%);
            border-radius: 16px;
            color: #ffffff;
            box-shadow: 0 10px 30px rgba(30, 38, 64, 0.15);
            position: relative;
            overflow: hidden;
        }
        .pr-hero-card::after {
            content: '';
            position: absolute;
            right: -20px;
            bottom: -30px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(105, 108, 255, 0.25) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        /* KPI Metric Cards */
        .kpi-card {
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.25s cubic-bezier(0.165, 0.84, 0.44, 1);
            position: relative;
            overflow: hidden;
        }
        .kpi-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
            border-color: rgba(105, 108, 255, 0.3);
        }
        .kpi-accent-bar {
            height: 4px;
            width: 100%;
            position: absolute;
            top: 0;
            left: 0;
        }

        /* Nav Tabs Pill Redesign */
        .pr-nav-pills {
            background: #f4f5f9;
            padding: 5px;
            border-radius: 12px;
            display: inline-flex;
            gap: 4px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .pr-nav-pills .nav-link {
            border-radius: 8px !important;
            padding: 8px 16px;
            color: #566a7f;
            font-weight: 600;
            font-size: 0.84rem;
            transition: all 0.2s ease;
            border: none !important;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .pr-nav-pills .nav-link:hover {
            color: #696cff;
            background: rgba(105, 108, 255, 0.08);
        }
        .pr-nav-pills .nav-link.active {
            color: #ffffff !important;
            background: #696cff !important;
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
        }

        /* Table Styling */
        .table thead th {
            font-size: 0.75rem !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #566a7f;
            background-color: #f8f9fa;
            border-bottom: 2px solid #e7eaf0 !important;
            vertical-align: middle;
        }
        .table td {
            font-size: 0.85rem;
            vertical-align: middle;
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
        #purchaseRequestOffcanvas {
            box-shadow: -15px 0 45px rgba(15, 23, 42, 0.3) !important;
            border-left: 1px solid rgba(0, 0, 0, 0.08) !important;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        /* High Contrast Badge Overrides for Maximum Readability */
        .badge.bg-label-warning {
            background-color: #fff2d6 !important;
            color: #8f5200 !important; /* High contrast dark amber */
            font-weight: 600 !important;
            border: 1px solid rgba(255, 171, 0, 0.3) !important;
        }
        .badge.bg-label-info {
            background-color: #e1f7fc !important;
            color: #036b82 !important; /* High contrast deep cyan */
            font-weight: 600 !important;
            border: 1px solid rgba(38, 198, 249, 0.3) !important;
        }
        .badge.bg-label-success {
            background-color: #e8fadf !important;
            color: #2e6d0e !important; /* High contrast deep forest green */
            font-weight: 600 !important;
            border: 1px solid rgba(113, 221, 55, 0.3) !important;
        }
        .badge.bg-label-primary {
            background-color: #eae8fd !important;
            color: #4b4ec7 !important; /* High contrast deep indigo */
            font-weight: 600 !important;
            border: 1px solid rgba(105, 108, 255, 0.3) !important;
        }
        .badge.bg-label-secondary {
            background-color: #ebeef0 !important;
            color: #3b4d61 !important; /* High contrast slate */
            font-weight: 600 !important;
            border: 1px solid rgba(133, 146, 163, 0.3) !important;
        }
        .badge.bg-label-danger {
            background-color: #ffe5e5 !important;
            color: #b31d1d !important; /* High contrast dark crimson */
            font-weight: 600 !important;
            border: 1px solid rgba(255, 77, 73, 0.3) !important;
        }
        .badge.bg-warning {
            background-color: #ffab00 !important;
            color: #2b2c34 !important; /* Dark bold text on solid yellow (avoids washed-out white text) */
            font-weight: 700 !important;
        }

        /* High-Contrast Nav Pill Badges */
        .pr-nav-pills .badge {
            font-size: 0.72rem !important;
            padding: 3px 8px !important;
            font-weight: 700 !important;
            line-height: 1.2 !important;
        }
        .pr-nav-pills .badge.bg-warning {
            background-color: #ffab00 !important;
            color: #2b2c34 !important;
        }
        .pr-nav-pills .nav-link.active .badge {
            background-color: #ffffff !important;
            color: #696cff !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2) !important;
        }

        /* Hero Pill Badges */
        .hero-badge-primary {
            background: rgba(105, 108, 255, 0.35) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            backdrop-filter: blur(4px);
        }
        .hero-badge-success {
            background: rgba(113, 221, 55, 0.3) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            backdrop-filter: blur(4px);
        }

        /* Subtle Pulse for Live Pending */
        .badge-pulse {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
            animation: pulse-ring 1.8s infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.9); opacity: 1; }
            70% { transform: scale(2); opacity: 0; }
            100% { transform: scale(0.9); opacity: 0; }
        }

        /* Modal Manual GR Styling */
        #modalManualGr .modal-content {
            border-radius: 16px;
            box-shadow: 0 16px 48px rgba(30, 38, 64, 0.18);
        }
        #modalManualGr .modal-header {
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
        #modalManualGr .select2-container--default .select2-selection--single {
            border-color: #d9dee3;
            border-radius: 8px;
            height: 42px;
            display: flex;
            align-items: center;
        }
        #modalManualGr .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            padding-left: 12px;
            font-size: 0.875rem;
            color: #566a7f;
        }
        #modalManualGr .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }
        #modalManualGr .select2-dropdown {
            border-radius: 10px;
            border-color: #d9dee3;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            font-size: 0.85rem;
            z-index: 9999;
        }
        #modalManualGr .form-control:focus, #modalManualGr .form-select:focus {
            border-color: #71dd37;
            box-shadow: 0 0 0 0.2rem rgba(113, 221, 55, 0.15);
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid flex-grow-1 container-p-y px-4">
        {{-- Flash Notification --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 shadow-sm border-0" role="alert">
                <i class="mdi mdi-check-circle-outline me-2 mdi-24px"></i>
                <div class="fw-semibold">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Top Bar Breadcrumb --}}
        <div class="mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mb-0 font-12">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Logistik &amp; Pengadaan</a></li>
                    <li class="breadcrumb-item active fw-bold">Purchase Request</li>
                </ol>
            </nav>
        </div>

        {{-- Hero Header Banner --}}
        <div class="pr-hero-card p-4 p-md-5 mb-4 position-relative">
            <div class="row align-items-center g-3">
                <div class="col-lg-8 col-md-12">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge hero-badge-primary px-3 py-1 font-11 rounded-pill fw-semibold">
                            <i class="mdi mdi-storefront-outline me-1 text-white"></i> LOGISTIK &amp; PROCUREMENT
                        </span>
                    </div>
                    <h3 class="fw-bold text-white mb-2 d-flex align-items-center gap-2">
                        Purchase Request (Pengadaan Barang)
                    </h3>
                    <p class="mb-0 font-14" style="max-width: 680px; color: #cbd5e1 !important; line-height: 1.6;">
                        Kelola seluruh pengajuan pembelian sparepart dari Sales Order (SO), verifikasi persetujuan (ACC), penerbitan PO Supplier, pelacakan ekspedisi/kurir, hingga penerimaan fisik (Goods Receipt).
                    </p>
                </div>
                <div class="col-lg-4 col-md-12 text-lg-end">
                    <div class="card border-0 shadow-lg text-start d-inline-block" style="background: #ffffff; border-radius: 14px; min-width: 250px; text-align: left;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted font-11 fw-bold text-uppercase" style="letter-spacing: 0.5px;">Total Siklus Berjalan</span>
                                <div class="avatar avatar-sm">
                                    <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                        <i class="mdi mdi-sync-circle font-20 text-primary"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-baseline gap-2 mb-1">
                                <h3 class="fw-bold text-heading mb-0" style="font-size: 1.75rem;">{{ $newCount + $accCount + $poCount + $deliveryCount + $doneCount }}</h3>
                                <span class="text-muted font-12 fw-semibold">Transaksi</span>
                            </div>
                            <div class="d-flex align-items-center gap-1 mt-2 pt-2 border-top">
                                <span class="badge bg-label-success rounded-pill font-11">
                                    <span class="badge-pulse me-1 text-success"></span> Aktif
                                </span>
                                <span class="text-muted font-11">Real-time sync</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5 KPI Metric Stat Cards --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-5 g-3 mb-4">
            {{-- 1. New PR --}}
            <div class="col">
                <div class="kpi-card p-3 h-100 shadow-sm">
                    <div class="kpi-accent-bar bg-primary"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted font-11 fw-bold text-uppercase">1. New PR</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-primary">
                                <i class="mdi mdi-file-plus-outline font-18"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="fw-bold text-heading mb-1 stat-count-new">{{ $newCount }}</h3>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-primary rounded-pill font-11 stat-badge-new">{{ $newCount }}</span>
                        <span class="text-muted font-11">Menunggu ACC</span>
                    </div>
                </div>
            </div>

            {{-- 2. Approved PR --}}
            <div class="col">
                <div class="kpi-card p-3 h-100 shadow-sm">
                    <div class="kpi-accent-bar bg-warning"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted font-11 fw-bold text-uppercase">2. Approved</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-warning">
                                <i class="mdi mdi-clipboard-check-outline font-18"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="fw-bold text-heading mb-1 stat-count-acc">{{ $accCount }}</h3>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-warning rounded-pill font-11 stat-badge-acc">{{ $accCount }}</span>
                        <span class="text-muted font-11">Telah Disetujui</span>
                    </div>
                </div>
            </div>

            {{-- 3. Purchase Order --}}
            <div class="col">
                <div class="kpi-card p-3 h-100 shadow-sm">
                    <div class="kpi-accent-bar bg-secondary"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted font-11 fw-bold text-uppercase">3. Purchase Order</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-secondary">
                                <i class="mdi mdi-file-document-outline font-18"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="fw-bold text-heading mb-1 stat-count-po">{{ $poCount }}</h3>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-secondary rounded-pill font-11 stat-badge-po">{{ $poCount }}</span>
                        <span class="text-muted font-11">PO Terbit</span>
                    </div>
                </div>
            </div>

            {{-- 4. Delivery --}}
            <div class="col">
                <div class="kpi-card p-3 h-100 shadow-sm">
                    <div class="kpi-accent-bar bg-info"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted font-11 fw-bold text-uppercase">4. Delivery</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-info">
                                <i class="mdi mdi-truck-fast-outline font-18"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="fw-bold text-heading mb-1 stat-count-delivery">{{ $deliveryCount }}</h3>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-info rounded-pill font-11 stat-badge-delivery">{{ $deliveryCount }}</span>
                        <span class="text-muted font-11">Dalam Ekspedisi</span>
                    </div>
                </div>
            </div>

            {{-- 5. Done / Goods Receipt --}}
            <div class="col">
                <div class="kpi-card p-3 h-100 shadow-sm">
                    <div class="kpi-accent-bar bg-success"></div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted font-11 fw-bold text-uppercase">5. Good Receipt</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-success">
                                <i class="mdi mdi-check-decagram-outline font-18"></i>
                            </span>
                        </div>
                    </div>
                    <h3 class="fw-bold text-heading mb-1 stat-count-done">{{ $doneCount }}</h3>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-success rounded-pill font-11 stat-badge-done">{{ $doneCount }}</span>
                        <span class="text-muted font-11">Selesai Diterima</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content Card --}}
        <div class="card shadow-sm border-0 mb-4">
            {{-- Top Navigation Pill Tabs --}}
            <div class="card-header border-bottom py-3 px-4 bg-white d-flex flex-wrap justify-content-between align-items-center gap-3">
                @php
                    $isServiceM = (Auth::check() && auth()->user()->role == 'ServiceM');
                @endphp
                <div class="pr-nav-pills" id="purchaseRequestTabs" role="tablist">
                    <button type="button" class="nav-link {{ !$isServiceM ? 'active' : '' }}" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-new" aria-controls="navs-pills-top-new" aria-selected="{{ !$isServiceM ? 'true' : 'false' }}">
                        <i class="mdi mdi-file-plus-outline"></i>
                        <span>New PR</span>
                        <span class="badge bg-danger rounded-pill tab-badge-new font-11" style="{{ @$newCount >= 1 ? '' : 'display:none;' }}">{{ $newCount }}</span>
                    </button>

                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-acc" aria-controls="navs-pills-top-acc" aria-selected="false">
                        <i class="mdi mdi-clipboard-check-outline"></i>
                        <span>Approved</span>
                        <span class="badge bg-warning rounded-pill tab-badge-acc font-11" style="{{ @$accCount >= 1 ? '' : 'display:none;' }}">{{ $accCount }}</span>
                    </button>

                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-po" aria-controls="navs-pills-top-po" aria-selected="false">
                        <i class="mdi mdi-file-document-outline"></i>
                        <span>Process</span>
                        <span class="badge bg-dark rounded-pill tab-badge-po font-11" style="{{ @$poCount >= 1 ? '' : 'display:none;' }}">{{ $poCount }}</span>
                    </button>

                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-delivery" aria-controls="navs-pills-top-delivery" aria-selected="false">
                        <i class="mdi mdi-truck-fast-outline"></i>
                        <span>Delivery</span>
                        <span class="badge bg-info rounded-pill tab-badge-delivery font-11" style="{{ @$deliveryCount >= 1 ? '' : 'display:none;' }}">{{ $deliveryCount }}</span>
                    </button>

                    <button type="button" class="nav-link {{ $isServiceM ? 'active' : '' }}" role="tab" data-bs-toggle="tab"
                        data-bs-target="#navs-pills-top-done" aria-controls="navs-pills-top-done" aria-selected="{{ $isServiceM ? 'true' : 'false' }}">
                        <i class="mdi mdi-check-all"></i>
                        <span>Good Receipt</span>
                        <span class="badge bg-success rounded-pill tab-badge-done font-11" style="{{ @$doneCount >= 1 ? '' : 'display:none;' }}">{{ $doneCount }}</span>
                    </button>
                </div>

                <div class="d-flex align-items-center gap-2">
                    @if(Auth::check() && in_array(Auth::user()->role, ['Developer', 'Admin', 'Super Admin', 'Logistic']))
                        <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-xs fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#modalCreateManualPr">
                            <i class="mdi mdi-plus-circle-outline fs-5"></i>
                            <span>Buat PR Manual</span>
                        </button>
                    @endif
                    <div class="text-muted font-12 d-none d-lg-flex align-items-center gap-1 ms-2">
                        <i class="mdi mdi-information-outline text-primary"></i>
                        <span>Klik tombol <strong class="text-primary">"N item"</strong> untuk rincian barang.</span>
                    </div>
                </div>
            </div>

            {{-- Tab Content Panes --}}
            <div class="tab-content p-0">
                {{-- TAB 1: New Purchase --}}
                <div class="tab-pane fade {{ !$isServiceM ? 'active show' : '' }} p-4" id="navs-pills-top-new" role="tabpanel">
                    <div class="table-responsive border rounded-3">
                        <table class="datatable-purchase-request-new table table-hover align-middle mb-0 w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer / Perusahaan</th>
                                    <th class="text-center" style="min-width: 120px;">Barang &amp; Qty</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th class="text-center" style="min-width: 130px;">Status Pembayaran</th>
                                    <th class="text-center" style="width: 70px;">Sign</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: Approved --}}
                <div class="tab-pane fade p-4" id="navs-pills-top-acc" role="tabpanel">
                    <div class="table-responsive border rounded-3">
                        <table class="datatable-purchase-request-acc table table-hover align-middle mb-0 w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer / Perusahaan</th>
                                    <th class="text-center" style="min-width: 120px;">Barang &amp; Qty</th>
                                    <th>Tanggal Disetujui</th>
                                    <th class="text-center" style="min-width: 130px;">Status Pembayaran</th>
                                    <th class="text-center" style="width: 70px;">Sign</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 3: Purchase Order --}}
                <div class="tab-pane fade p-4" id="navs-pills-top-po" role="tabpanel">
                    <div class="table-responsive border rounded-3">
                        <table class="datatable-purchase-order table table-hover align-middle mb-0 w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>No PO Supplier</th>
                                    <th>No PR Terkait</th>
                                    <th>Sales Order (SO)</th>
                                    <th>Customer / Perusahaan</th>
                                    <th class="text-center" style="min-width: 120px;">Barang &amp; Qty</th>
                                    <th>Tanggal PO</th>
                                    <th class="text-center" style="min-width: 130px;">Status Penerimaan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 4: Delivery --}}
                <div class="tab-pane fade p-4" id="navs-pills-top-delivery" role="tabpanel">
                    <div class="table-responsive border rounded-3">
                        <table class="datatable-purchase-request-delivery table table-hover align-middle mb-0 w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer / Perusahaan</th>
                                    <th class="text-center" style="min-width: 120px;">Barang &amp; Qty</th>
                                    <th>Info Pengiriman (Cargo / Resi)</th>
                                    <th class="text-center" style="width: 70px;">Sign</th>
                                    <th class="text-center" style="min-width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- TAB 5: Good Receipt (Done) --}}
                <div class="tab-pane fade {{ $isServiceM ? 'active show' : '' }} p-4" id="navs-pills-top-done" role="tabpanel">
                    <div class="table-responsive border rounded-3">
                        <table class="datatable-purchase-request-done table table-hover align-middle mb-0 w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>No Goods Receipt (GR)</th>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer / Perusahaan</th>
                                    <th class="text-center" style="min-width: 120px;">Barang &amp; Qty</th>
                                    <th>Tanggal Penerimaan</th>
                                    <th class="text-center" style="width: 70px;">Sign</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sliding Quick Preview Drawer (Offcanvas) for Purchase Request with Blur Effect --}}
        <div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="purchaseRequestOffcanvas" aria-labelledby="purchaseRequestOffcanvasLabel" style="width: 540px; max-width: 92vw;">
            <div class="offcanvas-header bg-label-primary border-bottom py-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary text-white font-11 rounded-pill" id="prDrawerNoPr">PR-0000</span>
                        <div id="prDrawerStatusBadge"></div>
                    </div>
                    <h5 class="offcanvas-title fw-bold text-heading" id="purchaseRequestOffcanvasLabel">Rincian Dokumen Purchase Request</h5>
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
                                <h6 class="fw-bold text-heading font-14 mb-0" id="prDrawerCompany">-</h6>
                            </div>
                            <div class="text-end" id="prDrawerPaymentSlot">
                                {{-- Payment status badge --}}
                            </div>
                        </div>

                        <div class="row g-2 font-12">
                            <div class="col-6">
                                <span class="text-muted d-block font-11">No. Sales Order (SO):</span>
                                <strong class="text-primary font-monospace" id="prDrawerNoPending">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">No. Purchase Order (PO):</span>
                                <strong class="text-heading font-monospace" id="prDrawerNoPo">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Tgl Pengajuan PR:</span>
                                <strong class="text-heading" id="prDrawerDate">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Sales In Charge:</span>
                                <div class="d-flex align-items-center gap-1 mt-1" id="prDrawerSalesBox">
                                    <span class="fw-semibold text-heading" id="prDrawerUserName">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delivery Info Card (if exists) --}}
                <div class="card border mb-3 bg-white" id="prDrawerDeliveryBox" style="display: none;">
                    <div class="card-body p-3">
                        <span class="text-muted font-11 d-block text-uppercase fw-bold mb-2">
                            <i class="mdi mdi-truck-fast-outline text-info me-1"></i> Info Logistik &amp; Pengiriman
                        </span>
                        <div class="row g-2 font-12">
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Ekspedisi / Cargo:</span>
                                <strong class="text-heading" id="prDrawerCargo">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">No. Resi:</span>
                                <strong class="text-heading font-monospace" id="prDrawerResi">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Tgl Pembelian:</span>
                                <strong class="text-heading" id="prDrawerPurchaseDate">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Tipe Pembelian:</span>
                                <strong class="text-heading" id="prDrawerPurchaseType">-</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Approved Action Box in Drawer (Rollback to New PR) --}}
                <div class="card border mb-3 border-warning bg-label-warning bg-opacity-10" id="prDrawerApprovedActionsBox" style="display: none;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="font-11 fw-bold text-uppercase text-dark">
                                <i class="mdi mdi-undo-variant me-1 text-warning"></i> Tindakan Status PR
                            </span>
                        </div>
                        <p class="font-12 text-muted mb-2">
                            Kembalikan status Purchase Request ini dari <strong>Approved</strong> ke <strong>New PR (Draft)</strong> jika perlu revisi barang / alokasi.
                        </p>
                        <div class="d-flex flex-wrap align-items-center gap-2" id="prDrawerApprovedActionButtons">
                            {{-- Dynamically populated: Rollback to New PR --}}
                        </div>
                    </div>
                </div>

                {{-- Delivery Manual GR & Dev Action Box in Drawer --}}
                <div class="card border mb-3 border-warning bg-label-warning bg-opacity-10" id="prDrawerDeliveryActionsBox" style="display: none;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="font-11 fw-bold text-uppercase text-dark">
                                <i class="mdi mdi-cube-send me-1 text-warning"></i> Penyelesaian Pengiriman (GR Manual)
                            </span>
                        </div>
                        <p class="font-12 text-muted mb-2">
                            Jika fisik barang sudah diterima atau diinput manual via menu Barang Masuk (Product In), selesaikan PR ini agar berpindah ke tab Done tanpa menduplikasi stok.
                        </p>
                        <div class="d-flex flex-wrap align-items-center gap-2" id="prDrawerDeliveryActionButtons">
                            {{-- Dynamically populated: Selesaikan GR & Dev Actions --}}
                        </div>
                    </div>
                </div>

                {{-- Goods Receipt Info (if in Done tab) --}}
                <div class="card border mb-3 bg-white" id="prDrawerGrBox" style="display: none;">
                    <div class="card-body p-3">
                        <span class="text-muted font-11 d-block text-uppercase fw-bold mb-2">
                            <i class="mdi mdi-check-decagram-outline text-success me-1"></i> Penerimaan Barang (Goods Receipt)
                        </span>
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted d-block font-11">No. Goods Receipt (GR):</span>
                                <strong class="text-success font-14 font-monospace" id="prDrawerNoGr">-</strong>
                            </div>
                            <div id="prDrawerGrBtnSlot"></div>
                        </div>
                    </div>
                </div>

                {{-- Ordered Items Table --}}
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="font-11 text-muted text-uppercase fw-bold mb-0">Daftar Barang / Sparepart (<span id="prDrawerItemCount">0</span> jenis)</label>
                        <span class="badge bg-label-primary font-11" id="prDrawerTotalPcs">0 item</span>
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
                            <tbody id="prDrawerItemsTbody" class="font-12">
                                {{-- Dynamically populated --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="offcanvas-footer border-top p-3 d-flex align-items-center justify-content-between gap-2">
                <a href="#" id="prDrawerDetailBtn" class="btn btn-primary d-flex align-items-center gap-1 shadow-xs">
                    <i class="mdi mdi-open-in-new me-1"></i> Buka Halaman Lengkap PR
                </a>
                <div class="d-flex align-items-center gap-2" id="prDrawerAdditionalLinks">
                    {{-- Extra links like View PO or SO --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Selesaikan dengan GR Manual --}}
    <div class="modal fade" id="modalManualGr" tabindex="-1" aria-labelledby="modalManualGrLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
                <form id="formManualGr">
                    <input type="hidden" id="manualGrPrId" name="pr_id" value="">

                    {{-- Modal Header --}}
                    <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar avatar-md flex-shrink-0">
                                <span class="avatar-initial rounded-3 bg-label-success shadow-xs">
                                    <i class="mdi mdi-cube-send font-22"></i>
                                </span>
                            </div>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0" id="modalManualGrLabel">Selesaikan dengan GR Manual</h5>
                                <small class="text-muted font-12">
                                    Konfirmasi penyelesaian penerimaan fisik untuk <span class="fw-semibold text-primary font-monospace" id="modalManualGrNoPr">-</span>
                                </small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="modal-body p-4">
                        {{-- System Notice Card --}}
                        <div class="alert alert-warning border-0 d-flex align-items-start gap-2 mb-4 py-3 px-3 rounded-3" style="background: rgba(255, 171, 0, 0.08); border-left: 4px solid #ffab00 !important;">
                            <i class="mdi mdi-shield-alert-outline text-warning fs-4 mt-0 flex-shrink-0"></i>
                            <div class="font-12 text-dark">
                                <span class="fw-bold d-block mb-1 text-warning text-uppercase font-11">Pemberitahuan Sistem:</span>
                                Gunakan opsi ini jika dokumen Barang Masuk sudah diinput secara manual di gudang. Purchase Request akan dialihkan ke status <strong>Done</strong> dan PO terkait menjadi <strong>Received</strong> <em>tanpa penambahan stok fisik ganda (mencegah double stock)</em>.
                            </div>
                        </div>

                        {{-- Section 1: Tautkan Dokumen Product In --}}
                        <div class="card border rounded-3 mb-4 bg-light bg-opacity-25 shadow-none">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label font-12 fw-bold text-dark mb-0 d-flex align-items-center gap-1">
                                        <i class="mdi mdi-link-variant text-primary font-16"></i>
                                        <span>Tautkan Dokumen Barang Masuk (Product In)</span>
                                    </label>
                                    <span class="badge bg-label-primary font-10 text-uppercase fw-bold">Rekomendasi</span>
                                </div>
                                <div class="mb-2">
                                    <select id="manualGrSelectProductIn" class="form-select form-select-sm" style="width: 100%;">
                                        <option value="">-- Cari No. Barang Masuk / DO / Surat Jalan / Supplier --</option>
                                    </select>
                                </div>
                                <div class="font-11 text-muted d-flex align-items-center gap-1 mt-1">
                                    <i class="mdi mdi-information-outline text-info"></i>
                                    <span>Menghubungkan dokumen akan mengaktifkan tombol <strong>"Buka Barang Masuk"</strong> langsung di tab Good Receipt (Done).</span>
                                </div>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="d-flex align-items-center my-3">
                            <hr class="flex-grow-1 my-0 text-muted">
                            <span class="px-3 font-11 text-uppercase text-muted fw-bold">Detail Dokumen Penerimaan</span>
                            <hr class="flex-grow-1 my-0 text-muted">
                        </div>

                        {{-- Section 2: Form Fields --}}
                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label class="form-label font-12 fw-semibold text-dark mb-1">
                                    No. Goods Receipt (GR) / Surat Jalan Vendor <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge shadow-none">
                                    <span class="input-group-text bg-white"><i class="mdi mdi-file-document-outline text-muted"></i></span>
                                    <input type="text" id="manualGrNoGr" class="form-control" placeholder="Contoh: 011-P/BM/IX/2026 atau No. Surat Jalan" required>
                                </div>
                                <div class="form-text font-11 text-muted">Otomatis terisi jika memilih Dokumen Barang Masuk di atas.</div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label font-12 fw-semibold text-dark mb-1">
                                    Tanggal Penerimaan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge shadow-none">
                                    <span class="input-group-text bg-white"><i class="mdi mdi-calendar-check-outline text-muted"></i></span>
                                    <input type="date" id="manualGrDate" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-1">
                            <label class="form-label font-12 fw-semibold text-dark mb-1">
                                Catatan Penyelesaian <span class="text-muted fw-normal">(Opsional)</span>
                            </label>
                            <textarea id="manualGrNote" class="form-control" rows="2" placeholder="Tuliskan keterangan singkat penerimaan / kondisi barang jika ada..."></textarea>
                        </div>
                    </div>

                    {{-- Modal Footer --}}
                    <div class="modal-footer border-top bg-light bg-opacity-25 px-4 py-3 d-flex justify-content-between">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                            <i class="mdi mdi-close me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-success d-flex align-items-center gap-1 shadow-xs" id="manualGrSubmitBtn">
                            <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>
                            <span>Selesaikan Sekarang</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Buat Purchase Request Manual --}}
    @if(Auth::check() && in_array(Auth::user()->role, ['Developer', 'Admin', 'Super Admin', 'Logistic']))
    <div class="modal fade" id="modalCreateManualPr" tabindex="-1" aria-labelledby="modalCreateManualPrLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                <div class="modal-header py-3 px-4 text-white" style="background: linear-gradient(135deg, #1e2640 0%, #2a3558 100%);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-white rounded-circle p-1 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-file-document-plus-outline fs-5 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0" id="modalCreateManualPrLabel">Buat Purchase Request Manual</h5>
                            <span class="font-11 text-white-50">Pengadaan sparepart internal / restock stok gudang tanpa Quotation</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('purchase-request.store-manual') }}" method="POST" id="formCreateManualPr">
                    @csrf
                    <div class="modal-body p-4">
                        {{-- Section 1: Info Dasar --}}
                        <div class="card border mb-4 shadow-none bg-light">
                            <div class="card-body p-3">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6 col-sm-12">
                                        <label class="form-label fw-semibold small text-dark d-flex align-items-center justify-content-between">
                                            <span>Pilih No. Sales Order (SO) Terdaftar</span>
                                            <span class="badge bg-label-primary font-10">Opsional / Terdaftar</span>
                                        </label>
                                        <select name="id_pending" id="manualPrSelectSo" class="form-select select2-manual-so" style="width: 100%;">
                                            <option value="">-- Non-SO (Pengadaan Internal / Restock Gudang) --</option>
                                        </select>
                                        <div class="form-text font-11 text-muted">
                                            Ketik No. SO atau nama customer. Kosongkan jika pengadaan internal umum tanpa SO.
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <label class="form-label fw-semibold small text-dark">Judul / Keperluan Pengadaan <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="manualPrTitle" class="form-control" placeholder="Contoh: Restock Stok Gudang / Sparepart Mesin Workshop" required value="Pengadaan Internal / Restock Gudang">
                                        <div class="form-text font-11 text-muted">
                                            Keterangan tujuan pengadaan barang / kebutuhan proyek.
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6 col-sm-6">
                                        <label class="form-label fw-semibold small text-dark">Tanggal Pengajuan <span class="text-danger">*</span></label>
                                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <label class="form-label fw-semibold small text-dark">Status Awal PR</label>
                                        <select name="status" class="form-select">
                                            <option value="1" selected>Langsung Approved (Siap PO)</option>
                                            <option value="0">Draft (New PR)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Daftar Item Barang --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-1">
                                    <i class="mdi mdi-format-list-bulleted text-primary"></i>
                                    <span>Daftar Sparepart / Barang yang Diajukan</span>
                                </h6>
                                <span class="text-muted font-11">Pilih sparepart dari master data menggunakan pencarian part number / nama barang</span>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm fw-semibold" id="btnAddManualPrItem">
                                <i class="mdi mdi-plus me-1"></i> Tambah Baris Item
                            </button>
                        </div>

                        <div class="table-responsive border rounded-3 mb-2">
                            <table class="table table-bordered table-sm align-middle mb-0" id="tableManualPrItems">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;" class="text-center">No</th>
                                        <th style="min-width: 320px;">Pilih Sparepart / Equivalent <span class="text-danger">*</span></th>
                                        <th style="width: 110px;" class="text-center">Qty <span class="text-danger">*</span></th>
                                        <th style="min-width: 180px;">Keterangan / Note Item</th>
                                        <th style="width: 50px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyManualPrItems">
                                    <tr class="manual-pr-row">
                                        <td class="text-center fw-bold row-no">1</td>
                                        <td>
                                            <select name="items[0][id_equivalent]" class="form-select select2-manual-equiv" required style="width: 100%;">
                                                <option value="">-- Ketik Part Number / Nama Barang --</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][qty]" class="form-control text-center item-qty" min="0.1" step="any" value="1" required>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][note]" class="form-control" placeholder="Catatan item (opsional)">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-remove-manual-row" disabled>
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted font-11">
                            <i class="mdi mdi-information-outline text-info me-1"></i>
                            Anda dapat menambahkan beberapa item sparepart sekaligus dalam satu nomor PR.
                        </small>
                    </div>

                    <div class="modal-footer bg-light py-2 px-4 d-flex justify-content-between">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-xs fw-semibold px-4" id="btnSubmitManualPr">
                            <i class="mdi mdi-check-circle-outline fs-5"></i>
                            <span>Simpan Purchase Request</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script>
        window.isDeveloper = {{ Auth::user() && (Auth::user()->isDeveloper() || Auth::user()->getRawOriginal('role') === 'Developer') ? 'true' : 'false' }};
        window.canManualGr = {{ Auth::user() && (in_array(Auth::user()->getRawOriginal('role'), ['Logistic', 'Admin', 'Super Admin', 'Developer']) || Auth::user()->isDeveloper()) ? 'true' : 'false' }};
        window.csrfToken = "{{ csrf_token() }}";
    </script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/includes/table-purchase-request.js?v={{ time() }}"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();

            // ── Inisialisasi Select2 Autocomplete untuk Pencarian Sales Order (SO) ──
            function initManualSoSelect2() {
                var $soSelect = $('#manualPrSelectSo');
                if ($soSelect.hasClass('select2-hidden-accessible')) {
                    return;
                }

                $soSelect.select2({
                    dropdownParent: $('#modalCreateManualPr'),
                    placeholder: '-- Cari No. SO / Customer (Kosongkan jika Non-SO) --',
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: '/db/sales-order/search-to-link',
                        dataType: 'json',
                        delay: 300,
                        data: function (params) {
                            return { q: params.term };
                        },
                        processResults: function (data) {
                            var items = data && data.data ? data.data : [];
                            return {
                                results: $.map(items, function (so) {
                                    return {
                                        id: so.id,
                                        text: so.text,
                                        no_pending: so.no_pending,
                                        company: so.company
                                    };
                                })
                            };
                        }
                    }
                });

                $soSelect.on('select2:select', function(e) {
                    var data = e.params.data;
                    if (data && data.no_pending) {
                        var defaultTitle = 'Pengadaan Internal / Restock Gudang';
                        var currentTitle = $('#manualPrTitle').val().trim();
                        if (!currentTitle || currentTitle === defaultTitle || currentTitle.indexOf('Pengadaan PR untuk SO:') === 0) {
                            var comp = data.company ? ' (' + data.company + ')' : '';
                            $('#manualPrTitle').val('Pengadaan PR untuk SO: ' + data.no_pending + comp);
                        }
                    }
                });

                $soSelect.on('select2:clear', function() {
                    var currentTitle = $('#manualPrTitle').val().trim();
                    if (currentTitle.indexOf('Pengadaan PR untuk SO:') === 0) {
                        $('#manualPrTitle').val('Pengadaan Internal / Restock Gudang');
                    }
                });
            }

            // ── Inisialisasi Select2 Autocomplete untuk PR Manual ──
            function initManualPrSelect2($element) {
                $element.select2({
                    dropdownParent: $('#modalCreateManualPr'),
                    placeholder: '-- Ketik Part Number / Nama Barang --',
                    allowClear: true,
                    width: '100%',
                    minimumInputLength: 1,
                    ajax: {
                        url: '/db/equivalent/search',
                        dataType: 'json',
                        delay: 300,
                        data: function (params) {
                            return { q: params.term };
                        },
                        processResults: function (data) {
                            var items = Array.isArray(data) ? data : (data.data || []);
                            return {
                                results: $.map(items, function (eq) {
                                    var id = eq.id_equivalent || eq.id;
                                    var pnBrand = (eq.brand ? eq.brand + ' ' : '') + (eq.pn ? eq.pn : '');
                                    var name = eq.product_name ? ' — ' + eq.product_name : '';
                                    var gen = eq.genuine_status === 'Replacement' ? ' (R)' : ' (G)';
                                    var text = pnBrand + name + gen;
                                    return {
                                        id: id,
                                        text: text.trim() || ('Item #' + id)
                                    };
                                })
                            };
                        }
                    }
                });
            }

            var manualRowIdx = 1;
            $('#btnAddManualPrItem').on('click', function() {
                var rowCount = $('#tbodyManualPrItems tr').length;
                var newRow = `
                    <tr class="manual-pr-row">
                        <td class="text-center fw-bold row-no">${rowCount + 1}</td>
                        <td>
                            <select name="items[${manualRowIdx}][id_equivalent]" class="form-select select2-manual-equiv" required style="width: 100%;">
                                <option value="">-- Ketik Part Number / Nama Barang --</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" name="items[${manualRowIdx}][qty]" class="form-control text-center item-qty" min="0.1" step="any" value="1" required>
                        </td>
                        <td>
                            <input type="text" name="items[${manualRowIdx}][note]" class="form-control" placeholder="Catatan item (opsional)">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-icon btn-sm btn-outline-danger btn-remove-manual-row">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </td>
                    </tr>
                `;
                var $newRowEl = $(newRow);
                $('#tbodyManualPrItems').append($newRowEl);
                initManualPrSelect2($newRowEl.find('.select2-manual-equiv'));
                manualRowIdx++;
                updateManualPrRowNumbers();
            });

            $(document).on('click', '.btn-remove-manual-row', function() {
                if ($('#tbodyManualPrItems tr').length > 1) {
                    $(this).closest('tr').remove();
                    updateManualPrRowNumbers();
                }
            });

            function updateManualPrRowNumbers() {
                $('#tbodyManualPrItems tr').each(function(index) {
                    $(this).find('.row-no').text(index + 1);
                    if ($('#tbodyManualPrItems tr').length === 1) {
                        $(this).find('.btn-remove-manual-row').prop('disabled', true);
                    } else {
                        $(this).find('.btn-remove-manual-row').prop('disabled', false);
                    }
                });
            }

            $('#modalCreateManualPr').on('shown.bs.modal', function () {
                initManualSoSelect2();
                if (!$('#tbodyManualPrItems .select2-manual-equiv').hasClass('select2-hidden-accessible')) {
                    initManualPrSelect2($('#tbodyManualPrItems .select2-manual-equiv'));
                }
            });
        });
    </script>
@endpush
