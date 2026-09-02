@extends('layouts.sales.app')
@section('title', 'Purchase Payment AP')
@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Account Payable /</span> Purchase Payment
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-cash-check me-1"></i> Daftar bukti pembayaran dan tanda terima transaksi pembelian supplier
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('payable.index_invoice') }}" class="btn btn-label-primary btn-sm">
                <i class="mdi mdi-receipt-text-outline me-1"></i> Purchase Invoice
            </a>
            <a href="{{ route('payable.index_aging') }}" class="btn btn-label-warning btn-sm">
                <i class="mdi mdi-calendar-clock-outline me-1"></i> Aging Report
            </a>
        </div>
    </div>

    {{-- Metric Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Payment Receipt --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px;">
                            <i class="mdi mdi-receipt-outline me-1"></i> Total Purchase Receipt
                        </span>
                        <span class="badge bg-label-primary rounded-pill px-2 py-1">{{ number_format($totalCount ?? 0) }} Bukti</span>
                    </div>
                    <div class="fw-bolder text-primary fs-4 mb-0">
                        Rp {{ number_format($receipt ?? 0, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 11px;">Total nilai seluruh pembayaran pembelian</small>
                </div>
            </div>
        </div>

        {{-- Paid Receipt --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%); border-left: 5px solid #28a745 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px;">
                            <i class="mdi mdi-check-decagram-outline me-1"></i> Sudah Dibayar (Paid)
                        </span>
                        <span class="badge bg-label-success rounded-pill px-2 py-1">{{ number_format($paidCount ?? 0) }} Lunas</span>
                    </div>
                    <div class="fw-bolder text-success fs-4 mb-0">
                        Rp {{ number_format($paid ?? 0, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 11px;">Pembelian yang sudah lunas terverifikasi</small>
                </div>
            </div>
        </div>

        {{-- Unpaid Receipt --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff8f8 0%, #ffeded 100%); border-left: 5px solid #ff3e1d !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-danger small" style="letter-spacing: .5px;">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> Belum Dibayar (Unpaid)
                        </span>
                        <span class="badge bg-label-danger rounded-pill px-2 py-1">{{ number_format($unpaidCount ?? 0) }} Tertunda</span>
                    </div>
                    <div class="fw-bolder text-danger fs-4 mb-0">
                        Rp {{ number_format($unpaid ?? 0, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 11px;">Pembelian yang belum diselesaikan pembayarannya</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Main DataTable Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="mdi mdi-format-list-bulleted me-2 text-primary fs-5"></i> Daftar Purchase Payment &amp; Bukti Transaksi
            </h6>
            <span class="badge bg-label-secondary">{{ number_format($totalCount ?? 0) }} Total Data</span>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-sales-receipt-ap table table-hover border-top">
                <thead class="table-light">
                    <tr>
                        <th class="fw-semibold text-dark">No. Receipt</th>
                        <th class="fw-semibold text-dark">Date</th>
                        <th class="fw-semibold text-dark">Invoice</th>
                        <th class="fw-semibold text-dark">Supplier</th>
                        <th class="fw-semibold text-dark text-end">Total</th>
                        <th class="fw-semibold text-dark text-center">Status</th>
                        <th class="fw-semibold text-dark text-center">Info</th>
                    </tr>
                </thead>
            </table>
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
        .datatable-sales-receipt-ap thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .datatable-sales-receipt-ap input.form-control {
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
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ap-receipt.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
