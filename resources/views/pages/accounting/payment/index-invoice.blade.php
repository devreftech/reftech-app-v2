@extends('layouts.sales.app')
@section('title', 'Sales Invoice AR')
@section('no-container') @endsection
@section('content')
    <div class="container-fluid px-4 py-3">
        {{-- Page Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-3">
            <div>
                <h4 class="fw-bold mb-1">
                    <span class="text-muted fw-light">Finance / Account Receivable (AR) /</span> Sales Invoice
                </h4>
                <p class="text-muted mb-0 small">
                    <i class="mdi mdi-receipt-text-outline me-1"></i> Rekap piutang penjualan, status pembayaran masuk, dan sisa outstanding klien
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('payment_index.payment') }}" class="btn btn-label-primary btn-sm">
                    <i class="mdi mdi-cash-check me-1"></i> Payment Receipt
                </a>
                <a href="{{ route('payment_index.aging') }}" class="btn btn-label-warning btn-sm">
                    <i class="mdi mdi-calendar-clock-outline me-1"></i> Aging Report
                </a>
            </div>
        </div>

        {{-- Metric KPI Cards --}}
        <div class="row g-3 mb-4">
            <!-- Total Invoice AR -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px;">
                                <i class="mdi mdi-receipt-text me-1"></i> Total Invoice AR
                            </span>
                            <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="mdi mdi-receipt-text fs-6"></i>
                            </div>
                        </div>
                        <h3 class="fw-bolder text-primary fs-4 mb-1" id="summary-total-invoice">Rp -</h3>
                        <small class="text-muted" style="font-size: 11px;">Total nilai invoice AR yang telah diterbitkan</small>
                    </div>
                </div>
            </div>

            <!-- Total Payment Masuk -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%); border-left: 5px solid #28a745 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px;">
                                <i class="mdi mdi-cash-check me-1"></i> Total Payment Masuk
                            </span>
                            <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="mdi mdi-cash-check fs-6"></i>
                            </div>
                        </div>
                        <h3 class="fw-bolder text-success fs-4 mb-1" id="summary-total-payment">Rp -</h3>
                        <small class="text-muted" style="font-size: 11px;">Pembayaran dari klien yang sudah terverifikasi</small>
                    </div>
                </div>
            </div>

            <!-- Total Outstanding AR -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff8f8 0%, #ffeded 100%); border-left: 5px solid #ff3e1d !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fw-bold text-danger small" style="letter-spacing: .5px;">
                                <i class="mdi mdi-clock-alert-outline me-1"></i> Total Outstanding AR
                            </span>
                            <div class="avatar avatar-xs bg-label-danger rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="mdi mdi-clock-alert-outline fs-6"></i>
                            </div>
                        </div>
                        <h3 class="fw-bolder text-danger fs-4 mb-1" id="summary-total-outstanding">Rp -</h3>
                        <small class="text-muted" style="font-size: 11px;">Sisa saldo piutang yang belum dilunasi</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-filter-variant text-primary fs-5"></i>
                        <h6 class="fw-bold mb-0 text-dark">Filter Invoice</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted small fw-semibold text-nowrap">Filter Tahun:</label>
                            <select class="form-select form-select-sm" id="invoice-year-filter" style="min-width:140px;">
                                <option value="all">Semua Tahun</option>
                                @for ($y = now()->year; $y >= 2022; $y--)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted small fw-semibold text-nowrap">Filter Sales:</label>
                            <select class="form-select form-select-sm" id="invoice-sales-filter" style="min-width:180px;">
                                <option value="all">Semua Sales</option>
                                @if(isset($salesUsers))
                                    @foreach($salesUsers as $sUser)
                                        <option value="{{ $sUser->id }}">{{ $sUser->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Table Container with Tabs --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-2">
                <ul class="nav nav-tabs card-header-tabs border-0 m-0 flex-nowrap overflow-auto" id="invoice-ar-tab-nav" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active py-2 px-3 fw-semibold" id="nav-inv-general" role="tab"
                            data-bs-toggle="tab" data-bs-target="#navs-pills-top-general" aria-controls="navs-pills-top-general"
                            aria-selected="true">
                            <i class="mdi mdi-view-list-outline me-1"></i>General
                            <span class="badge rounded-pill bg-label-primary ms-1" id="badge-inv-general">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 px-3 fw-semibold" id="nav-inv-reftech" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-reftech" aria-controls="navs-pills-top-reftech"
                            aria-selected="false" tabindex="-1">
                            <i class="mdi mdi-file-document-outline me-1"></i>Reftech
                            <span class="badge rounded-pill bg-label-info ms-1" id="badge-inv-reftech">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 px-3 fw-semibold" id="nav-inv-kojisha" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-kojisha" aria-controls="navs-pills-top-kojisha"
                            aria-selected="false" tabindex="-1">
                            <i class="mdi mdi-file-document-multiple-outline me-1"></i>Kojisha
                            <span class="badge rounded-pill bg-label-info ms-1" id="badge-inv-kojisha">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 px-3 fw-semibold" id="nav-inv-ahmad" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-ahmad" aria-controls="navs-pills-top-ahmad" aria-selected="false"
                            tabindex="-1">
                            <i class="mdi mdi-account-outline me-1"></i>Yusuf
                            <span class="badge rounded-pill bg-label-secondary ms-1" id="badge-inv-ahmad">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 px-3 fw-semibold" id="nav-inv-rayi" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-rayi" aria-controls="navs-pills-top-rayi" aria-selected="false"
                            tabindex="-1">
                            <i class="mdi mdi-account-outline me-1"></i>Rayi
                            <span class="badge rounded-pill bg-label-secondary ms-1" id="badge-inv-rayi">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link py-2 px-3 fw-semibold" id="nav-inv-escrow" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-escrow" aria-controls="navs-pills-top-escrow" aria-selected="false"
                            tabindex="-1">
                            <i class="mdi mdi-bank-outline me-1"></i>Escrow
                            <span class="badge rounded-pill bg-label-warning ms-1" id="badge-inv-escrow">-</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content border-0 p-0 m-0">
                    <div class="tab-pane fade show active p-3" id="navs-pills-top-general" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-ar table table-hover border-top" data-badge="badge-inv-general">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-dark">Invoice No.</th>
                                        <th class="fw-semibold text-dark">Date</th>
                                        <th class="fw-semibold text-dark">No PO.</th>
                                        <th class="fw-semibold text-dark">Company</th>
                                        <th class="fw-semibold text-dark text-end">Total Invoice</th>
                                        <th class="fw-semibold text-dark text-end">Advance Payment</th>
                                        <th class="fw-semibold text-dark text-end">Outstanding</th>
                                        <th class="fw-semibold text-dark text-center">Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-reftech" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-reftech table table-hover border-top" data-badge="badge-inv-reftech">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-dark">Invoice No.</th>
                                        <th class="fw-semibold text-dark">Date</th>
                                        <th class="fw-semibold text-dark">No PO.</th>
                                        <th class="fw-semibold text-dark">Company</th>
                                        <th class="fw-semibold text-dark text-end">Total Invoice</th>
                                        <th class="fw-semibold text-dark text-end">Advance Payment</th>
                                        <th class="fw-semibold text-dark text-end">Outstanding</th>
                                        <th class="fw-semibold text-dark text-center">Status</th>
                                        <th class="fw-semibold text-dark">Sales</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-kojisha" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-kojisha table table-hover border-top" data-badge="badge-inv-kojisha">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-dark">Invoice No.</th>
                                        <th class="fw-semibold text-dark">Date</th>
                                        <th class="fw-semibold text-dark">No PO.</th>
                                        <th class="fw-semibold text-dark">Company</th>
                                        <th class="fw-semibold text-dark text-end">Total Invoice</th>
                                        <th class="fw-semibold text-dark text-end">Advance Payment</th>
                                        <th class="fw-semibold text-dark text-end">Outstanding</th>
                                        <th class="fw-semibold text-dark text-center">Status</th>
                                        <th class="fw-semibold text-dark">Sales</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-ahmad" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-ahmad table table-hover border-top" data-badge="badge-inv-ahmad">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-dark">Invoice No.</th>
                                        <th class="fw-semibold text-dark">Date</th>
                                        <th class="fw-semibold text-dark">No PO.</th>
                                        <th class="fw-semibold text-dark">Company</th>
                                        <th class="fw-semibold text-dark text-end">Total Invoice</th>
                                        <th class="fw-semibold text-dark text-end">Advance Payment</th>
                                        <th class="fw-semibold text-dark text-end">Outstanding</th>
                                        <th class="fw-semibold text-dark text-center">Status</th>
                                        <th class="fw-semibold text-dark text-center">VAT</th>
                                        <th class="fw-semibold text-dark">Sales</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-rayi" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-rayi table table-hover border-top" data-badge="badge-inv-rayi">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-dark">Invoice No.</th>
                                        <th class="fw-semibold text-dark">Date</th>
                                        <th class="fw-semibold text-dark">No PO.</th>
                                        <th class="fw-semibold text-dark">Company</th>
                                        <th class="fw-semibold text-dark text-end">Total Invoice</th>
                                        <th class="fw-semibold text-dark text-end">Advance Payment</th>
                                        <th class="fw-semibold text-dark text-end">Outstanding</th>
                                        <th class="fw-semibold text-dark text-center">Status</th>
                                        <th class="fw-semibold text-dark text-center">VAT</th>
                                        <th class="fw-semibold text-dark">Sales</th>
                                        <th class="fw-semibold text-dark">Flag</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-escrow" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-escrow table table-hover border-top" data-badge="badge-inv-escrow">
                                <thead class="table-light">
                                    <tr>
                                        <th class="fw-semibold text-dark">Invoice No.</th>
                                        <th class="fw-semibold text-dark">Date</th>
                                        <th class="fw-semibold text-dark">Customer</th>
                                        <th class="fw-semibold text-dark text-end">Nominal</th>
                                        <th class="fw-semibold text-dark text-end">Fee</th>
                                        <th class="fw-semibold text-dark">Sales</th>
                                        <th class="fw-semibold text-dark">Flag</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .nav-tabs .nav-link {
            border: 1px solid transparent;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            color: #6c757d;
            transition: all 0.2s;
        }
        .nav-tabs .nav-link:hover {
            color: #696cff;
        }
        .nav-tabs .nav-link.active {
            border-color: #e0e2e8 #e0e2e8 #fff !important;
            background-color: #ffffff;
            color: #696cff !important;
            font-weight: 700;
        }
        table.dataTable thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        table.dataTable input.form-control {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-sales-invoice.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-sales-invoice-reftech.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-sales-invoice-kojisha.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-sales-invoice-ahmad.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-sales-invoice-rayi.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ar-sales-invoice-escrow.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        window.invoiceYearFilter = $('#invoice-year-filter').val() || 'all';
        window.invoiceSalesFilter = $('#invoice-sales-filter').val() || 'all';
        window.invoiceDataTables = window.invoiceDataTables || {};

        function loadInvoiceSummary() {
            var year = window.invoiceYearFilter || 'all';
            var salesId = window.invoiceSalesFilter || 'all';
            $.ajax({
                url: '/db/sales/invoice/summary',
                type: 'GET',
                data: { year: year, sales_id: salesId },
                success: function (res) {
                    $('#summary-total-invoice').text('Rp ' + new Intl.NumberFormat('id-ID').format(res.total_invoice || 0));
                    $('#summary-total-payment').text('Rp ' + new Intl.NumberFormat('id-ID').format(res.total_payment || 0));
                    $('#summary-total-outstanding').text('Rp ' + new Intl.NumberFormat('id-ID').format(res.total_outstanding || 0));
                }
            });
        }

        $(document).ready(function () {
            loadInvoiceSummary();
        });

        $('#invoice-year-filter, #invoice-sales-filter').on('change', function () {
            window.invoiceYearFilter = $('#invoice-year-filter').val() || 'all';
            window.invoiceSalesFilter = $('#invoice-sales-filter').val() || 'all';
            loadInvoiceSummary();
            Object.values(window.invoiceDataTables).forEach(function (dt) {
                dt.ajax.reload();
            });
        });

        $(document).on('draw.dt', function (e) {
            var $tbl = $(e.target);
            var badgeId = $tbl.data('badge');
            if (badgeId) {
                var api = $tbl.DataTable();
                $('#' + badgeId).text(api.page.info().recordsTotal);
            }
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                if (window.bootstrap && bootstrap.Tooltip) {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                } else if ($.fn.tooltip) {
                    $(tooltipTriggerEl).tooltip();
                }
            });
        });

        $('#invoice-ar-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust().responsive.recalc();
        });
    </script>
@endpush
