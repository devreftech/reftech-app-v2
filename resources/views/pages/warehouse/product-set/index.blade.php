@extends('layouts.sales.app')
@section('title', 'Data Product Set')
@section('content')
    <div class="container-fluid px-0 py-2">
        {{-- Page Header --}}
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h4 class="fw-bolder text-dark mb-1">
                    <span class="text-muted fw-light">Warehouse /</span> Product Set
                </h4>
                <p class="text-muted mb-0 small">
                    Kelola bundle produk set (Bearing Kit &amp; Non Bearing Kit) dan komponen replacement penyusunnya secara terpadu.
                </p>
            </div>
            <div>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#createProduct">
                    <i class="mdi mdi-plus fs-5"></i>
                    <span>Tambah Product Set</span>
                </button>
            </div>
        </div>

        {{-- Top KPI Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f0f4ff 0%, #ffffff 100%); border-left: 4px solid #696cff !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px; font-size: 11px;">
                                Total Product Set
                            </span>
                            <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="mdi mdi-package-variant-closed fs-6"></i>
                            </div>
                        </div>
                        <h3 class="fw-bolder text-dark fs-4 mb-1">{{ number_format($totalSet ?? 0, 0, ',', '.') }}</h3>
                        <small class="text-muted fw-semibold" style="font-size: 11.5px;">Bundle Terdaftar</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f3e8ff 0%, #ffffff 100%); border-left: 4px solid #696cff !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px; font-size: 11px;">
                                Bearing Kit
                            </span>
                            <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="mdi mdi-cog-sync-outline fs-6"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <h3 class="fw-bolder text-primary fs-4 mb-0">{{ number_format($bearingCount ?? 0, 0, ',', '.') }}</h3>
                            <span class="text-muted small">Bundle</span>
                        </div>
                        <div class="d-flex flex-wrap gap-1 mt-1" style="font-size: 10.5px;">
                            <span class="badge bg-label-primary py-0 px-1" title="Bearing Kit Airend">Airend: {{ $bearingAirendCount ?? 0 }}</span>
                            <span class="badge bg-label-info py-0 px-1" title="Bearing Kit Main Motor">Main: {{ $bearingMainMotorCount ?? 0 }}</span>
                            <span class="badge bg-label-warning py-0 px-1" title="Bearing Kit Fan Motor">Fan: {{ $bearingFanMotorCount ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #e8f9ff 0%, #ffffff 100%); border-left: 4px solid #03c3ec !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fw-bold text-info small" style="letter-spacing: .5px; font-size: 11px;">
                                Non Bearing Kit
                            </span>
                            <div class="avatar avatar-xs bg-label-info rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="mdi mdi-layers-outline fs-6"></i>
                            </div>
                        </div>
                        <h3 class="fw-bolder text-dark fs-4 mb-1">{{ number_format($nonBearingCount ?? 0, 0, ',', '.') }}</h3>
                        <small class="text-muted fw-semibold" style="font-size: 11.5px;">Bundle Non Bearing</small>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #e8fadf 0%, #ffffff 100%); border-left: 4px solid #71dd37 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px; font-size: 11px;">
                                Ready Stock
                            </span>
                            <div class="avatar avatar-xs bg-label-success rounded p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="mdi mdi-check-circle-outline fs-6"></i>
                            </div>
                        </div>
                        <h3 class="fw-bolder text-success fs-4 mb-1">{{ number_format($inStock ?? 0, 0, ',', '.') }}</h3>
                        <small class="text-muted fw-semibold" style="font-size: 11.5px;">Set Tersedia (Stok &gt; 0)</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="card border-0 shadow-sm">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-product-set table table-hover align-middle mb-0" style="font-size: 13px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 20px;"></th>
                            <th>ID</th>
                            <th>Product Set</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Komponen</th>
                            <th class="text-center">Total Stock</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('components.modal.warehouse.product-set.form')
    @include('components.modal.warehouse.product-set.view-components')
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .modal.fade .modal-dialog {
            transform: scale(0.94) translateY(-15px);
            opacity: 0;
            transition: transform 0.26s cubic-bezier(0.2, 0.9, 0.3, 1.15), opacity 0.22s ease-out !important;
        }
        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
            opacity: 1;
        }
        .modal-backdrop.fade {
            opacity: 0;
            transition: opacity 0.22s ease-out !important;
        }
        .modal-backdrop.show {
            opacity: 0.45;
            backdrop-filter: blur(2px);
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/includes/table-product-set.js?v={{ time() }}"></script>
    <script>
        $(document).ready(function() {
            $('.select2-category').select2({
                dropdownParent: $('#createProduct'),
                tags: true,
                width: '100%',
                placeholder: 'Pilih atau ketik kategori baru...'
            });
        });
    </script>
@endpush
