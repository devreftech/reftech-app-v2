@extends('layouts.sales.app')
@section('title', 'Master Material Piping & Pricelist Vendor')
@section('hide-chat', true)

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .select2-container {
            width: 100% !important;
        }
        .modal .select2-container--default .select2-selection--single {
            border: 1px solid #d9dee3;
            border-radius: 6px;
            height: 38px;
            display: flex;
            align-items: center;
        }
        .modal .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
            color: #566a7f;
            font-size: 13px;
        }
        .modal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .dataTables_wrapper .dataTables_length label {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .dataTables_wrapper .dataTables_length select {
            padding: 4px 10px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            font-size: 13px;
        }
        .dataTables_wrapper .dataTables_filter label {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .dataTables_wrapper .dataTables_filter input {
            padding: 6px 14px;
            border-radius: 20px;
            border: 1px solid #cbd5e1;
            font-size: 13px;
            min-width: 280px;
            outline: none;
            transition: all 0.2s ease;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }
        .dataTables_wrapper .dataTables_info {
            font-size: 12.5px;
            color: #64748b;
        }
        .dataTables_wrapper .dataTables_paginate .pagination {
            margin: 0;
            gap: 3px;
        }
        table.dataTable {
            width: 100% !important;
            margin: 0 !important;
        }
        .dataTables_wrapper {
            width: 100% !important;
            overflow-x: hidden !important;
        }
        .dataTables_wrapper .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        .card .table-responsive {
            overflow-x: visible !important;
        }
        #tablePipingMaterials th,
        #tablePipingMaterials td {
            vertical-align: middle;
        }
        #tablePipingMaterials thead th {
            font-size: 12.5px;
            font-weight: 600;
        }
        .column-search {
            width: 100% !important;
            min-width: 0 !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .btn-xs {
            padding: 1.5px 6px;
            font-size: 11px;
            line-height: 1.35;
            border-radius: 4px;
            font-weight: 500;
        }
        .row-actions {
            opacity: 0.8;
            transition: opacity 0.15s ease;
        }
        tr:hover .row-actions {
            opacity: 1;
        }
        .btn-manage-prices {
            font-size: 11px;
            transition: all 0.2s ease;
        }
        .btn-manage-prices:hover {
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('content')
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="mdi mdi-pipe text-primary me-2"></i>Master Material Piping</h4>
            <p class="text-muted mb-0">Kelola katalog material perpipaan & riwayat harga supplier untuk estimasi RAB.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('piping-rab.index') }}" class="btn btn-outline-primary rounded-pill px-3">
                <i class="mdi mdi-calculator-variant me-1"></i> Buka Estimasi / RAB
            </a>
            <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddMaterial">
                <i class="mdi mdi-plus me-1"></i> Tambah Material
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Category Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('piping-materials.index') }}" class="card text-decoration-none border {{ empty($category) ? 'border-primary shadow-sm bg-primary text-white' : 'bg-white' }}">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                    <small class="{{ empty($category) ? 'text-white-50' : 'text-muted' }}">Semua Material</small>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('piping-materials.index', ['category' => 'pipe']) }}" class="card text-decoration-none border {{ $category === 'pipe' ? 'border-primary shadow-sm bg-primary text-white' : 'bg-white' }}">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4">{{ $stats['pipe'] }}</div>
                    <small class="{{ $category === 'pipe' ? 'text-white-50' : 'text-muted' }}">Pipa (Pipe)</small>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('piping-materials.index', ['category' => 'fitting']) }}" class="card text-decoration-none border {{ $category === 'fitting' ? 'border-primary shadow-sm bg-primary text-white' : 'bg-white' }}">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4">{{ $stats['fitting'] }}</div>
                    <small class="{{ $category === 'fitting' ? 'text-white-50' : 'text-muted' }}">Fitting</small>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('piping-materials.index', ['category' => 'valve']) }}" class="card text-decoration-none border {{ $category === 'valve' ? 'border-primary shadow-sm bg-primary text-white' : 'bg-white' }}">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4">{{ $stats['valve'] }}</div>
                    <small class="{{ $category === 'valve' ? 'text-white-50' : 'text-muted' }}">Valves & Instr.</small>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('piping-materials.index', ['category' => 'support']) }}" class="card text-decoration-none border {{ $category === 'support' ? 'border-primary shadow-sm bg-primary text-white' : 'bg-white' }}">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4">{{ $stats['support'] }}</div>
                    <small class="{{ $category === 'support' ? 'text-white-50' : 'text-muted' }}">Support/Hanger</small>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('piping-materials.index', ['category' => 'consumable']) }}" class="card text-decoration-none border {{ $category === 'consumable' ? 'border-primary shadow-sm bg-primary text-white' : 'bg-white' }}">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4">{{ $stats['consumable'] }}</div>
                    <small class="{{ $category === 'consumable' ? 'text-white-50' : 'text-muted' }}">Consumable</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-label-primary fs-7 px-3 py-1 rounded-pill">
                    <i class="mdi mdi-database-outline me-1"></i> Data Master
                </span>
                <h5 class="mb-0 fw-semibold text-dark">Daftar Material & Harga Supplier</h5>
                <span class="badge bg-light text-muted border rounded-pill">{{ count($materials) }} Item</span>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="input-group input-group-sm input-group-merge" style="width: 140px;">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="mdi mdi-format-list-numbered"></i></span>
                        <select id="customPageLength" class="form-select form-select-sm border-start-0 ps-0 text-muted fw-semibold">
                            <option value="10">10 baris</option>
                            <option value="25" selected>25 baris</option>
                            <option value="50">50 baris</option>
                            <option value="100">100 baris</option>
                            <option value="-1">Semua</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm input-group-merge" style="min-width: 260px;">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" id="customSearchInput" class="form-control form-control-sm border-start-0 ps-0" placeholder="Cari nama, ukuran, tipe...">
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive p-0">
            <table class="table table-hover align-middle mb-0 w-100" id="tablePipingMaterials" style="width: 100%;">
                <thead class="table-light">
                    <tr>
                        <th>Kategori</th>
                        <th>Nama Item & Spesifikasi</th>
                        <th>Ukuran</th>
                        <th>Tipe Sambungan</th>
                        <th>Satuan</th>
                        <th>HPP Vendor</th>
                    </tr>
                    <tr class="table-search-row bg-white border-bottom">
                        <th class="p-1">
                            <select class="form-select form-select-sm column-search border-light bg-light" data-col="0" style="font-size: 11px; padding: 3px 6px;">
                                <option value="">Semua</option>
                                <option value="Pipa">Pipa</option>
                                <option value="Fitting">Fitting</option>
                                <option value="Valve">Valve</option>
                                <option value="Support">Support</option>
                                <option value="Consumable">Consumable</option>
                            </select>
                        </th>
                        <th class="p-1">
                            <input type="text" class="form-control form-control-sm column-search border-light bg-light" data-col="1" placeholder="🔍 Cari item..." style="font-size: 11px; padding: 3px 8px;">
                        </th>
                        <th class="p-1">
                            <input type="text" class="form-control form-control-sm column-search border-light bg-light" data-col="2" placeholder="🔍 Ukuran..." style="font-size: 11px; padding: 3px 8px;">
                        </th>
                        <th class="p-1">
                            <input type="text" class="form-control form-control-sm column-search border-light bg-light" data-col="3" placeholder="🔍 Sambungan..." style="font-size: 11px; padding: 3px 8px;">
                        </th>
                        <th class="p-1">
                            <select class="form-select form-select-sm column-search border-light bg-light" data-col="4" style="font-size: 11px; padding: 3px 6px;">
                                <option value="">Semua</option>
                                @php
                                    $distinctUnits = $materials->pluck('unit')->filter()->unique()->sort()->values();
                                @endphp
                                @foreach($distinctUnits as $u)
                                    <option value="{{ $u }}">{{ $u }}</option>
                                @endforeach
                            </select>
                        </th>
                        <th class="p-1">
                            <input type="text" class="form-control form-control-sm column-search border-light bg-light" data-col="5" placeholder="🔍 Nilai HPP / Tgl..." style="font-size: 11px; padding: 3px 8px;">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials as $mat)
                        @php
                            $cheapest = $mat->vendorPrices->sortBy('price_idr')->first();
                            $vendorCount = $mat->vendorPrices->count();
                            $badgeColor = match($mat->category) {
                                'pipe'       => 'primary',
                                'fitting'    => 'info',
                                'valve'      => 'warning',
                                'support'    => 'success',
                                'consumable' => 'secondary',
                                default      => 'dark',
                            };
                        @endphp
                        <tr>
                            <td>
                                <span class="badge bg-label-{{ $badgeColor }}">{{ $mat->formatted_category }}</span>
                            </td>
                            <td>
                                <a href="javascript:void(0);" 
                                   class="fw-bold text-dark text-primary-hover btn-open-drawer d-block mb-1 text-decoration-none" 
                                   data-material='@json($mat)' 
                                   data-prices='@json($mat->vendorPrices)'
                                   title="Klik untuk melihat detail & kelola harga">
                                    {{ $mat->item_name }} <i class="mdi mdi-open-in-app text-muted small ms-1" style="font-size: 13px;"></i>
                                </a>
                                <div class="d-flex flex-wrap align-items-center gap-1">
                                    @if($mat->material_type)
                                        <span class="text-muted small me-1">{{ $mat->material_type }}</span>
                                    @endif
                                    @if($mat->item_code)
                                        <span class="badge bg-light text-muted border font-monospace" style="font-size: 10px;">{{ $mat->item_code }}</span>
                                    @endif
                                </div>
                            </td>
                            <td><span class="badge bg-label-secondary font-monospace">{{ $mat->size ?: '-' }}</span></td>
                            <td><small class="text-muted">{{ $mat->connection_type ?: '-' }}</small></td>
                            <td>
                                <span class="fw-semibold">{{ $mat->unit }}</span>
                                @if($mat->category === 'pipe' && $mat->length_per_unit)
                                    <small class="text-muted d-block">(@ {{ (float)$mat->length_per_unit }} m)</small>
                                @endif
                            </td>
                            <td>
                                @if($cheapest)
                                    <div class="fw-bold text-success fs-6">Rp {{ number_format($cheapest->price_idr, 0, ',', '.') }}</div>
                                    <div class="d-flex flex-wrap align-items-center gap-1 mt-1">
                                        <button type="button" 
                                                class="btn btn-xs btn-outline-success btn-open-drawer d-inline-flex align-items-center py-0 px-2 rounded-pill shadow-none" 
                                                data-material='@json($mat)' 
                                                data-prices='@json($mat->vendorPrices)'
                                                title="Kelola & Bandingkan Harga Vendor">
                                            <i class="mdi mdi-store-cog-outline me-1"></i>
                                            {{ $vendorCount > 1 ? $vendorCount . ' Vendor' : 'Kelola' }}
                                        </button>
                                        @if($cheapest->date)
                                            @php
                                                $diffDays = now()->diffInDays($cheapest->date);
                                                $isOld = $diffDays > 60;
                                            @endphp
                                            <small class="{{ $isOld ? 'text-danger fw-bold' : 'text-muted' }} ms-1" style="font-size: 11px;">
                                                <i class="mdi mdi-calendar-clock-outline me-1"></i>{{ $cheapest->date->format('d/m/Y') }}
                                                @if($isOld)
                                                    <i class="mdi mdi-alert-circle text-danger ms-1" title="Harga > 60 hari"></i>
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-muted small mb-1">Belum ada harga</div>
                                    <button type="button" 
                                            class="btn btn-xs btn-outline-warning btn-open-drawer d-inline-flex align-items-center py-0 px-2 rounded-pill shadow-none" 
                                            data-material='@json($mat)' 
                                            data-prices='@json($mat->vendorPrices)'
                                            title="Input Harga Supplier">
                                        <i class="mdi mdi-plus-circle-outline me-1"></i> + Input Harga
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

