@extends('layouts.sales.app')
@section('title', 'GR Belum Ditagih')
@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Account Payable /</span> GR Belum Ditagih
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-package-variant-closed me-1"></i>
                Barang sudah diterima (Goods Receipt) tapi invoice supplier belum masuk &mdash; belum jadi hutang AP
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('payable.index_invoice') }}" class="btn btn-label-primary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Purchase Invoice
            </a>
        </div>
    </div>

    {{-- Metric Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff8f8 0%, #ffeded 100%); border-left: 4px solid #ff3e1d !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-uppercase fw-bold text-danger small" style="font-size: 11px;">Total GR Belum Ditagih</span>
                        <span class="badge bg-label-danger rounded-pill px-2 py-1">{{ number_format($totalCount ?? 0) }}</span>
                    </div>
                    <div class="fw-bolder text-danger fs-5 mb-0">Rp {{ number_format($totalAmount ?? 0, 0, ',', '.') }}</div>
                    <small class="text-muted" style="font-size: 10px;">Akrual barang diterima, menunggu invoice</small>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fffcf0 0%, #fef3c7 100%); border-left: 4px solid #f59e0b !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-uppercase fw-bold text-warning small" style="font-size: 11px;">
                            <i class="mdi mdi-clock-alert-outline me-1"></i> &gt; 30 Hari
                        </span>
                        <span class="badge bg-warning text-dark rounded-pill px-2 py-1">{{ number_format($overdue30 ?? 0) }}</span>
                    </div>
                    <div class="fw-bolder text-dark fs-5 mb-0">{{ number_format($overdue30 ?? 0) }} GR</div>
                    <small class="text-muted" style="font-size: 10px;">Perlu ditindaklanjuti ke supplier</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Main DataTable Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="mdi mdi-format-list-bulleted me-2 text-primary fs-5"></i> Daftar GR Belum Ditagih
            </h6>
            <span class="badge bg-label-secondary">{{ number_format($totalCount ?? 0) }} Total Data</span>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-gr-uninvoiced table table-hover border-top">
                <thead class="table-light">
                    <tr>
                        <th class="fw-semibold text-dark">No. GR</th>
                        <th class="fw-semibold text-dark">Tgl Terima</th>
                        <th class="fw-semibold text-dark">Supplier</th>
                        <th class="fw-semibold text-dark">No. PO</th>
                        <th class="fw-semibold text-dark text-center">Qty</th>
                        <th class="fw-semibold text-dark text-end">Nilai</th>
                        <th class="fw-semibold text-dark text-center">Umur</th>
                        <th class="fw-semibold text-dark text-center">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <style>
        .datatable-gr-uninvoiced thead th {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .datatable-gr-uninvoiced input.form-control {
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 4px;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/includes/table-ap-gr-uninvoiced.js?v={{ time() }}"></script>
@endpush
