@extends('layouts.sales.app')
@section('title', 'Unit Acquisition')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Unit Acquisition</h4>
        <a href="{{ route('fixed.create') }}" class="btn btn-primary waves-effect">
            Unit Acquisition Baru
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-header py-2 bg-transparent border-bottom">
            <ul class="nav nav-tabs card-header-tabs border-0 m-0" id="unitAcquisitionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link active px-3 py-2 fw-semibold" role="tab"
                        data-bs-toggle="tab" data-bs-target="#tab-unit-second"
                        aria-controls="tab-unit-second" aria-selected="true">
                        Unit Second
                        <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="unit-second-count-badge">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link px-3 py-2 fw-semibold" role="tab"
                        data-bs-toggle="tab" data-bs-target="#tab-unit-baru"
                        aria-controls="tab-unit-baru" aria-selected="false">
                        Unit Baru
                        <span class="badge bg-label-success rounded-pill ms-1 d-none" id="unit-baru-count-badge">0</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="tab-content p-0">
            {{-- Tab Unit Second — Fixed Asset (butuh QC dulu sebelum dipakai/dijual) —
                 dipecah per kategori unit (sub-tab), sama susunannya kayak tab Unit Baru. --}}
            <div class="tab-pane fade show active" id="tab-unit-second" role="tabpanel">
                <p class="text-muted small px-3 pt-3 mb-0">Unit bekas/trade-in yang didaftarkan sebagai aset perusahaan — perlu konfirmasi QC sebelum dipakai/dijual.</p>
                <ul class="nav nav-pills px-3 pt-3 mb-0" id="unitAcquisitionSubtabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active fw-semibold" id="subtab-second-screw-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-second-screw" type="button" role="tab" aria-controls="subtab-second-screw" aria-selected="true">
                            Compressor
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="unit-acquisition-screw-count-badge">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link fw-semibold" id="subtab-second-dryer-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-second-dryer" type="button" role="tab" aria-controls="subtab-second-dryer" aria-selected="false">
                            Dryer
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="unit-acquisition-dryer-count-badge">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link fw-semibold" id="subtab-second-filter-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-second-filter" type="button" role="tab" aria-controls="subtab-second-filter" aria-selected="false">
                            Filter
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="unit-acquisition-filter-count-badge">0</span>
                        </button>
                    </li>
                    {{-- Chiller sementara disembunyikan dulu di Unit Second — lihat komentar
                         di $secondSpecColumns dan @foreach di bawah. --}}
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link fw-semibold" id="subtab-second-tank-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-second-tank" type="button" role="tab" aria-controls="subtab-second-tank" aria-selected="false">
                            Air Receiver Tank
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="unit-acquisition-tank-count-badge">0</span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content p-0">
                    @php
                        // Kolom spesifikasi per kategori — disamakan dengan kolom yang
                        // dipakai tab "Unit Baru" (lihat sub-tab unit_inventory di bawah).
                        $secondSpecColumns = [
                            'screw' => ['Lubricant', 'Power', 'Air Capacity'],
                            'dryer' => ['Type', 'PDP', 'FAD'],
                            'filter' => ['FAD', 'Grade', 'Connection'],
                            'chiller' => ['Cooling Capacity', 'kW / Power'],
                            'tank' => ['Capacity', 'Material', 'Type'],
                        ];
                    @endphp
                    {{-- Chiller sementara disembunyikan dulu di Unit Second (tabel/route-nya
                         tetap ada, tinggal balikin ke array ini kalau mau ditampilkan lagi). --}}
                    @foreach (['screw' => 'subtab-second-screw', 'dryer' => 'subtab-second-dryer', 'filter' => 'subtab-second-filter', 'tank' => 'subtab-second-tank'] as $group => $paneId)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $paneId }}" role="tabpanel">
                            <div class="card-datatable table-responsive pt-0">
                                <table class="datatable-unit-acquisition table" data-group="{{ $group }}">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Unit</th>
                                            @foreach ($secondSpecColumns[$group] as $specLabel)
                                                <th>{{ $specLabel }}</th>
                                            @endforeach
                                            <th>SN (Serial Number)</th>
                                            <th>Status</th>
                                        </tr>
                                        <tr class="column-filters">
                                            <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                            <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                            @foreach ($secondSpecColumns[$group] as $specLabel)
                                                <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                            @endforeach
                                            <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                            <th>
                                                <select class="form-select form-select-sm" data-col-search-status>
                                                    <option value="">Semua Status</option>
                                                    <option value="checking">Dalam Pengecekan</option>
                                                    <option value="reject">Reject</option>
                                                    <option value="ok OK">Unit OK</option>
                                                    <option value="ok Service">Service</option>
                                                    <option value="ok Rental">Rental</option>
                                                    <option value="ok Breakdown">Breakdown</option>
                                                    <option value="ok Reserved">Reserved</option>
                                                    <option value="ok Sold">Sold</option>
                                                </select>
                                            </th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tab Unit Baru — Unit Inventory (stok jual, gak lewat QC/penyusutan) —
                 dipecah per kategori unit (sub-tab), masing-masing datatable sendiri. --}}
            <div class="tab-pane fade" id="tab-unit-baru" role="tabpanel">
                <p class="text-muted small px-3 pt-3 mb-0">Unit baru hasil Goods Receipt PO — langsung jadi stok jual, tidak melalui QC/penyusutan aset.</p>
                <ul class="nav nav-pills px-3 pt-3 mb-0" id="unitInventorySubtabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active fw-semibold" id="subtab-screw-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-screw" type="button" role="tab" aria-controls="subtab-screw" aria-selected="true">
                            Compressor
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="unit-inventory-screw-count-badge">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link fw-semibold" id="subtab-dryer-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-dryer" type="button" role="tab" aria-controls="subtab-dryer" aria-selected="false">
                            Dryer
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="unit-inventory-dryer-count-badge">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link fw-semibold" id="subtab-filter-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-filter" type="button" role="tab" aria-controls="subtab-filter" aria-selected="false">
                            Filter
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="unit-inventory-filter-count-badge">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link fw-semibold" id="subtab-chiller-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-chiller" type="button" role="tab" aria-controls="subtab-chiller" aria-selected="false">
                            Chiller
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="unit-inventory-chiller-count-badge">0</span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content p-0">
                    <div class="tab-pane fade show active" id="subtab-screw" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-unit-inventory table" data-group="screw">
                                <thead>
                                    <tr>
                                        <th>Unit</th>
                                        <th>Type</th>
                                        <th>Lubricant</th>
                                        <th>Power</th>
                                        <th>Air Capacity</th>
                                        <th>Stock</th>
                                        <th>Harga Jual</th>
                                        <th></th>
                                    </tr>
                                    <tr class="column-filters">
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="subtab-dryer" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-unit-inventory table" data-group="dryer">
                                <thead>
                                    <tr>
                                        <th>Unit</th>
                                        <th>Type</th>
                                        <th>PDP</th>
                                        <th>FAD</th>
                                        <th>Stock</th>
                                        <th>Harga Jual</th>
                                        <th></th>
                                    </tr>
                                    <tr class="column-filters">
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="subtab-filter" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-unit-inventory table" data-group="filter">
                                <thead>
                                    <tr>
                                        <th>Unit</th>
                                        <th>FAD</th>
                                        <th>Grade</th>
                                        <th>Connection</th>
                                        <th>Stock</th>
                                        <th>Harga Jual</th>
                                        <th></th>
                                    </tr>
                                    <tr class="column-filters">
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="subtab-chiller" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-unit-inventory table" data-group="chiller">
                                <thead>
                                    <tr>
                                        <th>Unit</th>
                                        <th>Cooling Capacity</th>
                                        <th>kW / Power</th>
                                        <th>Stock</th>
                                        <th>Harga Jual</th>
                                        <th></th>
                                    </tr>
                                    <tr class="column-filters">
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-style')
    <style>
        .column-filters th { padding-top: .25rem; padding-bottom: .5rem; }
        .column-filters input.form-control-sm { font-weight: 400; }
    </style>
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush

@push('page-script')
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-acquisition.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-inventory.js"></script>
@endpush
