@extends('layouts.sales.app')
@section('title', 'Detail Unit — ' . $product->sku)
@section('content')
    @php
        $isPriv = in_array(auth::user()->role, ['Admin', 'Sales', 'Logistic']);
        $isCompressor = in_array($product->unit, ['PISTON COMPRESSOR', 'AIR COMPRESSOR SCREW']);
        $isDryer      = in_array($product->unit, ['REFRIGERANT AIR DRYER', 'DESICANT DRYER']);
    @endphp

    {{-- Top Header Action Bar & Title --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('unit-global.index') }}" class="btn btn-icon btn-outline-secondary btn-sm rounded-circle" data-bs-toggle="tooltip" title="Kembali ke Daftar Unit">
                <i class="mdi mdi-arrow-left fs-4"></i>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="badge bg-label-primary px-2.5 py-1 rounded-pill small fw-semibold">{{ $product->unit }}</span>
                    @if ($product->status)
                        <span class="badge bg-label-success px-2.5 py-1 rounded-pill small fw-semibold">{{ $product->status }}</span>
                    @endif
                </div>
                <h4 class="fw-bold mb-0 text-dark">{{ $product->sku }}</h4>
            </div>
        </div>
        @if ($isPriv)
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#updateProduct-{{ $product->id }}">
                    <i class="mdi mdi-pencil-outline me-1"></i> Edit Unit
                </button>
                <button type="button" data-id="{{ $product->id }}" class="btn btn-label-danger delete-product">
                    <i class="mdi mdi-delete-outline me-1"></i> Delete
                </button>
            </div>
        @endif
    </div>

    {{-- Hero Summary Card --}}
    <div class="card unit-hero-card mb-4 border-0">
        <div class="card-body py-3 px-4">
            <div class="row align-items-center g-3">
                <div class="col-6 col-md-3 border-end-md">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-label-primary rounded-3 p-2 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cog-outline fs-3"></i>
                        </div>
                        <div>
                            <span class="metric-label d-block text-muted">Brand & Model</span>
                            <h6 class="mb-0 fw-bold text-dark">{{ $product->brand ?: '-' }} {{ $product->model ? '/ ' . $product->model : '' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 border-end-md">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-label-info rounded-3 p-2 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-lightning-bolt-outline fs-3"></i>
                        </div>
                        <div>
                            <span class="metric-label d-block text-muted">Motor Power</span>
                            <h6 class="mb-0 fw-bold text-dark">{{ $product->power ?: '-' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3 border-end-md">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-label-warning rounded-3 p-2 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-fan fs-3"></i>
                        </div>
                        <div>
                            <span class="metric-label d-block text-muted">Air Capacity / FAD</span>
                            <h6 class="mb-0 fw-bold text-dark">{{ $product->air_cap ? $product->air_cap . ' m³/min' : '-' }}</h6>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-label-success rounded-3 p-2 d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-cube-outline fs-3"></i>
                        </div>
                        <div>
                            <span class="metric-label d-block text-muted">Total Stock</span>
                            <h6 class="mb-0 fw-bold text-dark">{{ $allStock }} <span class="small text-muted fw-normal">(Awal: {{ $product->frist_stock ?: 0 }})</span></h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Nav Tabs Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom p-0">
            <ul class="nav nav-tabs custom-nav-tabs m-0" id="unit-global-detail-tab-nav" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-unit-detail" type="button">
                        <i class="mdi mdi-information-outline me-1.5 fs-5"></i>Detail Specifications
                    </button>
                </li>
                @if ($isPriv)
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-unit-pm-template" type="button">
                            <i class="mdi mdi-file-document-edit-outline me-1.5 fs-5"></i>Template Penawaran PM
                            <span class="badge bg-success rounded-pill ms-2">BARU</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-unit-equivalent" type="button">
                            <i class="mdi mdi-swap-horizontal me-1.5 fs-5"></i>Equivalent
                        </button>
                    </li>
                @endif
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content p-0">

                {{-- ==================== TAB: DETAIL ==================== --}}
                <div class="tab-pane fade show active" id="tab-unit-detail" role="tabpanel">
                    <div class="row g-4">
                        {{-- General Specs --}}
                        <div class="col-lg-6">
                            <div class="card spec-card">
                                <div class="spec-header d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 fw-bold text-dark">
                                        <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i>Informasi Umum
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row spec-item align-items-center">
                                        <div class="col-5 spec-label">Kategori Unit</div>
                                        <div class="col-7 spec-val">{{ $product->unit ?: '-' }}</div>
                                    </div>
                                    <div class="row spec-item align-items-center">
                                        <div class="col-5 spec-label">SKU</div>
                                        <div class="col-7 spec-val"><code class="text-primary fw-bold">{{ $product->sku ?: '-' }}</code></div>
                                    </div>
                                    <div class="row spec-item align-items-center">
                                        <div class="col-5 spec-label">Brand</div>
                                        <div class="col-7 spec-val">{{ $product->brand ?: '-' }}</div>
                                    </div>
                                    <div class="row spec-item align-items-center">
                                        <div class="col-5 spec-label">Model</div>
                                        <div class="col-7 spec-val">{{ $product->model ?: '-' }}</div>
                                    </div>
                                    <div class="row spec-item align-items-center">
                                        <div class="col-5 spec-label">Status Unit</div>
                                        <div class="col-7 spec-val">
                                            @if ($product->status)
                                                <span class="badge bg-label-info">{{ $product->status }}</span>
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row spec-item align-items-center">
                                        <div class="col-5 spec-label">Stock Awal</div>
                                        <div class="col-7 spec-val">{{ $product->frist_stock !== null ? $product->frist_stock : '-' }}</div>
                                    </div>
                                    @if ($product->desc && !$isCompressor && !$isDryer)
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Deskripsi</div>
                                            <div class="col-7 spec-val">{{ $product->desc }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Technical Specs --}}
                        <div class="col-lg-6">
                            <div class="card spec-card">
                                <div class="spec-header d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 fw-bold text-dark">
                                        <i class="mdi mdi-tune me-2 text-primary"></i>Spesifikasi Teknis
                                    </h6>
                                </div>
                                <div class="card-body p-3">
                                    @if ($isCompressor)
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Type Compressor</div>
                                            <div class="col-7 spec-val">{{ $product->type_unit ?: '-' }}</div>
                                        </div>
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Short Description</div>
                                            <div class="col-7 spec-val">{{ $product->desc ?: '-' }}</div>
                                        </div>
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Max. Working Pressure</div>
                                            <div class="col-7 spec-val">{{ $product->bar ? $product->bar . ' Bar' : '-' }}</div>
                                        </div>
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Air Capacity</div>
                                            <div class="col-7 spec-val">{{ $product->air_cap ? $product->air_cap . ' m³/min' : '-' }}</div>
                                        </div>
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Motor Power</div>
                                            <div class="col-7 spec-val"><span class="badge bg-label-primary">{{ $product->power ?: '-' }}</span></div>
                                        </div>
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Rated Voltage</div>
                                            <div class="col-7 spec-val">{{ $product->voltage ?: '-' }}</div>
                                        </div>
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Drive</div>
                                            <div class="col-7 spec-val">{{ $product->connect ?: '-' }}</div>
                                        </div>
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Cooling Method</div>
                                            <div class="col-7 spec-val">{{ $product->cooling ?: '-' }}</div>
                                        </div>
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Discharge Connection</div>
                                            <div class="col-7 spec-val">{{ $product->exhaust ?: '-' }}</div>
                                        </div>
                                    @elseif ($isDryer)
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">FAD / Air Capacity</div>
                                            <div class="col-7 spec-val">{{ $product->air_cap ? $product->air_cap . ' m³/min' : '-' }}</div>
                                        </div>
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Refrigerant Type</div>
                                            <div class="col-7 spec-val">{{ $product->refrigerant_type ?: '-' }}</div>
                                        </div>
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">PDP</div>
                                            <div class="col-7 spec-val">{{ $product->pdp ?: '-' }}</div>
                                        </div>
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Rated Voltage</div>
                                            <div class="col-7 spec-val">{{ $product->voltage ?: '-' }}</div>
                                        </div>
                                    @else
                                        <div class="row spec-item align-items-center">
                                            <div class="col-5 spec-label">Short Description</div>
                                            <div class="col-7 spec-val">{{ $product->desc ?: '-' }}</div>
                                        </div>
                                    @endif
                                    <div class="row spec-item align-items-center">
                                        <div class="col-5 spec-label">Dimension</div>
                                        <div class="col-7 spec-val">{{ $product->dimension ?: '-' }}</div>
                                    </div>
                                    <div class="row spec-item align-items-center">
                                        <div class="col-5 spec-label">Weight</div>
                                        <div class="col-7 spec-val">{{ $product->weight ? $product->weight . ' Kg' : '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($product->note)
                            <div class="col-12">
                                <div class="card spec-card bg-light border-0">
                                    <div class="card-body p-3.5">
                                        <h6 class="fw-bold text-dark mb-2">
                                            <i class="mdi mdi-note-text-outline me-2 text-warning"></i>Catatan (Note)
                                        </h6>
                                        <div class="p-3 bg-white rounded border text-secondary" style="font-family: inherit; white-space: pre-wrap;">{{ $product->note }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($isPriv)
                    {{-- ==================== TAB: TEMPLATE PENAWARAN PM ==================== --}}
                    <div class="tab-pane fade" id="tab-unit-pm-template" role="tabpanel">
                        <div id="pm-template-card"
                            data-endpoint="{{ route('unit-global.pm-template', $product->id) }}"
                            data-save-endpoint="{{ route('unit-global.pm-template.save', $product->id) }}">

                            <div class="alert alert-info border-0 shadow-xs d-flex align-items-center mb-4 rounded-3">
                                <i class="mdi mdi-information-outline fs-4 me-3 text-info"></i>
                                <div class="small">
                                    Susun manual item apa saja yang masuk penawaran PM per level untuk unit ini. Daftar item tersimpan
                                    permanen per unit + level, dan jadi sumber data <a href="{{ route('forecast.index') }}" target="_blank" class="fw-bold text-decoration-underline">Forecast Sales</a> juga.
                                    Biaya jasa tetap otomatis dari <a href="{{ route('forecast.prices') }}" target="_blank" class="fw-bold text-decoration-underline">pricelist Forecast</a> berdasarkan Motor Power unit ini.
                                    Untuk memakai template ini di penawaran, buka halaman <strong>Create Unit Quotation</strong> lalu pakai tombol "Load Template PM".
                                </div>
                            </div>

                            <div class="row g-4">
                                {{-- Kolom kiri: ringkasan unit + pilih level --}}
                                <div class="col-lg-3">
                                    <div class="card spec-card mb-3">
                                        <div class="card-body p-3">
                                            <h6 class="text-uppercase text-muted small fw-bold mb-3" style="letter-spacing:.04em;">
                                                Ringkasan Unit
                                            </h6>
                                            <dl class="row mb-0 small">
                                                <dt class="col-5 text-muted fw-normal">SKU</dt>
                                                <dd class="col-7 text-end mb-2 fw-semibold text-dark">{{ $product->sku }}</dd>
                                                <dt class="col-5 text-muted fw-normal">Brand</dt>
                                                <dd class="col-7 text-end mb-2 text-dark">{{ $product->brand ?: '-' }}</dd>
                                                <dt class="col-5 text-muted fw-normal">Model</dt>
                                                <dd class="col-7 text-end mb-2 text-dark">{{ $product->model ?: '-' }}</dd>
                                                <dt class="col-5 text-muted fw-normal">Power</dt>
                                                <dd class="col-7 text-end mb-0 fw-bold text-primary">{{ $product->power ?: '-' }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                    <div class="card spec-card">
                                        <div class="card-body p-3">
                                            <h6 class="text-uppercase text-muted small fw-bold mb-3" style="letter-spacing:.04em;">
                                                Pilih Level PM
                                            </h6>
                                            <div class="d-flex flex-wrap gap-2" id="pm-level-group">
                                                <button type="button" class="btn btn-sm rounded-pill px-3 pm-level-btn" data-level="PM1">PM1</button>
                                                <button type="button" class="btn btn-sm rounded-pill px-3 pm-level-btn" data-level="PM2">PM2</button>
                                                <button type="button" class="btn btn-sm rounded-pill px-3 pm-level-btn" data-level="PM3">PM3</button>
                                                <button type="button" class="btn btn-sm rounded-pill px-3 pm-level-btn" data-level="PM4">PM4</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Kolom kanan: builder --}}
                                <div class="col-lg-9">
                                    <div id="pm-template-empty" class="card spec-card">
                                        <div class="card-body text-center text-muted py-5">
                                            <i class="mdi mdi-arrow-left-bold-circle-outline fs-1 d-block mb-2 text-primary opacity-50"></i>
                                            Pilih level PM di sebelah kiri untuk menyusun / melihat template item unit ini.
                                        </div>
                                    </div>

                                    <div id="pm-template-result" style="display:none;">
                                        <div class="card spec-card">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h6 class="mb-0 fw-bold text-dark">Item Template</h6>
                                                    <span class="badge bg-label-primary rounded-pill px-2.5" id="pm-template-level-badge">-</span>
                                                </div>
                                                <p class="text-muted small mb-3"><i class="mdi mdi-drag-vertical me-1"></i>Geser ikon di kiri tiap baris untuk mengurutkan item.</p>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-hover mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th style="width:30px;"></th>
                                                                <th class="text-uppercase small text-muted">Item</th>
                                                                <th class="text-end text-uppercase small text-muted" style="width:70px;">Qty</th>
                                                                <th class="text-end text-uppercase small text-muted" style="width:140px;">Harga</th>
                                                                <th style="width:40px;"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="pm-template-rows"></tbody>
                                                    </table>
                                                </div>
                                                <div id="pm-template-noparts-warning" class="alert alert-warning py-2 px-3 small mt-2 mb-0 rounded" style="display:none;">
                                                    Belum ada item di template level ini. Susun mulai dari Head Title, lalu tambahkan part / jasa / item custom.
                                                </div>
                                                <div id="pm-template-power-warning" class="alert alert-warning py-2 px-3 small mt-2 mb-0 rounded" style="display:none;">
                                                    Tarif jasa untuk power unit ini belum ada di pricelist Forecast. Isi dulu di halaman <a href="{{ route('forecast.prices') }}" target="_blank" class="fw-bold text-decoration-underline">Master Harga Jasa PM</a>.
                                                </div>
                                                <div class="d-flex flex-wrap gap-2 mt-3">
                                                    <button type="button" class="btn btn-sm btn-outline-info" id="btn-pm-add-header">
                                                        <i class="mdi mdi-format-header-1 me-1"></i> Tambah Head Title
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-pm-add-part">
                                                        <i class="mdi mdi-plus me-1"></i> Tambah dari Product Equivalent
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-success" id="btn-pm-add-service">
                                                        <i class="mdi mdi-account-hard-hat-outline me-1"></i> Tambah Jasa Service
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-pm-add-custom">
                                                        <i class="mdi mdi-plus me-1"></i> Tambah Item Custom
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-warning ms-md-auto" id="btn-pm-copy-level">
                                                        <i class="mdi mdi-content-copy me-1"></i> Salin dari Level Lain
                                                    </button>
                                                </div>
                                                <div id="pm-add-part-picker" class="mt-3" style="display:none;">
                                                    <select class="form-select form-select-sm" id="pm-part-select" style="width:100%">
                                                        <option value="">Cari part (PN / Brand / Deskripsi)...</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card spec-card mt-3">
                                            <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-3 pm-total-bar">
                                                <div>
                                                    <div class="text-muted small">Total Estimated Price</div>
                                                    <div class="fw-bold fs-4 text-primary" id="pm-template-total">Rp 0</div>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-primary btn-md shadow-sm" id="btn-save-template">
                                                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Template
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ==================== TAB: EQUIVALENT ==================== --}}
                    <div class="tab-pane fade" id="tab-unit-equivalent" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0 fw-bold text-dark">Data Equivalent Unit</h6>
                            <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#createEquivalent-{{ $product->id }}">
                                <i class="mdi mdi-plus me-1"></i> New Equivalent
                            </button>
                        </div>
                        <div class="table-responsive text-nowrap rounded border p-2">
                            <table class="datatable-product-equivalent table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Brand</th>
                                        <th>PN</th>
                                        <th>Bar</th>
                                        <th>Air Capacity</th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('components.modal.warehouse.unit.form-global')
    @include('components.modal.warehouse.replacement.form')
    @include('components.modal.warehouse.equivalent.form-global')
    @php
        $no = 0;
    @endphp
    @foreach ($serials as $serial)
        @include('components.modal.warehouse.equivalent.form-global')
        @php
            $no++;
        @endphp
    @endforeach
    @foreach ($details as $detail)
        @include('components.modal.warehouse.replacement.form-price')
    @endforeach
@endsection()

@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/bootstrap-select/bootstrap-select.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />

    <style>
        .unit-hero-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border: 1px solid rgba(0, 0, 0, 0.08) !important;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }
        .border-end-md {
            border-right: 1px solid rgba(0, 0, 0, 0.08);
        }
        @media (max-width: 767.98px) {
            .border-end-md {
                border-right: none;
                border-bottom: 1px solid rgba(0, 0, 0, 0.08);
                padding-bottom: 12px;
            }
        }
        .metric-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .spec-card {
            border-radius: 14px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.02);
            height: 100%;
            transition: all 0.2s ease;
        }
        .spec-card:hover {
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        }
        .spec-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            padding: 14px 18px;
            background: rgba(248, 249, 250, 0.7);
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
        }
        .spec-item {
            padding: 10px 0;
            border-bottom: 1px dashed rgba(0, 0, 0, 0.08);
        }
        .spec-item:last-child {
            border-bottom: none;
        }
        .spec-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }
        .spec-val {
            font-size: 0.9rem;
            color: #2b3445;
            font-weight: 600;
        }
        .custom-nav-tabs .nav-link {
            border: none;
            border-bottom: 2.5px solid transparent;
            color: #6c757d;
            font-weight: 600;
            padding: 14px 22px;
            border-radius: 0;
            transition: all 0.2s ease;
        }
        .custom-nav-tabs .nav-link:hover {
            color: #696cff;
            border-bottom-color: rgba(105, 108, 255, 0.4);
        }
        .custom-nav-tabs .nav-link.active {
            color: #696cff;
            background: transparent;
            border-bottom-color: #696cff;
        }
        .table-modern th {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #566a7f;
            background-color: #f8f9fa;
            border-top: none;
            border-bottom: 1px solid #e7e7e8;
            padding: 12px 16px;
        }
        .table-modern td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f1f2;
        }
        #tab-unit-pm-template .pm-level-btn {
            border: 1px solid #696cff;
            background: rgba(105, 108, 255, 0.05);
            color: #696cff;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        #tab-unit-pm-template .pm-level-btn:hover {
            background: rgba(105, 108, 255, 0.15);
        }
        #tab-unit-pm-template .pm-level-btn.active {
            background: #696cff;
            border-color: #696cff;
            color: #fff;
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.3);
        }
        #tab-unit-pm-template .pm-source-note {
            font-family: ui-monospace, SFMono-Regular, Consolas, monospace;
            font-size: .72rem;
            color: var(--bs-secondary-color, #6c757d);
            display: block;
            margin-top: 2px;
        }
        #tab-unit-pm-template .pm-total-bar {
            border-top: 1px dashed var(--bs-border-color);
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/tagify/tagify.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/bloodhound/bloodhound.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sortablejs/sortable.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-basic.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/includes/table-equivalent-global.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-in-detail.js"></script>
    <script src="{{ asset('assets') }}/includes/table-product-out-detail.js"></script>
    <script src="{{ asset('assets') }}/includes/table-quotation-product.js"></script>
    <script src="{{ asset('assets') }}/js/forms-selects.js"></script>
