@extends('layouts.sales.app')
@section('title', 'Edit Ruangan - ' . $room->room_name)

@section('content')
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('hvac.room.result', $room->id) }}" class="btn btn-sm btn-icon btn-light">
                    <i class="mdi mdi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0">Edit Ruangan: {{ $room->room_name }}</h4>
            </div>
            <p class="text-muted mb-0">Proyek: <strong class="text-dark">{{ $room->project->project_code }} - {{ $room->project->project_name }}</strong></p>
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

    <form action="{{ route('hvac.room.update', $room->id) }}" method="POST" id="hvacRoomForm">
        @csrf
        @method('PUT')

        <!-- Mode Selector Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h6 class="fw-bold mb-1"><i class="mdi mdi-tune-vertical text-primary me-2"></i>Metode Perhitungan Cooling Load</h6>
                        <small class="text-muted">Pilih <strong>Quick Mode</strong> untuk estimasi cepat Sales, atau <strong>Detailed Mode</strong> untuk analisis teknis Engineering.</small>
                    </div>
                    <div class="btn-group shadow-sm" role="group">
                        <input type="radio" class="btn-check" name="calculation_mode" id="modeQuick" value="quick" autocomplete="off" {{ $room->calculation_mode == 'quick' ? 'checked' : '' }} onchange="switchMode('quick')">
                        <label class="btn btn-outline-warning px-3 py-2 fw-semibold" for="modeQuick" onclick="switchMode('quick')">
                            <i class="mdi mdi-flash me-1"></i> Quick Calculation (Sales)
                        </label>

                        <input type="radio" class="btn-check" name="calculation_mode" id="modeDetailed" value="detailed" autocomplete="off" {{ $room->calculation_mode == 'detailed' ? 'checked' : '' }} onchange="switchMode('detailed')">
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
                        <input type="text" name="room_name" class="form-control" value="{{ old('room_name', $room->room_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tipe / Fungsi Ruangan</label>
                        <input type="text" name="room_type" class="form-control" value="{{ old('room_type', $room->room_type) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Panjang (P) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="length" id="room_length" class="form-control calc-dim" value="{{ old('length', $room->length) }}" required>
                            <span class="input-group-text">m</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Lebar (L) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="width" id="room_width" class="form-control calc-dim" value="{{ old('width', $room->width) }}" required>
                            <span class="input-group-text">m</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Tinggi (T) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.1" name="height" id="room_height" class="form-control calc-dim" value="{{ old('height', $room->height) }}" required>
                            <span class="input-group-text">m</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Luas &amp; Volume Ruangan</label>
                        <div class="form-control bg-light text-dark fw-bold">
                            <span id="preview_area">{{ $room->floor_area }}</span> m² &bull; <span id="preview_volume">{{ $room->room_volume }}</span> m³
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Target Suhu Dalam (°C)</label>
                        <div class="input-group">
                            <input type="number" step="0.5" name="indoor_temp" class="form-control" value="{{ old('indoor_temp', $room->indoor_temp) }}" required>
                            <span class="input-group-text">°C</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Target Kelembaban (% RH)</label>
                        <div class="input-group">
                            <input type="number" step="1" name="indoor_rh" class="form-control" value="{{ old('indoor_rh', $room->indoor_rh) }}" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Suhu Udara Luar (°C)</label>
                        <div class="input-group">
                            <input type="number" step="0.5" name="outdoor_temp" class="form-control" value="{{ old('outdoor_temp', $room->outdoor_temp) }}" required>
                            <span class="input-group-text">°C</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Kelembaban Udara Luar (% RH)</label>
                        <div class="input-group">
                            <input type="number" step="1" name="outdoor_rh" class="form-control" value="{{ old('outdoor_rh', $room->outdoor_rh) }}" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QUICK MODE SECTION -->
        <div id="quickModeSection" style="{{ $room->calculation_mode == 'quick' ? 'display: block;' : 'display: none;' }}">
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
                            <input type="number" name="quick_occupants_count" class="form-control" min="0" value="{{ old('quick_occupants_count', $room->quick_occupants_count) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kondisi Insulasi / Beban Panas Ruangan</label>
                            <select name="quick_room_condition" class="form-select">
                                <option value="light_insulated" {{ $room->quick_room_condition == 'light_insulated' ? 'selected' : '' }}>Insulasi Bagus / Kamar Teduh (500 BTU/m²)</option>
                                <option value="standard" {{ $room->quick_room_condition == 'standard' ? 'selected' : '' }}>Standar Kantor / Rumah / Ruko (600 BTU/m²)</option>
                                <option value="glass_heavy" {{ $room->quick_room_condition == 'glass_heavy' ? 'selected' : '' }}>Banyak Kaca / Lantai Teratas (750 BTU/m²)</option>
                                <option value="high_heat" {{ $room->quick_room_condition == 'high_heat' ? 'selected' : '' }}>Beban Tinggi / Dinding Barat Terik (900 BTU/m²)</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Paparan Sinar Matahari</label>
                            <select name="quick_sun_exposure" class="form-select">
                                <option value="low" {{ $room->quick_sun_exposure == 'low' ? 'selected' : '' }}>Rendah</option>
                                <option value="medium" {{ $room->quick_sun_exposure == 'medium' ? 'selected' : '' }}>Sedang</option>
                                <option value="high" {{ $room->quick_sun_exposure == 'high' ? 'selected' : '' }}>Tinggi (Barat langsung)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAILED MODE SECTION -->
        <div id="detailedModeSection" style="{{ $room->calculation_mode == 'detailed' ? 'display: block;' : 'display: none;' }}">
            <ul class="nav nav-tabs nav-fill mb-3" id="detailedTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#wallsPane" type="button"><i class="mdi mdi-wall me-1 text-primary"></i> 1. Dinding &amp; Partisi</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#roofsPane" type="button"><i class="mdi mdi-home-roof me-1 text-danger"></i> 2. Atap / Plafon</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#windowsPane" type="button"><i class="mdi mdi-window-closed-variant me-1 text-info"></i> 3. Kaca &amp; Jendela</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#internalsPane" type="button"><i class="mdi mdi-account-group me-1 text-success"></i> 4. Internal Loads</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#ventPane" type="button"><i class="mdi mdi-weather-windy me-1 text-secondary"></i> 5. Ventilasi &amp; Infiltrasi</button>
                </li>
            </ul>

            <div class="tab-content border-0 p-0">
                <!-- WALLS -->
                <div class="tab-pane fade show active" id="wallsPane">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-wall text-primary me-2"></i>Dinding &amp; Partisi</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addWallRow()"><i class="mdi mdi-plus"></i> Tambah Dinding</button>
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
                                    @forelse($room->walls as $idx => $w)
                                        <tr>
                                            <td><input type="text" name="walls[{{ $idx }}][wall_name]" class="form-control form-control-sm" value="{{ $w->wall_name }}" required></td>
                                            <td>
                                                <select name="walls[{{ $idx }}][orientation]" class="form-select form-select-sm">
                                                    @foreach(['N' => 'Utara (N)', 'NE' => 'Timur Laut (NE)', 'E' => 'Timur (E)', 'SE' => 'Tenggara (SE)', 'S' => 'Selatan (S)', 'SW' => 'Barat Daya (SW)', 'W' => 'Barat (W)', 'NW' => 'Barat Laut (NW)', 'Internal' => 'Internal / Partisi'] as $val => $label)
                                                        <option value="{{ $val }}" {{ $w->orientation == $val ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.1" name="walls[{{ $idx }}][length]" class="form-control" value="{{ $w->length }}">
                                                    <input type="number" step="0.1" name="walls[{{ $idx }}][height]" class="form-control" value="{{ $w->height }}">
                                                </div>
                                            </td>
                                            <td><input type="number" step="0.1" name="walls[{{ $idx }}][window_deduction_area]" class="form-control form-control-sm" value="{{ $w->window_deduction_area }}"></td>
                                            <td>
                                                <select name="walls[{{ $idx }}][id_material]" class="form-select form-select-sm" onchange="updateWallU(this)">
                                                    @foreach($materials->whereIn('category', ['wall', 'partition']) as $m)
                                                        <option value="{{ $m->id }}" data-u="{{ $m->u_value }}" {{ $w->id_material == $m->id ? 'selected' : '' }}>
                                                            {{ $m->material_name }} (U={{ $m->u_value }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" step="0.01" name="walls[{{ $idx }}][u_value]" class="form-control form-control-sm wall-u" value="{{ $w->u_value }}"></td>
                                            <td><input type="number" step="0.5" name="walls[{{ $idx }}][temp_difference]" class="form-control form-control-sm" value="{{ $w->temp_difference }}"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td><input type="text" name="walls[0][wall_name]" class="form-control form-control-sm" value="Dinding Luar" required></td>
                                            <td><select name="walls[0][orientation]" class="form-select form-select-sm"><option value="N">Utara (N)</option></select></td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.1" name="walls[0][length]" class="form-control" value="6.0">
                                                    <input type="number" step="0.1" name="walls[0][height]" class="form-control" value="3.0">
                                                </div>
                                            </td>
                                            <td><input type="number" step="0.1" name="walls[0][window_deduction_area]" class="form-control form-control-sm" value="0.0"></td>
                                            <td>
                                                <select name="walls[0][id_material]" class="form-select form-select-sm" onchange="updateWallU(this)">
                                                    @foreach($materials->whereIn('category', ['wall', 'partition']) as $m)
                                                        <option value="{{ $m->id }}" data-u="{{ $m->u_value }}">{{ $m->material_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" step="0.01" name="walls[0][u_value]" class="form-control form-control-sm wall-u" value="2.80"></td>
                                            <td><input type="number" step="0.5" name="walls[0][temp_difference]" class="form-control form-control-sm" value="9.0"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ROOFS -->
                <div class="tab-pane fade" id="roofsPane">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-home-roof text-danger me-2"></i>Atap &amp; Plafon</h6>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="addRoofRow()"><i class="mdi mdi-plus"></i> Tambah Atap</button>
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
                                    @forelse($room->roofs as $idx => $r)
                                        <tr>
                                            <td><input type="text" name="roofs[{{ $idx }}][roof_name]" class="form-control form-control-sm" value="{{ $r->roof_name }}" required></td>
                                            <td><input type="number" step="0.1" name="roofs[{{ $idx }}][area]" class="form-control form-control-sm" value="{{ $r->area }}"></td>
                                            <td>
                                                <select name="roofs[{{ $idx }}][id_material]" class="form-select form-select-sm" onchange="updateRoofU(this)">
                                                    @foreach($materials->whereIn('category', ['roof', 'ceiling']) as $m)
                                                        <option value="{{ $m->id }}" data-u="{{ $m->u_value }}" {{ $r->id_material == $m->id ? 'selected' : '' }}>{{ $m->material_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" step="0.01" name="roofs[{{ $idx }}][u_value]" class="form-control form-control-sm roof-u" value="{{ $r->u_value }}"></td>
                                            <td>
                                                <select name="roofs[{{ $idx }}][is_exposed_to_sun]" class="form-select form-select-sm">
                                                    <option value="1" {{ $r->is_exposed_to_sun ? 'selected' : '' }}>Terpapar Langsung</option>
                                                    <option value="0" {{ !$r->is_exposed_to_sun ? 'selected' : '' }}>Teduh</option>
                                                </select>
                                            </td>
                                            <td><input type="number" step="0.5" name="roofs[{{ $idx }}][temp_difference]" class="form-control form-control-sm" value="{{ $r->temp_difference }}"></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td><input type="text" name="roofs[0][roof_name]" class="form-control form-control-sm" value="Plafon" required></td>
                                            <td><input type="number" step="0.1" name="roofs[0][area]" class="form-control form-control-sm" value="{{ $room->floor_area }}"></td>
                                            <td>
                                                <select name="roofs[0][id_material]" class="form-select form-select-sm" onchange="updateRoofU(this)">
                                                    @foreach($materials->whereIn('category', ['roof', 'ceiling']) as $m)
                                                        <option value="{{ $m->id }}" data-u="{{ $m->u_value }}">{{ $m->material_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" step="0.01" name="roofs[0][u_value]" class="form-control form-control-sm roof-u" value="1.50"></td>
                                            <td><select name="roofs[0][is_exposed_to_sun]" class="form-select form-select-sm"><option value="1">Terpapar Langsung</option></select></td>
                                            <td><input type="number" step="0.5" name="roofs[0][temp_difference]" class="form-control form-control-sm" value="15.0"></td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- WINDOWS -->
                <div class="tab-pane fade" id="windowsPane">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-window-closed-variant text-info me-2"></i>Kaca &amp; Jendela (Solar Heat Gain)</h6>
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="addWindowRow()"><i class="mdi mdi-plus"></i> Tambah Jendela</button>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-bordered align-middle mb-0" id="windowsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Jendela</th>
                                        <th style="width: 140px;">Dimensi L × T (m)</th>
                                        <th style="width: 70px;">Qty</th>
                                        <th style="width: 120px;">Arah Hadap</th>
                                        <th>Jenis Kaca</th>
                                        <th style="width: 130px;">Tirai</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($room->windows as $idx => $win)
                                        <tr>
                                            <td><input type="text" name="windows[{{ $idx }}][window_name]" class="form-control form-control-sm" value="{{ $win->window_name }}" required></td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" step="0.1" name="windows[{{ $idx }}][width]" class="form-control" value="{{ $win->width }}">
                                                    <input type="number" step="0.1" name="windows[{{ $idx }}][height]" class="form-control" value="{{ $win->height }}">
                                                </div>
                                            </td>
                                            <td><input type="number" name="windows[{{ $idx }}][quantity]" class="form-control form-control-sm" value="{{ $win->quantity }}"></td>
                                            <td>
                                                <select name="windows[{{ $idx }}][orientation]" class="form-select form-select-sm">
                                                    @foreach(['N' => 'Utara (N)', 'E' => 'Timur (E)', 'S' => 'Selatan (S)', 'W' => 'Barat (W)'] as $val => $label)
                                                        <option value="{{ $val }}" {{ $win->orientation == $val ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="windows[{{ $idx }}][id_glass_type]" class="form-select form-select-sm" onchange="updateGlassProperties(this)">
                                                    @foreach($glassTypes as $g)
                                                        <option value="{{ $g->id }}" data-u="{{ $g->u_value }}" data-shgc="{{ $g->shgc }}" {{ $win->id_glass_type == $g->id ? 'selected' : '' }}>{{ $g->glass_name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="windows[{{ $idx }}][u_value]" class="glass-u" value="{{ $win->u_value }}">
                                                <input type="hidden" name="windows[{{ $idx }}][shgc]" class="glass-shgc" value="{{ $win->shgc }}">
                                            </td>
                                            <td>
                                                <select name="windows[{{ $idx }}][internal_shading_factor]" class="form-select form-select-sm">
                                                    <option value="1.00" {{ $win->internal_shading_factor == 1.00 ? 'selected' : '' }}>Tanpa Tirai (1.0)</option>
                                                    <option value="0.70" {{ $win->internal_shading_factor == 0.70 ? 'selected' : '' }}>Tirai Gorden (0.7)</option>
                                                    <option value="0.55" {{ $win->internal_shading_factor == 0.55 ? 'selected' : '' }}>Venetian Blinds (0.55)</option>
                                                </select>
                                            </td>
                                            <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button></td>
                                        </tr>
                                    @empty
                                        <!-- fallback 1 row -->
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- INTERNAL LOADS -->
                <div class="tab-pane fade" id="internalsPane">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-bold text-success"><i class="mdi mdi-account-multiple me-1"></i>Penghuni</h6>
                                    <button type="button" class="btn btn-xs btn-outline-success" onclick="addOccupantRow()"><i class="mdi mdi-plus"></i> Tambah</button>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-bordered align-middle mb-0" id="occupantsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Aktivitas</th>
                                                <th style="width: 100px;">Jumlah</th>
                                                <th style="width: 120px;">Sensible W</th>
                                                <th style="width: 120px;">Latent W</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($room->occupants as $idx => $occ)
                                                <tr>
                                                    <td>
                                                        <select name="occupants[{{ $idx }}][id_activity]" class="form-select form-select-sm" onchange="updateActivityLoad(this)">
                                                            @foreach($activities as $act)
                                                                <option value="{{ $act->id }}" data-sen="{{ $act->sensible_watt }}" data-lat="{{ $act->latent_watt }}" {{ $occ->id_activity == $act->id ? 'selected' : '' }}>{{ $act->activity_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="occupants[{{ $idx }}][quantity]" class="form-control form-control-sm" value="{{ $occ->quantity }}"></td>
                                                    <td><input type="number" step="0.1" name="occupants[{{ $idx }}][sensible_watt_per_person]" class="form-control form-control-sm occ-sen" value="{{ $occ->sensible_watt_per_person }}"></td>
                                                    <td><input type="number" step="0.1" name="occupants[{{ $idx }}][latent_watt_per_person]" class="form-control form-control-sm occ-lat" value="{{ $occ->latent_watt_per_person }}"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-bold text-warning"><i class="mdi mdi-lightbulb-on-outline me-1"></i>Penerangan</h6>
                                    <button type="button" class="btn btn-xs btn-outline-warning" onclick="addLightingRow()"><i class="mdi mdi-plus"></i> Tambah</button>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-bordered align-middle mb-0" id="lightingsTable">
                                        <thead class="table-light">
                                            <tr><th>Nama Lampu</th><th style="width: 70px;">Qty</th><th style="width: 90px;">Watt/unit</th><th style="width: 50px;"></th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach($room->lightings as $idx => $l)
                                                <tr>
                                                    <td><input type="text" name="lightings[{{ $idx }}][lighting_name]" class="form-control form-control-sm" value="{{ $l->lighting_name }}"></td>
                                                    <td><input type="number" name="lightings[{{ $idx }}][quantity]" class="form-control form-control-sm" value="{{ $l->quantity }}"></td>
                                                    <td><input type="number" step="1" name="lightings[{{ $idx }}][watt_per_unit]" class="form-control form-control-sm" value="{{ $l->watt_per_unit }}"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-light py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0 fw-bold text-primary"><i class="mdi mdi-laptop me-1"></i>Peralatan &amp; Mesin</h6>
                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="addEquipmentRow()"><i class="mdi mdi-plus"></i> Tambah</button>
                                </div>
                                <div class="card-body p-0 table-responsive">
                                    <table class="table table-bordered align-middle mb-0" id="equipmentsTable">
                                        <thead class="table-light">
                                            <tr><th>Nama Alat</th><th style="width: 70px;">Qty</th><th style="width: 90px;">Watt/unit</th><th style="width: 50px;"></th></tr>
                                        </thead>
                                        <tbody>
                                            @foreach($room->equipments as $idx => $eq)
                                                <tr>
                                                    <td><input type="text" name="equipments[{{ $idx }}][equipment_name]" class="form-control form-control-sm" value="{{ $eq->equipment_name }}"></td>
                                                    <td><input type="number" name="equipments[{{ $idx }}][quantity]" class="form-control form-control-sm" value="{{ $eq->quantity }}"></td>
                                                    <td><input type="number" step="1" name="equipments[{{ $idx }}][watt_per_unit]" class="form-control form-control-sm" value="{{ $eq->watt_per_unit }}"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-light text-danger" onclick="removeRow(this)"><i class="mdi mdi-trash-can-outline"></i></button></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VENTILATION & INFILTRATION -->
                <div class="tab-pane fade" id="ventPane">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-light py-3 border-bottom">
                                    <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-weather-windy text-info me-2"></i>Ventilasi Udara Segar (Fresh Air)</h6>
                                </div>
                                <div class="card-body p-4">
                                    @php $v = $room->ventilations->first(); @endphp
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Metode Perhitungan</label>
                                        <select name="ventilations[0][method]" class="form-select">
                                            <option value="per_person" {{ ($v?->method ?? '') == 'per_person' ? 'selected' : '' }}>Berdasarkan Orang (10 L/s/orang)</option>
                                            <option value="ach" {{ ($v?->method ?? '') == 'ach' ? 'selected' : '' }}>Air Change per Hour (ACH)</option>
                                            <option value="direct_airflow" {{ ($v?->method ?? '') == 'direct_airflow' ? 'selected' : '' }}>Debit Udara Langsung (L/s atau CFM)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nilai Input Debit</label>
                                        <div class="input-group">
                                            <input type="number" step="0.5" name="ventilations[0][input_value]" class="form-control" value="{{ $v?->input_value ?? 10.0 }}">
                                            <span class="input-group-text">L/s/org atau ACH</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-header bg-light py-3 border-bottom">
                                    <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-door-open text-warning me-2"></i>Infiltrasi Udara</h6>
                                </div>
                                <div class="card-body p-4">
                                    @php $inf = $room->infiltrations->first(); @endphp
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tingkat Kebocoran Udara (ACH)</label>
                                        <select name="infiltrations[0][ach_value]" class="form-select">
                                            <option value="0.25" {{ ($inf?->ach_value ?? 0.50) == 0.25 ? 'selected' : '' }}>Sangat Rapat (0.25 ACH)</option>
                                            <option value="0.50" {{ ($inf?->ach_value ?? 0.50) == 0.50 ? 'selected' : '' }}>Normal (0.50 ACH)</option>
                                            <option value="1.00" {{ ($inf?->ach_value ?? 0.50) == 1.00 ? 'selected' : '' }}>Keluar Masuk Sering (1.00 ACH)</option>
                                            <option value="1.50" {{ ($inf?->ach_value ?? 0.50) == 1.50 ? 'selected' : '' }}>Toko Terbuka (1.50 ACH)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Safety Factor & Submit Card -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-body p-4">
                <div class="row align-items-center justify-content-between g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Faktor Keamanan (Safety Factor) <span class="badge bg-light-primary text-primary ms-2" id="safetyBadge">{{ $room->safety_factor_percent }}%</span></label>
                        <div class="d-flex align-items-center gap-3">
                            <input type="range" class="form-range" min="0" max="30" step="5" id="safetySlider" value="{{ old('safety_factor_percent', $room->safety_factor_percent) }}" oninput="updateSafety(this.value)">
                            <input type="number" name="safety_factor_percent" id="safetyInput" class="form-control form-control-sm text-center" style="width: 80px;" value="{{ old('safety_factor_percent', $room->safety_factor_percent) }}">
                            <span class="fw-bold">%</span>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('hvac.room.result', $room->id) }}" class="btn btn-outline-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary btn-lg px-4 shadow-sm">
                            <i class="mdi mdi-check me-1"></i> Simpan &amp; Hitung Ulang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('after-script')
    <script>
        document.querySelectorAll('.calc-dim').forEach(input => {
            input.addEventListener('input', updateDimensionPreviews);
        });

        function updateDimensionPreviews() {
            const l = parseFloat(document.getElementById('room_length')?.value) || 0;
            const w = parseFloat(document.getElementById('room_width')?.value) || 0;
            const h = parseFloat(document.getElementById('room_height')?.value) || 0;
            if (document.getElementById('preview_area')) document.getElementById('preview_area').innerText = (l * w).toFixed(1);
            if (document.getElementById('preview_volume')) document.getElementById('preview_volume').innerText = (l * w * h).toFixed(1);
        }

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

            const checked = document.querySelector('input[name="calculation_mode"]:checked');
            switchMode(checked ? checked.value : 'detailed');
            updateDimensionPreviews();
        });

        function updateSafety(val) {
            document.getElementById('safetyInput').value = val;
            document.getElementById('safetyBadge').innerText = val + '%';
        }
        document.getElementById('safetyInput')?.addEventListener('input', function() {
            document.getElementById('safetySlider').value = this.value;
            document.getElementById('safetyBadge').innerText = this.value + '%';
        });

        function removeRow(btn) {
            const tr = btn.closest('tr');
            if (tr.parentElement.children.length > 1) {
                tr.remove();
            } else {
                alert('Minimal harus ada 1 baris input.');
            }
        }
    </script>
    @endpush
@endsection
