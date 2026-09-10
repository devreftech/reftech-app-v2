<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cooling Load Calculation Sheet - {{ $room->room_name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 11px; margin: 0; padding: 10mm; }
            .card { border: 1px solid #ddd !important; }
            .page-break { page-break-after: always; }
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            background: #fff;
        }
        .header-box {
            border-bottom: 2px solid #0056b3;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .table-condensed th, .table-condensed td {
            padding: 4px 8px;
            font-size: 11px;
        }
        .kpi-box {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container py-3">
        <!-- Print Button Bar -->
        <div class="no-print d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded border">
            <div>
                <strong>Print / Export Sheet:</strong> {{ $room->room_name }} ({{ $room->project->project_code }})
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-primary btn-sm me-2">
                    Cetak / Simpan PDF
                </button>
                <button onclick="window.close()" class="btn btn-outline-secondary btn-sm">
                    Tutup
                </button>
            </div>
        </div>

        <!-- Header -->
        <div class="header-box d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold text-primary mb-1">PT. REFTECH HVAC &amp; COMPRESSED AIR</h4>
                <div class="text-uppercase fw-semibold" style="letter-spacing: 1px; font-size: 13px;">
                    HVAC Engineering Cooling Load Calculation Sheet
                </div>
            </div>
            <div class="text-end small">
                <div><strong>Kode Proyek:</strong> {{ $room->project->project_code }}</div>
                <div><strong>Tanggal:</strong> {{ now()->format('d F Y') }}</div>
                <div><strong>Metode:</strong> {{ strtoupper($room->calculation_mode) }} CALCULATION</div>
            </div>
        </div>

        <!-- Project & Room Info Grid -->
        <div class="row g-3 mb-3">
            <div class="col-6">
                <table class="table table-bordered table-condensed mb-0">
                    <tr>
                        <th class="table-light" style="width: 35%;">Nama Proyek</th>
                        <td>{{ $room->project->project_name }}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Customer / Client</th>
                        <td>{{ $room->project->customer_display_name }}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Lokasi Site</th>
                        <td>{{ $room->project->location ?: '-' }}</td>
                    </tr>
                    <tr>
                        <th class="table-light">Sales In-Charge</th>
                        <td>{{ $room->project->sales?->name ?: '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-6">
                <table class="table table-bordered table-condensed mb-0">
                    <tr>
                        <th class="table-light" style="width: 35%;">Nama Ruangan</th>
                        <td><strong>{{ $room->room_name }}</strong> ({{ $room->room_type ?: 'Office' }})</td>
                    </tr>
                    <tr>
                        <th class="table-light">Dimensi (P × L × T)</th>
                        <td>{{ $room->length }} m × {{ $room->width }} m × {{ $room->height }} m ({{ $room->floor_area }} m² / {{ $room->room_volume }} m³)</td>
                    </tr>
                    <tr>
                        <th class="table-light">Desain Indoor</th>
                        <td>{{ $room->indoor_temp }}°C Dry Bulb &bull; {{ $room->indoor_rh }}% RH</td>
                    </tr>
                    <tr>
                        <th class="table-light">Desain Outdoor</th>
                        <td>{{ $room->outdoor_temp }}°C Dry Bulb &bull; {{ $room->outdoor_rh }}% RH</td>
                    </tr>
                </table>
            </div>
        </div>

        @php $res = $room->result; @endphp

        <!-- KPI Summary Cards -->
        <div class="row g-2 mb-3">
            <div class="col-3">
                <div class="kpi-box">
                    <small class="text-muted text-uppercase fw-semibold">Design Cooling Load</small>
                    <div class="fs-5 fw-bold text-primary">{{ number_format($res->design_load_btuh, 0) }} BTU/h</div>
                </div>
            </div>
            <div class="col-3">
                <div class="kpi-box">
                    <small class="text-muted text-uppercase fw-semibold">Kapasitas kW</small>
                    <div class="fs-5 fw-bold text-dark">{{ number_format($res->design_load_kw, 2) }} kW</div>
                </div>
            </div>
            <div class="col-3">
                <div class="kpi-box">
                    <small class="text-muted text-uppercase fw-semibold">Ton Ref (TR)</small>
                    <div class="fs-5 fw-bold text-dark">{{ number_format($res->design_load_tr, 2) }} TR</div>
                </div>
            </div>
            <div class="col-3">
                <div class="kpi-box">
                    <small class="text-muted text-uppercase fw-semibold">Kapasitas AC (PK)</small>
                    <div class="fs-5 fw-bold text-success">{{ number_format($res->design_load_pk, 1) }} PK</div>
                </div>
            </div>
        </div>

        <!-- Breakdown Table -->
        <h6 class="fw-bold text-dark border-bottom pb-1 mb-2">Breakdown Beban Panas Ruangan (Heat Gain Breakdown)</h6>
        <table class="table table-bordered table-condensed mb-3">
            <thead class="table-light">
                <tr>
                    <th>Komponen Beban</th>
                    <th>Spesifikasi &amp; Asumsi Perhitungan</th>
                    <th class="text-center" style="width: 100px;">Sensible (W)</th>
                    <th class="text-center" style="width: 100px;">Latent (W)</th>
                    <th class="text-end" style="width: 120px;">Total (BTU/h)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Dinding &amp; Partisi</strong></td>
                    <td>Konduksi dinding eksternal &amp; partisi internal (U × A × ΔT)</td>
                    <td class="text-center">{{ number_format($res->wall_sensible_w, 1) }}</td>
                    <td class="text-center">-</td>
                    <td class="text-end">{{ number_format(round($res->wall_sensible_w * 3.412142)) }}</td>
                </tr>
                <tr>
                    <td><strong>Atap &amp; Plafon</strong></td>
                    <td>Konduksi atap terpapar sinar &amp; plafon ruangan</td>
                    <td class="text-center">{{ number_format($res->roof_sensible_w, 1) }}</td>
                    <td class="text-center">-</td>
                    <td class="text-end">{{ number_format(round($res->roof_sensible_w * 3.412142)) }}</td>
                </tr>
                <tr>
                    <td><strong>Kaca &amp; Jendela (Konduksi)</strong></td>
                    <td>Perpindahan panas konduksi melalui kaca jendela</td>
                    <td class="text-center">{{ number_format($res->glass_conduction_sensible_w, 1) }}</td>
                    <td class="text-center">-</td>
                    <td class="text-end">{{ number_format(round($res->glass_conduction_sensible_w * 3.412142)) }}</td>
                </tr>
                <tr>
                    <td><strong>Radiasi Solar Matahari</strong></td>
                    <td>Radiasi matahari menembus kaca (SHGC × Irradiance × Shading)</td>
                    <td class="text-center">{{ number_format($res->glass_solar_sensible_w, 1) }}</td>
                    <td class="text-center">-</td>
                    <td class="text-end">{{ number_format(round($res->glass_solar_sensible_w * 3.412142)) }}</td>
                </tr>
                <tr>
                    <td><strong>Penghuni (Occupants)</strong></td>
                    <td>Metabolisme manusia (Sensible 75W &bull; Latent 55W per orang standar)</td>
                    <td class="text-center">{{ number_format($res->occupant_sensible_w, 1) }}</td>
                    <td class="text-center">{{ number_format($res->occupant_latent_w, 1) }}</td>
                    <td class="text-end">{{ number_format(round(($res->occupant_sensible_w + $res->occupant_latent_w) * 3.412142)) }}</td>
                </tr>
                <tr>
                    <td><strong>Penerangan (Lighting)</strong></td>
                    <td>Beban panas lampu dan ballast</td>
                    <td class="text-center">{{ number_format($res->lighting_sensible_w, 1) }}</td>
                    <td class="text-center">-</td>
                    <td class="text-end">{{ number_format(round($res->lighting_sensible_w * 3.412142)) }}</td>
                </tr>
                <tr>
                    <td><strong>Peralatan &amp; Elektronik</strong></td>
                    <td>Komputer, server rack, printer, dan mesin elektrik</td>
                    <td class="text-center">{{ number_format($res->equipment_sensible_w, 1) }}</td>
                    <td class="text-center">{{ number_format($res->equipment_latent_w, 1) }}</td>
                    <td class="text-end">{{ number_format(round(($res->equipment_sensible_w + $res->equipment_latent_w) * 3.412142)) }}</td>
                </tr>
                <tr>
                    <td><strong>Ventilasi Udara Segar</strong></td>
                    <td>Fresh air intake (Sensible temp diff + Latent humidity moisture)</td>
                    <td class="text-center">{{ number_format($res->ventilation_sensible_w, 1) }}</td>
                    <td class="text-center">{{ number_format($res->ventilation_latent_w, 1) }}</td>
                    <td class="text-end">{{ number_format(round(($res->ventilation_sensible_w + $res->ventilation_latent_w) * 3.412142)) }}</td>
                </tr>
                <tr>
                    <td><strong>Infiltrasi Celah &amp; Pintu</strong></td>
                    <td>Kebocoran udara luar melalui celah bukaan</td>
                    <td class="text-center">{{ number_format($res->infiltration_sensible_w, 1) }}</td>
                    <td class="text-center">{{ number_format($res->infiltration_latent_w, 1) }}</td>
                    <td class="text-end">{{ number_format(round(($res->infiltration_sensible_w + $res->infiltration_latent_w) * 3.412142)) }}</td>
                </tr>
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="2">Subtotal Beban Ruangan (Watt &amp; BTU/h)</td>
                    <td class="text-center">{{ number_format($res->total_sensible_load_w, 1) }} W</td>
                    <td class="text-center">{{ number_format($res->total_latent_load_w, 1) }} W</td>
                    <td class="text-end">{{ number_format(round($res->subtotal_load_w * 3.412142)) }} BTU/h</td>
                </tr>
                <tr>
                    <td colspan="4">Faktor Keamanan (Safety Factor +{{ $res->safety_factor_percent }}%)</td>
                    <td class="text-end">+{{ number_format(round(($res->design_load_w - $res->subtotal_load_w) * 3.412142)) }} BTU/h</td>
                </tr>
                <tr class="table-primary fs-6">
                    <td colspan="4">TOTAL DESIGN COOLING LOAD</td>
                    <td class="text-end fw-bold">{{ number_format($res->design_load_btuh, 0) }} BTU/h</td>
                </tr>
            </tfoot>
        </table>

        <!-- Recommended AC Unit Box -->
        <div class="border rounded p-3 mb-4 bg-light">
            <h6 class="fw-bold text-success mb-2">Rekomendasi Kapasitas Unit Pengkondisi Udara (AC Unit Selection)</h6>
            <div class="row">
                <div class="col-8">
                    @if($res->recommended_ac_model)
                        <div class="fs-6 fw-bold text-dark mb-1">
                            {{ $res->recommended_unit_qty > 1 ? $res->recommended_unit_qty . 'x ' : '' }}{{ $res->recommended_ac_model }}
                        </div>
                        <div class="text-muted small">
                            Kapasitas: {{ number_format($res->recommended_ac_capacity_btuh, 0) }} BTU/h per unit (Nominal {{ $res->recommended_ac_pk }} PK)
                        </div>
                        <div class="small text-muted mt-1">{{ $res->recommendation_notes }}</div>
                    @else
                        <div class="fw-bold text-dark">Unit AC Kapasitas {{ number_format($res->design_load_pk, 1) }} PK ({{ number_format($res->design_load_btuh, 0) }} BTU/h)</div>
                    @endif
                </div>
                <div class="col-4 text-end">
                    <span class="badge bg-success fs-6">{{ number_format($res->design_load_pk, 1) }} PK AC</span>
                </div>
            </div>
        </div>

        <!-- Signature Section -->
        <div class="row pt-4 text-center">
            <div class="col-4">
                <div class="small text-muted mb-5">Dihitung Oleh (Engineer / Sales):</div>
                <div class="fw-bold text-dark">({{ $room->project->sales?->name ?: '................................' }})</div>
            </div>
            <div class="col-4">
                <div class="small text-muted mb-5">Diverifikasi Oleh (Lead Engineer):</div>
                <div class="fw-bold text-dark">(................................)</div>
            </div>
            <div class="col-4">
                <div class="small text-muted mb-5">Disetujui Client / Customer:</div>
                <div class="fw-bold text-dark">({{ $room->project->customer_display_name }})</div>
            </div>
        </div>
    </div>
</body>
</html>
