@extends('layouts.sales.app')
@section('title', 'Unit Acquisition')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Unit Acquisition</h4>
        @if (Auth::user()->role != 'Sales')
            <a href="{{ route('fixed.create') }}" class="btn btn-primary waves-effect">
                Unit Acquisition Baru
            </a>
        @endif
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
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link px-3 py-2 fw-semibold" role="tab"
                        data-bs-toggle="tab" data-bs-target="#tab-accessories"
                        aria-controls="tab-accessories" aria-selected="false">
                        Accessories
                        <span class="badge bg-label-info rounded-pill ms-1 d-none" id="accessories-count-badge">0</span>
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
                            'screw' => ['Lubricant', 'Power', 'Air Capacity', 'Pressure'],
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
                                        <th>Speed Type</th>
                                        <th>Lubricant</th>
                                        <th>Power</th>
                                        <th>Air Capacity</th>
                                        <th>Pressure</th>
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

            {{-- Tab Accessories — Perlengkapan & Aksesoris Kelengkapan Rental --}}
            <div class="tab-pane fade" id="tab-accessories" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center flex-wrap px-3 pt-3 gap-2">
                    <p class="text-muted small mb-0">Kelengkapan aksesoris instalasi pendukung unit rental (Flexible Hose, Header, Reducer, Kabel Power, Double Nipple).</p>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddAccessory">
                        <i class="mdi mdi-plus me-1"></i> Tambah Aksesoris
                    </button>
                </div>
                <ul class="nav nav-pills px-3 pt-3 mb-0" id="accessoriesSubtabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active fw-semibold" id="subtab-acc-hose-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-acc-hose" type="button" role="tab" aria-controls="subtab-acc-hose" aria-selected="true">
                            Flexible Hose
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="acc-hose-count-badge">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link fw-semibold" id="subtab-acc-header-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-acc-header" type="button" role="tab" aria-controls="subtab-acc-header" aria-selected="false">
                            Header
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="acc-header-count-badge">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link fw-semibold" id="subtab-acc-reducer-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-acc-reducer" type="button" role="tab" aria-controls="subtab-acc-reducer" aria-selected="false">
                            Reducer
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="acc-reducer-count-badge">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link fw-semibold" id="subtab-acc-cable-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-acc-cable" type="button" role="tab" aria-controls="subtab-acc-cable" aria-selected="false">
                            Kabel Power
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="acc-cable-count-badge">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link fw-semibold" id="subtab-acc-nipple-btn" data-bs-toggle="pill"
                            data-bs-target="#subtab-acc-nipple" type="button" role="tab" aria-controls="subtab-acc-nipple" aria-selected="false">
                            Double Nipple
                            <span class="badge bg-label-primary rounded-pill ms-1 d-none" id="acc-nipple-count-badge">0</span>
                        </button>
                    </li>
                </ul>
                <div class="tab-content p-0">
                    {{-- Sub-tab Flexible Hose --}}
                    <div class="tab-pane fade show active" id="subtab-acc-hose" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-unit-accessories table" data-group="hose">
                                <thead>
                                    <tr>
                                        <th>Kode / Item</th>
                                        <th>Ukuran (Diameter)</th>
                                        <th>Panjang</th>
                                        <th>Max. Pressure</th>
                                        <th>Koneksi / Fitting</th>
                                        <th>Kondisi</th>
                                        <th>Status Rental</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                    <tr class="column-filters">
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
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

                    {{-- Sub-tab Header --}}
                    <div class="tab-pane fade" id="subtab-acc-header" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-unit-accessories table" data-group="header">
                                <thead>
                                    <tr>
                                        <th>Kode / Item</th>
                                        <th>Ukuran Pipa Utama</th>
                                        <th>Jumlah Outlet</th>
                                        <th>Ukuran Outlet</th>
                                        <th>Material</th>
                                        <th>Kondisi</th>
                                        <th>Status Rental</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                    <tr class="column-filters">
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
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

                    {{-- Sub-tab Reducer --}}
                    <div class="tab-pane fade" id="subtab-acc-reducer" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-unit-accessories table" data-group="reducer">
                                <thead>
                                    <tr>
                                        <th>Kode / Item</th>
                                        <th>Ukuran Inlet</th>
                                        <th>Ukuran Outlet</th>
                                        <th>Material</th>
                                        <th>Tipe Koneksi</th>
                                        <th>Kondisi</th>
                                        <th>Status Rental</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                    <tr class="column-filters">
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
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

                    {{-- Sub-tab Kabel Power --}}
                    <div class="tab-pane fade" id="subtab-acc-cable" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-unit-accessories table" data-group="cable">
                                <thead>
                                    <tr>
                                        <th>Kode / Item</th>
                                        <th>Tipe / Ukuran Kabel</th>
                                        <th>Panjang (Meter)</th>
                                        <th>Kapasitas Arus / Ampere</th>
                                        <th>Tipe Terminal / Plug</th>
                                        <th>Kondisi</th>
                                        <th>Status Rental</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                    <tr class="column-filters">
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
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

                    {{-- Sub-tab Double Nipple --}}
                    <div class="tab-pane fade" id="subtab-acc-nipple" role="tabpanel">
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-unit-accessories table" data-group="nipple">
                                <thead>
                                    <tr>
                                        <th>Kode / Item</th>
                                        <th>Ukuran Ulir / Thread</th>
                                        <th>Tipe Ulir (NPT / BSPT)</th>
                                        <th>Material</th>
                                        <th>Rating Tekanan</th>
                                        <th>Kondisi</th>
                                        <th>Status Rental</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                    <tr class="column-filters">
                                        <th><input type="text" class="form-control form-control-sm" data-col-search /></th>
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
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Aksesoris --}}
    <div class="modal fade" id="modalAddAccessory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formAddAccessory">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="mdi mdi-plus-box-outline me-1 text-primary"></i> Tambah Aksesoris Rental</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Kategori Aksesoris</label>
                                <select class="form-select select-acc-category" name="category" required>
                                    <option value="hose">Flexible Hose</option>
                                    <option value="header">Header</option>
                                    <option value="reducer">Reducer</option>
                                    <option value="cable">Kabel Power</option>
                                    <option value="nipple">Double Nipple</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Item / Barcode</label>
                                <input type="text" class="form-control" name="code" placeholder="Contoh: ACC-FH-001" />
                            </div>
                            <div class="col-md-8">
                                <label class="form-label required">Nama / Deskripsi Aksesoris</label>
                                <input type="text" class="form-control" name="name" required placeholder="Contoh: Flexible Hose 2 Inch x 5 Meter" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Merk / Brand</label>
                                <input type="text" class="form-control" name="brand" placeholder="Contoh: Alfagomma / Toyox" />
                            </div>

                            {{-- Spesifikasi Teknis Dinamis --}}
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-0 text-primary fw-semibold"><i class="mdi mdi-cog-outline me-1"></i> Spesifikasi Teknis</h6>
                            </div>
                            
                            <div class="col-md-6 field-size">
                                <label class="form-label label-size">Ukuran (Diameter / Pipa / Thread)</label>
                                <input type="text" class="form-control" name="size" placeholder="Contoh: 2 Inch" />
                            </div>
                            <div class="col-md-6 field-length">
                                <label class="form-label label-length">Panjang</label>
                                <input type="text" class="form-control" name="length" placeholder="Contoh: 5 Meter / 25 Meter" />
                            </div>
                            <div class="col-md-6 field-max-pressure">
                                <label class="form-label label-max-pressure">Max. Pressure / Rating Tekanan</label>
                                <input type="text" class="form-control" name="max_pressure" placeholder="Contoh: 10 Bar / 3000 PSI" />
                            </div>
                            <div class="col-md-6 field-connection">
                                <label class="form-label label-connection">Tipe Koneksi / Fitting / Outlet</label>
                                <input type="text" class="form-control" name="connection" placeholder="Contoh: Camlock Type C+E / NPT Male" />
                            </div>
                            <div class="col-md-6 field-material">
                                <label class="form-label label-material">Material</label>
                                <input type="text" class="form-control" name="material" placeholder="Contoh: Stainless Steel / Kuningan / Rubber" />
                            </div>
                            <div class="col-md-6 field-extra-spec">
                                <label class="form-label label-extra-spec">Spesifikasi Tambahan (Port / Ampere)</label>
                                <input type="text" class="form-control" name="extra_spec" placeholder="Contoh: 4 Port Outlet / 100 Ampere" />
                            </div>

                            {{-- Stok & Kondisi --}}
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-0 text-primary fw-semibold"><i class="mdi mdi-package-variant-closed me-1"></i> Stok & Status</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jumlah / Stok Unit</label>
                                <input type="number" class="form-control" name="stock" value="1" min="0" required />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kondisi Fisik</label>
                                <select class="form-select" name="condition">
                                    <option value="ok">Bagus / OK</option>
                                    <option value="fair">Cukup (Layak Pakai)</option>
                                    <option value="damaged">Rusak</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status Rental</label>
                                <select class="form-select" name="rental_status">
                                    <option value="available">Tersedia (Ready di Gudang)</option>
                                    <option value="rental">Sedang Dirental</option>
                                    <option value="reserved">Reserved</option>
                                    <option value="maintenance">Perawatan / Service</option>
                                    <option value="broken">Rusak / Tidak Layak</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lokasi / Rak Gudang</label>
                                <input type="text" class="form-control" name="location" placeholder="Contoh: Rak Aksesoris B-02" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catatan</label>
                                <input type="text" class="form-control" name="notes" placeholder="Catatan tambahan (opsional)" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-submit-acc">
                            <span class="spinner-border spinner-border-sm me-1 d-none"></span>
                            Simpan Aksesoris
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Aksesoris --}}
    <div class="modal fade" id="modalEditAccessory" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditAccessory">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="edit-acc-id" />
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="mdi mdi-pencil-outline me-1 text-primary"></i> Edit Aksesoris Rental</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Kategori Aksesoris</label>
                                <select class="form-select select-acc-category" name="category" id="edit-acc-category" required>
                                    <option value="hose">Flexible Hose</option>
                                    <option value="header">Header</option>
                                    <option value="reducer">Reducer</option>
                                    <option value="cable">Kabel Power</option>
                                    <option value="nipple">Double Nipple</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Kode Item / Barcode</label>
                                <input type="text" class="form-control" name="code" id="edit-acc-code" placeholder="Contoh: ACC-FH-001" />
                            </div>
                            <div class="col-md-8">
                                <label class="form-label required">Nama / Deskripsi Aksesoris</label>
                                <input type="text" class="form-control" name="name" id="edit-acc-name" required placeholder="Contoh: Flexible Hose 2 Inch x 5 Meter" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Merk / Brand</label>
                                <input type="text" class="form-control" name="brand" id="edit-acc-brand" placeholder="Contoh: Alfagomma / Toyox" />
                            </div>

                            {{-- Spesifikasi Teknis Dinamis --}}
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-0 text-primary fw-semibold"><i class="mdi mdi-cog-outline me-1"></i> Spesifikasi Teknis</h6>
                            </div>
                            
                            <div class="col-md-6 field-size">
                                <label class="form-label label-size">Ukuran (Diameter / Pipa / Thread)</label>
                                <input type="text" class="form-control" name="size" id="edit-acc-size" placeholder="Contoh: 2 Inch" />
                            </div>
                            <div class="col-md-6 field-length">
                                <label class="form-label label-length">Panjang</label>
                                <input type="text" class="form-control" name="length" id="edit-acc-length" placeholder="Contoh: 5 Meter / 25 Meter" />
                            </div>
                            <div class="col-md-6 field-max-pressure">
                                <label class="form-label label-max-pressure">Max. Pressure / Rating Tekanan</label>
                                <input type="text" class="form-control" name="max_pressure" id="edit-acc-max-pressure" placeholder="Contoh: 10 Bar / 3000 PSI" />
                            </div>
                            <div class="col-md-6 field-connection">
                                <label class="form-label label-connection">Tipe Koneksi / Fitting / Outlet</label>
                                <input type="text" class="form-control" name="connection" id="edit-acc-connection" placeholder="Contoh: Camlock Type C+E / NPT Male" />
                            </div>
                            <div class="col-md-6 field-material">
                                <label class="form-label label-material">Material</label>
                                <input type="text" class="form-control" name="material" id="edit-acc-material" placeholder="Contoh: Stainless Steel / Kuningan / Rubber" />
                            </div>
                            <div class="col-md-6 field-extra-spec">
                                <label class="form-label label-extra-spec">Spesifikasi Tambahan (Port / Ampere)</label>
                                <input type="text" class="form-control" name="extra_spec" id="edit-acc-extra-spec" placeholder="Contoh: 4 Port Outlet / 100 Ampere" />
                            </div>

                            {{-- Stok & Kondisi --}}
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 mb-0 text-primary fw-semibold"><i class="mdi mdi-package-variant-closed me-1"></i> Stok & Status</h6>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jumlah / Stok Unit</label>
                                <input type="number" class="form-control" name="stock" id="edit-acc-stock" value="1" min="0" required />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kondisi Fisik</label>
                                <select class="form-select" name="condition" id="edit-acc-condition">
                                    <option value="ok">Bagus / OK</option>
                                    <option value="fair">Cukup (Layak Pakai)</option>
                                    <option value="damaged">Rusak</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Status Rental</label>
                                <select class="form-select" name="rental_status" id="edit-acc-rental-status">
                                    <option value="available">Tersedia (Ready di Gudang)</option>
                                    <option value="rental">Sedang Dirental</option>
                                    <option value="reserved">Reserved</option>
                                    <option value="maintenance">Perawatan / Service</option>
                                    <option value="broken">Rusak / Tidak Layak</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lokasi / Rak Gudang</label>
                                <input type="text" class="form-control" name="location" id="edit-acc-location" placeholder="Contoh: Rak Aksesoris B-02" />
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Catatan</label>
                                <input type="text" class="form-control" name="notes" id="edit-acc-notes" placeholder="Catatan tambahan (opsional)" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary btn-submit-acc">
                            <span class="spinner-border spinner-border-sm me-1 d-none"></span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (Auth::user()->role == 'Admin')
        <!-- Modal Edit Harga Jual Unit Baru -->
        <div class="modal fade" id="modalEditHargaJualInventory" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="formEditHargaJualInventory">
                        @csrf
                        <input type="hidden" name="id_unit" id="modal-edit-harga-unit-id" value="">
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title fw-bold mb-0">Edit Harga Jual Unit Baru</h5>
                                <small class="text-muted" id="modal-edit-harga-unit-name">-</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Info Pricelist dari Catalog Unit -->
                            <div id="modal-edit-harga-catalog-info" class="alert alert-info py-2 px-3 mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="d-block text-muted"><i class="mdi mdi-tag-outline me-1"></i>Pricelist Resmi Katalog:</small>
                                    <strong id="modal-edit-harga-catalog-val" class="fs-6">Rp 0</strong>
                                </div>
                                <button type="button" class="btn btn-xs btn-outline-primary" id="btnApplyCatalogPrice">
                                    <i class="mdi mdi-arrow-down-bold me-1"></i>Gunakan Pricelist
                                </button>
                            </div>
                            <div id="modal-edit-harga-no-catalog-info" class="alert alert-light border py-2 px-3 mb-3 d-none">
                                <small class="text-muted"><i class="mdi mdi-information-outline me-1"></i>Unit ini belum terdaftar atau belum memiliki setting harga di Katalog Unit.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Harga Jual Unit (Rp) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="harga_jual" id="modal-input-harga-jual" min="0" step="1" required placeholder="0">
                                <div class="form-text small">Harga jual ini berlaku untuk semua unit fisik dengan model ini.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btnSubmitHargaJualInventory">
                                <span class="spinner-border spinner-border-sm me-1 d-none" id="spinnerSubmitHargaJual"></span>
                                Simpan Harga
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
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
    <script>
        window.isInventoryAdmin = {{ Auth::user()->role == 'Admin' ? 'true' : 'false' }};
    </script>
    <script src="{{ asset('assets') }}/js/tables-datatables-advanced.js"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-acquisition.js?v={{ file_exists(public_path('assets/includes/table-unit-acquisition.js')) ? filemtime(public_path('assets/includes/table-unit-acquisition.js')) : time() }}"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-inventory.js?v={{ file_exists(public_path('assets/includes/table-unit-inventory.js')) ? filemtime(public_path('assets/includes/table-unit-inventory.js')) : time() }}"></script>
    <script src="{{ asset('assets') }}/includes/table-unit-accessories.js?v={{ file_exists(public_path('assets/includes/table-unit-accessories.js')) ? filemtime(public_path('assets/includes/table-unit-accessories.js')) : time() }}"></script>
@endpush


