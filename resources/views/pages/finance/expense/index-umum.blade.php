@extends('layouts.sales.app')
@section('title', 'Expense Kas Umum - Finance ERP')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css"/>
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
        .datatable-expense-umum-data th {
            font-size: 11.5px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700 !important;
            background-color: #f8fafc !important;
            color: #475569 !important;
        }
        .dark-style .datatable-expense-umum-data th {
            background-color: rgba(255, 255, 255, 0.04) !important;
            color: #cbd5e1 !important;
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance /</span> Expense Kas Umum
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-cash-register me-1 text-primary"></i> Pencatatan jurnal kas umum pengeluaran operasional tunai non-rekening bank
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('finance.expense-budget.index') }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-chart-timeline-variant me-1"></i> Budgeting
            </a>
            <a href="{{ route('expense-umum.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Buat Kas Umum Baru
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
        {{-- KPI 1: Pengeluaran Kas Bulan Ini --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Kas Umum Bulan Ini</span>
                        <div class="avatar avatar-sm bg-label-danger rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cash-minus fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-danger mb-1">
                        Rp {{ number_format($currentMonthKas, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">{{ Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                        <span class="badge bg-label-danger fw-bold rounded-pill">{{ $currentMonthCount }} Transaksi</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 2: Total Akumulasi Kas Umum --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Pengeluaran Kas</span>
                        <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-wallet-outline fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-primary mb-1">
                        Rp {{ number_format($totalKasUmum, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Total Keseluruhan Kas</span>
                        <span class="badge bg-label-primary fw-bold rounded-pill">All Time</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 3: Jumlah Voucher Kas --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Voucher Kas</span>
                        <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-receipt-text-outline fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-info mb-1">
                        {{ number_format($countKasUmum, 0, ',', '.') }} Voucher
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Transaksi Kas Tunai</span>
                        <span class="badge bg-label-info fw-bold rounded-pill">Total Slip</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 4: Rata-rata per Kas Bon --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Rata-rata per Transaksi</span>
                        <div class="avatar avatar-sm bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-chart-bell-curve-cumulative fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">
                        Rp {{ number_format($avgKasUmum, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Average Ticket Size</span>
                        <span class="badge bg-label-warning text-dark fw-bold rounded-pill">Avg Kas</span>
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
            <a class="nav-link active" href="{{ route('expense-umum.index') }}">
                <i class="mdi mdi-cash-register me-1"></i> Expense Kas Umum
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-inventory.index') }}">
                <i class="mdi mdi-package-variant-closed me-1"></i> Inventory Adjustment
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('expense-ongkir.index') }}">
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
                        <i class="mdi mdi-cash-register text-primary fs-5"></i>
                        Daftar Transaksi Expense Kas Umum
                    </h6>
                    <small class="text-muted">Buku kas pengeluaran operasional tunai langsung</small>
                </div>

                {{-- Interactive Filter Toolbar --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <select id="filter-umum-year" class="form-select form-select-sm" style="width: 130px;">
                        <option value="" selected>Semua Tahun</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                        @endforeach
                    </select>

                    <select id="filter-umum-month" class="form-select form-select-sm" style="width: 140px;">
                        <option value="" selected>Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">
                                {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>

                    <button type="button" id="btn-reset-umum-filter" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table Element --}}
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-expense-umum-data table table-hover border-bottom mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 110px;">Tanggal</th>
                        <th style="min-width: 250px;">Keperluan / Memo Transaksi</th>
                        <th style="min-width: 150px;">No. Bukti / Invoice</th>
                        <th style="min-width: 140px;">Ref. Kas Bon / Cek</th>
                        <th class="text-end" style="min-width: 140px;">Nominal (IDR)</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-expense-umum-data.js?v={{ time() }}"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $(document).on('click', '.delete-expense', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: "Hapus Transaksi Kas Umum?",
                text: "Transaksi pengeluaran ini tidak akan dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('expense') }}/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil Dihapus!",
                                    text: "Voucher pengeluaran kas telah dihapus.",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $('.datatable-expense-umum-data').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Gagal menghapus voucher pengeluaran!'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan sistem saat menghapus data!'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
