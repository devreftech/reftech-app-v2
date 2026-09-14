@extends('layouts.sales.app')
@section('title', 'Ongkir Logistik - Finance ERP')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .finance-kpi-card {
            transition: all 0.25s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.07);
        }
        .finance-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px 0 rgba(67, 89, 113, 0.12);
        }
        .nav-tabs-finance .nav-link {
            font-weight: 600;
            color: #64748b;
            border: none;
            border-bottom: 2px solid transparent;
            padding: 0.75rem 1.25rem;
            transition: all 0.2s ease;
        }
        .nav-tabs-finance .nav-link:hover {
            color: #4f46e5;
        }
        .nav-tabs-finance .nav-link.active {
            color: #4f46e5;
            border-bottom-color: #4f46e5;
            background: transparent;
        }
        .datatable-expense-ongkir th {
            font-size: 11.5px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700 !important;
            background-color: #f8fafc !important;
            color: #475569 !important;
        }
        .dark-style .datatable-expense-ongkir th {
            background-color: rgba(255, 255, 255, 0.04) !important;
            color: #cbd5e1 !important;
        }
        .posting-summary-card {
            background: #f8fafc;
            border-left: 4px solid #4f46e5;
            border-radius: 4px;
        }
        .dark-style .posting-summary-card {
            background: rgba(255, 255, 255, 0.03);
            border-left-color: #818cf8;
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance /</span> Ongkir Logistik
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-truck-delivery-outline me-1 text-primary"></i> Rekonsiliasi resi kurir logistik dan posting pengeluaran ongkos kirim ke Finance &amp; Bank
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('finance.expense-budget.index') }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-chart-timeline-variant me-1"></i> Budgeting
            </a>
            <a href="{{ route('expense.index') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-bank-transfer me-1"></i> Expense Bank
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 4 Executive Finance KPI Cards --}}
    <div class="row g-3 mb-4">
        {{-- KPI 1: Menunggu Posting (Pending Action) --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Menunggu Posting</span>
                        <div class="avatar avatar-sm bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-clock-alert-outline fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-warning mb-1">
                        {{ number_format($pendingCount, 0, ',', '.') }} Resi
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Antrian Finance</span>
                        <span class="badge bg-label-warning text-dark fw-bold rounded-pill">Needs Posting</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 2: Total Biaya Pending --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Biaya Ongkir Pending</span>
                        <div class="avatar avatar-sm bg-label-danger rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cash-clock fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-danger mb-1">
                        Rp {{ number_format($pendingCost, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Belum Masuk Beban</span>
                        <span class="badge bg-label-danger fw-bold rounded-pill">Unposted Cost</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 3: Sudah Diposting ke Finance --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Sudah Diposting</span>
                        <div class="avatar avatar-sm bg-label-success rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-check-decagram-outline fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-success mb-1">
                        {{ number_format($postedCount, 0, ',', '.') }} Resi
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Total Rp {{ number_format($postedCost, 0, ',', '.') }}</span>
                        <span class="badge bg-label-success fw-bold rounded-pill">Posted</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 4: Total Resi Terdaftar --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Resi Logistik</span>
                        <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-truck-check-outline fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-primary mb-1">
                        {{ number_format($totalResi, 0, ',', '.') }} Resi
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Pengiriman Charged</span>
                        <span class="badge bg-label-primary fw-bold rounded-pill">Total Logistik</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ERP Navigation Sub-Module Tabs --}}
    <ul class="nav nav-tabs nav-tabs-finance mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense.index') }}">
                <i class="mdi mdi-bank-transfer me-1"></i> Expense Bank
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-umum.index') }}">
                <i class="mdi mdi-cash-register me-1"></i> Expense Kas Umum
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-inventory.index') }}">
                <i class="mdi mdi-package-variant-closed me-1"></i> Inventory Adjustment
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('expense-ongkir.index') }}">
                <i class="mdi mdi-truck-delivery-outline me-1"></i> Ongkir Logistik
            </a>
        </li>
    </ul>

    {{-- Main Datatable Card --}}
    <div class="card border-0 shadow-sm rounded-3">
        {{-- Card Header with Filter Toolbar --}}
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="mdi mdi-truck-delivery text-primary fs-5"></i>
                        Daftar Tagihan Ongkir Ekspedisi &amp; Kurir
                    </h6>
                    <small class="text-muted">Biaya ongkos kirim resi pengiriman logistik yang perlu dibayar dan dibukukan</small>
                </div>

                {{-- Interactive Filter Toolbar --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <select id="filter-ongkir-status" class="form-select form-select-sm" style="width: 170px;">
                        <option value="" selected>Semua Status</option>
                        <option value="pending">Menunggu Posting</option>
                        <option value="posted">Sudah Diposting</option>
                    </select>

                    <select id="filter-ongkir-kurir" class="form-select form-select-sm" style="width: 160px;">
                        <option value="" selected>Semua Ekspedisi</option>
                        @foreach($couriers as $cr)
                            <option value="{{ $cr }}">{{ $cr }}</option>
                        @endforeach
                    </select>

                    <button type="button" id="btn-reset-ongkir-filter" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table Element --}}
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-expense-ongkir table table-hover border-bottom mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 105px;">Tanggal</th>
                        <th style="min-width: 130px;">No. Pending PO</th>
                        <th style="min-width: 220px;">Judul / Penawaran</th>
                        <th style="min-width: 130px;">Kurir</th>
                        <th style="min-width: 160px;">No. Resi (AWB)</th>
                        <th class="text-end" style="min-width: 130px;">Biaya Ongkir</th>
                        <th class="text-center" style="width: 130px;">Status</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>

{{-- Posting Modal --}}
<form id="formPostOngkir" method="post" action="">
    @csrf
    <div class="modal fade" id="postOngkirModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary py-3 text-white">
                    <h5 class="modal-title text-white d-flex align-items-center">
                        <i class="mdi mdi-bank-transfer me-2"></i> Posting Ongkir ke Finance
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 mb-3 posting-summary-card">
                        <div class="d-flex align-items-center mb-1">
                            <i class="mdi mdi-information-outline text-primary me-2 fs-5"></i>
                            <span class="fw-bold text-dark small text-uppercase">Rincian Dokumen Logistik</span>
                        </div>
                        <p class="mb-0 text-dark small fw-medium" id="ongkir-info-text"></p>
                    </div>

                    <div class="form-floating form-floating-outline mb-3">
                        <select class="form-select" id="id_bank" name="id_bank" required>
                            <option value="">---- Pilih Sumber Rekening Kas/Bank ----</option>
                            @foreach ($bank as $b)
                                <option value="{{ $b->id }}">{{ $b->bank }} - {{ $b->no_rek }} (Saldo: Rp {{ number_format($b->saldo, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                        <label for="id_bank">Sumber Kas / Rekening Bank</label>
                    </div>

                    <div class="form-floating form-floating-outline mb-3">
                        <select class="form-select" id="id_account" name="id_account" required>
                            <option value="">---- Pilih Akun Beban (COA) ----</option>
                            @foreach ($account as $a)
                                <option value="{{ $a->id }}">{{ $a->code }} - {{ $a->name }} ({{ $a->category }})</option>
                            @endforeach
                        </select>
                        <label for="id_account">Akun Alokasi Biaya (COA)</label>
                    </div>

                    <div class="form-floating form-floating-outline mb-2">
                        <input type="text" class="form-control" id="memo" name="memo" placeholder="Memo Transaksi">
                        <label for="memo">Keterangan / Memo Pembayaran</label>
                    </div>
                    <small class="text-muted d-block">Saldo rekening bank akan otomatis terpotong dan voucher pengeluaran akan dibuat.</small>
                </div>
                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Otorisasi &amp; Posting
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-expense-ongkir.js?v={{ time() }}"></script>
@endpush
