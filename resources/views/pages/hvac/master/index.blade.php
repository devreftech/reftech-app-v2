@extends('layouts.sales.app')
@section('title', 'HVAC Master Data & Katalog AC')

@section('content')
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('hvac.project.index') }}" class="btn btn-sm btn-icon btn-light">
                    <i class="mdi mdi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0">Master Data &amp; Katalog AC</h4>
            </div>
            <p class="text-muted mb-0">Konfigurasi parameter engineering HVAC: Katalog Unit AC, Material Konstruksi, Jenis Kaca (SHGC), dan Beban Manusia.</p>
        </div>
        <a href="{{ route('hvac.project.index') }}" class="btn btn-outline-secondary">
            <i class="mdi mdi-folder-outline me-1"></i> Kembali ke Daftar Proyek
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Tabs Header -->
    <ul class="nav nav-pills mb-4" id="masterTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link {{ $tab == 'ac_catalog' ? 'active' : '' }}" id="ac-tab" data-bs-toggle="pill" data-bs-target="#acPane" type="button">
                <i class="mdi mdi-air-conditioner me-1"></i> Katalog Unit AC ({{ $acUnits->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $tab == 'materials' ? 'active' : '' }}" id="mat-tab" data-bs-toggle="pill" data-bs-target="#matPane" type="button">
                <i class="mdi mdi-wall me-1"></i> Material Dinding &amp; Atap ({{ $materials->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $tab == 'glass' ? 'active' : '' }}" id="glass-tab" data-bs-toggle="pill" data-bs-target="#glassPane" type="button">
                <i class="mdi mdi-window-closed-variant me-1"></i> Tipe Kaca &amp; SHGC ({{ $glassTypes->count() }})
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link {{ $tab == 'activities' ? 'active' : '' }}" id="act-tab" data-bs-toggle="pill" data-bs-target="#actPane" type="button">
                <i class="mdi mdi-account-group me-1"></i> Beban Aktivitas Manusia ({{ $activities->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- 1. AC CATALOG PANE -->
        <div class="tab-pane fade {{ $tab == 'ac_catalog' ? 'show active' : '' }}" id="acPane">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div>
                        <h6 class="card-title mb-0 fw-bold d-flex align-items-center gap-2">
                            <i class="mdi mdi-air-conditioner text-primary fs-5"></i>
                            Daftar Master Katalog Unit AC
                        </h6>
                        <small class="text-muted" id="acCountSummary">
                            Menampilkan <strong id="visibleAcCount" class="text-primary">{{ $acUnits->count() }}</strong> dari total {{ $totalAcCount ?? $acUnits->count() }} unit AC
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAcModal">
                            <i class="mdi mdi-plus me-1"></i> Tambah Unit AC
                        </button>
                    </div>
                </div>

                <!-- FILTER TOOLBAR SESUAI TIPE AC & PENCARIAN -->
                <div class="card-body bg-white border-bottom py-3">
                    <div class="row g-2 align-items-center justify-content-between">
                        <!-- Tipe AC Filter Pill Buttons (Desktop) -->
                        <div class="col-12 col-xl-8">
                            <div class="d-flex flex-wrap align-items-center gap-1 ac-type-filter-group">
                                <span class="text-muted small fw-semibold me-1 d-none d-sm-inline"><i class="mdi mdi-filter-variant me-1"></i>Filter Tipe:</span>
                                <button type="button" class="btn btn-xs btn-outline-primary btn-ac-filter active" data-type="all">
                                    Semua ({{ $totalAcCount ?? $acUnits->count() }})
                                </button>
                                @foreach($allAcTypes as $type)
                                    @php
                                        $typeLabel = match($type) {
                                            'split_wall' => 'Split Wall',
                                            'cassette' => 'Cassette',
                                            'floor_standing' => 'Floor Standing',
                                            'ceiling_ducted' => 'Ceiling Ducted',
                                            'vrv_vrf' => 'VRV / VRF',
                                            'package' => 'Packaged Unit',
                                            'chiller' => 'Chiller',
                                            default => ucwords(str_replace('_', ' ', $type))
                                        };
                                        $count = $acTypeCounts[$type] ?? 0;
                                    @endphp
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-ac-filter" data-type="{{ $type }}">
                                        {{ $typeLabel }} <span class="badge bg-light text-dark ms-1">{{ $count }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Dropdown & Quick Search -->
                        <div class="col-12 col-xl-4">
                            <div class="d-flex align-items-center gap-2">
                                <select class="form-select form-select-sm d-xl-none" id="selectAcTypeMobile">
                                    <option value="all">Semua Tipe AC ({{ $totalAcCount ?? $acUnits->count() }})</option>
                                    @foreach($allAcTypes as $type)
                                        @php
                                            $typeLabel = match($type) {
                                                'split_wall' => 'Split Wall',
                                                'cassette' => 'Cassette',
                                                'floor_standing' => 'Floor Standing',
                                                'ceiling_ducted' => 'Ceiling Ducted',
                                                'vrv_vrf' => 'VRV / VRF',
                                                'package' => 'Packaged Unit',
                                                'chiller' => 'Chiller',
                                                default => ucwords(str_replace('_', ' ', $type))
                                            };
                                            $count = $acTypeCounts[$type] ?? 0;
                                        @endphp
                                        <option value="{{ $type }}">{{ $typeLabel }} ({{ $count }})</option>
                                    @endforeach
                                </select>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i class="mdi mdi-magnify"></i></span>
                                    <input type="text" id="searchAcInput" class="form-control" placeholder="Cari brand, model, PK...">
                                    <button class="btn btn-outline-secondary d-none" type="button" id="clearSearchAc">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="acCatalogTable">
                        <thead class="table-light">
                            <tr>
                                <th>Brand &amp; Model</th>
                                <th>Tipe AC</th>
                                <th class="text-center">Kapasitas (PK)</th>
                                <th class="text-end">Cooling Load (BTU/h)</th>
                                <th class="text-end">Cooling Load (kW)</th>
                                <th class="text-end">Daya Listrik (W)</th>
                                <th class="text-center">Refrigerant</th>
                                <th>Rating Energi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($acUnits as $u)
                                @php
                                    $typeBadgeClass = match($u->ac_type) {
                                        'split_wall' => 'bg-label-primary',
                                        'cassette' => 'bg-label-info',
                                        'floor_standing' => 'bg-label-warning',
                                        'ceiling_ducted' => 'bg-label-success',
                                        'vrv_vrf' => 'bg-label-danger',
                                        'package' => 'bg-label-secondary',
                                        default => 'bg-label-dark'
                                    };
                                    $typeIcon = match($u->ac_type) {
                                        'split_wall' => 'mdi-air-conditioner',
                                        'cassette' => 'mdi-fan',
                                        'floor_standing' => 'mdi-server',
                                        'ceiling_ducted' => 'mdi-pipe',
                                        'vrv_vrf' => 'mdi-lan-connect',
                                        default => 'mdi-cube-outline'
                                    };
                                    $typeTitle = match($u->ac_type) {
                                        'split_wall' => 'Split Wall',
                                        'cassette' => 'Cassette',
                                        'floor_standing' => 'Floor Standing',
                                        'ceiling_ducted' => 'Ceiling Ducted',
                                        'vrv_vrf' => 'VRV / VRF',
                                        'package' => 'Packaged Unit',
                                        'chiller' => 'Chiller',
                                        default => ucwords(str_replace('_', ' ', $u->ac_type))
                                    };
                                    $searchString = strtolower($u->brand . ' ' . $u->model_name . ' ' . $u->nominal_pk . 'pk ' . $typeTitle . ' ' . $u->refrigerant);
                                @endphp
                                <tr class="ac-unit-row" data-ac-type="{{ $u->ac_type }}" data-search="{{ $searchString }}">
                                    <td>
                                        <div class="fw-bold text-dark">{{ $u->brand }}</div>
                                        <small class="text-muted">{{ $u->model_name }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $typeBadgeClass }} text-uppercase">
                                            <i class="mdi {{ $typeIcon }} me-1"></i>{{ $typeTitle }}
                                        </span>
                                    </td>
                                    <td class="text-center font-monospace fw-bold">{{ $u->nominal_pk }} PK</td>
                                    <td class="text-end font-monospace text-primary fw-bold">{{ number_format($u->cooling_capacity_btuh, 0) }}</td>
                                    <td class="text-end font-monospace">{{ number_format($u->cooling_capacity_kw, 2) }}</td>
                                    <td class="text-end font-monospace">{{ $u->power_input_watt ? number_format($u->power_input_watt, 0) . ' W' : '-' }}</td>
                                    <td class="text-center"><span class="badge bg-label-info">{{ $u->refrigerant }}</span></td>
                                    <td><small class="text-muted">{{ $u->energy_rating ?: '-' }}</small></td>
                                </tr>
                            @endforeach
                            <tr id="emptyAcFilterRow" style="display: none;">
                                <td colspan="8" class="text-center py-5">
                                    <div class="py-3">
                                        <i class="mdi mdi-air-filter text-muted fs-1 mb-2 d-block"></i>
                                        <h6 class="fw-bold mb-1">Tidak ada unit AC yang cocok</h6>
                                        <p class="text-muted small mb-3">Tidak ditemukan unit AC dengan tipe atau kriteria pencarian yang Anda pilih.</p>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnResetAcFilter">
                                            <i class="mdi mdi-refresh me-1"></i> Tampilkan Semua Unit AC
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 2. MATERIALS PANE -->
        <div class="tab-pane fade {{ $tab == 'materials' ? 'show active' : '' }}" id="matPane">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-wall text-primary me-2"></i>Koefisien Perpindahan Panas (U-Value) Material</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addMaterialModal">
                        <i class="mdi mdi-plus me-1"></i> Tambah Material
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Kategori</th>
                                <th>Nama Material</th>
                                <th class="text-center">U-Value (W/m²·K)</th>
                                <th class="text-center">Default CLTD (°C)</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materials as $m)
                                <tr>
                                    <td><span class="badge bg-label-primary text-uppercase">{{ $m->category }}</span></td>
                                    <td class="fw-semibold text-dark">{{ $m->material_name }}</td>
                                    <td class="text-center font-monospace fw-bold text-primary">{{ number_format($m->u_value, 2) }}</td>
                                    <td class="text-center font-monospace">{{ $m->default_cltd ? number_format($m->default_cltd, 1) . '°C' : '-' }}</td>
                                    <td><small class="text-muted">{{ $m->description ?: '-' }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. GLASS PANE -->
        <div class="tab-pane fade {{ $tab == 'glass' ? 'show active' : '' }}" id="glassPane">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-window-closed-variant text-info me-2"></i>Tipe Kaca &amp; SHGC</h6>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addGlassModal">
                        <i class="mdi mdi-plus me-1"></i> Tambah Kaca
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Jenis Kaca</th>
                                <th class="text-center">U-Value (W/m²·K)</th>
                                <th class="text-center">SHGC (Solar Heat Gain)</th>
                                <th class="text-center">Shading Coeff (SC)</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($glassTypes as $g)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $g->glass_name }}</td>
                                    <td class="text-center font-monospace fw-bold text-primary">{{ number_format($g->u_value, 2) }}</td>
                                    <td class="text-center font-monospace fw-bold text-success">{{ number_format($g->shgc, 3) }}</td>
                                    <td class="text-center font-monospace">{{ $g->shading_coefficient ? number_format($g->shading_coefficient, 2) : '-' }}</td>
                                    <td><small class="text-muted">{{ $g->description ?: '-' }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 4. ACTIVITIES PANE -->
        <div class="tab-pane fade {{ $tab == 'activities' ? 'show active' : '' }}" id="actPane">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-account-group text-success me-2"></i>Beban Kalor Manusia (ASHRAE Standard)</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Aktivitas Tubuh Manusia</th>
                                <th class="text-center">Sensible Heat (W/org)</th>
                                <th class="text-center">Latent Moisture (W/org)</th>
                                <th class="text-center">Total Kalor (W/org)</th>
                                <th class="text-center">Total (BTU/h per orang)</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $act)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $act->activity_name }}</td>
                                    <td class="text-center font-monospace text-danger fw-bold">{{ number_format($act->sensible_watt, 1) }} W</td>
                                    <td class="text-center font-monospace text-info fw-bold">{{ number_format($act->latent_watt, 1) }} W</td>
                                    <td class="text-center font-monospace fw-bold">{{ number_format($act->total_watt, 1) }} W</td>
                                    <td class="text-center font-monospace text-primary fw-bold">~{{ number_format(round($act->total_watt * 3.412142)) }} BTU/h</td>
                                    <td><small class="text-muted">{{ $act->description ?: '-' }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: ADD AC -->
    <div class="modal fade" id="addAcModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('hvac.master.ac.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Unit AC ke Katalog</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" class="form-control" placeholder="Daikin, Panasonic, dll" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Model Name</label>
                            <input type="text" name="model_name" class="form-control" placeholder="FTKQ25, CS-PN9, dll" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipe Unit AC</label>
                            <select name="ac_type" class="form-select">
                                <option value="split_wall">Split Wall</option>
                                <option value="cassette">Cassette 4-Way</option>
                                <option value="floor_standing">Floor Standing</option>
                                <option value="ceiling_ducted">Ceiling Ducted</option>
                                <option value="vrv_vrf">VRV / VRF</option>
                                <option value="package">Packaged Unit</option>
                                <option value="chiller">Chiller</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kapasitas Nominal (PK)</label>
                            <input type="number" step="0.25" name="nominal_pk" class="form-control" placeholder="1.0, 1.5, 2.0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cooling Capacity (BTU/h)</label>
                            <input type="number" step="100" name="cooling_capacity_btuh" class="form-control" placeholder="9000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cooling Capacity (kW)</label>
                            <input type="number" step="0.01" name="cooling_capacity_kw" class="form-control" placeholder="2.64" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Daya Listrik (Watt)</label>
                            <input type="number" step="10" name="power_input_watt" class="form-control" placeholder="680">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Refrigerant</label>
                            <input type="text" name="refrigerant" class="form-control" value="R32" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan ke Katalog</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: ADD MATERIAL -->
    <div class="modal fade" id="addMaterialModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('hvac.master.material.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Material Konstruksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kategori</label>
                            <select name="category" class="form-select">
                                <option value="wall">Dinding Luar (Wall)</option>
                                <option value="partition">Dinding Partisi (Partition)</option>
                                <option value="roof">Atap (Roof)</option>
                                <option value="ceiling">Plafon (Ceiling)</option>
                                <option value="floor">Lantai (Floor)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">U-Value (W/m²·K)</label>
                            <input type="number" step="0.01" name="u_value" class="form-control" placeholder="Contoh: 2.80" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Nama Material</label>
                            <input type="text" name="material_name" class="form-control" placeholder="Contoh: Bata Merah Plester 15cm" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Material</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: ADD GLASS -->
    <div class="modal fade" id="addGlassModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('hvac.master.glass.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Tipe Kaca</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Jenis Kaca</label>
                            <input type="text" name="glass_name" class="form-control" placeholder="Contoh: Single Clear Glass 8mm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">U-Value (W/m²·K)</label>
                            <input type="number" step="0.01" name="u_value" class="form-control" placeholder="Contoh: 5.70" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">SHGC (0.00 - 1.00)</label>
                            <input type="number" step="0.001" name="shgc" class="form-control" placeholder="Contoh: 0.820" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Tipe Kaca</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('after-script')
<script>
    $(document).ready(function() {
        var activeAcType = '{{ $selectedAcType ?? "all" }}';
        var searchQuery = '';

        function filterAcCatalog() {
            var visibleCount = 0;

            $('.ac-unit-row').each(function() {
                var rowType = $(this).data('ac-type');
                var searchData = $(this).data('search') || '';

                var matchesType = (activeAcType === 'all' || rowType === activeAcType);
                var matchesSearch = (searchQuery === '' || searchData.indexOf(searchQuery) !== -1);

                if (matchesType && matchesSearch) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            $('#visibleAcCount').text(visibleCount);

            if (visibleCount === 0) {
                $('#emptyAcFilterRow').show();
            } else {
                $('#emptyAcFilterRow').hide();
            }
        }

        // Filter Pill Buttons
        $('.btn-ac-filter').on('click', function() {
            var selectedType = $(this).data('type');
            activeAcType = selectedType;

            $('.btn-ac-filter').removeClass('active btn-outline-primary').addClass('btn-outline-secondary');
            $(this).removeClass('btn-outline-secondary').addClass('active btn-outline-primary');

            $('#selectAcTypeMobile').val(selectedType);
            filterAcCatalog();
        });

        // Mobile Dropdown Filter
        $('#selectAcTypeMobile').on('change', function() {
            var selectedType = $(this).val();
            activeAcType = selectedType;

            $('.btn-ac-filter').removeClass('active btn-outline-primary').addClass('btn-outline-secondary');
            $('.btn-ac-filter[data-type="' + selectedType + '"]').removeClass('btn-outline-secondary').addClass('active btn-outline-primary');

            filterAcCatalog();
        });

        // Search Input
        $('#searchAcInput').on('input', function() {
            searchQuery = $(this).val().toLowerCase().trim();
            if (searchQuery.length > 0) {
                $('#clearSearchAc').removeClass('d-none');
            } else {
                $('#clearSearchAc').addClass('d-none');
            }
            filterAcCatalog();
        });

        // Clear Search Button
        $('#clearSearchAc').on('click', function() {
            $('#searchAcInput').val('');
            searchQuery = '';
            $(this).addClass('d-none');
            filterAcCatalog();
        });

        // Reset Filter Button
        $('#btnResetAcFilter').on('click', function() {
            activeAcType = 'all';
            searchQuery = '';
            $('#searchAcInput').val('');
            $('#clearSearchAc').addClass('d-none');

            $('.btn-ac-filter').removeClass('active btn-outline-primary').addClass('btn-outline-secondary');
            $('.btn-ac-filter[data-type="all"]').removeClass('btn-outline-secondary').addClass('active btn-outline-primary');
            $('#selectAcTypeMobile').val('all');

            filterAcCatalog();
        });

        // Apply initial filter if URL parameter specified
        if (activeAcType !== 'all') {
            $('.btn-ac-filter[data-type="' + activeAcType + '"]').trigger('click');
        }
    });
</script>
@endpush
