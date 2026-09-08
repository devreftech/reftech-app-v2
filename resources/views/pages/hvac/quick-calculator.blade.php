@extends('layouts.sales.app')
@section('title', 'Quick Cooling Load Estimator (Sales)')

@push('after-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
<style>
    .kpi-quick-card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-quick-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #d9dee3;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="mdi mdi-flash text-warning me-2"></i>Quick Cooling Load Estimator (Sales)
            </h4>
            <p class="text-muted mb-0">Kalkulator estimasi cepat kebutuhan kapasitas AC secara instan (real-time) untuk tim Sales Reftech.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('hvac.project.index') }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-folder-outline me-1"></i> Proyek HVAC
            </a>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#saveToProjectModal">
                <i class="mdi mdi-content-save-outline me-1"></i> Simpan ke Proyek ERP
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Input Form Card -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 border-top border-warning border-4 h-100">
                <div class="card-header bg-light py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-tune text-warning me-2"></i>Input Parameter Ruangan
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Dimensi -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Panjang (P)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" id="quick_length" class="form-control" value="6.0" oninput="runLiveCalculation()">
                                <span class="input-group-text">m</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Lebar (L)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" id="quick_width" class="form-control" value="4.0" oninput="runLiveCalculation()">
                                <span class="input-group-text">m</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tinggi (T)</label>
                            <div class="input-group">
                                <input type="number" step="0.1" id="quick_height" class="form-control" value="3.0" oninput="runLiveCalculation()">
                                <span class="input-group-text">m</span>
                            </div>
                        </div>

                        <!-- Luas & Volume Output -->
                        <div class="col-12">
                            <div class="p-2 px-3 bg-light rounded d-flex justify-content-between align-items-center">
                                <span class="text-muted small">Luas Lantai: <strong id="live_area" class="text-dark">24.0 m²</strong></span>
                                <span class="text-muted small">Volume Ruangan: <strong id="live_volume" class="text-dark">72.0 m³</strong></span>
                            </div>
                        </div>

                        <!-- Penghuni -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jumlah Penghuni (Orang)</label>
                            <div class="input-group">
                                <input type="number" id="quick_occupants" class="form-control" value="4" min="0" oninput="runLiveCalculation()">
                                <span class="input-group-text">Orang</span>
                            </div>
                            <small class="text-muted">+500 BTU/h per orang</small>
                        </div>

                        <!-- Kondisi Ruangan / Insulasi -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kondisi Insulasi Ruangan</label>
                            <select id="quick_condition" class="form-select" onchange="runLiveCalculation()">
                                <option value="500">Insulasi Bagus / Kamar Teduh (500 BTU/m²)</option>
                                <option value="600" selected>Standar Kantor / Rumah / Ruko (600 BTU/m²)</option>
                                <option value="750">Banyak Kaca / Lantai Teratas (750 BTU/m²)</option>
                                <option value="900">Beban Tinggi / Dinding Barat Terik (900 BTU/m²)</option>
                            </select>
                        </div>

                        <!-- Paparan Matahari -->
                        <div class="col-12">
                            <label class="form-label fw-semibold">Paparan Sinar Matahari</label>
                            <select id="quick_sun" class="form-select" onchange="runLiveCalculation()">
                                <option value="0">Rendah (Terlindung gedung lain / kanopi teduh) [+0 BTU/h]</option>
                                <option value="1000" selected>Sedang (Jendela standar ada sinar pagi / miring) [+1,000 BTU/h]</option>
                                <option value="2500">Tinggi (Dinding kaca hadap barat / atap seng panas terik) [+2,500 BTU/h]</option>
                            </select>
                        </div>

                        <!-- Safety Factor Slider -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label fw-semibold mb-0">Safety Factor Cadangan</label>
                                <span class="badge bg-primary fs-7" id="live_safety_badge">10%</span>
                            </div>
                            <input type="range" class="form-range" min="0" max="30" step="5" id="quick_safety" value="10" oninput="runLiveCalculation()">
                            <small class="text-muted">Standar HVAC komersial: 10% s/d 15%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-Time Output & AC Recommendation Card -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 border-top border-primary border-4 h-100">
                <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-calculator text-primary me-2"></i>Hasil Estimasi Beban &amp; Unit AC
                    </h5>
                    <span class="badge bg-label-success">Live Real-time</span>
                </div>
                <div class="card-body p-4">
                    <!-- Main KPI Box -->
                    <div class="text-center p-4 bg-primary text-white rounded-3 mb-4 shadow-sm">
                        <small class="text-uppercase opacity-75 fw-semibold" style="letter-spacing: 1px;">Kebutuhan Kapasitas AC</small>
                        <div class="display-5 fw-bold my-1" id="res_btuh">17,490</div>
                        <div class="fs-5 opacity-90 font-monospace">BTU/h &bull; <span id="res_pk" class="fw-bold">1.94 PK</span></div>
                    </div>

                    <!-- Minor KPI Row -->
                    <div class="row g-2 mb-4">
                        <div class="col-4">
                            <div class="border rounded p-2 text-center bg-light">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 10px;">Kapasitas kW</small>
                                <div class="fs-5 fw-bold text-dark" id="res_kw">5.13 kW</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 text-center bg-light">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 10px;">Ton Ref (TR)</small>
                                <div class="fs-5 fw-bold text-dark" id="res_tr">1.46 TR</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 text-center bg-light">
                                <small class="text-muted text-uppercase fw-semibold" style="font-size: 10px;">Estimasi PK</small>
                                <div class="fs-5 fw-bold text-success" id="res_nominal_pk">2.0 PK</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recommended AC Match Box -->
                    <div class="border rounded p-3 bg-light-primary border-primary mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-primary">Rekomendasi Unit AC Standar</span>
                            <small class="text-muted font-monospace" id="rec_capacity_badge">18,000 BTU/h (2.0 PK)</small>
                        </div>
                        <h5 class="fw-bold text-dark mb-1" id="rec_model_name">Daikin FTKQ50 (Inverter Split Wall)</h5>
                        <p class="small text-muted mb-0" id="rec_explanation">Kapasitas unit AC mencukupi beban pendinginan ruangan dengan cadangan aman.</p>
                    </div>

                    <!-- Action to Save -->
                    <button type="button" class="btn btn-primary w-100 py-2" data-bs-toggle="modal" data-bs-target="#saveToProjectModal">
                        <i class="mdi mdi-folder-plus-outline me-1"></i> Simpan Estimasi Ini ke Proyek ERP
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL: SAVE TO PROJECT -->
    <div class="modal fade" id="saveToProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('hvac.quick-calculator.save') }}" method="POST" class="modal-content">
                @csrf
                <!-- Hidden room params from live calculator -->
                <input type="hidden" name="length" id="modal_length" value="6.0">
                <input type="hidden" name="width" id="modal_width" value="4.0">
                <input type="hidden" name="height" id="modal_height" value="3.0">
                <input type="hidden" name="quick_occupants_count" id="modal_occupants" value="4">
                <input type="hidden" name="quick_room_condition" id="modal_condition" value="standard">
                <input type="hidden" name="quick_sun_exposure" id="modal_sun" value="medium">
                <input type="hidden" name="safety_factor_percent" id="modal_safety" value="10">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Simpan Estimasi ke Proyek ERP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Proyek / Gedung <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" class="form-control" placeholder="Contoh: Pengadaan AC Ruko Grand Galaxy" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Customer / Klien <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-office-building-outline"></i></span>
                                <input type="text" name="customer_name" id="modal_customer_name" class="form-control" placeholder="Contoh: PT Industri Maju Bersama / Bapak Budi" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Ruangan <span class="text-danger">*</span></label>
                            <input type="text" name="room_name" class="form-control" placeholder="Contoh: Ruang Utama Lt. 1" value="Ruang Utama" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Lokasi Site</label>
                            <input type="text" name="location" id="modal_location" class="form-control" placeholder="Contoh: Bekasi">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Simpan &amp; Lihat Sheet Perhitungan</button>
                </div>
            </form>
        </div>
    </div>

    @push('after-scripts')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        // Catalog data for live recommendation matching
        const acCatalog = @json($acUnits);

        function runLiveCalculation() {
            const l = parseFloat(document.getElementById('quick_length')?.value) || 0;
            const w = parseFloat(document.getElementById('quick_width')?.value) || 0;
            const h = parseFloat(document.getElementById('quick_height')?.value) || 3.0;

            const area = l * w;
            const volume = area * h;

            document.getElementById('live_area').innerText = area.toFixed(1) + ' m²';
            document.getElementById('live_volume').innerText = volume.toFixed(1) + ' m³';

            const baseIndex = parseFloat(document.getElementById('quick_condition')?.value) || 600;
            const heightMultiplier = h > 3.0 ? (h / 3.0) : 1.0;
            const occupants = parseInt(document.getElementById('quick_occupants')?.value) || 0;
            const sunAdd = parseFloat(document.getElementById('quick_sun')?.value) || 0;
            const safetyPct = parseFloat(document.getElementById('quick_safety')?.value) || 10;

            document.getElementById('live_safety_badge').innerText = safetyPct + '%';

            // Calculation formula
            const baseAreaBtuh = area * baseIndex * heightMultiplier;
            const occupantBtuh = occupants * 500;
            const subtotalBtuh = baseAreaBtuh + occupantBtuh + sunAdd;
            const designBtuh = Math.round(subtotalBtuh * (1.0 + (safetyPct / 100.0)));

            const pk = (designBtuh / 9000.0).toFixed(2);
            const kw = (designBtuh * 0.293071 / 1000.0).toFixed(2);
            const tr = (designBtuh / 12000.0).toFixed(2);

            document.getElementById('res_btuh').innerText = designBtuh.toLocaleString('id-ID');
            document.getElementById('res_pk').innerText = pk + ' PK';
            document.getElementById('res_kw').innerText = kw + ' kW';
            document.getElementById('res_tr').innerText = tr + ' TR';

            // Find closest AC unit from catalog >= designBtuh * 0.95
            let matched = null;
            for (let i = 0; i < acCatalog.length; i++) {
                if (parseFloat(acCatalog[i].cooling_capacity_btuh) >= (designBtuh * 0.95)) {
                    matched = acCatalog[i];
                    break;
                }
            }

            if (matched) {
                document.getElementById('res_nominal_pk').innerText = matched.nominal_pk + ' PK';
                document.getElementById('rec_model_name').innerText = matched.brand + ' ' + matched.model_name;
                document.getElementById('rec_capacity_badge').innerText = parseInt(matched.cooling_capacity_btuh).toLocaleString() + ' BTU/h (' + matched.nominal_pk + ' PK)';
                const margin = Math.round(((matched.cooling_capacity_btuh - designBtuh) / designBtuh) * 100);
                document.getElementById('rec_explanation').innerText = 'Kapasitas unit mencukupi beban design pendinginan dengan margin cadangan ' + margin + '%.';
            } else {
                const estPk = (designBtuh / 9000.0).toFixed(1);
                document.getElementById('res_nominal_pk').innerText = estPk + ' PK';
                document.getElementById('rec_model_name').innerText = 'Unit AC Commercial ' + estPk + ' PK';
                document.getElementById('rec_capacity_badge').innerText = designBtuh.toLocaleString() + ' BTU/h';
                document.getElementById('rec_explanation').innerText = 'Dibutuhkan unit kapasitas besar atau multi-unit sistem.';
            }

            // Sync hidden inputs for modal
            document.getElementById('modal_length').value = l;
            document.getElementById('modal_width').value = w;
            document.getElementById('modal_height').value = h;
            document.getElementById('modal_occupants').value = occupants;
            document.getElementById('modal_safety').value = safetyPct;

            const condEl = document.getElementById('quick_condition');
            if (condEl.value === '500') document.getElementById('modal_condition').value = 'light_insulated';
            else if (condEl.value === '750') document.getElementById('modal_condition').value = 'glass_heavy';
            else if (condEl.value === '900') document.getElementById('modal_condition').value = 'high_heat';
            else document.getElementById('modal_condition').value = 'standard';

            const sunEl = document.getElementById('quick_sun');
            if (sunEl.value === '0') document.getElementById('modal_sun').value = 'low';
            else if (sunEl.value === '2500') document.getElementById('modal_sun').value = 'high';
            else document.getElementById('modal_sun').value = 'medium';
        }

        $(document).ready(function() {
            runLiveCalculation();
        });
    </script>
    @endpush
@endsection
