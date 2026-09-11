@extends('layouts.sales.app')
@section('title', 'Sales Urgent Order (SUO)')
@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Sales /</span> Sales Urgent Order (SUO)
            </h4>
            <p class="text-muted mb-0 small">
                Kelola antrean Sales Urgent Order, pantau proses pengeluaran barang gudang, dan konversi ke invoice penawaran.
            </p>
        </div>
        <div>
            <a href="{{ route('unit-quotation.index') }}" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                <i class="mdi mdi-plus fs-5"></i> Buat SUO Baru
            </a>
        </div>
    </div>

    {{-- Metrics Summary Cards (KPI Cards) --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm suo-kpi-card active-kpi" data-kpi-target="all" style="cursor: pointer;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-pill bg-label-primary p-2">
                            <i class="mdi mdi-view-list-outline fs-5"></i>
                        </span>
                        <span class="text-muted small fw-semibold">Semua</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-primary" id="kpi-count-all">0</h3>
                    <small class="text-muted" style="font-size: 11px;">Total semua SUO</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm suo-kpi-card" data-kpi-target="submitted" style="cursor: pointer;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-pill bg-label-warning p-2">
                            <i class="mdi mdi-warehouse fs-5"></i>
                        </span>
                        <span class="text-muted small fw-semibold">Gudang</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-warning" id="kpi-count-submitted">0</h3>
                    <small class="text-muted" style="font-size: 11px;">Menunggu konfirmasi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm suo-kpi-card" data-kpi-target="confirmed" style="cursor: pointer;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-pill bg-label-info p-2">
                            <i class="mdi mdi-package-check fs-5"></i>
                        </span>
                        <span class="text-muted small fw-semibold">Dikonfirmasi</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-info" id="kpi-count-confirmed">0</h3>
                    <small class="text-muted" style="font-size: 11px;">Stok tersedia</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm suo-kpi-card" data-kpi-target="goods_out" style="cursor: pointer;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-pill bg-label-primary p-2">
                            <i class="mdi mdi-truck-fast-outline fs-5"></i>
                        </span>
                        <span class="text-muted small fw-semibold">Barang Keluar</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-primary" id="kpi-count-goods_out">0</h3>
                    <small class="text-muted" style="font-size: 11px;">Surat jalan terbit</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card border-0 shadow-sm suo-kpi-card" data-kpi-target="converted" style="cursor: pointer;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge rounded-pill bg-label-success p-2">
                            <i class="mdi mdi-check-decagram-outline fs-5"></i>
                        </span>
                        <span class="text-muted small fw-semibold">Invoice Terbit</span>
                    </div>
                    <h3 class="fw-bold mb-0 text-success" id="kpi-count-converted">0</h3>
                    <small class="text-muted" style="font-size: 11px;">Dikonversi ke Invoice</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Card with Status Tabs & Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0 flex-nowrap overflow-auto" id="suo-status-tabs" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active py-2 px-3 fw-semibold" data-status="all" role="tab">
                        <i class="mdi mdi-view-list-outline me-1"></i>Semua Data SUO
                        <span class="badge rounded-pill bg-label-primary ms-1" id="badge-tab-all">0</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-2 px-3 fw-semibold" data-status="submitted" role="tab">
                        <i class="mdi mdi-warehouse me-1"></i>Menunggu Gudang
                        <span class="badge rounded-pill bg-label-warning ms-1" id="badge-tab-submitted">0</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-2 px-3 fw-semibold" data-status="confirmed" role="tab">
                        <i class="mdi mdi-package-check me-1"></i>Stok Dikonfirmasi
                        <span class="badge rounded-pill bg-label-info ms-1" id="badge-tab-confirmed">0</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-2 px-3 fw-semibold" data-status="goods_out" role="tab">
                        <i class="mdi mdi-truck-fast-outline me-1"></i>Barang Keluar
                        <span class="badge rounded-pill bg-label-primary ms-1" id="badge-tab-goods_out">0</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link py-2 px-3 fw-semibold" data-status="converted" role="tab">
                        <i class="mdi mdi-check-decagram-outline me-1"></i>Sudah Dikonversi ke Invoice
                        <span class="badge rounded-pill bg-label-success ms-1" id="badge-tab-converted">0</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-datatable table-responsive pt-3 pb-3">
            @php
                $isSuoManager = in_array(Auth::user()->role, ['Admin', 'Sales Manager']);
            @endphp
            <table class="datatable-suo-sales table table-hover border-top" id="datatable-suo-sales-table" data-is-manager="{{ $isSuoManager ? '1' : '0' }}" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>No. SUO</th>
                        <th>Tanggal</th>
                        <th>Company</th>
                        <th>PIC</th>
                        <th class="text-center">Status</th>
                        <th>No. Invoice Booking</th>
                        <th class="text-center">Sales</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css"/>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css"/>
    <style>
        .suo-kpi-card {
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }
        .suo-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.35rem 1rem rgba(0, 0, 0, 0.08) !important;
        }
        .suo-kpi-card.active-kpi {
            border: 2px solid var(--bs-primary) !important;
            background: rgba(105, 108, 255, 0.04);
        }
        #suo-status-tabs .nav-link {
            color: #566a7f;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        #suo-status-tabs .nav-link:hover {
            color: var(--bs-primary);
        }
        #suo-status-tabs .nav-link.active {
            color: var(--bs-primary);
            border-bottom: 2px solid var(--bs-primary);
            background: transparent;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-suo-sales.js?v={{ file_exists(public_path('assets/includes/table-suo-sales.js')) ? filemtime(public_path('assets/includes/table-suo-sales.js')) : time() }}"></script>
@endpush
