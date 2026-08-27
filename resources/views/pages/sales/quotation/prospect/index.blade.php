@extends('layouts.sales.app')
@section('title', 'Quotation Prospects — Pipeline')

@section('content')
    {{-- Hero Header Card --}}
    <div class="card border-0 shadow-sm mb-4 prospect-hero-card">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-label-primary px-3 py-1 rounded-pill fw-semibold">
                            <i class="mdi {{ Auth::user()->role == 'Admin' ? 'mdi-shield-crown-outline' : 'mdi-account-tie-outline' }} me-1"></i>
                            {{ Auth::user()->role == 'Admin' ? 'Admin Overview' : 'Sales Pipeline' }}
                        </span>
                        <span class="text-muted small">&bull;</span>
                        <span class="text-muted small">Marketing Prospect Conversion</span>
                    </div>
                    <h3 class="fw-bold text-heading mb-1">
                        {{ Auth::user()->role == 'Admin' ? 'Quotation Prospect Pipeline (All Sales)' : 'My Prospect Quotations' }}
                    </h3>
                    <p class="text-muted mb-0">
                        {{ Auth::user()->role == 'Admin'
                            ? 'Monitoring seluruh penawaran harga aktif hasil konversi prospek tim marketing.'
                            : 'Kelola dokumen penawaran harga aktif hasil alokasi prospek dari tim marketing.' }}
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-label-primary fs-6 px-3 py-2 rounded-pill">
                        <i class="mdi mdi-file-document-outline me-1"></i> {{ $totalQuotesCount ?? 0 }} Total Quote
                    </span>
                    <span class="badge bg-label-warning fs-6 px-3 py-2 rounded-pill">
                        <i class="mdi mdi-fire me-1"></i> {{ $hotQuotesCount ?? 0 }} Hot 80%
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Executive KPI Cards --}}
    <div class="row g-3 mb-4">
        {{-- Total Active Pipeline --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-primary">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-primary rounded-3 shadow-xs">
                                <i class="mdi mdi-file-document-multiple-outline fs-4"></i>
                            </div>
                        </div>
                        <span class="badge bg-label-primary rounded-pill small px-2 py-1">Pipeline</span>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Pipeline</span>
                        <h4 class="fw-bold mb-1 text-primary">Rp {{ number_format($totalQuotesSum ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted">{{ $totalQuotesCount ?? 0 }} Dokumen Penawaran Aktif</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hot Prospects 80% --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-warning">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-warning rounded-3 shadow-xs">
                                <i class="mdi mdi-fire fs-4"></i>
                            </div>
                        </div>
                        <span class="badge bg-label-warning rounded-pill small px-2 py-1">Hot 80%</span>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Hot Prospects</span>
                        <h4 class="fw-bold mb-1 text-warning">Rp {{ number_format($hotQuotesSum ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted">{{ $hotQuotesCount ?? 0 }} Prospek Probabilitas Tinggi</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Negotiation 60% --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-info">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-info rounded-3 shadow-xs">
                                <i class="mdi mdi-handshake-outline fs-4"></i>
                            </div>
                        </div>
                        <span class="badge bg-label-info rounded-pill small px-2 py-1">Nego 60%</span>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Negotiation / Revisi</span>
                        <h4 class="fw-bold mb-1 text-info">Rp {{ number_format($negotiationSum ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted">{{ $negotiationCount ?? 0 }} Transaksi Negosiasi</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Initial Follow Up 20-40% --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 border-0 shadow-sm prospect-kpi-card border-start-secondary">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="avatar avatar-md">
                            <div class="avatar-initial bg-label-secondary rounded-3 shadow-xs">
                                <i class="mdi mdi-progress-clock fs-4 text-dark"></i>
                            </div>
                        </div>
                        <span class="badge bg-label-secondary rounded-pill small px-2 py-1">FU 20-40%</span>
                    </div>
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Initial Follow Up</span>
                        <h4 class="fw-bold mb-1 text-dark">Rp {{ number_format($progressSum ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted">{{ $progressCount ?? 0 }} Prospek Dalam Tahap Awal</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main DataTable Card (Clean, Modern, No Checkboxes, No Striped) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-quotation-prospect{{ Auth::user()->role == 'Admin' ? '' : '-sales' }} table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="min-width: 200px;">Quote No. & Title</th>
                        <th style="min-width: 200px;">Company (Client)</th>
                        <th class="text-end" style="min-width: 140px;">Total Price (Rp)</th>
                        <th style="min-width: 200px;">Description / Note</th>
                        <th style="min-width: 130px;">Target Date</th>
                        <th class="text-center" style="min-width: 150px;">Status</th>
                        @if (Auth::user()->role == 'Admin')
                            <th style="min-width: 140px;">Assigned Sales</th>
                        @endif
                        <th class="text-center" style="min-width: 80px;">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <style>
        .prospect-hero-card {
            background: linear-gradient(135deg, #ffffff 0%, #f4f6ff 100%);
            border: 1px solid rgba(105, 108, 255, 0.15) !important;
            border-radius: 12px;
        }
        .prospect-kpi-card {
            border-radius: 12px;
            background: #ffffff;
            transition: all 0.25s ease-in-out;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.08);
        }
        .prospect-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 16px 0 rgba(67, 89, 113, 0.14);
        }
        .border-start-primary   { border-left: 4px solid #696cff !important; }
        .border-start-warning   { border-left: 4px solid #ffab00 !important; }
        .border-start-info      { border-left: 4px solid #03c3ec !important; }
        .border-start-secondary { border-left: 4px solid #8592a3 !important; }
        .border-start-success   { border-left: 4px solid #71dd37 !important; }
        .border-start-danger    { border-left: 4px solid #ff3e1d !important; }
        .text-primary-hover:hover { color: #696cff !important; }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-quotation-prospect.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation-prospect-sales.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