<!-- Offcanvas Side Drawer: Detail Material & Pricelist Vendor -->
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="offcanvasMaterialDetail" aria-labelledby="offcanvasMaterialDetailLabel" style="width: 600px; max-width: 95vw;">
    <div class="offcanvas-header bg-light border-bottom py-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary fs-7" id="drawerBadgeCategory">Pipa</span>
            <h5 id="offcanvasMaterialDetailLabel" class="offcanvas-title fw-bold mb-0 text-dark">Detail & Pricelist Material</h5>
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-4">
        <!-- Title & Action Buttons -->
        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom gap-2">
            <div>
                <h5 class="fw-bold text-dark mb-1" id="drawerItemName">-</h5>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge bg-light text-muted border font-monospace" id="drawerItemCode">-</span>
                    <span class="text-muted small" id="drawerMaterialType">-</span>
                </div>
            </div>
            <div class="d-flex gap-1 flex-shrink-0">
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnDrawerEditMaterial" title="Edit Data Spesifikasi">
                    <i class="mdi mdi-pencil me-1"></i> Edit
                </button>
                <form id="formDrawerDeleteMaterial" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus material ini?')">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Hapus Material">
                        <i class="mdi mdi-trash-can-outline"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Technical Specification Card -->
        <div class="card bg-lighter border shadow-none mb-4">
            <div class="card-body p-3">
                <h6 class="fw-bold text-muted text-uppercase mb-3" style="font-size: 11px; letter-spacing: 0.5px;">
                    <i class="mdi mdi-pipe me-1 text-primary"></i> Spesifikasi Teknis
                </h6>
                <div class="row g-2">
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Ukuran (Size / DN)</small>
                        <span class="fw-bold font-monospace text-dark" id="drawerSize">-</span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Tipe Sambungan</small>
                        <span class="fw-semibold text-dark" id="drawerConnectionType">-</span>
                    </div>
                    <div class="col-6 col-md-4">
                        <small class="text-muted d-block">Satuan Unit</small>
                        <span class="fw-semibold text-dark" id="drawerUnit">-</span>
                    </div>
                    <div class="col-6 col-md-6" id="drawerWrapperLength">
                        <small class="text-muted d-block">Standar Panjang / Batang</small>
                        <span class="fw-semibold text-dark" id="drawerLength">-</span>
                    </div>
                    <div class="col-6 col-md-6">
                        <small class="text-muted d-block">Safety Waste Margin</small>
                        <span class="fw-bold text-warning" id="drawerWaste">-</span>
                    </div>
                    <div class="col-12 mt-2 pt-2 border-top" id="drawerWrapperNotes">
                        <small class="text-muted d-block">Catatan / Spesifikasi Tambahan</small>
                        <p class="mb-0 text-dark small" id="drawerNotes">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Pricelist Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-dark"><i class="mdi mdi-store-outline text-primary me-1"></i> Penawaran Harga Supplier (HPP)</h6>
            <span class="badge bg-label-success" id="drawerCheapestBadge">-</span>
        </div>

        <!-- Form Add Vendor Price -->
        <form id="formDrawerAddVendorPrice" method="POST" class="p-3 bg-light rounded border mb-3">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0 text-primary" id="drawerFormTitle" style="font-size: 12px;">
                    <i class="mdi mdi-plus-circle-outline me-1"></i>Tambah / Perbarui Harga Vendor
                </h6>
                <button type="button" class="btn btn-xs btn-outline-secondary" id="btnDrawerCancelEditPrice" style="display: none;">
                    <i class="mdi mdi-close me-1"></i> Batal Edit
                </button>
            </div>
            <div class="row g-2">
                <div class="col-12">
                    <label class="form-label required small mb-1">Pilih Supplier / Toko</label>
                    <select name="id_supplier" id="drawerSupplierSelect" class="form-select form-select-sm select2-drawer-supplier" required style="width: 100%;">
                        <option value="">-- Cari & Pilih Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}">{{ $sup->supplier }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label required small mb-1">Harga Beli HPP (Rp)</label>
                    <input type="number" step="100" name="price_idr" id="drawerInputPrice" class="form-control form-control-sm" placeholder="Rp 0" required>
                </div>
                <div class="col-6">
                    <label class="form-label required small mb-1">Tanggal Inquiry</label>
                    <input type="date" name="date" id="drawerInputDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-12">
                    <input type="text" name="notes" id="drawerInputNotes" class="form-control form-control-sm" placeholder="Catatan: min. order, syarat bayar, ongkir...">
                </div>
                <div class="col-12 text-end mt-2">
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3" id="btnDrawerSubmitPrice">
                        <i class="mdi mdi-check me-1"></i> Simpan Harga
                    </button>
                </div>
            </div>
        </form>

        <!-- Vendor Prices Table -->
        <div class="table-responsive border rounded" style="overflow: visible !important;">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Supplier</th>
                        <th>HPP</th>
                        <th>Tgl Update</th>
                        <th>Catatan</th>
                        <th class="text-center" style="width: 36px;"></th>
                    </tr>
                </thead>
                <tbody id="drawerVendorPriceTableBody">
                    <!-- Populated dynamically via JS -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Material -->
