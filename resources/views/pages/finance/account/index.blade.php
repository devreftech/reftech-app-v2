@extends('layouts.sales.app')
@section('title', 'Chart of Accounts (COA) - Finance ERP')
@section('no-container') @endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
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
        .datatable-account-data th {
            font-size: 11.5px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700 !important;
            background-color: #f8fafc !important;
            color: #475569 !important;
        }
        .dark-style .datatable-account-data th {
            background-color: rgba(255, 255, 255, 0.04) !important;
            color: #cbd5e1 !important;
        }
        .code-pill {
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance /</span> Chart of Accounts (COA)
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-format-list-bulleted-type me-1 text-primary"></i> Master basis rekening akun pembukuan, klasifikasi aset, kewajiban, modal, pendapatan, dan beban
            </p>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('finance.expense-budget.index') }}" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
                <i class="mdi mdi-chart-timeline-variant me-1"></i> Budgeting
            </a>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createAccount">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Akun COA
            </button>
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
        {{-- KPI 1: Total Master Akun --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Total Master COA</span>
                        <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-file-tree-outline fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-primary mb-1">
                        {{ number_format($totalAccounts, 0, ',', '.') }} Akun
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Struktur Chart of Accounts</span>
                        <span class="badge bg-label-primary fw-bold rounded-pill">Active</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 2: Akun Induk (Header Level 1) --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Akun Induk (Header Lvl 1)</span>
                        <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-folder-outline fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-info mb-1">
                        {{ number_format($headerAccounts, 0, ',', '.') }} Header
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Kelompok Utama Akun</span>
                        <span class="badge bg-label-info fw-bold rounded-pill">Level 1</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 3: Sub-Akun Transaksi (Level 2) --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Sub-Akun Transaksi (Lvl 2)</span>
                        <div class="avatar avatar-sm bg-label-success rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-format-list-checks fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-success mb-1">
                        {{ number_format($detailAccounts, 0, ',', '.') }} Sub-Akun
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Siap Posting Transaksi</span>
                        <span class="badge bg-label-success fw-bold rounded-pill">Posting</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 4: Kategori Akuntansi --}}
        <div class="col-xl-3 col-sm-6 col-12">
            <div class="card finance-kpi-card h-100 rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Klasifikasi Kategori</span>
                        <div class="avatar avatar-sm bg-label-warning rounded-3 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-shape-outline fs-4"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">
                        {{ $categoriesCount }} Kategori
                    </h4>
                    <div class="d-flex align-items-center justify-content-between small">
                        <span class="text-muted">Asset, Liability, Equity, dll</span>
                        <span class="badge bg-label-warning text-dark fw-bold rounded-pill">Standar PSAK</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Main Datatable Card --}}
    <div class="card border-0 shadow-sm rounded-3">
        {{-- Card Header with Filter Toolbar --}}
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="mdi mdi-format-list-bulleted-square text-primary fs-5"></i>
                        Daftar Chart of Accounts (COA)
                    </h6>
                    <small class="text-muted">Hirarki kode akun, nama rekening, klasifikasi tipe beban, dan status saldo</small>
                </div>

                {{-- Interactive Filter Toolbar --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <select id="filter-account-category" class="form-select form-select-sm" style="width: 200px;">
                        <option value="" selected>Semua Kategori</option>
                        @foreach($availableCategories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>

                    <select id="filter-account-level" class="form-select form-select-sm" style="width: 150px;">
                        <option value="" selected>Semua Level</option>
                        <option value="1">Level 1 (Induk)</option>
                        <option value="2">Level 2 (Sub-Akun)</option>
                    </select>

                    <button type="button" id="btn-reset-account-filter" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Table Element --}}
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-account-data table table-hover border-bottom mb-0">
                <thead>
                    <tr>
                        <th style="width: 140px;">Kode Akun</th>
                        <th style="min-width: 230px;">Nama Akun (COA)</th>
                        <th style="min-width: 180px;">Kategori Akuntansi</th>
                        <th style="width: 110px;">Currency</th>
                        <th style="width: 110px;">Saldo Normal</th>
                        <th class="text-center" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>

@include('components.modal.finance.form-account')
@include('components.modal.finance.edit-account')
@endsection

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-account-data.js?v={{ time() }}"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $(document).on('click', '.editAccount', function() {
            let id = $(this).data('id');
            $('#editForm').attr('action', '/expense-account/' + id);

            $('.edit_code').val('');
            $('.edit_category').val('');
            $('.edit_name').val('');
            $('.edit_currency').val('');
            $('.edit_saldo').val('');
            $('.edit_parent').val('').trigger('change');

            $.ajax({
                url: '/get/account/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    $('.edit_code').val(res.code);
                    $('.edit_category').val(res.category);
                    $('.edit_name').val(res.name);
                    $('.edit_currency').val(res.currency || 'IDR');
                    $('.edit_saldo').val(res.saldo);
                    if (res.parent && $("#parent option[value='" + res.parent + "']").length) {
                        $('#parent').val(String(res.parent)).trigger('change');
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal memuat detail akun!'
                    });
                }
            });
        });

        $(document).on('click', '.delete-account', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: "Hapus Akun COA?",
                text: "Akun ini tidak akan dapat dipulihkan jika dihapus!",
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
                        url: '{{ url('expense-account') }}/' + id,
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
                                    text: "Data akun telah berhasil dihapus dari COA.",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                $('.datatable-account-data').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: 'Akun gagal dihapus. Mungkin sedang digunakan dalam transaksi!'
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
