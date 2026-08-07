@extends('layouts.sales.app')
@section('title', 'Quotation')
@section('content')
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm custom-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Quotation</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                <i class="mdi mdi-file-document-outline mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    <h4 class="mb-2 fw-bold text-dark">Rp
                        <span id="card-forecast-sum">{{ number_format(Auth::user()->role == 'Admin' ? $forecastAdmin : $forecast, 2, ',', '.') }}</span>
                    </h4>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-primary rounded-pill fw-semibold" id="card-forecast-count">{{ Auth::user()->role == 'Admin' ? $forecastAdminCount : $forecastCount }}</span>
                        <span class="text-muted small">Total Quotation</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm custom-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Hot Prospect</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-warning shadow-xs">
                                <i class="mdi mdi-fire mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    <h4 class="mb-2 fw-bold text-dark">Rp
                        <span id="card-prospect-sum">{{ number_format(Auth::user()->role == 'Admin' ? $prospectAdmin : $prospect, 2, ',', '.') }}</span>
                    </h4>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-warning rounded-pill fw-semibold" id="card-prospect-count">{{ Auth::user()->role == 'Admin' ? $prospectAdminCount : $prospectCount }}</span>
                        <span class="text-muted small">Prospek Aktif</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm custom-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Purchase Order</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-success shadow-xs">
                                <i class="mdi mdi-cart-check mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    <h4 class="mb-2 fw-bold text-dark">Rp
                        <span id="card-po-sum">{{ number_format(Auth::user()->role == 'Admin' ? $poAdmin : $po, 2, ',', '.') }}</span>
                    </h4>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-success rounded-pill fw-semibold" id="card-po-count">{{ Auth::user()->role == 'Admin' ? $poAdminCount : $poCount }}</span>
                        <span class="text-muted small">PO Goal</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm custom-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Loss Order</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-danger shadow-xs">
                                <i class="mdi mdi-close-circle-outline mdi-20px"></i>
                            </span>
                        </div>
                    </div>
                    <h4 class="mb-2 fw-bold text-dark">Rp
                        <span id="card-loss-sum">{{ number_format(Auth::user()->role == 'Admin' ? $lossAdmin : $loss, 2, ',', '.') }}</span>
                    </h4>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-label-danger rounded-pill fw-semibold" id="card-loss-count">{{ Auth::user()->role == 'Admin' ? $lossAdminCount : $lossCount }}</span>
                        <span class="text-muted small">Order Loss</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (Auth::user()->role !== 'Admin')
    {{-- ── SALES: Tabbed view ─────────────────────────────────────── --}}
    <div class="d-flex align-items-center justify-content-end mb-3 gap-2">
        <label class="form-label mb-0 text-muted" style="white-space:nowrap;">Filter Tahun:</label>
        <select class="form-select form-select-sm" id="quotation-year-filter" style="max-width:150px;">
            <option value="all">Semua Tahun</option>
            @for ($y = now()->year; $y >= 2022; $y--)
                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
    </div>
    <div class="card">
        <div class="card-header py-2">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="quotation-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-quotation" type="button">
                        <i class="mdi mdi-file-document-outline me-1"></i>Quotation
                        <span class="badge rounded-pill bg-primary ms-1" id="badge-quotation">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-unit-quotation" type="button">
                        <i class="mdi mdi-sparkles me-1 text-warning"></i>Smart Quote
                        <span class="badge rounded-pill bg-primary ms-1" id="badge-unit-quotation">-</span>
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
                        <span class="badge rounded-pill bg-success ms-1" id="badge-po">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-loss" type="button">
                        <i class="mdi mdi-close-circle-outline me-1"></i>Loss
                        <span class="badge rounded-pill bg-secondary ms-1" id="badge-loss">-</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-archive" type="button">
                        <i class="mdi mdi-archive-outline me-1"></i>Archive
                        <span class="badge rounded-pill bg-secondary ms-1" id="badge-archive">-</span>
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
        <label class="form-label mb-0 text-muted" style="white-space:nowrap;">Filter Tahun:</label>
        <select class="form-select form-select-sm" id="admin-year-filter" style="max-width:150px;">
            <option value="all">Semua Tahun</option>
            @for ($y = now()->year; $y >= 2022; $y--)
                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <label class="form-label mb-0 text-muted ms-2" style="white-space:nowrap;">Filter Sales:</label>
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
                        <i class="mdi mdi-sparkles me-1 text-warning"></i>Smart Quote
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
        .custom-stat-card {
            border-radius: 12px !important;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }
        .custom-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08) !important;
        }
        .tracking-wider {
            letter-spacing: 0.5px;
            font-size: 0.725rem;
        }
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

        function updateCardStats() {
            var isAdmin = {{ Auth::user()->role === 'Admin' ? 'true' : 'false' }};
            var year = isAdmin ? (window.adminQuotationYearFilter || 'all') : (window.quotationYearFilter || 'all');
            var salesId = isAdmin ? (window.adminSalesFilter || '') : '';

            $.ajax({
                url: '{{ route("quotation.card-stats") }}',
                type: 'GET',
                data: {
                    year: year,
                    sales_id: salesId
                },
                success: function(response) {
                    var localeOptions = { minimumFractionDigits: 2, maximumFractionDigits: 2 };
                    
                    $('#card-forecast-sum').text(parseFloat(response.forecast_sum).toLocaleString('id-ID', localeOptions));
                    $('#card-forecast-count').text(response.forecast_count);
                    
                    $('#card-prospect-sum').text(parseFloat(response.prospect_sum).toLocaleString('id-ID', localeOptions));
                    $('#card-prospect-count').text(response.prospect_count);
                    
                    $('#card-po-sum').text(parseFloat(response.po_sum).toLocaleString('id-ID', localeOptions));
                    $('#card-po-count').text(response.po_count);
                    
                    $('#card-loss-sum').text(parseFloat(response.loss_sum).toLocaleString('id-ID', localeOptions));
                    $('#card-loss-count').text(response.loss_count);
                },
                error: function(xhr) {
                    console.error('Error updating card stats:', xhr);
                }
            });
        }

        window.quotationYearFilter = $('#quotation-year-filter').val() || 'all';
        $('#quotation-year-filter').on('change', function () {
            window.quotationYearFilter = $(this).val();
            ['dtQuotation', 'dtUnitQuotation', 'dtHot', 'dtPo', 'dtLoss', 'dtArchive'].forEach(function (key) {
                if (window[key]) window[key].ajax.reload();
            });
            updateCardStats();
        });

        window.adminSalesFilter = '';
        window.adminQuotationYearFilter = $('#admin-year-filter').val() || 'all';
        var reloadAdminTables = function () {
            ['dtAdminQuotation', 'dtAdminUnitQuotation', 'dtAdminHot', 'dtAdminPo', 'dtAdminLoss'].forEach(function (key) {
                if (window[key]) window[key].ajax.reload();
            });
        };
        $('#admin-sales-filter').on('change', function () {
            window.adminSalesFilter = $(this).val();
            reloadAdminTables();
            updateCardStats();
        });
        $('#admin-year-filter').on('change', function () {
            window.adminQuotationYearFilter = $(this).val();
            reloadAdminTables();
            updateCardStats();
        });

        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
