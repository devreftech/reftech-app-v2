@extends('layouts.sales.app')
@section('title', 'My Quotation')
@section('content')
    <div class="card mb-4">
        <div class="card-widget-separator-wrapper">
            <div class="card-body card-widget-separator">
                <div class="row gy-4 gy-sm-1">
                    <div class="col-sm-6 col-lg-3">
                        <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                            <div>
                                <p class="mb-2">Quotation</p>
                                <h4 class="mb-2">Rp
                                    {{ number_format(Auth::user()->role == 'Admin' ? $forecastAdmin : $forecast, 2, ',', '.') }}
                                </h4>
                                <p class="mb-0"><span
                                        class="badge rounded-pill bg-label-success">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->whereIn('status', ['20', '30', '40', '60', '80'])->count() }}</span>
                                </p>
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
                        <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                            <div>
                                <p class="mb-2">Hot Prospect</p>
                                <h4 class="mb-2">Rp
                                    {{ number_format(Auth::user()->role == 'Admin' ? $prospectAdmin : $prospect, 2, ',', '.') }}
                                </h4>
                                <p class="mb-0"><span
                                        class="badge rounded-pill bg-label-success">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '80')->count() }}</span>
                                </p>
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
                        <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                            <div>
                                <p class="mb-2">Purchase Order</p>
                                <h4 class="mb-2">Rp
                                    {{ number_format(Auth::user()->role == 'Admin' ? $poAdmin : $po, 2, ',', '.') }}</h4>
                                <p class="mb-0"><span
                                        class="badge rounded-pill bg-label-success">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '100')->count() }}</span>
                                </p>
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
                                <p class="mb-2">Loss Order</p>
                                <h4 class="mb-2">Rp
                                    {{ number_format(Auth::user()->role == 'Admin' ? $lossAdmin : $loss, 2, ',', '.') }}
                                </h4>
                                <p class="mb-0"><span
                                        class="badge rounded-pill bg-label-danger">{{ (Auth::user()->role == 'Admin' ? $quotationAdmin : $quotation)->where('status', '0')->count() }}</span>
                                </p>
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
    @if (Auth::user()->role !== 'Admin')
    {{-- ── SALES: Tabbed view ─────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="quotation-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-quotation" type="button">
                        <i class="mdi mdi-file-document-outline me-1"></i>Quotation
                        <span class="badge rounded-pill bg-danger ms-1" id="badge-quotation">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-unit-quotation" type="button">
                        <i class="mdi mdi-file-document-outline me-1"></i>Quotation Unit
                        <span class="badge rounded-pill bg-danger ms-1" id="badge-unit-quotation">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-hot" type="button">
                        <i class="mdi mdi-fire me-1"></i>Hot Prospect
                        <span class="badge rounded-pill bg-danger ms-1" id="badge-hot">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-po" type="button">
                        <i class="mdi mdi-cart-check me-1"></i>Purchase Order
                        <span class="badge rounded-pill bg-danger ms-1" id="badge-po">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-loss" type="button">
                        <i class="mdi mdi-close-circle-outline me-1"></i>Loss
                        <span class="badge rounded-pill bg-danger ms-1" id="badge-loss">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-archive" type="button">
                        <i class="mdi mdi-archive-outline me-1"></i>Archive
                        <span class="badge rounded-pill bg-danger ms-1" id="badge-archive">-</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content">

                {{-- Tab 1: Quotation --}}
                <div class="tab-pane fade show active" id="tab-quotation">
                    <div class="table-responsive">
                        <table class="datatable-quotation table table-bordered" data-badge="badge-quotation">
                            <thead>
                                <tr>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Tab 2: Hot Prospect --}}
                <div class="tab-pane fade" id="tab-hot">
                    <div class="table-responsive">
                        <table class="datatable-hot-prospect table table-bordered" data-badge="badge-hot">
                            <thead>
                                <tr>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Tab 3: Purchase Order --}}
                <div class="tab-pane fade" id="tab-po">
                    <div class="table-responsive">
                        <table class="datatable-po-quote table table-bordered" data-badge="badge-po">
                            <thead>
                                <tr>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th>Date PO</th>
                                    <th>PO Number</th>
                                    <th>Invoice Number</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Tab 4: Loss --}}
                <div class="tab-pane fade" id="tab-loss">
                    <div class="table-responsive">
                        <table class="datatable-loss-quote table table-bordered" data-badge="badge-loss">
                            <thead>
                                <tr>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Tab 5: Archive --}}
                <div class="tab-pane fade" id="tab-archive">
                    <div class="table-responsive">
                        <table class="datatable-quotation-archive table table-bordered" data-badge="badge-archive">
                            <thead>
                                <tr>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Tab 6: Penawaran Unit --}}
                <div class="tab-pane fade" id="tab-unit-quotation">
                    <div class="table-responsive">
                        <table class="datatable-unit-quotation table table-bordered" data-badge="badge-unit-quotation">
                            <thead>
                                <tr>
                                    <th class="text-center">No. Quotation</th>
                                    <th>Client</th>
                                    <th>Description</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>{{-- /tab-content --}}
        </div>
    </div>

    @else
    {{-- ── ADMIN: Tabbed view ──────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-end mb-3 gap-2">
        <label class="form-label mb-0 text-muted" style="white-space:nowrap;">Filter Sales:</label>
        <select class="form-select form-select-sm" id="admin-sales-filter" style="max-width:220px;">
            <option value="">Semua Sales</option>
            @foreach ($salesList as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="card">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="quotation-admin-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#admin-tab-quotation" type="button">
                        <i class="mdi mdi-file-document-outline me-1"></i>Quotation
                        <span class="badge rounded-pill bg-primary ms-1" id="admin-badge-quotation">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#admin-tab-unit-quotation" type="button">
                        <i class="mdi mdi-file-document-outline me-1"></i>Quotation Unit
                        <span class="badge rounded-pill bg-primary ms-1" id="admin-badge-unit-quotation">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#admin-tab-hot" type="button">
                        <i class="mdi mdi-fire me-1"></i>Hot Prospect
                        <span class="badge rounded-pill bg-danger ms-1" id="admin-badge-hot">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#admin-tab-po" type="button">
                        <i class="mdi mdi-cart-check me-1"></i>Purchase Order
                        <span class="badge rounded-pill bg-success ms-1" id="admin-badge-po">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#admin-tab-loss" type="button">
                        <i class="mdi mdi-close-circle-outline me-1"></i>Loss
                        <span class="badge rounded-pill bg-secondary ms-1" id="admin-badge-loss">-</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content">

                {{-- Admin Tab 1: Quotation --}}
                <div class="tab-pane fade show active" id="admin-tab-quotation">
                    <div class="table-responsive">
                        <table class="datatable-quotation-admin-tab table table-bordered" data-badge="admin-badge-quotation">
                            <thead>
                                <tr>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width:48px;"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Admin Tab 2: Quotation Unit --}}
                <div class="tab-pane fade" id="admin-tab-unit-quotation">
                    <div class="table-responsive">
                        <table class="datatable-unit-quotation-admin table table-bordered" data-badge="admin-badge-unit-quotation">
                            <thead>
                                <tr>
                                    <th class="text-center">No. Quotation</th>
                                    <th>Client</th>
                                    <th>Description</th>
                                    <th class="text-center">Date</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="width:48px;"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Admin Tab 3: Hot Prospect --}}
                <div class="tab-pane fade" id="admin-tab-hot">
                    <div class="table-responsive">
                        <table class="datatable-hot-prospect-admin table table-bordered" data-badge="admin-badge-hot">
                            <thead>
                                <tr>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width:48px;"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Admin Tab 4: Purchase Order --}}
                <div class="tab-pane fade" id="admin-tab-po">
                    <div class="table-responsive">
                        <table class="datatable-po-quote-admin-tab table table-bordered" data-badge="admin-badge-po">
                            <thead>
                                <tr>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Date PO</th>
                                    <th>PO Number</th>
                                    <th>Invoice Number</th>
                                    <th class="text-center" style="width:48px;"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

                {{-- Admin Tab 5: Loss --}}
                <div class="tab-pane fade" id="admin-tab-loss">
                    <div class="table-responsive">
                        <table class="datatable-loss-quote-admin-tab table table-bordered" data-badge="admin-badge-loss">
                            <thead>
                                <tr>
                                    <th>Quote No.</th>
                                    <th>Company</th>
                                    <th>Total Price</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-center" style="width:48px;"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>{{-- /tab-content --}}
        </div>
    </div>
    @endif

    @include('components.modal.quotation.overhaul.form')
@endsection()

@push('after-style')
    <style>
        .tooltip-quote-no .tooltip-inner {
            max-width: 320px;
            font-size: 13px;
            padding: 6px 12px;
            letter-spacing: 0.3px;
        }
        table.datatable-quotation td, table.datatable-quotation th,
        table.datatable-unit-quotation td, table.datatable-unit-quotation th,
        table.datatable-hot-prospect td, table.datatable-hot-prospect th,
        table.datatable-po-quote td, table.datatable-po-quote th,
        table.datatable-loss-quote td, table.datatable-loss-quote th,
        table.datatable-quotation-archive td, table.datatable-quotation-archive th,
        table.datatable-quotation-admin-tab td, table.datatable-quotation-admin-tab th,
        table.datatable-unit-quotation-admin td, table.datatable-unit-quotation-admin th,
        table.datatable-hot-prospect-admin td, table.datatable-hot-prospect-admin th,
        table.datatable-po-quote-admin-tab td, table.datatable-po-quote-admin-tab th,
        table.datatable-loss-quote-admin-tab td, table.datatable-loss-quote-admin-tab th { font-size: 14px; }
    </style>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/bootstrap-select/bootstrap-select.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/tagify/tagify.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/bloodhound/bloodhound.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation.js"></script>
    <script src="{{ asset('assets') }}/includes/table-hot-prospect.js"></script>
    <script src="{{ asset('assets') }}/includes/table-po.js"></script>
    <script src="{{ asset('assets') }}/includes/table-loss.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation-archive.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-quotation.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation-admin-tab.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-quotation-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-hot-prospect-admin.js"></script>
    <script src="{{ asset('assets') }}/includes/table-po-admin-tab.js"></script>
    <script src="{{ asset('assets') }}/includes/table-loss-admin-tab.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

@push('script')
    <script>
        $('#quotation-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
        });
        $('#quotation-admin-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
        });

        $(document).on('draw.dt', function (e) {
            var $tbl    = $(e.target);
            var badgeId = $tbl.data('badge');
            if (!badgeId) return;
            var api   = $tbl.DataTable();
            var count = api.page.info().recordsTotal;
            $('#' + badgeId).text(count);
        });

        window.adminSalesFilter = '';
        $('#admin-sales-filter').on('change', function () {
            window.adminSalesFilter = $(this).val();
            ['dtAdminQuotation', 'dtAdminUnitQuotation', 'dtAdminHot', 'dtAdminPo', 'dtAdminLoss'].forEach(function (key) {
                if (window[key]) window[key].ajax.reload();
            });
        });

        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
