@extends('layouts.sales.app')
@section('title', 'Sales Invoice AR')
@section('no-container') @endsection
@section('content')
    <div class="container-fluid px-4 py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between pb-3 mb-2">
            <h4 class="fw-bold mb-0"><span class="text-muted fw-normal">Account Receivable /</span> Payment Receipt</h4>
        </div>

        <div class="row g-4 mb-4">
            <!-- Invoice Receipt -->
            <div class="col-12 col-md-4">
                <div class="card metric-card card-projection border-0" style="border-top: 4px solid #6366f1 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="metric-label text-primary">Invoice Receipt</span>
                            <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(99, 102, 241, 0.12); color: #6366f1;">
                                <i class="mdi mdi-cash-multiple mdi-24px"></i>
                            </div>
                        </div>
                        <h3 class="metric-value mb-1" style="color: #6366f1;" id="summary-payment-receipt">Rp {{ number_format(@$receipt, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;">Total Penerimaan</p>
                    </div>
                </div>
            </div>

            <!-- Confirm -->
            <div class="col-12 col-md-4">
                <div class="card metric-card card-actual border-0" style="border-top: 4px solid #10b981 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="metric-label text-success">Confirm</span>
                            <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
                                <i class="mdi mdi-check-decagram-outline mdi-24px"></i>
                            </div>
                        </div>
                        <h3 class="metric-value mb-1" style="color: #10b981;" id="summary-payment-confirm">Rp {{ number_format(@$confirm, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;">Sudah Dikonfirmasi</p>
                    </div>
                </div>
            </div>

            <!-- Un-Confirm -->
            <div class="col-12 col-md-4">
                <div class="card metric-card border-0" style="border-top: 4px solid #f59e0b !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="metric-label" style="color:#f59e0b;">Un-Confirm</span>
                            <div class="metric-icon-box m-0" style="width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background-color: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                                <i class="mdi mdi-clock-alert-outline mdi-24px"></i>
                            </div>
                        </div>
                        <h3 class="metric-value mb-1" style="color: #f59e0b;" id="summary-payment-unconfirm">Rp {{ number_format(@$unconfirm, 0, ',', '.') }}</h3>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;">Belum Dikonfirmasi</p>
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
                        <h6 class="fw-bold mb-0 text-dark">Filter Payment</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted fw-semibold text-nowrap" style="font-size:0.85rem;">Filter Tahun:</label>
                            <select class="form-select form-select-sm" id="payment-year-filter" style="min-width:140px;">
                                <option value="all">Semua Tahun</option>
                                @for ($y = now()->year; $y >= 2022; $y--)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted fw-semibold text-nowrap" style="font-size:0.85rem;">Filter Sales:</label>
                            <select class="form-select form-select-sm" id="payment-sales-filter" style="min-width:180px;">
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
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-receipt-text-outline text-primary mdi-24px"></i>
                    <h6 class="fw-bold mb-0 text-dark">Payment Receipt List</h6>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="card-datatable table-responsive pt-0">
                    <table class="datatable-payment-receipt-ar table table-bordered">
                        <thead>
                            <tr>
                                <th>No. Receipt</th>
                                <th>Date</th>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Amount Paid</th>
                                <th>Ballance</th>
                                <th>Payment</th>
                                <th>Confirm</th>
                                <th>Status</th>
                                <th>Sales</th>
                                <th>flag</th>
                            </tr>
                        </thead>
                    </table>
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
        /* Metric Card Stylings matching Sales Invoice AR */
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
    <script src="{{ asset('assets') }}/includes/table-ar-payment-receipt.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        window.paymentYearFilter = $('#payment-year-filter').val() || 'all';
        window.paymentSalesFilter = $('#payment-sales-filter').val() || 'all';
        window.paymentDataTables = window.paymentDataTables || {};

        function loadPaymentSummary() {
            var year = window.paymentYearFilter || 'all';
            var salesId = window.paymentSalesFilter || 'all';
            $.ajax({
                url: '/db/payment/summary',
                type: 'GET',
                data: { year: year, sales_id: salesId },
                success: function (res) {
                    $('#summary-payment-receipt').text('Rp ' + new Intl.NumberFormat('id-ID').format(res.receipt || 0));
                    $('#summary-payment-confirm').text('Rp ' + new Intl.NumberFormat('id-ID').format(res.confirm || 0));
                    $('#summary-payment-unconfirm').text('Rp ' + new Intl.NumberFormat('id-ID').format(res.unconfirm || 0));
                }
            });
        }

        $(document).ready(function () {
            loadPaymentSummary();
        });

        $('#payment-year-filter, #payment-sales-filter').on('change', function () {
            window.paymentYearFilter = $('#payment-year-filter').val() || 'all';
            window.paymentSalesFilter = $('#payment-sales-filter').val() || 'all';
            loadPaymentSummary();
            Object.values(window.paymentDataTables).forEach(function (dt) {
                dt.ajax.reload();
            });
        });
    </script>
@endpush
