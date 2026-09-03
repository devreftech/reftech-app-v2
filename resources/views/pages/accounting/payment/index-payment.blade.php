@extends('layouts.sales.app')
@section('title', 'Payment Receipt AR')
@section('no-container') @endsection
@section('content')
    <div class="container-fluid px-4 py-3">
        {{-- Page Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-3">
            <div>
                <h4 class="fw-bold mb-1">
                    <span class="text-muted fw-light">Finance / Account Receivable (AR) /</span> Payment Receipt
                </h4>
                <p class="text-muted mb-0 small">
                    <i class="mdi mdi-cash-multiple me-1"></i> Rekap bukti penerimaan pembayaran, verifikasi pelunasan, dan saldo berjalan klien
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('payment_index.invoice') }}" class="btn btn-label-primary btn-sm">
                    <i class="mdi mdi-receipt-text-outline me-1"></i> Sales Invoice
                </a>
                <a href="{{ route('payment_index.aging') }}" class="btn btn-label-warning btn-sm">
                    <i class="mdi mdi-calendar-clock-outline me-1"></i> Aging Report
                </a>
            </div>
        </div>

        {{-- Metric KPI Cards --}}
        <div class="row g-3 mb-4">
            <!-- Invoice Receipt -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px;">
                                <i class="mdi mdi-cash-multiple me-1"></i> Total Penerimaan (Receipt)
                            </span>
                            <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="mdi mdi-cash-multiple fs-6"></i>
                            </div>
                        </div>
                        <h3 class="fw-bolder text-primary fs-4 mb-1" id="summary-payment-receipt">Rp {{ number_format(@$receipt, 0, ',', '.') }}</h3>
                        <small class="text-muted" style="font-size: 11px;">Total seluruh bukti pembayaran diterima</small>
                    </div>
                </div>
            </div>

            <!-- Confirm -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%); border-left: 5px solid #28a745 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px;">
                                <i class="mdi mdi-check-decagram-outline me-1"></i> Sudah Dikonfirmasi (Confirm)
                            </span>
                            <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="mdi mdi-check-decagram-outline fs-6"></i>
                            </div>
                        </div>
                        <h3 class="fw-bolder text-success fs-4 mb-1" id="summary-payment-confirm">Rp {{ number_format(@$confirm, 0, ',', '.') }}</h3>
                        <small class="text-muted" style="font-size: 11px;">Pembayaran tervalidasi masuk ke rekening</small>
                    </div>
                </div>
            </div>

            <!-- Un-Confirm -->
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fffbf0 0%, #fff4db 100%); border-left: 5px solid #ffab00 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fw-bold text-warning small" style="letter-spacing: .5px;">
                                <i class="mdi mdi-clock-alert-outline me-1"></i> Belum Dikonfirmasi (Un-Confirm)
                            </span>
                            <div class="avatar avatar-xs bg-label-warning rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="mdi mdi-clock-alert-outline fs-6"></i>
                            </div>
                        </div>
                        <h3 class="fw-bolder text-warning fs-4 mb-1" id="summary-payment-unconfirm">Rp {{ number_format(@$unconfirm, 0, ',', '.') }}</h3>
                        <small class="text-muted" style="font-size: 11px;">Pembayaran menunggu verifikasi bagian finance</small>
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
                        <h6 class="fw-bold mb-0 text-dark">Filter Payment</h6>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted small fw-semibold text-nowrap">Filter Tahun:</label>
                            <select class="form-select form-select-sm" id="payment-year-filter" style="min-width:140px;">
                                <option value="all">Semua Tahun</option>
                                @for ($y = now()->year; $y >= 2022; $y--)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <label class="form-label mb-0 text-muted small fw-semibold text-nowrap">Filter Sales:</label>
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

        {{-- Main Table Container --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="mdi mdi-receipt-text-outline me-2 text-primary fs-5"></i> Daftar Bukti Pembayaran (Payment Receipt)
                </h6>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-payment-receipt-ar table table-hover border-top">
                    <thead class="table-light">
                        <tr>
                            <th class="fw-semibold text-dark">Receipt &amp; Ref</th>
                            <th class="fw-semibold text-dark">Customer &amp; Sales</th>
                            <th class="fw-semibold text-dark text-end">Nominal &amp; Sisa</th>
                            <th class="fw-semibold text-dark text-center">Status &amp; Verifikasi</th>
                        </tr>
                    </thead>
                </table>
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
