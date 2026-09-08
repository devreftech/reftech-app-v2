@extends('layouts.sales.app')
@section('title', 'Hasil Perhitungan Cooling Load - ' . $room->room_name)

@section('content')
    @php
        $res = $room->result;
        $totalSubtotalW = $res->subtotal_load_w > 0 ? $res->subtotal_load_w : 1;
        $sensiblePct = round(($res->total_sensible_load_w / $totalSubtotalW) * 100, 1);
        $latentPct = round(($res->total_latent_load_w / $totalSubtotalW) * 100, 1);

        // Individual breakdown items in Watt
        $components = [
            ['name' => 'Dinding (Wall Conduction)', 'w' => $res->wall_sensible_w, 'type' => 'sensible', 'icon' => 'mdi-wall', 'color' => 'primary'],
            ['name' => 'Atap / Plafon (Roof Conduction)', 'w' => $res->roof_sensible_w, 'type' => 'sensible', 'icon' => 'mdi-home-roof', 'color' => 'danger'],
            ['name' => 'Konduksi Kaca (Glass Conduction)', 'w' => $res->glass_conduction_sensible_w, 'type' => 'sensible', 'icon' => 'mdi-window-closed-variant', 'color' => 'info'],
            ['name' => 'Radiasi Matahari (Solar Heat Gain)', 'w' => $res->glass_solar_sensible_w, 'type' => 'sensible', 'icon' => 'mdi-weather-sunny', 'color' => 'warning'],
            ['name' => 'Penghuni - Sensible (People Sensible)', 'w' => $res->occupant_sensible_w, 'type' => 'sensible', 'icon' => 'mdi-account', 'color' => 'success'],
            ['name' => 'Penghuni - Latent (People Moisture)', 'w' => $res->occupant_latent_w, 'type' => 'latent', 'icon' => 'mdi-water-outline', 'color' => 'info'],
            ['name' => 'Lampu Penerangan (Lighting)', 'w' => $res->lighting_sensible_w, 'type' => 'sensible', 'icon' => 'mdi-lightbulb-on', 'color' => 'warning'],
            ['name' => 'Peralatan & Mesin (Equipment Sensible)', 'w' => $res->equipment_sensible_w, 'type' => 'sensible', 'icon' => 'mdi-laptop', 'color' => 'primary'],
            ['name' => 'Peralatan - Latent (Equipment Moisture)', 'w' => $res->equipment_latent_w, 'type' => 'latent', 'icon' => 'mdi-kettle-outline', 'color' => 'info'],
            ['name' => 'Ventilasi Udara Segar (Vent Sensible)', 'w' => $res->ventilation_sensible_w, 'type' => 'sensible', 'icon' => 'mdi-weather-windy', 'color' => 'secondary'],
            ['name' => 'Ventilasi - Latent (Outdoor Humidity)', 'w' => $res->ventilation_latent_w, 'type' => 'latent', 'icon' => 'mdi-water-percent', 'color' => 'info'],
            ['name' => 'Infiltrasi Udara (Infiltration)', 'w' => $res->infiltration_sensible_w + $res->infiltration_latent_w, 'type' => 'both', 'icon' => 'mdi-door-open', 'color' => 'secondary'],
        ];
    @endphp

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('hvac.project.show', $room->id_hvac_project) }}" class="btn btn-sm btn-icon btn-light">
                    <i class="mdi mdi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0">{{ $room->room_name }} - Hasil Cooling Load</h4>
                @if($room->calculation_mode == 'quick')
                    <span class="badge bg-label-warning"><i class="mdi mdi-flash me-1"></i>Quick Sales Mode</span>
                @else
                    <span class="badge bg-label-primary"><i class="mdi mdi-ruler-square me-1"></i>Detailed Engineering</span>
                @endif
            </div>
            <p class="text-muted mb-0">
                Proyek: <strong class="text-dark">{{ $room->project->project_name }}</strong> ({{ $room->project->customer_display_name }}) &bull; Dimensi: {{ $room->length }}m × {{ $room->width }}m × {{ $room->height }}m ({{ $room->floor_area }} m²)
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('hvac.room.print', $room->id) }}" target="_blank" class="btn btn-outline-secondary">
                <i class="mdi mdi-printer me-1"></i> Cetak / Export Sheet
            </a>
            <a href="{{ route('hvac.room.edit', $room->id) }}" class="btn btn-outline-primary">
                <i class="mdi mdi-pencil-outline me-1"></i> Edit Parameter
            </a>
            <a href="{{ route('hvac.project.show', $room->id_hvac_project) }}" class="btn btn-primary">
                <i class="mdi mdi-check me-1"></i> Selesai
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Top KPI Cards -->
    <div class="row g-3 mb-4">
        <!-- Design Cooling Load BTU/h -->
        <div class="col-12 col-md-4">
            <div class="card shadow-sm border-0 bg-primary text-white h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-white text-primary fw-bold text-uppercase">Design Cooling Load</span>
                        <i class="mdi mdi-snowflake fs-2 opacity-75"></i>
                    </div>
                    <div class="display-6 fw-bold mb-1">{{ number_format($res->design_load_btuh, 0) }}</div>
                    <div class="small opacity-75">BTU/h (Safety Factor +{{ $res->safety_factor_percent }}%)</div>
                    <hr class="opacity-25 my-3">
                    <div class="d-flex justify-content-between small">
                        <span>Subtotal Murni:</span>
                        <span class="fw-bold">{{ number_format(round($res->subtotal_load_w * 3.412142), 0) }} BTU/h</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Conversions: kW, TR, PK -->
        <div class="col-12 col-md-8">
            <div class="row g-3">
                <div class="col-6 col-sm-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 text-center">
                            <div class="avatar bg-light-info text-info rounded p-2 mx-auto mb-2">
                                <i class="mdi mdi-lightning-bolt fs-4"></i>
                            </div>
                            <small class="text-muted text-uppercase fw-semibold">Kapasitas kW</small>
                            <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($res->design_load_kw, 2) }}</div>
                            <small class="text-muted">kW Thermal</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 text-center">
                            <div class="avatar bg-light-success text-success rounded p-2 mx-auto mb-2">
                                <i class="mdi mdi-snowflake-alert fs-4"></i>
                            </div>
                            <small class="text-muted text-uppercase fw-semibold">Ton Ref (TR)</small>
                            <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($res->design_load_tr, 2) }}</div>
                            <small class="text-muted">1 TR = 12,000 BTU</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 text-center">
                            <div class="avatar bg-light-warning text-warning rounded p-2 mx-auto mb-2">
                                <i class="mdi mdi-fan fs-4"></i>
                            </div>
                            <small class="text-muted text-uppercase fw-semibold">Daya AC (PK / HP)</small>
                            <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($res->design_load_pk, 1) }}</div>
                            <small class="text-muted">Horse Power AC</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 text-center">
                            <div class="avatar bg-light-danger text-danger rounded p-2 mx-auto mb-2">
                                <i class="mdi mdi-thermometer-high fs-4"></i>
                            </div>
                            <small class="text-muted text-uppercase fw-semibold">Sensible Load</small>
                            <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($res->total_sensible_load_w, 0) }} W</div>
                            <small class="text-muted">{{ $sensiblePct }}% dari subtotal</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 text-center">
                            <div class="avatar bg-light-primary text-primary rounded p-2 mx-auto mb-2">
                                <i class="mdi mdi-water-percent fs-4"></i>
                            </div>
                            <small class="text-muted text-uppercase fw-semibold">Latent Load</small>
                            <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($res->total_latent_load_w, 0) }} W</div>
                            <small class="text-muted">{{ $latentPct }}% dari subtotal</small>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 text-center">
                            <div class="avatar bg-light-secondary text-secondary rounded p-2 mx-auto mb-2">
                                <i class="mdi mdi-chart-bell-curve fs-4"></i>
                            </div>
                            <small class="text-muted text-uppercase fw-semibold">Sensible Heat Ratio</small>
                            <div class="fs-4 fw-bold text-dark mt-1">{{ number_format($res->sensible_heat_ratio, 2) }}</div>
                            <small class="text-muted">SHR = Qs / Qtotal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sensible vs Latent Ratio Bar -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold text-dark"><i class="mdi mdi-scale-balance text-primary me-2"></i>Proporsi Beban: Sensible vs Latent</span>
                <span class="small text-muted">
                    <span class="badge bg-danger rounded-circle p-1"></span> Sensible: {{ $sensiblePct }}% ({{ number_format($res->total_sensible_load_w) }} W) &nbsp;|&nbsp;
                    <span class="badge bg-info rounded-circle p-1"></span> Latent: {{ $latentPct }}% ({{ number_format($res->total_latent_load_w) }} W)
                </span>
            </div>
            <div class="progress" style="height: 14px;">
                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $sensiblePct }}%;" aria-valuenow="{{ $sensiblePct }}" aria-valuemin="0" aria-valuemax="100"></div>
                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $latentPct }}%;" aria-valuenow="{{ $latentPct }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <!-- Recommendations & Breakdown Section -->
    <div class="row g-4">
        <!-- AC Recommendation Card -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 border-top border-success border-4 h-100">
                <div class="card-header bg-light py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-air-conditioner text-success me-2"></i>Rekomendasi Kapasitas Unit AC
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($res->recommended_ac_model)
                        <div class="text-center p-3 bg-light rounded-3 mb-4">
                            <span class="badge bg-success mb-2">Pilihan Terbaik</span>
                            <h4 class="fw-bold text-primary mb-1">
                                {{ $res->recommended_unit_qty > 1 ? $res->recommended_unit_qty . 'x ' : '' }}{{ $res->recommended_ac_model }}
                            </h4>
                            <div class="fs-5 font-monospace text-dark fw-bold">
                                {{ number_format($res->recommended_ac_capacity_btuh, 0) }} BTU/h ({{ $res->recommended_ac_pk }} PK)
                            </div>
                        </div>

                        <div class="list-group list-group-flush border rounded mb-3">
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Design Load Ruangan:</span>
                                <span class="fw-bold">{{ number_format($res->design_load_btuh, 0) }} BTU/h</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Kapasitas Unit Rekomendasi:</span>
                                <span class="fw-bold text-success">{{ number_format($res->recommended_ac_capacity_btuh * $res->recommended_unit_qty, 0) }} BTU/h</span>
                            </div>
                            <div class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Margin Kapasitas AC:</span>
                                @php
                                    $margin = round(((($res->recommended_ac_capacity_btuh * $res->recommended_unit_qty) - $res->design_load_btuh) / max(1, $res->design_load_btuh)) * 100, 1);
                                @endphp
                                <span class="badge bg-light-success text-success">+{{ $margin }}% Cadangan Aman</span>
                            </div>
                        </div>

                        <div class="alert alert-info py-2 px-3 small mb-0">
                            <i class="mdi mdi-information-outline me-1"></i> {{ $res->recommendation_notes }}
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="mdi mdi-alert-circle-outline fs-1 d-block mb-2 text-warning"></i>
                            Belum ada unit yang cocok persis di katalog master AC.
                            <div class="mt-2 fw-bold text-dark">
                                Estimasi Kapasitas Dibutuhkan: {{ number_format($res->design_load_pk, 1) }} PK ({{ number_format($res->design_load_btuh) }} BTU/h)
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Heat Gain Breakdown Table -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-chart-bar text-primary me-2"></i>Heat Gain Breakdown per Komponen
                    </h5>
                    <small class="text-muted">Analisis Sumber Beban Panas</small>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Komponen Beban</th>
                                <th class="text-center" style="width: 80px;">Tipe</th>
                                <th class="text-end" style="width: 120px;">Daya (Watt)</th>
                                <th class="text-end" style="width: 120px;">Panas (BTU/h)</th>
                                <th style="width: 140px;">Kontribusi (%)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($components as $c)
                                @php
                                    $w = (float) $c['w'];
                                    $btu = round($w * 3.412142);
                                    $pct = $totalSubtotalW > 0 ? round(($w / $totalSubtotalW) * 100, 1) : 0;
                                @endphp
                                @if($w > 0)
                                    <tr>
                                        <td>
                                            <i class="mdi {{ $c['icon'] }} text-{{ $c['color'] }} me-2"></i>
                                            <span class="fw-semibold text-dark">{{ $c['name'] }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($c['type'] == 'sensible')
                                                <span class="badge bg-light-danger text-danger" style="font-size: 10px;">Sensible</span>
                                            @elseif($c['type'] == 'latent')
                                                <span class="badge bg-light-info text-info" style="font-size: 10px;">Latent</span>
                                            @else
                                                <span class="badge bg-light-secondary text-secondary" style="font-size: 10px;">Mixed</span>
                                            @endif
                                        </td>
                                        <td class="text-end font-monospace">{{ number_format($w, 1) }}</td>
                                        <td class="text-end font-monospace">{{ number_format($btu, 0) }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $c['color'] }}" style="width: {{ $pct }}%;"></div>
                                                </div>
                                                <small class="text-muted" style="width: 40px;">{{ $pct }}%</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>Subtotal Beban Ruangan Murni</td>
                                <td></td>
                                <td class="text-end">{{ number_format($res->subtotal_load_w, 1) }} W</td>
                                <td class="text-end">{{ number_format(round($res->subtotal_load_w * 3.412142), 0) }} BTU/h</td>
                                <td>100%</td>
                            </tr>
                            <tr class="table-warning">
                                <td>+ Faktor Keamanan (Safety Factor {{ $res->safety_factor_percent }}%)</td>
                                <td></td>
                                <td class="text-end">+{{ number_format($res->design_load_w - $res->subtotal_load_w, 1) }} W</td>
                                <td class="text-end">+{{ number_format(round(($res->design_load_w - $res->subtotal_load_w) * 3.412142), 0) }} BTU/h</td>
                                <td>+{{ $res->safety_factor_percent }}%</td>
                            </tr>
                            <tr class="table-primary fs-6">
                                <td>TOTAL DESIGN COOLING LOAD</td>
                                <td></td>
                                <td class="text-end">{{ number_format($res->design_load_w, 1) }} W</td>
                                <td class="text-end">{{ number_format($res->design_load_btuh, 0) }} BTU/h</td>
                                <td>{{ number_format($res->design_load_pk, 2) }} PK</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
