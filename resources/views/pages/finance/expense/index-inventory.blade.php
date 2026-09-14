@extends('layouts.sales.app')
@section('title', 'Inventory Adjustment - Finance ERP')

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
        .datatable-expense-inventory th {
            font-size: 11.5px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700 !important;
            background-color: #f8fafc !important;
            color: #475569 !important;
        }
        .dark-style .datatable-expense-inventory th {
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
                <span class="text-muted fw-light">Finance /</span> Inventory Adjustment
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-clipboard-text-outline me-1 text-primary"></i> Penyesuaian nilai persediaan gudang, rekonsiliasi stok fisik dengan buku besar (COA Expense &amp; HPP)
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('finance.expense-budget.index') }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-chart-timeline-variant me-1"></i> Budgeting
            </a>
            <a href="{{ route('expense-inventory.create') }}" class="btn btn-primary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Buat Koreksi Stok
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
        {{-- KPI 1: Nilai Koreksi Total --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Nilai Penyesuaian</span>
                        <div class="avatar avatar-sm bg-label-danger rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cash-register fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-danger mb-1">
                        Rp {{ number_format($totalAdjValue, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Total Koreksi Buku</span>
                        <span class="badge bg-label-danger fw-bold rounded-pill">Total Nominal</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 2: Nilai Koreksi Bulan Ini --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Bulan Ini ({{ Carbon\Carbon::now()->translatedFormat('M Y') }})</span>
                        <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-calendar-month-outline fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-primary mb-1">
                        Rp {{ number_format($currentMonthValue, 0, ',', '.') }}
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Penyesuaian Bulan Berjalan</span>
                        <span class="badge bg-label-primary fw-bold rounded-pill">Current MTD</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 3: Jumlah Dokumen Koreksi --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Dokumen Penyesuaian</span>
                        <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-file-document-edit-outline fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-info mb-1">
                        {{ number_format($totalAdjCount, 0, ',', '.') }} Dokumen
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Voucher Koreksi Terbit</span>
                        <span class="badge bg-label-info fw-bold rounded-pill">Voucher</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 4: Total Fisik Item Disesuaikan --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Volume Unit Barang</span>
                        <div class="avatar avatar-sm bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-package-variant fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">
                        {{ number_format($totalItemsAdjusted, 0, ',', '.') }} Unit
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Item Fisik Tersesuaikan</span>
                        <span class="badge bg-label-warning text-dark fw-bold rounded-pill">Total Qty</span>
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
            <a class="nav-link active" href="{{ route('expense-inventory.index') }}">
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
                        <i class="mdi mdi-clipboard-text-outline text-primary fs-5"></i>
                        Daftar Transaksi Inventory Adjustment
                    </h6>
                    <small class="text-muted">Koreksi mutasi barang persediaan gudang ke akun beban / HPP</small>
                </div>

                {{-- Interactive Filter Toolbar --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <select id="filter-inventory-year" class="form-select form-select-sm" style="width: 130px;">
                        <option value="" selected>Semua Tahun</option>
                        @foreach($availableYears as $yr)
                            <option value="{{ $yr }}">Tahun {{ $yr }}</option>
                        @endforeach
                    </select>

                    <select id="filter-inventory-month" class="form-select form-select-sm" style="width: 140px;">
                        <option value="" selected>Semua Bulan</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}">
                                {{ Carbon\Carbon::create(null, $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>

                    <button type="button" id="btn-reset-inventory-filter" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table Element --}}
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-expense-inventory table table-hover border-bottom mb-0">
                <thead>
                    <tr>
                        <th style="min-width: 110px;">Tanggal</th>
                        <th style="min-width: 140px;">No. Dokumen / Inv</th>
                        <th style="min-width: 200px;">Alokasi Akun (COA)</th>
                        <th style="min-width: 220px;">Deskripsi / Memo</th>
                        <th style="min-width: 200px;">Item Barang Pengganti</th>
                        <th class="text-end" style="min-width: 140px;">Nilai Koreksi (IDR)</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
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
    <script src="{{ asset('assets') }}/includes/table-expense-inventory.js?v={{ time() }}"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $(document).on('click', '.delete-inventory', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: "Hapus Penyesuaian Persediaan?",
                text: "Stok produk akan dikembalikan dan jurnal penyesuaian akan dibatalkan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Batalkan & Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('expense-inventory') }}/' + id,
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
                                    text: "Penyesuaian persediaan berhasil dibatalkan.",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $('.datatable-expense-inventory').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Gagal membatalkan penyesuaian persediaan!'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan sistem saat memproses penghapusan!'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
