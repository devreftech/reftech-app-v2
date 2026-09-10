@extends('layouts.sales.app')
@section('title', 'Purchase Order')
@section('content')
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <div class="d-flex align-items-center justify-content-between py-3 mb-2 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold m-0 text-dark">Purchase Order</h4>
                <p class="text-muted small mb-0">Kelola dan pantau seluruh PO ke vendor beserta status penerimaan &amp; invoice</p>
            </div>
            <a href="{{ route('purchase.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus me-1"></i>Buat Purchase Order
            </a>
        </div>

        {{-- Stat summary cards (dihitung dari data tabel di sisi klien) --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
            <div class="col">
                <div class="card h-100 border-0 custom-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase tracking-wider">Total PO</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                    <i class="mdi mdi-cart-outline mdi-20px"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="mb-2 fw-bold text-dark" id="po-stat-total">0</h4>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-label-primary rounded-pill fw-semibold" id="po-stat-total-badge">0</span>
                            <span class="text-muted small">PO terdaftar</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 custom-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase tracking-wider">Total Nilai</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-3 bg-label-success shadow-xs">
                                    <i class="mdi mdi-cash-multiple mdi-20px"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="mb-2 fw-bold text-dark" id="po-stat-value">Rp 0</h4>
                        <div class="d-flex align-items-center gap-1">
                            <span class="text-muted small">Akumulasi seluruh PO</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 custom-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase tracking-wider">Bulan Ini</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-3 bg-label-info shadow-xs">
                                    <i class="mdi mdi-calendar-month-outline mdi-20px"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="mb-2 fw-bold text-dark" id="po-stat-month">0</h4>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-label-info rounded-pill fw-semibold" id="po-stat-month-value">Rp 0</span>
                            <span class="text-muted small">nilai bulan berjalan</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100 border-0 custom-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase tracking-wider">Belum Diterima</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-3 bg-label-warning shadow-xs">
                                    <i class="mdi mdi-truck-alert-outline mdi-20px"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="mb-2 fw-bold text-dark" id="po-stat-pending">0</h4>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-label-warning rounded-pill fw-semibold" id="po-stat-noinvoice">0</span>
                            <span class="text-muted small">belum ada invoice</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card custom-main-card mb-4">
            <div class="card-header py-2 bg-transparent border-bottom">
                <ul class="nav nav-tabs card-header-tabs border-0 m-0" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active px-3 py-2 fw-semibold" data-po-filter="all">
                            <i class="mdi mdi-format-list-bulleted me-1"></i>Semua
                            <span class="badge bg-secondary rounded-pill ms-1" data-po-count="all">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link px-3 py-2 fw-semibold" data-po-filter="pending">
                            <i class="mdi mdi-truck-alert-outline me-1"></i>Belum Diterima
                            <span class="badge bg-warning rounded-pill ms-1" data-po-count="pending">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link px-3 py-2 fw-semibold" data-po-filter="received">
                            <i class="mdi mdi-check-all me-1"></i>Sudah Diterima
                            <span class="badge bg-success rounded-pill ms-1" data-po-count="received">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link px-3 py-2 fw-semibold" data-po-filter="noinvoice">
                            <i class="mdi mdi-file-remove-outline me-1"></i>Tanpa Invoice
                            <span class="badge bg-danger rounded-pill ms-1" data-po-count="noinvoice">0</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-datatable table-responsive pt-0 p-3">
                <table class="datatable-purchase-order table table-bordered">
                    <thead>
                        <tr>
                            <th>No PO</th>
                            <th>Company / Vendor</th>
                            <th>Kategori</th>
                            <th>Sumber</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Date</th>
                            <th>Payment</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
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
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />

    <style>
        .custom-stat-card,
        .custom-main-card,
        .card {
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.06), 0 0 1px 0 rgba(67, 89, 113, 0.15);
            border-radius: 0.75rem !important;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .custom-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px 0 rgba(67, 89, 113, 0.1);
        }

        .card-header-tabs .nav-link {
            color: #64748b;
            border: none !important;
            border-bottom: 2px solid transparent !important;
            border-radius: 0 !important;
            transition: all 0.2s ease;
            background-color: transparent !important;
        }

        .card-header-tabs .nav-link:hover {
            color: #3b82f6;
        }

        .card-header-tabs .nav-link.active {
            color: #2563eb !important;
            border-bottom: 2px solid #2563eb !important;
        }

        .datatable-purchase-order td {
            font-size: 0.875rem;
            vertical-align: middle;
        }

        .po-vendor-name {
            font-weight: 600;
        }

        .po-sub {
            font-size: 0.75rem;
            color: #94a3b8;
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
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/includes/table-purchase-order.js?v={{ time() }}"></script>
@endpush

@push('script')
@endpush
