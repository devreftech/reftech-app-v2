@extends('layouts.sales.app')
@section('title', 'Purchase Order')
@section('content')
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <div class="d-flex align-items-center justify-content-between py-3 mb-2 flex-wrap gap-2">
            <div>
                <h4 class="fw-bold m-0 text-dark">Purchase Order &amp; Direct Purchase</h4>
                <p class="text-muted small mb-0">Kelola dan pantau seluruh pengadaan vendor (PO Resmi &amp; Direct Purchase) beserta status pengiriman &amp; penerimaan</p>
            </div>
            <div class="btn-group">
                <button type="button" class="btn btn-primary dropdown-toggle shadow-xs" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="mdi mdi-plus me-1"></i> Buat Pembelian
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 280px;">
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('purchase.create') }}">
                            <i class="mdi mdi-file-document-outline me-2 text-primary fs-5"></i>
                            <div>
                                <div class="fw-bold text-dark">Purchase Order (Resmi)</div>
                                <small class="text-muted">Form lengkap dengan invoice, PPN, &amp; dokumen PO</small>
                            </div>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item py-2 d-flex align-items-center" href="{{ route('purchase.direct-create') }}">
                            <i class="mdi mdi-cart-arrow-down me-2 text-teal fs-5" style="color: #0d9488;"></i>
                            <div>
                                <div class="fw-bold text-dark">Direct Purchase (Beli Langsung)</div>
                                <small class="text-muted">Form ringkas untuk toko/marketplace/retail</small>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Stat summary cards (dihitung dari data tabel di sisi klien) --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mb-4">
            <div class="col">
                <div class="card h-100 border-0 custom-stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fw-semibold small text-uppercase tracking-wider">Total Transaksi</span>
                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                    <i class="mdi mdi-cart-outline mdi-20px"></i>
                                </span>
                            </div>
                        </div>
                        <h4 class="mb-2 fw-bold text-dark" id="po-stat-total">0</h4>
                        <div class="d-flex align-items-center gap-1">
                            <span class="badge bg-label-primary rounded-pill fw-semibold" id="po-stat-total-badge">0</span>
                            <span class="text-muted small">Transaksi terdaftar</span>
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
                        <button type="button" class="nav-link px-3 py-2 fw-semibold" data-po-filter="po_formal">
                            <i class="mdi mdi-file-document-outline me-1"></i>PO Resmi
                            <span class="badge bg-primary rounded-pill ms-1" data-po-count="po_formal">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link px-3 py-2 fw-semibold" data-po-filter="direct">
                            <i class="mdi mdi-cart-arrow-down me-1"></i>Direct Purchase
                            <span class="badge bg-info rounded-pill ms-1" data-po-count="direct">0</span>
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
                            <th>Item</th>
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

        {{-- Sliding Quick Preview Drawer (Offcanvas) for Purchase Order with Blur Effect --}}
        <div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="purchaseOrderOffcanvas" aria-labelledby="purchaseOrderOffcanvasLabel" style="width: 560px; max-width: 94vw;">
            <div class="offcanvas-header bg-label-primary border-bottom py-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge bg-primary text-white font-11 rounded-pill" id="poDrawerNoPo">PO-0000</span>
                        <div id="poDrawerStatusBadge"></div>
                    </div>
                    <h5 class="offcanvas-title fw-bold text-heading" id="purchaseOrderOffcanvasLabel">Rincian Dokumen Purchase Order</h5>
                </div>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body p-4">
                {{-- Document Summary Card --}}
                <div class="card border mb-3 bg-light">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
                            <div>
                                <span class="text-muted font-11 d-block text-uppercase fw-bold">Company / Vendor:</span>
                                <h6 class="fw-bold text-heading font-14 mb-0" id="poDrawerCompany">-</h6>
                            </div>
                            <div class="text-end" id="poDrawerPaymentSlot">
                                {{-- Payment status / type badge --}}
                            </div>
                        </div>

                        <div class="row g-2 font-12">
                            <div class="col-6">
                                <span class="text-muted d-block font-11">No. Purchase Order:</span>
                                <strong class="text-primary font-monospace" id="poDrawerSummaryNoPo">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Tanggal PO:</span>
                                <strong class="text-heading" id="poDrawerDate">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Total Nilai PO:</span>
                                <strong class="text-success font-14" id="poDrawerTotal">-</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">ATTN / Kontak:</span>
                                <strong class="text-heading" id="poDrawerAttn">-</strong>
                            </div>
                            <div class="col-6" id="poDrawerPrCol" style="display: none;">
                                <span class="text-muted d-block font-11">Terkait Purchase Request:</span>
                                <strong class="text-info font-monospace" id="poDrawerNoPr">-</strong>
                            </div>
                            <div class="col-6" id="poDrawerCategoryCol">
                                <span class="text-muted d-block font-11">Kategori:</span>
                                <span id="poDrawerCategoryBadge">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Penerimaan & Dokumen Card --}}
                <div class="card border mb-3 bg-white">
                    <div class="card-body p-3">
                        <span class="text-muted font-11 d-block text-uppercase fw-bold mb-2">
                            <i class="mdi mdi-truck-delivery-outline text-primary me-1"></i> Status Penerimaan &amp; Dokumen
                        </span>
                        <div class="row g-2 font-12">
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Status Barang:</span>
                                <div id="poDrawerReceiptBadge" class="mt-1"></div>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">No. Goods Receipt (GR):</span>
                                <div id="poDrawerGrBox" class="mt-1">
                                    <strong class="text-heading font-monospace" id="poDrawerNoGr">-</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Invoice Supplier:</span>
                                <div id="poDrawerInvoiceBox" class="mt-1">-</div>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block font-11">Tanda Tangan Vendor:</span>
                                <div id="poDrawerSignBox" class="mt-1">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ordered Items Table --}}
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="font-11 text-muted text-uppercase fw-bold mb-0">Daftar Barang / Item (<span id="poDrawerItemCount">0</span> jenis)</label>
                        <span class="badge bg-label-primary font-11" id="poDrawerTotalPcs">0 item</span>
                    </div>
                    <div class="table-responsive border rounded-3 bg-white">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr class="font-11 text-muted text-uppercase">
                                    <th style="width: 30px;" class="text-center">#</th>
                                    <th>Nama Barang &amp; Spesifikasi</th>
                                    <th class="text-center" style="width: 90px;">Kuantitas</th>
                                    <th class="text-end" style="width: 120px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="poDrawerItemsTbody" class="font-12">
                                {{-- Dynamically populated --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="offcanvas-footer border-top p-3 d-flex align-items-center justify-content-between gap-2">
                <a href="#" id="poDrawerDetailBtn" class="btn btn-primary d-flex align-items-center gap-1 shadow-xs">
                    <i class="mdi mdi-open-in-new me-1"></i> Buka Halaman Lengkap PO
                </a>
                <div class="d-flex align-items-center gap-2" id="poDrawerAdditionalLinks">
                    {{-- Dynamically populated --}}
                </div>
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

        .datatable-purchase-order td.po-col-payment {
            white-space: normal !important;
            max-width: 180px;
            min-width: 130px;
        }

        .datatable-purchase-order td.po-col-payment .badge {
            white-space: normal !important;
            word-break: break-word;
            line-height: 1.35 !important;
            text-align: left;
        }

        .po-vendor-name {
            font-weight: 600;
        }

        .po-sub {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        .btn-item-preview {
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.78rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
        }

        .btn-item-preview:hover {
            background-color: #e0e7ff;
            color: #3730a3;
            border-color: #a5b4fc;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(99, 102, 241, 0.15);
        }

        .btn-item-preview:hover i,
        .btn-item-preview:hover span {
            color: #4338ca;
        }

        .offcanvas-backdrop {
            backdrop-filter: blur(4px);
            -webkit-backdrop-filter: blur(4px);
            background-color: rgba(15, 23, 42, 0.35) !important;
        }

        .offcanvas-backdrop.show {
            opacity: 1 !important;
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
