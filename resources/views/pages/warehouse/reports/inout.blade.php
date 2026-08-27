@extends('layouts.sales.app')
@section('title', 'Year Reports')
@section('content')
    <div class="d-flex align-items-center justify-content-between py-3 mb-2">
        <div>
            <h4 class="fw-bold m-0 text-dark">Report Product In - Out</h4>
            <p class="text-muted small mb-0">Ringkasan pergerakan stok per produk untuk tahun {{ $year }}</p>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary dropdown-toggle waves-effect waves-light"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="mdi mdi-calendar-outline me-1"></i> Tahun {{ $year }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @forelse ($availableYears as $y)
                    <li>
                        <a class="dropdown-item waves-effect {{ (int) $year === $y ? 'active' : '' }}"
                            href="{{ route('reports.yearly', $y) }}">Tahun {{ $y }}</a>
                    </li>
                @empty
                    <li><span class="dropdown-item-text text-muted">Belum ada data</span></li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Stat summary cards --}}
    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-5 g-3 mb-4">
        <div class="col">
            <div class="card h-100 border-0 custom-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Total SKU</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-secondary shadow-xs"><i class="mdi mdi-format-list-bulleted mdi-20px"></i></span>
                        </div>
                    </div>
                    <h4 class="mb-0 fw-bold text-dark">{{ number_format($summary['totalSku'], 0, '', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 custom-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">SKU Bergerak</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-primary shadow-xs"><i class="mdi mdi-swap-vertical mdi-20px"></i></span>
                        </div>
                    </div>
                    <h4 class="mb-0 fw-bold text-dark">{{ number_format($summary['totalActive'], 0, '', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 custom-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Total Masuk</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-success shadow-xs"><i class="mdi mdi-tray-arrow-down mdi-20px"></i></span>
                        </div>
                    </div>
                    <h4 class="mb-0 fw-bold text-dark">{{ number_format($summary['totalIn'], 0, '', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 custom-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Total Keluar</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-danger shadow-xs"><i class="mdi mdi-tray-arrow-up mdi-20px"></i></span>
                        </div>
                    </div>
                    <h4 class="mb-0 fw-bold text-dark">{{ number_format($summary['totalOut'], 0, '', '.') }}</h4>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card h-100 border-0 custom-stat-card">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold small text-uppercase tracking-wider">Total Stok Saat Ini</span>
                        <div class="avatar avatar-sm">
                            <span class="avatar-initial rounded-3 bg-label-info shadow-xs"><i class="mdi mdi-package-variant-closed mdi-20px"></i></span>
                        </div>
                    </div>
                    <h4 class="mb-0 fw-bold text-dark">{{ number_format($summary['totalStock'], 0, '', '.') }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-sales-reports-year table table-bordered" data-year="{{ $year }}">
                <thead class="table-light text-center align-middle">
                    <tr>
                        <th rowspan="2" style="width: 30px;"></th>
                        <th rowspan="2">SKU</th>
                        <th rowspan="2" style="width: 90px;">G/R</th>
                        <th colspan="2" class="border-bottom">Semester 1</th>
                        <th colspan="2" class="border-bottom">Semester 2</th>
                        <th rowspan="2">Total Product In</th>
                        <th rowspan="2">Total Product Out</th>
                        <th rowspan="2">Current Stock</th>
                    </tr>
                    <tr>
                        <th>Product In</th>
                        <th>Product Out</th>
                        <th>Product In</th>
                        <th>Product Out</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    {{-- @include('pages.warehouse.reports.form') --}}
@endsection()

@push('after-style')
    <style>
        .custom-stat-card {
            border-radius: 12px !important;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
        }
        .custom-stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08) !important;
        }
        .tracking-wider {
            letter-spacing: 0.5px;
            font-size: 0.725rem;
        }
        table.datatable-sales-reports-year td, table.datatable-sales-reports-year th {
            font-size: 13.5px;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/includes/table-sales-reports-year.js"></script>
@endpush
