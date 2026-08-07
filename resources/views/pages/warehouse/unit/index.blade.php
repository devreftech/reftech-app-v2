@extends('layouts.sales.app')
@section('title', 'Unit — Siap Ditawarkan')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Warehouse /</span> Unit — Siap Ditawarkan
        </h4>
    </div>
    <p class="text-muted mb-4">
        Daftar unit second yang sudah lolos pengecekan (QC OK) dan siap ditawarkan ke customer.
        Sumbernya dari <a href="{{ route('unit-acquisition.index') }}">Unit Acquisition</a>.
    </p>

    <div class="card mb-4">
        {{-- ── Top-level category tabs ─────────────────────────────────── --}}
        <div class="card-header p-0 border-bottom">
            <ul class="nav nav-tabs px-3 pt-2" id="unitReadyMainTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active px-3 py-3" data-bs-toggle="tab"
                        data-bs-target="#tab-main-compressor" type="button" role="tab">
                        <i class="mdi mdi-air-conditioner me-1"></i>Air Compressor
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3 py-3" data-bs-toggle="tab"
                        data-bs-target="#tab-main-dryer" type="button" role="tab">
                        <i class="mdi mdi-snowflake me-1"></i>Dryer
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3 py-3" data-bs-toggle="tab"
                        data-bs-target="#tab-main-filtration" type="button" role="tab">
                        <i class="mdi mdi-filter me-1"></i>Filtration System
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-3 py-3" data-bs-toggle="tab"
                        data-bs-target="#tab-main-tank" type="button" role="tab">
                        <i class="mdi mdi-propane-tank me-1"></i>Air Receiver Tank
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content">

            {{-- ── Tab: Air Compressor ─────────────────────────────────── --}}
            <div class="tab-pane fade show active p-0" id="tab-main-compressor" role="tabpanel">
                <ul class="nav nav-tabs px-3 pt-2" id="compressorTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-oil-injected-btn" data-bs-toggle="tab"
                            data-bs-target="#tab-oil-injected" type="button" role="tab">
                            <i class="mdi mdi-water me-1"></i>Oil-Injected
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-oil-free-btn" data-bs-toggle="tab"
                            data-bs-target="#tab-oil-free" type="button" role="tab">
                            <i class="mdi mdi-water-off me-1"></i>Oil-Free
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-compact-btn" data-bs-toggle="tab"
                            data-bs-target="#tab-compact" type="button" role="tab">
                            <i class="mdi mdi-package-variant me-1"></i>Compact
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active p-3" id="tab-oil-injected" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-oil-injected">
                                <thead>
                                    <tr>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th class="text-center">Air Capacity</th>
                                        <th class="text-center">Pressure</th>
                                        <th class="text-center">Motor Power</th>
                                        <th class="text-center">SN</th>
                                        <th class="text-center">Kondisi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Harga Jual</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-3" id="tab-oil-free" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-oil-free">
                                <thead>
                                    <tr>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th class="text-center">Air Capacity</th>
                                        <th class="text-center">Pressure</th>
                                        <th class="text-center">Motor Power</th>
                                        <th class="text-center">SN</th>
                                        <th class="text-center">Kondisi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Harga Jual</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade p-5 text-center" id="tab-compact" role="tabpanel">
                        <i class="mdi mdi-package-variant-closed mdi-48px text-muted d-block mb-2"></i>
                        <p class="text-muted mb-0">Belum ada data Compact compressor.</p>
                    </div>
                </div>
            </div>

            {{-- ── Tab: Dryer ──────────────────────────────────────────── --}}
            <div class="tab-pane fade p-0" id="tab-main-dryer" role="tabpanel">
                <div class="px-3 pt-3 pb-1 border-bottom">
                    <ul class="nav nav-pills gap-1" id="dryerSubTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active btn-sm" id="tab-ref-dryer-btn" data-bs-toggle="pill"
                                data-bs-target="#tab-ref-dryer" type="button" role="tab">
                                <i class="mdi mdi-coolant-temperature me-1"></i>Refrigerant
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link btn-sm" id="tab-desiccant-btn" data-bs-toggle="pill"
                                data-bs-target="#tab-desiccant" type="button" role="tab">
                                <i class="mdi mdi-water-remove me-1"></i>Desiccant
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="tab-content p-3">
                    <div class="tab-pane fade show active" id="tab-ref-dryer" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-ref-dryer">
                                <thead>
                                    <tr>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th class="text-center">Air Capacity</th>
                                        <th class="text-center">Voltage</th>
                                        <th class="text-center">Connection</th>
                                        <th class="text-center">SN</th>
                                        <th class="text-center">Kondisi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Harga Jual</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tab-desiccant" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="table-desiccant">
                                <thead>
                                    <tr>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th class="text-center">Air Capacity</th>
                                        <th class="text-center">Voltage</th>
                                        <th class="text-center">SN</th>
                                        <th class="text-center">Kondisi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Harga Jual</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Tab: Filtration System ──────────────────────────────── --}}
            <div class="tab-pane fade p-3" id="tab-main-filtration" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-filtration">
                        <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Model</th>
                                <th class="text-center">Connection</th>
                                <th class="text-center">Filtration</th>
                                <th class="text-center">Oil Content</th>
                                <th class="text-center">Grade</th>
                                <th class="text-center">SN</th>
                                <th class="text-center">Kondisi</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Harga Jual</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            {{-- ── Tab: Air Receiver Tank ──────────────────────────────── --}}
            <div class="tab-pane fade p-3" id="tab-main-tank" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-tank">
                        <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Model</th>
                                <th class="text-center">Capacity</th>
                                <th class="text-center">Pressure</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">SN</th>
                                <th class="text-center">Kondisi</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Harga Jual</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal spesifikasi unit — dipakai semua tab, isinya diisi lewat JS saat diklik --}}
    <div class="modal fade" id="unitSpecModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unitSpecModalTitle">Spesifikasi Unit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-0 small" id="unitSpecModalBody"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection()

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-ready.js"></script>
@endpush