@endpush

@push('script')
    <script>
        // Re-adjust DataTables column widths when switching tabs
        $('#unit-global-detail-tab-nav button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust().responsive.recalc();
        });

        // Rupiah formatter untuk field pricelist
        $(document).on('input', '.rupiah-price', function () {
            var raw = $(this).val().replace(/\./g, '').replace(/\D/g, '');
            $(this).val(raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
            $('#price-raw').val(raw);
        });

        $(document).on('click', '.delete-product', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('unit') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/unit-global';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });

        $(document).on('click', '.delete-replacement', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('product') }}/replacement/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });

        $(document).on('click', '.delete-equivalent', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('product') }}/equivalent/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });

        $(() => {
            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }

            $(".invoice-item-price-label").on('keyup', function() {
                var input = $(this)
                var id = input.data('id');
                var input_val = input.val();
                input_val = formatNumber(input_val);
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                $(`#price-${id}`).val(nomorInt);
            });

            $(".invoice-item-modal-label").on('keyup', function() {
                var input = $(this)
                var id = input.data('id');
                var input_val = input.val();
                input_val = formatNumber(input_val);
                input.val(input_val);
                var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
                $(`#modal-${id}`).val(nomorInt);
            });

            // ── Template Penawaran PM (Unit Global) — builder manual per level ──
            var $pmCard = $('#pm-template-card');
            if ($pmCard.length) {
                var pmEndpoint = $pmCard.data('endpoint');
                var pmSaveEndpoint = $pmCard.data('save-endpoint');
                var pmCurrentLevel = null;
                var pmCurrentUnit = null;
                var pmServiceSuggestion = null;
                var pmUidCounter = 0;
                var pmSortable = null;
                var pmItems = []; // {_uid, type: 'header'|'part'|'custom', id_equivalent, label, description, qty, info_qty, price}

                function pmNextUid() {
                    pmUidCounter++;
                    return 'u' + pmUidCounter;
                }

                function pmFormatRupiah(n) {
                    n = Math.round(n || 0);
                    return 'Rp ' + String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                // Format angka jadi "000.000" (titik ribuan, tanpa "Rp") — dipakai di input yang bisa diedit
                function pmFormatThousand(n) {
                    n = Math.round(parseFloat(n) || 0);
                    return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                }

                function pmParseThousand(str) {
                    return parseFloat(String(str).replace(/\D/g, '')) || 0;
                }

                function pmEscapeHtml(str) {
                    return $('<div>').text(str == null ? '' : str).html();
                }

                function pmTotal() {
                    var total = 0;
                    $.each(pmItems, function(i, it) {
                        if (it.type !== 'header') {
                            total += (parseFloat(it.qty) || 0) * (parseFloat(it.price) || 0);
                        }
                    });
                    return total;
                }

                function renderPmItems() {
                    var $rows = $('#pm-template-rows').empty();
                    var dragHandle = '<td class="text-muted pm-drag-handle" style="cursor:grab;"><i class="mdi mdi-drag-vertical fs-5"></i></td>';

                    $.each(pmItems, function(i, it) {
                        if (!it._uid) it._uid = pmNextUid();
                        var $tr;
                        if (it.type === 'header') {
                            $tr = $(
                                '<tr class="table-light">' +
                                    dragHandle +
                                    '<td colspan="3"><span class="fw-bold text-dark text-uppercase" style="letter-spacing:.03em;">' + pmEscapeHtml(it.label) + '</span></td>' +
                                    '<td class="text-end"><button type="button" class="btn btn-icon btn-sm btn-label-danger pm-item-remove"><i class="mdi mdi-delete-outline"></i></button></td>' +
                                '</tr>'
                            );
                        } else {
                            $tr = $(
                                '<tr>' +
                                    dragHandle +
                                    '<td><span class="fw-semibold text-dark">' + pmEscapeHtml(it.label) + '</span>' +
                                        (it.description ? '<span class="text-muted small d-block" style="white-space:pre-line;">' + pmEscapeHtml(it.description) + '</span>' : '') +
                                    '</td>' +
                                    '<td class="text-end"><input type="number" min="0" step="1" class="form-control form-control-sm text-end pm-item-qty" value="' + it.qty + '"></td>' +
                                    '<td class="text-end"><input type="text" class="form-control form-control-sm text-end pm-item-price" value="' + pmFormatThousand(it.price) + '"></td>' +
                                    '<td class="text-end"><button type="button" class="btn btn-icon btn-sm btn-label-danger pm-item-remove"><i class="mdi mdi-delete-outline"></i></button></td>' +
                                '</tr>'
                            );
                        }
                        $tr.data('index', i);
                        $tr.attr('data-uid', it._uid);
                        $rows.append($tr);
                    });

                    $('#pm-template-noparts-warning').toggle(pmItems.length === 0);
                    $('#pm-template-total').text(pmFormatRupiah(pmTotal()));

                    if (!pmSortable) {
                        pmSortable = Sortable.create(document.getElementById('pm-template-rows'), {
                            animation: 150,
                            handle: '.pm-drag-handle',
                            onEnd: function() {
                                var newOrder = $('#pm-template-rows tr').map(function() {
                                    return $(this).attr('data-uid');
                                }).get();
                                pmItems.sort(function(a, b) {
                                    return newOrder.indexOf(a._uid) - newOrder.indexOf(b._uid);
                                });
                                renderPmItems();
                            }
                        });
                    }
                }

                function loadPmLevel(level) {
                    $.ajax({
                        url: pmEndpoint,
                        method: 'GET',
                        data: { level: level },
                        success: function(data) {
                            pmCurrentLevel = data.level;
                            pmCurrentUnit = data.unit;
                            pmServiceSuggestion = data.service_suggestion;
                            pmItems = $.map(data.items, function(p) {
                                return {
                                    type: p.type || 'part',
                                    id_equivalent: p.id_equivalent || null,
                                    label: p.label || 'Item',
                                    description: p.description || '',
                                    qty: p.qty,
                                    info_qty: p.info_qty || 'Pcs',
                                    price: p.price
                                };
                            });

                            $('#pm-template-level-badge').text(data.level);
                            renderPmItems();
                            $('#pm-template-power-warning').toggle(!pmServiceSuggestion.matched);
                            $('#pm-add-part-picker').hide();
                            $('#pm-template-empty').hide();
                            $('#pm-template-result').show();
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal memuat template',
                                text: 'Coba pilih ulang level PM.'
                            });
                        }
                    });
                }

                function savePmTemplate() {
                    return $.ajax({
                        url: pmSaveEndpoint,
                        method: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            level: pmCurrentLevel,
                            items: pmItems
                        }
                    });
                }

                $(document).on('click', '.pm-level-btn', function() {
                    var level = $(this).data('level');
                    $('.pm-level-btn').removeClass('active');
                    $(this).addClass('active');
                    loadPmLevel(level);
                });

                // ── Edit qty/price inline ──
                $(document).on('input', '.pm-item-qty', function() {
                    var i = $(this).closest('tr').data('index');
                    pmItems[i].qty = parseFloat($(this).val()) || 0;
                    $('#pm-template-total').text(pmFormatRupiah(pmTotal()));
                });
                $(document).on('input', '.pm-item-price', function() {
                    var raw = pmParseThousand($(this).val());
                    $(this).val(pmFormatThousand(raw));
                    var i = $(this).closest('tr').data('index');
                    pmItems[i].price = raw;
                    $('#pm-template-total').text(pmFormatRupiah(pmTotal()));
                });
                // Format ribuan real-time buat input harga di modal SweetAlert2 (Custom Item & Jasa Service)
                $(document).on('input', '#pm-custom-price, #pm-service-price', function() {
                    $(this).val(pmFormatThousand(pmParseThousand($(this).val())));
                });

                $(document).on('click', '.pm-item-remove', function() {
                    var i = $(this).closest('tr').data('index');
                    pmItems.splice(i, 1);
                    renderPmItems();
                });

                // ── Tambah item dari master Product/Equivalent (search real-time) ──
                $(document).on('click', '#btn-pm-add-part', function() {
                    var $picker = $('#pm-add-part-picker');
                    if ($picker.is(':visible')) {
                        $picker.hide();
                        return;
                    }
                    var $select = $('#pm-part-select');
                    if (!$select.data('select2')) {
                        $select.select2({
                            width: '100%',
                            placeholder: 'Cari part (PN / Brand / Deskripsi)...',
                            minimumInputLength: 1,
                            ajax: {
                                url: '/db/equivalent/search',
                                dataType: 'json',
                                delay: 300,
                                data: function(params) { return { q: params.term }; },
                                processResults: function(data) {
                                    return {
                                        results: $.map(data, function(p) {
                                            return {
                                                id: p.id_equivalent,
                                                text: (p.brand || '-') + ' — ' + (p.pn || '') + (p.product_desc ? ' (' + p.product_desc + ')' : ''),
                                                part: p
                                            };
                                        })
                                    };
                                }
                            }
                        });
                    }
                    $picker.show();
                    $select.val(null).trigger('change');
                    $select.select2('open');
                });

                $(document).on('select2:select', '#pm-part-select', function(e) {
                    var part = e.params.data.part;
                    if (!part) return;
                    var partLabel = part.brand ? (part.brand + ' ' + (part.pn || '')) : (part.pn || part.product_name || 'Item');
                    pmItems.push({
                        type: 'part',
                        id_equivalent: part.id_equivalent,
                        label: partLabel.trim(),
                        description: part.product_desc || '',
                        qty: 1,
                        info_qty: part.product_unit || 'Pcs',
                        price: parseFloat(part.price) || 0
                    });
                    renderPmItems();
                    $('#pm-add-part-picker').hide();
                });

                // ── Tambah item custom ──
                $(document).on('click', '#btn-pm-add-custom', function() {
                    Swal.fire({
                        title: 'Tambah Item Custom',
                        html:
                            '<input id="pm-custom-label" class="swal2-input" placeholder="Nama Item">' +
                            '<input id="pm-custom-qty" type="number" min="1" value="1" class="swal2-input" placeholder="Qty">' +
                            '<input id="pm-custom-price" type="text" class="swal2-input" placeholder="Harga">',
                        confirmButtonText: 'Tambah',
                        showCancelButton: true,
                        preConfirm: function() {
                            var label = document.getElementById('pm-custom-label').value.trim();
                            var qty = parseFloat(document.getElementById('pm-custom-qty').value) || 1;
                            var price = pmParseThousand(document.getElementById('pm-custom-price').value);
                            if (!label) {
                                Swal.showValidationMessage('Nama item wajib diisi');
                                return false;
                            }
                            return { label: label, qty: qty, price: price };
                        }
                    }).then(function(result) {
                        if (!result.isConfirmed) return;
                        pmItems.push({
                            type: 'custom',
                            id_equivalent: null,
                            label: result.value.label,
                            description: '',
                            qty: result.value.qty,
                            info_qty: 'Pcs',
                            price: result.value.price
                        });
                        renderPmItems();
                    });
                });

                // ── Salin item dari level lain (mis. PM1 sudah diisi, PM2 tinggal salin lalu edit) ──
                $(document).on('click', '#btn-pm-copy-level', function() {
                    if (!pmCurrentLevel) return;
                    var otherLevels = $.grep(['PM1', 'PM2', 'PM3', 'PM4'], function(l) { return l !== pmCurrentLevel; });
                    var options = {};
                    $.each(otherLevels, function(i, l) { options[l] = l; });

                    Swal.fire({
                        title: 'Salin dari Level Mana?',
                        input: 'select',
                        inputOptions: options,
                        inputPlaceholder: '-- Pilih Level --',
                        confirmButtonText: 'Salin',
                        showCancelButton: true,
                        inputValidator: function(value) {
                            if (!value) return 'Pilih salah satu level';
                        }
                    }).then(function(result) {
                        if (!result.isConfirmed) return;
                        var sourceLevel = result.value;

                        $.ajax({
                            url: pmEndpoint,
                            method: 'GET',
                            data: { level: sourceLevel },
                            success: function(data) {
                                if (!data.items.length) {
                                    Swal.fire({ icon: 'warning', title: 'Level ' + sourceLevel + ' masih kosong', text: 'Belum ada item yang bisa disalin dari level ini.' });
                                    return;
                                }

                                function doCopy() {
                                    $.each(data.items, function(i, it) {
                                        pmItems.push({
                                            type: it.type || 'part',
                                            id_equivalent: it.id_equivalent || null,
                                            label: it.label || 'Item',
                                            description: it.description || '',
                                            qty: it.qty,
                                            info_qty: it.info_qty || 'Pcs',
                                            price: it.price
                                        });
                                    });
                                    renderPmItems();
                                    Swal.fire({ icon: 'success', title: 'Tersalin', text: data.items.length + ' item dari ' + sourceLevel + ' ditambahkan. Silakan sesuaikan lalu Simpan Template.', timer: 2500, showConfirmButton: false });
                                }

                                if (pmItems.length > 0) {
                                    Swal.fire({
                                        icon: 'question',
                                        title: 'Tambahkan ke item yang sudah ada?',
                                        text: 'Level ' + pmCurrentLevel + ' sudah punya ' + pmItems.length + ' item. ' + data.items.length + ' item dari ' + sourceLevel + ' akan ditambahkan di bawahnya (bukan menggantikan).',
                                        showCancelButton: true,
                                        confirmButtonText: 'Ya, tambahkan',
                                        cancelButtonText: 'Batal'
                                    }).then(function(confirmResult) {
                                        if (confirmResult.isConfirmed) doCopy();
                                    });
                                } else {
                                    doCopy();
                                }
                            },
                            error: function() {
                                Swal.fire({ icon: 'error', title: 'Gagal memuat level ' + sourceLevel });
                            }
                        });
                    });
                });

                // ── Tambah Head Title (preset selection, beda per level) ──
                var PM_HEADER_PRESETS = {
                    'PM1': ['A. CONSUMABLE PART', 'B. SERVICE COST'],
                    'PM2': ['A. CONSUMABLE PART', 'B. SERVICE COST'],
                    'PM3': ['A. CONSUMABLE PART', 'B. NON-CONSUMABLE PART', 'C. SERVICE COST'],
                    'PM4': ['A. CONSUMABLE PART', 'B. NON-CONSUMABLE PART', 'C. SERVICE COST']
                };
                var PM_HEADER_CUSTOM = '__custom__';

                function pmAddHeader(label) {
                    pmItems.push({
                        type: 'header',
                        id_equivalent: null,
                        label: label,
                        description: '',
                        qty: 0,
                        info_qty: '',
                        price: 0
                    });
                    renderPmItems();
                }

                $(document).on('click', '#btn-pm-add-header', function() {
                    var presets = PM_HEADER_PRESETS[pmCurrentLevel] || PM_HEADER_PRESETS['PM1'];
                    var options = {};
                    $.each(presets, function(i, p) { options[p] = p; });
                    options[PM_HEADER_CUSTOM] = 'Lainnya (tulis manual)';

                    Swal.fire({
                        title: 'Tambah Head Title',
                        input: 'select',
                        inputOptions: options,
                        inputPlaceholder: '-- Pilih Head Title --',
                        confirmButtonText: 'Tambah',
                        showCancelButton: true,
                        inputValidator: function(value) {
                            if (!value) return 'Pilih salah satu head title';
                        }
                    }).then(function(result) {
                        if (!result.isConfirmed) return;
                        if (result.value === PM_HEADER_CUSTOM) {
                            Swal.fire({
                                title: 'Tulis Head Title',
                                input: 'text',
                                inputPlaceholder: 'Contoh: D. CATATAN TAMBAHAN',
                                confirmButtonText: 'Tambah',
                                showCancelButton: true,
                                inputValidator: function(value) {
                                    if (!value || !value.trim()) return 'Judul wajib diisi';
                                }
                            }).then(function(customResult) {
                                if (!customResult.isConfirmed) return;
                                pmAddHeader(customResult.value.trim());
                            });
                            return;
                        }
                        pmAddHeader(result.value);
                    });
                });

                // ── Tambah Jasa Service (prefill dari pricelist Forecast, tetap bisa diedit) ──
                $(document).on('click', '#btn-pm-add-service', function() {
                    if (!pmServiceSuggestion) return;
                    Swal.fire({
                        title: 'Tambah Jasa Service',
                        html:
                            '<input id="pm-service-label" class="swal2-input" placeholder="Nama Item" value="' + pmEscapeHtml(pmServiceSuggestion.label) + '">' +
                            '<textarea id="pm-service-desc" class="swal2-textarea" placeholder="Deskripsi scope kerja">' + pmEscapeHtml(pmServiceSuggestion.description || '') + '</textarea>' +
                            '<input id="pm-service-price" type="text" class="swal2-input" placeholder="Harga" value="' + pmFormatThousand(pmServiceSuggestion.amount || 0) + '">' +
                            (!pmServiceSuggestion.matched ? '<div class="swal2-validation-message" style="display:block;">Belum ada harga jasa untuk power unit ini di pricelist Forecast — isi manual.</div>' : ''),
                        confirmButtonText: 'Tambah',
                        showCancelButton: true,
                        preConfirm: function() {
                            var label = document.getElementById('pm-service-label').value.trim();
                            var description = document.getElementById('pm-service-desc').value.trim();
                            var price = pmParseThousand(document.getElementById('pm-service-price').value);
                            if (!label) {
                                Swal.showValidationMessage('Nama item wajib diisi');
                                return false;
                            }
                            return { label: label, description: description, price: price };
                        }
                    }).then(function(result) {
                        if (!result.isConfirmed) return;
                        pmItems.push({
                            type: 'custom',
                            id_equivalent: null,
                            label: result.value.label,
                            description: result.value.description,
                            qty: 1,
                            info_qty: 'Lot',
                            price: result.value.price
                        });
                        renderPmItems();
                    });
                });

                $(document).on('click', '#btn-save-template', function() {
                    if (!pmCurrentLevel) return;
                    savePmTemplate().done(function(res) {
                        Swal.fire({ icon: 'success', title: 'Tersimpan', text: res.message, timer: 1500, showConfirmButton: false });
                    }).fail(function() {
                        Swal.fire({ icon: 'error', title: 'Gagal menyimpan template' });
                    });
                });
            }
        });
    </script>
@endpush

