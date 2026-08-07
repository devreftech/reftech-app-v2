@extends('layouts.sales.app')
@section('title', 'Sales Invoice AR')
@section('no-container') @endsection
@section('content')
    <div class="container-fluid px-4 py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between pb-3 mb-2">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-normal">Account Receivable /</span> Sales Invoice</h4>
        </div>

        <div class="row g-4 mb-4">
            <!-- Total Invoice AR -->
            <div class="col-12 col-md-4">
                <div class="card metric-card card-projection border-0" style="border-top: 4px solid #6366f1 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="metric-label text-primary">Total Invoice AR</span>
                            <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(99, 102, 241, 0.12); color: #6366f1;">
                                <i class="mdi mdi-receipt-text mdi-24px"></i>
                            </div>
                        </div>
                        <h3 class="metric-value mb-1" style="color: #6366f1;" id="summary-total-invoice">Rp -</h3>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;">Total nilai invoice AR terbit</p>
                    </div>
                </div>
            </div>

            <!-- Total Payment Masuk -->
            <div class="col-12 col-md-4">
                <div class="card metric-card card-actual border-0" style="border-top: 4px solid #10b981 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="metric-label text-success">Total Payment Masuk</span>
                            <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
                                <i class="mdi mdi-cash-check mdi-24px"></i>
                            </div>
                        </div>
                        <h3 class="metric-value mb-1" style="color: #10b981;" id="summary-total-payment">Rp -</h3>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;">Pembayaran terverifikasi masuk</p>
                    </div>
                </div>
            </div>

            <!-- Total Outstanding AR -->
            <div class="col-12 col-md-4">
                <div class="card metric-card card-danger border-0" style="border-top: 4px solid #ef4444 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="metric-label text-danger">Total Outstanding AR</span>
                            <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(239, 68, 68, 0.12); color: #ef4444;">
                                <i class="mdi mdi-clock-alert-outline mdi-24px"></i>
                            </div>
                        </div>
                        <h3 class="metric-value mb-1" style="color: #ef4444;" id="summary-total-outstanding">Rp -</h3>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;">Sisa piutang belum terbayar</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card card-minimalist mb-4">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-filter-variant text-primary mdi-24px"></i>
                        <h6 class="fw-bold mb-0 text-dark">Filter Invoice</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted fw-semibold text-nowrap" style="font-size:0.85rem;">Filter Tahun:</label>
                            <select class="form-select form-select-sm" id="invoice-year-filter" style="min-width:140px;">
                                <option value="all">Semua Tahun</option>
                                @for ($y = now()->year; $y >= 2022; $y--)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted fw-semibold text-nowrap" style="font-size:0.85rem;">Filter Sales:</label>
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

        <div class="card card-minimalist">
            <div class="card-header card-minimalist-header py-2">
                <ul class="nav nav-tabs card-header-tabs border-0 m-0 flex-nowrap overflow-auto" id="invoice-ar-tab-nav" role="tablist">
                    <li class="nav-item">
                        <button type="button" class="nav-link active" id="nav-inv-general" role="tab"
                            data-bs-toggle="tab" data-bs-target="#navs-pills-top-general" aria-controls="navs-pills-top-general"
                            aria-selected="true">
                            <i class="mdi mdi-view-list-outline me-1"></i>General
                            <span class="badge rounded-pill bg-primary ms-1" id="badge-inv-general">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-inv-reftech" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-reftech" aria-controls="navs-pills-top-reftech"
                            aria-selected="false" tabindex="-1">
                            <i class="mdi mdi-file-document-outline me-1"></i>Reftech
                            <span class="badge rounded-pill bg-info ms-1" id="badge-inv-reftech">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-inv-kojisha" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-kojisha" aria-controls="navs-pills-top-kojisha"
                            aria-selected="false" tabindex="-1">
                            <i class="mdi mdi-file-document-multiple-outline me-1"></i>Kojisha
                            <span class="badge rounded-pill bg-info ms-1" id="badge-inv-kojisha">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-inv-ahmad" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-ahmad" aria-controls="navs-pills-top-ahmad" aria-selected="false"
                            tabindex="-1">
                            <i class="mdi mdi-account-outline me-1"></i>Yusuf
                            <span class="badge rounded-pill bg-secondary ms-1" id="badge-inv-ahmad">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-inv-rayi" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-rayi" aria-controls="navs-pills-top-rayi" aria-selected="false"
                            tabindex="-1">
                            <i class="mdi mdi-account-outline me-1"></i>Rayi
                            <span class="badge rounded-pill bg-secondary ms-1" id="badge-inv-rayi">-</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button type="button" class="nav-link" id="nav-inv-escrow" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-escrow" aria-controls="navs-pills-top-escrow" aria-selected="false"
                            tabindex="-1">
                            <i class="mdi mdi-bank-outline me-1"></i>Escrow
                            <span class="badge rounded-pill bg-warning ms-1" id="badge-inv-escrow">-</span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <div class="tab-pane fade show active p-3" id="navs-pills-top-general" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-ar table table-bordered" data-badge="badge-inv-general">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>No PO.</th>
                                        <th>Company</th>
                                        <th>Total Invoice</th>
                                        <th>Advance Payment</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-reftech" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-reftech table table-bordered" data-badge="badge-inv-reftech">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>No PO.</th>
                                        <th>Company</th>
                                        <th>Total Invoice</th>
                                        <th>Advance Payment</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>Sales</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-kojisha" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-kojisha table table-bordered" data-badge="badge-inv-kojisha">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>No PO.</th>
                                        <th>Company</th>
                                        <th>Total Invoice</th>
                                        <th>Advance Payment</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>Sales</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-ahmad" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-ahmad table table-bordered" data-badge="badge-inv-ahmad">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>No PO.</th>
                                        <th>Company</th>
                                        <th>Total Invoice</th>
                                        <th>Advance Payment</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>VAT</th>
                                        <th>Sales</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-rayi" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-rayi table table-bordered" data-badge="badge-inv-rayi">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>No PO.</th>
                                        <th>Company</th>
                                        <th>Total Invoice</th>
                                        <th>Advance Payment</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                        <th>VAT</th>
                                        <th>Sales</th>
                                        <th>Flag</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="navs-pills-top-escrow" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-sales-invoice-escrow table table-bordered" data-badge="badge-inv-escrow">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Nominal</th>
                                        <th>Fee</th>
                                        <th>Sales</th>
                                        <th>Flag</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
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
        /* Metric Card Stylings matching Forecast Dashboard */
        .metric-card {
            border-radius: 20px;
            border: 1px solid rgba(229, 231, 235, 0.6) !important;
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.08) !important;
        }
        .metric-label {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .metric-value {
            font-size: 1.85rem;
            font-weight: 800;
            letter-spacing: -0.025em;
        }

        .card-minimalist {
            border: 1px solid #e0e2e8 !important;
            box-shadow: none !important;
            border-radius: 12px;
        }
        .card-minimalist-header {
            border-bottom: 1px solid #e0e2e8 !important;
            background-color: #fafbfe;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }
        .nav-tabs .nav-link {
            border-radius: 6px 6px 0 0;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            border-color: #e0e2e8 #e0e2e8 #fff !important;
            background-color: #ffffff;
            font-weight: 600;
        }
        .tooltip-quote-no .tooltip-inner {
            max-width: 320px;
            font-size: 13px;
            padding: 6px 12px;
            letter-spacing: 0.3px;
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
