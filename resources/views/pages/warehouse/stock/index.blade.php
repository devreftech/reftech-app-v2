@extends('layouts.sales.app')
@section('title', 'Data Product')
@section('content')
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Warehouse &amp; Inventory /</span> Product Stock &amp; Safety Stock Alert
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-cube-outline me-1"></i> Monitoring stok sparepart gudang, peringatan batas minimum (Safety Stock), dan ketersediaan barang
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('product.index') }}" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-format-list-bulleted me-1"></i> Master Product
            </a>
        </div>
    </div>

    {{-- Stock Health KPI Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Total SKU Produk</span>
                        <h4 class="fw-bolder text-dark mb-0 mt-1">{{ number_format($totalProducts ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted" style="font-size: 10px;">Item terdaftar</small>
                    </div>
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-primary"><i class="mdi mdi-cube-send fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Total Kuantitas Fisik</span>
                        <h4 class="fw-bolder text-primary mb-0 mt-1">{{ number_format($totalStock ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted" style="font-size: 10px;">Unit / pcs di gudang</small>
                    </div>
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-info"><i class="mdi mdi-layers-triple fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="background-color: {{ ($lowStockCount ?? 0) > 0 ? '#fffbeb' : '#fff' }}; border-color: {{ ($lowStockCount ?? 0) > 0 ? '#fde68a' : 'transparent' }} !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-warning small fw-semibold text-uppercase" style="font-size: 11px;">Low Stock (Safety Alert)</span>
                        <h4 class="fw-bolder text-warning mb-0 mt-1">{{ number_format($lowStockCount ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted" style="font-size: 10px;">Stok &le; Batas Minimum</small>
                    </div>
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-warning"><i class="mdi mdi-alert-circle-outline fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100" style="background-color: {{ ($outOfStockCount ?? 0) > 0 ? '#fef2f2' : '#fff' }}; border-color: {{ ($outOfStockCount ?? 0) > 0 ? '#fecaca' : 'transparent' }} !important;">
                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-danger small fw-semibold text-uppercase" style="font-size: 11px;">Out of Stock (Habis)</span>
                        <h4 class="fw-bolder text-danger mb-0 mt-1">{{ number_format($outOfStockCount ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted" style="font-size: 10px;">Stok = 0 (Perlu Restock)</small>
                    </div>
                    <div class="avatar avatar-md flex-shrink-0">
                        <span class="avatar-initial rounded bg-label-danger"><i class="mdi mdi-close-circle-outline fs-4"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-stock table table-bordered">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Commodity / Nama Barang</th>
                        <th>Deskripsi</th>
                        <th>Stok Awal</th>
                        <th>Stok Terkini</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection()

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
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
    <script src="{{ asset('assets') }}/includes/table-stock.js"></script>
@endpush
