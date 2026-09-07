@extends('layouts.sales.app')
@section('title', 'Purchase Invoice AP')
@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Account Payable /</span> Purchase Invoice
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-receipt-text-outline me-1"></i> Kelola data faktur pembelian barang &amp; status pelunasan cicilan ke supplier
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('payable.statement') }}" class="btn btn-label-info btn-sm">
                <i class="mdi mdi-book-open-outline me-1"></i> Kartu Hutang (SOA)
            </a>
            <a href="{{ route('payable.expenses') }}" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-cash-multiple me-1"></i> Biaya Proyek
            </a>
            <a href="{{ route('payable.index_receipt') }}" class="btn btn-label-primary btn-sm">
                <i class="mdi mdi-cash-check me-1"></i> Purchase Payment
            </a>
            <a href="{{ route('payable.index_aging') }}" class="btn btn-label-warning btn-sm">
                <i class="mdi mdi-calendar-clock-outline me-1"></i> Aging Report
            </a>
        </div>
    </div>

    {{-- Metric Cards (Point 4: Due Date Alert & Badges) --}}
    <div class="row g-3 mb-4">
        {{-- Total Invoice --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 4px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-uppercase fw-bold text-primary small" style="font-size: 11px;">
                            Total Invoice
                        </span>
                        <span class="badge bg-label-primary rounded-pill px-2 py-1">{{ number_format($totalCount ?? 0) }}</span>
                    </div>
                    <div class="fw-bolder text-primary fs-5 mb-0">
                        Rp {{ number_format($totalAmount ?? 0, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 10px;">Semua faktur pembelian</small>
                </div>
            </div>
        </div>

        {{-- Paid Invoices --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%); border-left: 4px solid #28a745 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-uppercase fw-bold text-success small" style="font-size: 11px;">
                            Sudah Lunas
                        </span>
                        <span class="badge bg-label-success rounded-pill px-2 py-1">{{ number_format($paidCount ?? 0) }}</span>
                    </div>
                    <div class="fw-bolder text-success fs-5 mb-0">
                        Rp {{ number_format($paidAmount ?? 0, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 10px;">Faktur terbayar lunas</small>
                </div>
            </div>
        </div>

        {{-- Partial & Unpaid --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff8f8 0%, #ffeded 100%); border-left: 4px solid #ff3e1d !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-uppercase fw-bold text-danger small" style="font-size: 11px;">
                            Belum Lunas / Partial
                        </span>
                        <span class="badge bg-label-danger rounded-pill px-2 py-1">{{ number_format(($unpaidCount ?? 0) + ($partialCount ?? 0)) }}</span>
                    </div>
                    <div class="fw-bolder text-danger fs-5 mb-0">
                        Rp {{ number_format(($unpaidAmount ?? 0) + ($partialAmount ?? 0), 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 10px;">{{ $partialCount ?? 0 }} partial, {{ $unpaidCount ?? 0 }} unpaid</small>
                </div>
            </div>
        </div>

        {{-- Due Alerts (Overdue & Due Soon) --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fffcf0 0%, #fef3c7 100%); border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-uppercase fw-bold text-warning small" style="font-size: 11px;">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> Due Date Alert
                        </span>
                        @if(($overdueCount ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill px-2 py-1">{{ $overdueCount }} Overdue</span>
                        @elseif(($dueSoonCount ?? 0) > 0)
                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1">{{ $dueSoonCount }} Soon</span>
                        @else
                            <span class="badge bg-label-success rounded-pill px-2 py-1">Aman</span>
                        @endif
                    </div>
                    <div class="fw-bolder text-dark fs-5 mb-0">
                        Rp {{ number_format(($overdueAmount ?? 0) + ($dueSoonAmount ?? 0), 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 10px;">{{ $overdueCount ?? 0 }} jatuh tempo, {{ $dueSoonCount ?? 0 }} segera</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Main DataTable Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="mdi mdi-format-list-bulleted me-2 text-primary fs-5"></i> Daftar Purchase Invoice
            </h6>
            <span class="badge bg-label-secondary" id="invoice-count-badge">{{ number_format($totalCount ?? 0) }} Total Data</span>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-sales-invoice-ap table table-hover border-top">
                <thead class="table-light">
                    <tr>
                        <th class="fw-semibold text-dark">Invoice No.</th>
                        <th class="fw-semibold text-dark">Date / Due</th>
                        <th class="fw-semibold text-dark">Supplier</th>
                        <th class="fw-semibold text-dark text-end">Total &amp; Sisa</th>
                        <th class="fw-semibold text-dark text-center">Total Item</th>
                        <th class="fw-semibold text-dark text-center">Status</th>
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
        .datatable-sales-invoice-ap thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .datatable-sales-invoice-ap input.form-control {
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
    <script src="{{ asset('assets') }}/includes/table-ap-invoice.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
