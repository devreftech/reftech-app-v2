@extends('layouts.sales.app')
@section('title', 'Tambah Ruangan - ' . $project->project_name)

@section('content')
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('hvac.project.show', $project->id) }}" class="btn btn-sm btn-icon btn-light">
                    <i class="mdi mdi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0">Tambah Ruangan Baru</h4>
            </div>
            <p class="text-muted mb-0">Proyek: <strong class="text-dark">{{ $project->project_code }} - {{ $project->project_name }}</strong></p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terdapat kesalahan input:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('hvac.room.store', $project->id) }}" method="POST" id="hvacRoomForm">
        @csrf

        <!-- Mode Selector Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h6 class="fw-bold mb-1"><i class="mdi mdi-tune-vertical text-primary me-2"></i>Pilih Metode Perhitungan Cooling Load</h6>
                        <small class="text-muted">Pilih <strong>Quick Mode</strong> untuk estimasi cepat Sales, atau <strong>Detailed Mode</strong> untuk analisis teknis Engineering.</small>
                    </div>
                    <div class="btn-group shadow-sm" role="group">
                        <input type="radio" class="btn-check" name="calculation_mode" id="modeQuick" value="quick" autocomplete="off" onchange="switchMode('quick')">
                        <label class="btn btn-outline-warning px-3 py-2 fw-semibold" for="modeQuick" onclick="switchMode('quick')">
                            <i class="mdi mdi-flash me-1"></i> Quick Calculation (Sales)
                        </label>

                        <input type="radio" class="btn-check" name="calculation_mode" id="modeDetailed" value="detailed" autocomplete="off" checked onchange="switchMode('detailed')">
                        <label class="btn btn-outline-primary px-3 py-2 fw-semibold" for="modeDetailed" onclick="switchMode('detailed')">
                            <i class="mdi mdi-ruler-square me-1"></i> Detailed Engineering
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- General Room Info & Dimensions Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light py-3 border-bottom">
                <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-cube-outline text-primary me-2"></i>Dimensi &amp; Kondisi Ruangan</h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="room_name" class="form-control" placeholder="Contoh: Server Room, Meeting Room 01, Office Lt. 2" value="{{ old('room_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tipe / Fungsi Ruangan</label>
                        <input type="text" name="room_type" class="form-control" placeholder="Contoh: Kantor, IT Server, Ruang Rapat, Cleanroom" value="{{ old('room_type', 'Office') }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Panjang (P) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="length" id="room_length" class="form-control calc-dim" placeholder="Panjang" value="{{ old('length', 6.0) }}" required>
                            <span class="input-group-text">m</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Lebar (L) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="width" id="room_width" class="form-control calc-dim" placeholder="Lebar" value="{{ old('width', 4.0) }}" required>
                            <span class="input-group-text">m</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tinggi (T) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="height" id="room_height" class="form-control calc-dim" placeholder="Tinggi Plafon" value="{{ old('height', 3.0) }}" required>
                            <span class="input-group-text">m</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Luas &amp; Volume Ruangan</label>
                        <div class="form-control bg-light text-dark fw-bold">
                            <span id="preview_area">24.0</span> m² &bull; <span id="preview_volume">72.0</span> m³
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Target Suhu Dalam (°C)</label>
                        <div class="input-group">
                            <input type="number" step="0.5" name="indoor_temp" class="form-control" value="{{ old('indoor_temp', 24.0) }}" required>
                            <span class="input-group-text">°C</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Target Kelembaban (% RH)</label>
                        <div class="input-group">
                            <input type="number" step="1" name="indoor_rh" class="form-control" value="{{ old('indoor_rh', 50) }}" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Suhu Udara Luar (°C)</label>
                        <div class="input-group">
                            <input type="number" step="0.5" name="outdoor_temp" class="form-control" value="{{ old('outdoor_temp', $project->design_outdoor_temp) }}" required>
                            <span class="input-group-text">°C</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Kelembaban Udara Luar (% RH)</label>
                        <div class="input-group">
                            <input type="number" step="1" name="outdoor_rh" class="form-control" value="{{ old('outdoor_rh', $project->design_outdoor_rh) }}" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- SECTION A: QUICK MODE CONTAINER                -->
        <!-- ============================================== -->
        <div id="quickModeSection" style="display: none;">
            <div class="card shadow-sm border-0 border-top border-warning border-3 mb-4">
                <div class="card-header bg-light py-3 border-bottom">
                    <h6 class="card-title mb-0 fw-bold text-warning">
                        <i class="mdi mdi-flash me-2"></i>Parameter Estimasi Cepat (Quick Sales)
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jumlah Penghuni (Orang)</label>
                            <input type="number" name="quick_occupants_count" class="form-control" min="0" value="{{ old('quick_occupants_count', 2) }}">
                            <small class="text-muted">+500 BTU/h per orang</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kondisi Insulasi / Beban Panas Ruangan</label>
                            <select name="quick_room_condition" class="form-select">
                                <option value="light_insulated">Insulasi Bagus / Kamar Teduh (500 BTU/m²)</option>
                                <option value="standard" selected>Standar Kantor / Rumah / Ruko (600 BTU/m²)</option>
                                <option value="glass_heavy">Banyak Kaca / Lantai Teratas (750 BTU/m²)</option>
                                <option value="high_heat">Beban Tinggi / Dinding Barat Terik (900 BTU/m²)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Paparan Sinar Matahari</label>
                            <select name="quick_sun_exposure" class="form-select">
                                <option value="low">Rendah (Terlindung kanopi / gedung lain)</option>
                                <option value="medium" selected>Sedang (Jendela standar)</option>
                                <option value="high">Tinggi (Sinar matahari barat langsung / Atap panas)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- SECTION B: DETAILED ENGINEERING CONTAINER     -->
        <!-- ============================================== -->
        <div id="detailedModeSection">
            <!-- Navigation Tabs for Detailed Mode -->
            <ul class="nav nav-tabs nav-fill mb-3" id="detailedTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-semibold" id="walls-tab" data-bs-toggle="tab" data-bs-target="#wallsPane" type="button" role="tab">
                        <i class="mdi mdi-wall me-1 text-primary"></i> 1. Dinding &amp; Partisi
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" id="roofs-tab" data-bs-toggle="tab" data-bs-target="#roofsPane" type="button" role="tab">
                        <i class="mdi mdi-home-roof me-1 text-danger"></i> 2. Atap / Plafon
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" id="windows-tab" data-bs-toggle="tab" data-bs-target="#windowsPane" type="button" role="tab">
                        <i class="mdi mdi-window-closed-variant me-1 text-info"></i> 3. Kaca &amp; Jendela (Solar)
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" id="internals-tab" data-bs-toggle="tab" data-bs-target="#internalsPane" type="button" role="tab">
                        <i class="mdi mdi-account-group me-1 text-success"></i> 4. Penghuni, Lampu &amp; Beban Alat
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" id="vent-tab" data-bs-toggle="tab" data-bs-target="#ventPane" type="button" role="tab">
                        <i class="mdi mdi-weather-windy me-1 text-secondary"></i> 5. Ventilasi &amp; Infiltrasi
                    </button>
                </li>
            </ul>

            <div class="tab-content border-0 p-0">
                <!-- TAB 1: WALLS -->
                <div class="tab-pane fade show active" id="wallsPane" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-wall text-primary me-2"></i>Komponen Dinding &amp; Partisi</h6>
                                <small class="text-muted">Perhitungan perpindahan panas konduksi dinding: Q = Net Area × U × ΔT</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addWallRow()">
                                <i class="mdi mdi-plus me-1"></i> Tambah Dinding
                            </button>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="wallsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Dinding</th>
                                        <th style="width: 130px;">Arah Orientasi</th>
                                        <th style="width: 110px;">P × T (m)</th>
                                        <th style="width: 110px;">Deduksi Kaca (m²)</th>
                                        <th>Material Konstruksi</th>
                                        <th style="width: 100px;">U (W/m²K)</th>
                                        <th style="width: 90px;">ΔT (°C)</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Row Dinding 1 -->
                                    <tr>
                                        <td><input type="text" name="walls[0][wall_name]" class="form-control form-control-sm" value="Dinding Luar Utara" required></td>
                                        <td>
                                            <select name="walls[0][orientation]" class="form-select form-select-sm">
                                                <option value="N" selected>Utara (N)</option>
                                                <option value="NE">Timur Laut (NE)</option>
                                                <option value="E">Timur (E)</option>
                                                <option value="SE">Tenggara (SE)</option>
                                                <option value="S">Selatan (S)</option>
                                                <option value="SW">Barat Daya (SW)</option>
                                                <option value="W">Barat (W)</option>
                                                <option value="NW">Barat Laut (NW)</option>
                                                <option value="Internal">Internal / Partisi</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.1" name="walls[0][length]" class="form-control" value="6.0" placeholder="P">
                                                <input type="number" step="0.1" name="walls[0][height]" class="form-control" value="3.0" placeholder="T">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" step="0.1" name="walls[0][window_deduction_area]" class="form-control form-control-sm" value="0.0">
                                        </td>
                                        <td>
                                            <select name="walls[0][id_material]" class="form-select form-select-sm wall-mat-select" onchange="updateWallU(this)">
                                                @foreach($materials->whereIn('category', ['wall', 'partition']) as $m)
                                                    <option value="{{ $m->id }}" data-u="{{ $m->u_value }}" data-cltd="{{ $m->default_cltd }}" {{ $loop->first ? 'selected' : '' }}>
                                                        {{ $m->material_name }} (U={{ $m->u_value }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.01" name="walls[0][u_value]" class="form-control form-control-sm wall-u" value="2.80"></td>
                                        <td><input type="number" step="0.5" name="walls[0][temp_difference]" class="form-control form-control-sm" value="9.0"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                        </td>
                                    </tr>
                                    <!-- Row Dinding 2 -->
                                    <tr>
                                        <td><input type="text" name="walls[1][wall_name]" class="form-control form-control-sm" value="Dinding Luar Barat (Terik)" required></td>
                                        <td>
                                            <select name="walls[1][orientation]" class="form-select form-select-sm">
                                                <option value="W" selected>Barat (W)</option>
                                                <option value="N">Utara (N)</option>
                                                <option value="E">Timur (E)</option>
                                                <option value="S">Selatan (S)</option>
                                                <option value="Internal">Internal / Partisi</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.1" name="walls[1][length]" class="form-control" value="4.0" placeholder="P">
                                                <input type="number" step="0.1" name="walls[1][height]" class="form-control" value="3.0" placeholder="T">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" step="0.1" name="walls[1][window_deduction_area]" class="form-control form-control-sm" value="2.0">
                                        </td>
                                        <td>
                                            <select name="walls[1][id_material]" class="form-select form-select-sm wall-mat-select" onchange="updateWallU(this)">
                                                @foreach($materials->whereIn('category', ['wall', 'partition']) as $m)
                                                    <option value="{{ $m->id }}" data-u="{{ $m->u_value }}" data-cltd="{{ $m->default_cltd }}" {{ $loop->first ? 'selected' : '' }}>
                                                        {{ $m->material_name }} (U={{ $m->u_value }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.01" name="walls[1][u_value]" class="form-control form-control-sm wall-u" value="2.80"></td>
                                        <td><input type="number" step="0.5" name="walls[1][temp_difference]" class="form-control form-control-sm" value="12.0"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: ROOFS -->
                <div class="tab-pane fade" id="roofsPane" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-home-roof text-danger me-2"></i>Komponen Atap &amp; Plafon</h6>
                                <small class="text-muted">Perpindahan panas atap langsung atau plafon lantai teratas.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="addRoofRow()">
                                <i class="mdi mdi-plus me-1"></i> Tambah Atap / Plafon
                            </button>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="roofsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Bidang Atap</th>
                                        <th style="width: 120px;">Luas (m²)</th>
                                        <th>Material Atap / Plafon</th>
                                        <th style="width: 120px;">U (W/m²K)</th>
                                        <th style="width: 150px;">Paparan Matahari</th>
                                        <th style="width: 100px;">ΔT (°C)</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" name="roofs[0][roof_name]" class="form-control form-control-sm" value="Plafon Gypsum di bawah Atap" required></td>
                                        <td><input type="number" step="0.1" name="roofs[0][area]" class="form-control form-control-sm" value="24.0"></td>
                                        <td>
                                            <select name="roofs[0][id_material]" class="form-select form-select-sm" onchange="updateRoofU(this)">
                                                @foreach($materials->whereIn('category', ['roof', 'ceiling']) as $m)
                                                    <option value="{{ $m->id }}" data-u="{{ $m->u_value }}" {{ str_contains($m->material_name, 'Plafon') ? 'selected' : '' }}>
                                                        {{ $m->material_name }} (U={{ $m->u_value }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.01" name="roofs[0][u_value]" class="form-control form-control-sm roof-u" value="1.50"></td>
                                        <td>
                                            <select name="roofs[0][is_exposed_to_sun]" class="form-select form-select-sm">
                                                <option value="1" selected>Terpapar Langsung</option>
                                                <option value="0">Teduh / Ada Lantai Atas</option>
                                            </select>
                                        </td>
                                        <td><input type="number" step="0.5" name="roofs[0][temp_difference]" class="form-control form-control-sm" value="15.0"></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: WINDOWS -->
                <div class="tab-pane fade" id="windowsPane" role="tabpanel">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-window-closed-variant text-info me-2"></i>Komponen Jendela Kaca &amp; Radiasi Solar</h6>
                                <small class="text-muted">Menghitung konduksi panas kaca + radiasi radiasi matahari langsung (SHGC &amp; Shading factor).</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="addWindowRow()">
                                <i class="mdi mdi-plus me-1"></i> Tambah Kaca / Jendela
                            </button>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="windowsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Jendela</th>
                                        <th style="width: 140px;">Dimensi L × T (m)</th>
                                        <th style="width: 70px;">Qty</th>
                                        <th style="width: 120px;">Arah Hadap</th>
                                        <th>Jenis Kaca (U &amp; SHGC)</th>
                                        <th style="width: 130px;">Tirai / Gorden</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" name="windows[0][window_name]" class="form-control form-control-sm" value="Jendela Kaca Depan Barat" required></td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.1" name="windows[0][width]" class="form-control" value="2.0" placeholder="L">
                                                <input type="number" step="0.1" name="windows[0][height]" class="form-control" value="1.0" placeholder="T">
                                            </div>
                                        </td>
                                        <td><input type="number" name="windows[0][quantity]" class="form-control form-control-sm" value="1" min="1"></td>
                                        <td>
                                            <select name="windows[0][orientation]" class="form-select form-select-sm">
                                                <option value="W" selected>Barat (W - 550 W/m²)</option>
                                                <option value="E">Timur (E - 500 W/m²)</option>
                                                <option value="N">Utara (N - 250 W/m²)</option>
                                                <option value="S">Selatan (S - 250 W/m²)</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="windows[0][id_glass_type]" class="form-select form-select-sm" onchange="updateGlassProperties(this)">
                                                @foreach($glassTypes as $g)
                                                    <option value="{{ $g->id }}" data-u="{{ $g->u_value }}" data-shgc="{{ $g->shgc }}" {{ $loop->first ? 'selected' : '' }}>
                                                        {{ $g->glass_name }} (SHGC: {{ $g->shgc }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="windows[0][u_value]" class="glass-u" value="5.80">
                                            <input type="hidden" name="windows[0][shgc]" class="glass-shgc" value="0.820">
                                        </td>
                                        <td>
                                            <select name="windows[0][internal_shading_factor]" class="form-select form-select-sm">
                                                <option value="1.00">Tanpa Tirai (1.0)</option>
                                                <option value="0.70" selected>Tirai Gorden Terang (0.7)</option>
                                                <option value="0.55">Venetian Blinds (0.55)</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: INTERNAL LOADS (OCCUPANTS, LIGHTING, EQUIPMENT) -->
                <div class="tab-pane fade" id="internalsPane" role="tabpanel">
                    <div class="row g-4">
                        <!-- Penghuni -->
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-bold text-success"><i class="mdi mdi-account-multiple me-1"></i>Penghuni (Occupants Sensible &amp; Latent)</h6>
                                    <button type="button" class="btn btn-xs btn-outline-success" onclick="addOccupantRow()"><i class="mdi mdi-plus"></i> Tambah</button>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-bordered align-middle mb-0" id="occupantsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Aktivitas Penghuni</th>
                                                <th style="width: 100px;">Jumlah Orang</th>
                                                <th style="width: 120px;">Sensible (W/org)</th>
                                                <th style="width: 120px;">Latent (W/org)</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <select name="occupants[0][id_activity]" class="form-select form-select-sm" onchange="updateActivityLoad(this)">
                                                        @foreach($activities as $act)
                                                            <option value="{{ $act->id }}" data-sen="{{ $act->sensible_watt }}" data-lat="{{ $act->latent_watt }}" {{ str_contains($act->activity_name, 'Kantor') ? 'selected' : '' }}>
                                                                {{ $act->activity_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" name="occupants[0][quantity]" class="form-control form-control-sm" value="4" min="1"></td>
                                                <td><input type="number" step="0.1" name="occupants[0][sensible_watt_per_person]" class="form-control form-control-sm occ-sen" value="75.0"></td>
                                                <td><input type="number" step="0.1" name="occupants[0][latent_watt_per_person]" class="form-control form-control-sm occ-lat" value="55.0"></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Lampu & Elektronik -->
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-bold text-warning"><i class="mdi mdi-lightbulb-on-outline me-1"></i>Penerangan (Lighting)</h6>
                                    <button type="button" class="btn btn-xs btn-outline-warning" onclick="addLightingRow()"><i class="mdi mdi-plus"></i> Tambah</button>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-bordered align-middle mb-0" id="lightingsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Lampu</th>
                                                <th style="width: 70px;">Qty</th>
                                                <th style="width: 90px;">Watt/unit</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="text" name="lightings[0][lighting_name]" class="form-control form-control-sm" value="LED Troffer Panel 60x60"></td>
                                                <td><input type="number" name="lightings[0][quantity]" class="form-control form-control-sm" value="4" min="1"></td>
                                                <td><input type="number" step="1" name="lightings[0][watt_per_unit]" class="form-control form-control-sm" value="36"></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-bold text-primary"><i class="mdi mdi-laptop me-1"></i>Peralatan &amp; Mesin (Equipment)</h6>
                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="addEquipmentRow()"><i class="mdi mdi-plus"></i> Tambah</button>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-bordered align-middle mb-0" id="equipmentsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Nama Alat</th>
                                                <th style="width: 70px;">Qty</th>
                                                <th style="width: 90px;">Watt/unit</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><input type="text" name="equipments[0][equipment_name]" class="form-control form-control-sm" value="PC Desktop + Monitor"></td>
                                                <td><input type="number" name="equipments[0][quantity]" class="form-control form-control-sm" value="4" min="1"></td>
                                                <td><input type="number" step="1" name="equipments[0][watt_per_unit]" class="form-control form-control-sm" value="150"></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: VENTILATION & INFILTRATION -->
                <div class="tab-pane fade" id="ventPane" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-light py-3 border-bottom">
                                    <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-weather-windy text-info me-2"></i>Ventilasi Udara Segar (Fresh Air)</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Metode Perhitungan Fresh Air</label>
                                        <select name="ventilations[0][method]" class="form-select">
                                            <option value="per_person" selected>Berdasarkan Orang (Standar 10 L/s/orang)</option>
                                            <option value="ach">Air Change per Hour (ACH)</option>
                                            <option value="direct_airflow">Debit Udara Langsung (L/s atau CFM)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nilai Input Debit</label>
                                        <div class="input-group">
                                            <input type="number" step="0.5" name="ventilations[0][input_value]" class="form-control" value="10.0">
                                            <span class="input-group-text">L/s/org atau ACH</span>
                                        </div>
                                        <small class="text-muted">Beban Sensible &amp; Latent udara luar dihitung via psikrometrik sifat udara.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-light py-3 border-bottom">
                                    <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-door-open text-warning me-2"></i>Infiltrasi Udara (Air Leakage)</h6>
                                </div>
                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tingkat Kebocoran Udara (ACH)</label>
                                        <select name="infiltrations[0][ach_value]" class="form-select">
                                            <option value="0.25">Sangat Rapat / Kamar Tanpa Bukaan (0.25 ACH)</option>
                                            <option value="0.50" selected>Normal / Pintu Jarang Dibuka (0.50 ACH)</option>
                                            <option value="1.00">Pintu Keluar Masuk Sering (1.00 ACH)</option>
                                            <option value="1.50">Kios / Toko Terbuka (1.50 ACH)</option>
                                        </select>
                                    </div>
                                    <p class="small text-muted mb-0">Kebocoran udara dari celah kusen jendela dan frekuensi pembukaan pintu luar.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Safety Factor & Action Card -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4">
                <div class="row align-items-center justify-content-between g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Faktor Keamanan (Safety Factor) <span class="badge bg-light-primary text-primary ms-2" id="safetyBadge">10%</span>
                        </label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range" min="0" max="30" step="5" id="safetySlider" value="{{ old('safety_factor_percent', 10) }}" oninput="updateSafety(this.value)">
                            <input type="number" name="safety_factor_percent" id="safetyInput" class="form-control form-control-sm text-center" style="width: 80px;" value="{{ old('safety_factor_percent', 10) }}" min="0" max="50">
                            <span class="fw-bold">%</span>
                        </div>
                        <small class="text-muted">Design Load = Total Cooling Load × (1 + Safety Factor %)</small>
                    </div>

                    <div class="col-md-6 text-end">
                        <a href="{{ route('hvac.project.show', $project->id) }}" class="btn btn-outline-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="mdi mdi-calculator-variant me-1"></i> Hitung Cooling Load &amp; Rekomendasi AC
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('after-script')
    <script>
        // Dimension preview updater
        document.querySelectorAll('.calc-dim').forEach(input => {
            input.addEventListener('input', updateDimensionPreviews);
        });

        function updateDimensionPreviews() {
            const l = parseFloat(document.getElementById('room_length')?.value) || 0;
            const w = parseFloat(document.getElementById('room_width')?.value) || 0;
            const h = parseFloat(document.getElementById('room_height')?.value) || 0;

            const area = (l * w).toFixed(1);
            const vol = (l * w * h).toFixed(1);

            if (document.getElementById('preview_area')) document.getElementById('preview_area').innerText = area;
            if (document.getElementById('preview_volume')) document.getElementById('preview_volume').innerText = vol;
        }

        // Mode switcher
        function switchMode(mode) {
            const quickSec = document.getElementById('quickModeSection');
            const detailedSec = document.getElementById('detailedModeSection');
            const quickRadio = document.getElementById('modeQuick');
            const detailedRadio = document.getElementById('modeDetailed');

            if (mode === 'quick') {
                if (quickRadio) quickRadio.checked = true;
                if (quickSec) quickSec.style.display = 'block';
                if (detailedSec) {
                    detailedSec.style.display = 'none';
                    // Temporarily remove required from hidden detailed fields
                    detailedSec.querySelectorAll('input[required], select[required]').forEach(el => {
                        el.dataset.wasRequired = 'true';
                        el.removeAttribute('required');
                    });
                }
            } else {
                if (detailedRadio) detailedRadio.checked = true;
                if (quickSec) quickSec.style.display = 'none';
                if (detailedSec) {
                    detailedSec.style.display = 'block';
                    // Restore required on detailed fields
                    detailedSec.querySelectorAll('[data-was-required="true"]').forEach(el => {
                        el.setAttribute('required', 'required');
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('input[name="calculation_mode"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    switchMode(this.value);
                });
            });

            // Initial check
            const checked = document.querySelector('input[name="calculation_mode"]:checked');
            switchMode(checked ? checked.value : 'detailed');
            updateDimensionPreviews();
        });

        // Safety factor sync
        function updateSafety(val) {
            document.getElementById('safetyInput').value = val;
            document.getElementById('safetyBadge').innerText = val + '%';
        }
        document.getElementById('safetyInput')?.addEventListener('input', function() {
            document.getElementById('safetySlider').value = this.value;
            document.getElementById('safetyBadge').innerText = this.value + '%';
        });

        // Dynamic Table Row Adders
        let wallIdx = 2;
        function addWallRow() {
            const tbody = document.querySelector('#wallsTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="walls[${wallIdx}][wall_name]" class="form-control form-control-sm" value="Dinding Luar" required></td>
                <td>
                    <select name="walls[${wallIdx}][orientation]" class="form-select form-select-sm">
                        <option value="N">Utara (N)</option>
                        <option value="E">Timur (E)</option>
                        <option value="S">Selatan (S)</option>
                        <option value="W">Barat (W)</option>
                        <option value="Internal">Internal / Partisi</option>
                    </select>
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.1" name="walls[${wallIdx}][length]" class="form-control" value="5.0" placeholder="P">
                        <input type="number" step="0.1" name="walls[${wallIdx}][height]" class="form-control" value="3.0" placeholder="T">
                    </div>
                </td>
                <td><input type="number" step="0.1" name="walls[${wallIdx}][window_deduction_area]" class="form-control form-control-sm" value="0.0"></td>
                <td>
                    <select name="walls[${wallIdx}][id_material]" class="form-select form-select-sm wall-mat-select" onchange="updateWallU(this)">
                        @foreach($materials->whereIn('category', ['wall', 'partition']) as $m)
                            <option value="{{ $m->id }}" data-u="{{ $m->u_value }}" data-cltd="{{ $m->default_cltd }}">{{ $m->material_name }} (U={{ $m->u_value }})</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" step="0.01" name="walls[${wallIdx}][u_value]" class="form-control form-control-sm wall-u" value="2.80"></td>
                <td><input type="number" step="0.5" name="walls[${wallIdx}][temp_difference]" class="form-control form-control-sm" value="9.0"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            `;
            tbody.appendChild(row);
            wallIdx++;
        }

        let roofIdx = 1;
        function addRoofRow() {
            const tbody = document.querySelector('#roofsTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="roofs[${roofIdx}][roof_name]" class="form-control form-control-sm" value="Atap Ruangan" required></td>
                <td><input type="number" step="0.1" name="roofs[${roofIdx}][area]" class="form-control form-control-sm" value="20.0"></td>
                <td>
                    <select name="roofs[${roofIdx}][id_material]" class="form-select form-select-sm" onchange="updateRoofU(this)">
                        @foreach($materials->whereIn('category', ['roof', 'ceiling']) as $m)
                            <option value="{{ $m->id }}" data-u="{{ $m->u_value }}">{{ $m->material_name }} (U={{ $m->u_value }})</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" step="0.01" name="roofs[${roofIdx}][u_value]" class="form-control form-control-sm roof-u" value="1.50"></td>
                <td>
                    <select name="roofs[${roofIdx}][is_exposed_to_sun]" class="form-select form-select-sm">
                        <option value="1">Terpapar Langsung</option>
                        <option value="0">Teduh</option>
                    </select>
                </td>
                <td><input type="number" step="0.5" name="roofs[${roofIdx}][temp_difference]" class="form-control form-control-sm" value="15.0"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            `;
            tbody.appendChild(row);
            roofIdx++;
        }

        let winIdx = 1;
        function addWindowRow() {
            const tbody = document.querySelector('#windowsTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="windows[${winIdx}][window_name]" class="form-control form-control-sm" value="Jendela Kaca" required></td>
                <td>
                    <div class="input-group input-group-sm">
                        <input type="number" step="0.1" name="windows[${winIdx}][width]" class="form-control" value="1.5" placeholder="L">
                        <input type="number" step="0.1" name="windows[${winIdx}][height]" class="form-control" value="1.2" placeholder="T">
                    </div>
                </td>
                <td><input type="number" name="windows[${winIdx}][quantity]" class="form-control form-control-sm" value="1" min="1"></td>
                <td>
                    <select name="windows[${winIdx}][orientation]" class="form-select form-select-sm">
                        <option value="E">Timur (E)</option>
                        <option value="W">Barat (W)</option>
                        <option value="N">Utara (N)</option>
                        <option value="S">Selatan (S)</option>
                    </select>
                </td>
                <td>
                    <select name="windows[${winIdx}][id_glass_type]" class="form-select form-select-sm" onchange="updateGlassProperties(this)">
                        @foreach($glassTypes as $g)
                            <option value="{{ $g->id }}" data-u="{{ $g->u_value }}" data-shgc="{{ $g->shgc }}">{{ $g->glass_name }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="windows[${winIdx}][u_value]" class="glass-u" value="5.80">
                    <input type="hidden" name="windows[${winIdx}][shgc]" class="glass-shgc" value="0.820">
                </td>
                <td>
                    <select name="windows[${winIdx}][internal_shading_factor]" class="form-select form-select-sm">
                        <option value="1.00">Tanpa Tirai (1.0)</option>
                        <option value="0.70" selected>Tirai Gorden (0.7)</option>
                    </select>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            `;
            tbody.appendChild(row);
            winIdx++;
        }

        let occIdx = 1;
        function addOccupantRow() {
            const tbody = document.querySelector('#occupantsTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select name="occupants[${occIdx}][id_activity]" class="form-select form-select-sm" onchange="updateActivityLoad(this)">
                        @foreach($activities as $act)
                            <option value="{{ $act->id }}" data-sen="{{ $act->sensible_watt }}" data-lat="{{ $act->latent_watt }}">{{ $act->activity_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="occupants[${occIdx}][quantity]" class="form-control form-control-sm" value="2" min="1"></td>
                <td><input type="number" step="0.1" name="occupants[${occIdx}][sensible_watt_per_person]" class="form-control form-control-sm occ-sen" value="75.0"></td>
                <td><input type="number" step="0.1" name="occupants[${occIdx}][latent_watt_per_person]" class="form-control form-control-sm occ-lat" value="55.0"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            `;
            tbody.appendChild(row);
            occIdx++;
        }

        let lightIdx = 1;
        function addLightingRow() {
            const tbody = document.querySelector('#lightingsTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="lightings[${lightIdx}][lighting_name]" class="form-control form-control-sm" value="Lampu Downlight LED"></td>
                <td><input type="number" name="lightings[${lightIdx}][quantity]" class="form-control form-control-sm" value="4" min="1"></td>
                <td><input type="number" step="1" name="lightings[${lightIdx}][watt_per_unit]" class="form-control form-control-sm" value="18"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            `;
            tbody.appendChild(row);
            lightIdx++;
        }

        let equipIdx = 1;
        function addEquipmentRow() {
            const tbody = document.querySelector('#equipmentsTable tbody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><input type="text" name="equipments[${equipIdx}][equipment_name]" class="form-control form-control-sm" value="Printer Laser / Mesin Fotokopi"></td>
                <td><input type="number" name="equipments[${equipIdx}][quantity]" class="form-control form-control-sm" value="1" min="1"></td>
                <td><input type="number" step="1" name="equipments[${equipIdx}][watt_per_unit]" class="form-control form-control-sm" value="300"></td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                </td>
            `;
            tbody.appendChild(row);
            equipIdx++;
        }

        function removeRow(btn) {
            const tr = btn.closest('tr');
            if (tr.parentElement.children.length > 1) {
                tr.remove();
            } else {
                alert('Minimal harus ada 1 baris input.');
            }
        }

        function updateWallU(select) {
            const opt = select.options[select.selectedIndex];
            const tr = select.closest('tr');
            if (opt && tr) {
                tr.querySelector('.wall-u').value = opt.dataset.u || 2.80;
            }
        }

        function updateRoofU(select) {
            const opt = select.options[select.selectedIndex];
            const tr = select.closest('tr');
            if (opt && tr) {
                tr.querySelector('.roof-u').value = opt.dataset.u || 1.50;
            }
        }

        function updateGlassProperties(select) {
            const opt = select.options[select.selectedIndex];
            const tr = select.closest('tr');
            if (opt && tr) {
                tr.querySelector('.glass-u').value = opt.dataset.u || 5.80;
                tr.querySelector('.glass-shgc').value = opt.dataset.shgc || 0.820;
            }
        }

        function updateActivityLoad(select) {
            const opt = select.options[select.selectedIndex];
            const tr = select.closest('tr');
            if (opt && tr) {
                tr.querySelector('.occ-sen').value = opt.dataset.sen || 75.0;
                tr.querySelector('.occ-lat').value = opt.dataset.lat || 55.0;
            }
        }
    </script>
    @endpush
@endsection