<div class="modal fade" id="modalAddMaterial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('piping-materials.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="mdi mdi-plus-box text-primary me-2"></i>Tambah Material Piping Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label required">Kategori</label>
                        <select name="category" id="addCategory" class="form-select" required>
                            <option value="pipe">Pipa (Pipe)</option>
                            <option value="fitting">Fitting / Sambungan</option>
                            <option value="valve">Valve & Instrument</option>
                            <option value="support">Support & Fastener</option>
                            <option value="consumable">Bahan Habis Pakai (Consumable)</option>
                            <option value="other">Lain-lain</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Material Type</label>
                        <input type="text" name="material_type" class="form-control" placeholder="Contoh: Aluminium Airnet, SS304, CS Sch40, PPR">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kode Item / Part No.</label>
                        <input type="text" name="item_code" class="form-control" placeholder="Opsional (Auto/Custom)">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required">Nama Item</label>
                        <input type="text" name="item_name" class="form-control" placeholder="Contoh: Pipa Aluminium 50mm / Elbow 90 / Ball Valve" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ukuran (Size / DN)</label>
                        <input type="text" name="size" class="form-control" placeholder="Contoh: 1/2&quot;, 1&quot;, 2&quot;, 50mm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipe Sambungan</label>
                        <input type="text" name="connection_type" class="form-control" placeholder="Quick-Fit, Drat, Las, Flange">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label required">Satuan Unit</label>
                        <input type="text" name="unit" class="form-control" value="Batang" placeholder="Batang, Meter, Pcs, Roll" required>
                    </div>
                    <div class="col-md-4" id="wrapperLengthPerUnit">
                        <label class="form-label">Panjang per Batang (Meter)</label>
                        <input type="number" step="0.01" name="length_per_unit" class="form-control" value="6.00">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Default Waste Scrap (%)</label>
                        <input type="number" step="0.1" name="default_waste_percent" class="form-control" value="5.0">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Catatan / Spesifikasi Tambahan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Keterangan standar rating tekanan (PN16, JIS 10K, dll)"></textarea>
                    </div>

                    <!-- Initial Vendor Price Section -->
                    <div class="col-12 mt-4">
                        <div class="p-3 bg-light rounded border">
                            <h6 class="fw-bold mb-2 text-primary"><i class="mdi mdi-cash-plus me-1"></i>Input Harga Supplier Awal (Opsional)</h6>
                            <div class="row g-2">
                                <div class="col-md-5">
                                    <label class="form-label">Pilih Supplier / Toko</label>
                                    <select name="id_supplier" id="supplierSelectAdd" class="form-select select2-supplier-add" style="width: 100%;">
                                        <option value="">-- Cari & Pilih Supplier --</option>
                                        @foreach($suppliers as $sup)
                                            <option value="{{ $sup->id }}">{{ $sup->supplier }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Harga Beli / HPP (Rp)</label>
                                    <input type="number" step="100" name="price_idr" class="form-control" placeholder="Rp 0">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Update</label>
                                    <input type="date" name="price_date" class="form-control" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-check me-1"></i> Simpan Material</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Material -->
<div class="modal fade" id="modalEditMaterial" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="formEditMaterial" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold"><i class="mdi mdi-pencil-box text-primary me-2"></i>Edit Data Material</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label required">Kategori</label>
                        <select name="category" id="editCategory" class="form-select" required>
                            <option value="pipe">Pipa (Pipe)</option>
                            <option value="fitting">Fitting / Sambungan</option>
                            <option value="valve">Valve & Instrument</option>
                            <option value="support">Support & Fastener</option>
                            <option value="consumable">Bahan Habis Pakai (Consumable)</option>
                            <option value="other">Lain-lain</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Material Type</label>
                        <input type="text" name="material_type" id="editMaterialType" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kode Item</label>
                        <input type="text" name="item_code" id="editItemCode" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label required">Nama Item</label>
                        <input type="text" name="item_name" id="editItemName" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Ukuran (Size / DN)</label>
                        <input type="text" name="size" id="editSize" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipe Sambungan</label>
                        <input type="text" name="connection_type" id="editConnectionType" class="form-control">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label required">Satuan Unit</label>
                        <input type="text" name="unit" id="editUnit" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Panjang per Batang (Meter)</label>
                        <input type="number" step="0.01" name="length_per_unit" id="editLengthPerUnit" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Default Waste Scrap (%)</label>
                        <input type="number" step="0.1" name="default_waste_percent" id="editDefaultWaste" class="form-control">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" id="editNotes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Update Data</button>
            </div>
        </form>
    </div>
</div>

@push('after-script')
<script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
<script>
    let activeDrawerMaterial = null;

    function populateEditModal(mat) {
        if (!mat || !mat.id) return;
        const form = document.getElementById('formEditMaterial');
        form.action = `/piping-materials/${mat.id}`;

        document.getElementById('editCategory').value = mat.category || 'pipe';
        document.getElementById('editMaterialType').value = mat.material_type || '';
        document.getElementById('editItemCode').value = mat.item_code || '';
        document.getElementById('editItemName').value = mat.item_name || '';
        document.getElementById('editSize').value = mat.size || '';
        document.getElementById('editConnectionType').value = mat.connection_type || '';
        document.getElementById('editUnit').value = mat.unit || 'Batang';
        document.getElementById('editLengthPerUnit').value = mat.length_per_unit || '';
        document.getElementById('editDefaultWaste').value = mat.default_waste_percent || '';
        document.getElementById('editNotes').value = mat.notes || '';
    }

    $(document).ready(function () {
        // Initialize Select2 in Add Material Modal
        $('#modalAddMaterial').on('shown.bs.modal', function () {
            if (!$('#supplierSelectAdd').data('select2')) {
                $('#supplierSelectAdd').select2({
                    dropdownParent: $('#modalAddMaterial'),
                    placeholder: '-- Cari & Pilih Supplier --',
                    allowClear: true,
                    width: '100%'
                });
            }
        });

        // Initialize Select2 in Offcanvas Drawer
        $('#offcanvasMaterialDetail').on('shown.bs.offcanvas', function () {
            if (!$('#drawerSupplierSelect').data('select2')) {
                $('#drawerSupplierSelect').select2({
                    dropdownParent: $('#offcanvasMaterialDetail'),
                    placeholder: '-- Cari & Pilih Supplier --',
                    allowClear: true,
                    width: '100%'
                });
            }
            $('#drawerSupplierSelect').val('').trigger('change');
        });

        // Initialize DataTable
        const table = $('#tablePipingMaterials').DataTable({
            orderCellsTop: true,
            autoWidth: false,
            responsive: true,
            pageLength: 25,
            language: {
                info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ material",
                infoEmpty: "Tidak ada material ditemukan",
                infoFiltered: "(difilter dari _MAX_ total material)",
                zeroRecords: "Material tidak ditemukan",
                paginate: {
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»"
                }
            },
            order: [[0, 'asc'], [1, 'asc']],
            dom: 'rt<"row px-3 py-3 align-items-center border-top"<"col-12 col-md-6"i><"col-12 col-md-6 d-flex justify-content-md-end"p>>'
        });

        // Top Global Search Input Event
        $('#customSearchInput').on('keyup input', function () {
            table.search(this.value).draw();
        });

        // Custom Page Length Selector Event
        $('#customPageLength').on('change', function () {
            table.page.len(parseInt(this.value)).draw();
        });

        // Individual Column Search Handlers
        $('.column-search').on('keyup change input', function () {
            const colIdx = $(this).data('col');
            table.column(colIdx).search(this.value).draw();
        });

        // Reset Column Search Button
        $('#btnResetColSearch').on('click', function () {
            $('.column-search').val('');
            $('#customSearchInput').val('');
            table.search('').columns().search('').draw();
        });

        // Open Drawer Event Handler
        $(document).on('click', '.btn-open-drawer', function () {
            let mat = $(this).data('material');
            if (typeof mat === 'string') {
                try { mat = JSON.parse(mat); } catch (e) { mat = {}; }
            }
            let prices = $(this).data('prices');
            if (typeof prices === 'string') {
                try { prices = JSON.parse(prices); } catch (e) { prices = []; }
            }
            if (!Array.isArray(prices)) {
                prices = [];
            }

            activeDrawerMaterial = mat;

            // Populate Header & Info
            const categoryBadge = document.getElementById('drawerBadgeCategory');
            categoryBadge.innerText = (mat.category || 'other').toUpperCase();
            categoryBadge.className = 'badge fs-7 ' + (
                mat.category === 'pipe' ? 'bg-primary' :
                mat.category === 'fitting' ? 'bg-info' :
                mat.category === 'valve' ? 'bg-warning' :
                mat.category === 'support' ? 'bg-success' : 'bg-secondary'
            );

            document.getElementById('drawerItemName').innerText = mat.item_name || '-';
            document.getElementById('drawerItemCode').innerText = mat.item_code || 'Auto ID';
            document.getElementById('drawerMaterialType').innerText = mat.material_type || '-';

            // Delete Form Action
            document.getElementById('formDrawerDeleteMaterial').action = `/piping-materials/${mat.id}`;

            // Technical Specs
            document.getElementById('drawerSize').innerText = mat.size || '-';
            document.getElementById('drawerConnectionType').innerText = mat.connection_type || '-';
            document.getElementById('drawerUnit').innerText = mat.unit || '-';
            
            const wrapperLength = document.getElementById('drawerWrapperLength');
            if (mat.category === 'pipe' && mat.length_per_unit) {
                wrapperLength.style.display = 'block';
                document.getElementById('drawerLength').innerText = `${parseFloat(mat.length_per_unit)} Meter`;
            } else {
                wrapperLength.style.display = 'none';
            }

            document.getElementById('drawerWaste').innerText = `${parseFloat(mat.default_waste_percent || 0)}%`;
            
            const wrapperNotes = document.getElementById('drawerWrapperNotes');
            if (mat.notes) {
                wrapperNotes.style.display = 'block';
                document.getElementById('drawerNotes').innerText = mat.notes;
            } else {
                wrapperNotes.style.display = 'none';
            }

            // Reset Add Price Form
            document.getElementById('formDrawerAddVendorPrice').action = `/piping-materials/${mat.id}/vendor-prices`;
            $('#btnDrawerCancelEditPrice').trigger('click');

            // Populate Vendor Prices Table
            const tbody = document.getElementById('drawerVendorPriceTableBody');
            tbody.innerHTML = '';

            const cheapestBadge = document.getElementById('drawerCheapestBadge');

            if (prices.length === 0) {
                cheapestBadge.className = 'badge bg-label-secondary';
                cheapestBadge.innerText = 'Belum Ada Harga';
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="mdi mdi-store-off-outline me-1"></i> Belum ada penawaran supplier untuk item ini.</td></tr>';
            } else {
                // Sort ascending by price_idr
                prices.sort((a, b) => parseFloat(a.price_idr) - parseFloat(b.price_idr));
                const cheapestPrice = parseFloat(prices[0].price_idr);
                cheapestBadge.className = 'badge bg-label-success';
                cheapestBadge.innerText = `Termurah: Rp ${new Intl.NumberFormat('id-ID').format(cheapestPrice)}`;

                prices.forEach((vp, idx) => {
                    const isCheapest = idx === 0;
                    const priceFormatted = new Intl.NumberFormat('id-ID').format(vp.price_idr);
                    const supName = vp.supplier ? vp.supplier.supplier : 'Supplier #' + vp.id_supplier;
                    const dateFormatted = vp.date ? new Date(vp.date).toLocaleDateString('id-ID') : '-';

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <div class="fw-bold ${isCheapest ? 'text-primary' : 'text-dark'}">${supName}</div>
                            ${isCheapest ? '<span class="badge bg-label-success" style="font-size: 10px;">Harga Utama / Termurah</span>' : ''}
                        </td>
                        <td class="fw-bold ${isCheapest ? 'text-success' : 'text-dark'}">Rp ${priceFormatted}</td>
                        <td><small class="text-muted">${dateFormatted}</small></td>
                        <td><small class="text-muted">${vp.notes || '-'}</small></td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button type="button" class="btn btn-xs btn-icon p-0 border-0 dropdown-toggle hide-arrow shadow-none text-muted" data-bs-toggle="dropdown" aria-expanded="false" title="Pilihan Aksi">
                                    <i class="mdi mdi-dots-vertical fs-5"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 130px; font-size: 12.5px;">
                                    <li>
                                        <a class="dropdown-item btn-edit-vendor-price d-flex align-items-center py-2" href="javascript:void(0);" data-price='${JSON.stringify(vp).replace(/'/g, "&apos;")}'>
                                            <i class="mdi mdi-pencil-outline me-2 text-primary"></i> Edit Harga
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form action="/piping-materials/vendor-prices/${vp.id}" method="POST" onsubmit="return confirm('Hapus harga supplier ${supName.replace(/'/g, "\\'")}?')">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="dropdown-item d-flex align-items-center py-2 text-danger border-0 bg-transparent w-100 text-start">
                                                <i class="mdi mdi-trash-can-outline me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }

            // Open Offcanvas Drawer
            const offcanvasEl = document.getElementById('offcanvasMaterialDetail');
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.show();
        });

        // Edit Vendor Price in Drawer Handler
        $(document).on('click', '.btn-edit-vendor-price', function () {
            let vp = $(this).data('price');
            if (typeof vp === 'string') {
                try { vp = JSON.parse(vp); } catch (e) { vp = {}; }
            }
            if (!vp) return;

            const supName = vp.supplier ? vp.supplier.supplier : 'Supplier #' + vp.id_supplier;

            $('#drawerSupplierSelect').val(vp.id_supplier).trigger('change');
            $('#drawerInputPrice').val(parseFloat(vp.price_idr || 0)).focus().select();
            if (vp.date) {
                $('#drawerInputDate').val(vp.date.split('T')[0]);
            }
            $('#drawerInputNotes').val(vp.notes || '');

            $('#drawerFormTitle').html(`<i class="mdi mdi-pencil text-warning me-1"></i>Edit Harga: ${supName}`);
            $('#btnDrawerSubmitPrice').html('<i class="mdi mdi-check me-1"></i> Update Harga');
            $('#btnDrawerCancelEditPrice').show();
        });

        // Cancel Edit Vendor Price Handler
        $('#btnDrawerCancelEditPrice').on('click', function () {
            $('#drawerSupplierSelect').val('').trigger('change');
            $('#drawerInputPrice').val('');
            $('#drawerInputDate').val(new Date().toISOString().split('T')[0]);
            $('#drawerInputNotes').val('');

            $('#drawerFormTitle').html('<i class="mdi mdi-plus-circle-outline me-1"></i>Tambah / Perbarui Harga Vendor');
            $('#btnDrawerSubmitPrice').html('<i class="mdi mdi-check me-1"></i> Simpan Harga');
            $(this).hide();
        });

        // Edit button inside Drawer Handler
        $('#btnDrawerEditMaterial').on('click', function () {
            if (!activeDrawerMaterial) return;

            // Hide offcanvas
            const offcanvasEl = document.getElementById('offcanvasMaterialDetail');
            const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (offcanvas) offcanvas.hide();

            // Populate and show edit modal
            populateEditModal(activeDrawerMaterial);
            const modalEdit = new bootstrap.Modal(document.getElementById('modalEditMaterial'));
            modalEdit.show();
        });
    });
</script>
@endpush
@endsection
