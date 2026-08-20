@extends('layouts.sales.app')
@section('title', 'Developer — System Maintenance Control & Planning')

@section('content')
    @php
        $activeTemplate = $details['template'] ?? 'animated';
        $templateNames = [
            'animated' => 'Template 3 (Light Dynamic & Animated)',
            'light'    => 'Template 2 (Light Clean & Elegant)',
            'dark'     => 'Template 1 (Dark Glassmorphism)',
        ];
    @endphp

    <div class="d-flex justify-content-between align-items-center py-3 mb-4 flex-wrap gap-2">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Developer /</span> System Maintenance & Planning
        </h4>
        <div class="d-flex align-items-center gap-2">
            <!-- Active Theme Indicator -->
            <span class="badge rounded-pill bg-label-info px-3 py-2 fs-6">
                <i class="mdi mdi-palette-outline me-1"></i>
                Tema: {{ $templateNames[$activeTemplate] ?? 'Template 3 (Animated)' }}
            </span>

            @if ($details['is_active'])
                <span class="badge rounded-pill bg-danger px-3 py-2 fs-6 animate__animated animate__pulse animate__infinite">
                    <i class="mdi mdi-alert-octagon me-1"></i> HARD MAINTENANCE ACTIVE
                </span>
            @elseif (!empty($details['is_planned']))
                <span class="badge rounded-pill bg-warning text-dark px-3 py-2 fs-6">
                    <i class="mdi mdi-clock-alert-outline me-1"></i> PLANNING SCHEDULED
                </span>
            @else
                <span class="badge rounded-pill bg-success px-3 py-2 fs-6">
                    <i class="mdi mdi-check-circle-outline me-1"></i> SYSTEM LIVE (NORMAL)
                </span>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-check-circle fs-4 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-alert-circle fs-4 me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4" role="tablist">
        <li class="nav-item">
            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#tab-planning">
                <i class="mdi mdi-calendar-clock me-1"></i> 1. Jadwal Terencana (Planning & Peringatan)
                @if (!empty($details['is_planned']))
                    <span class="badge rounded-pill bg-danger ms-1">Aktif</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-immediate">
                <i class="mdi mdi-lightning-bolt me-1"></i> 2. Eksekusi Langsung (Instant Hard Block)
                @if ($details['is_active'])
                    <span class="badge rounded-pill bg-danger ms-1">Running</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-templates">
                <i class="mdi mdi-palette-swatch-outline me-1"></i> 3. Pilihan Template Tampilan
                <span class="badge rounded-pill bg-primary ms-1">3 Desain</span>
            </button>
        </li>
    </ul>

    <div class="tab-content p-0">
        <!-- ========================================== -->
        <!-- TAB 1: PLANNING MAINTENANCE (JADWAL)       -->
        <!-- ========================================== -->
        <div class="tab-pane fade show active" id="tab-planning" role="tabpanel">
            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-3">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-calendar-clock me-2 text-warning"></i>Jadwal Pemeliharaan (Planning Maintenance)
                            </h5>
                            <span class="badge bg-label-warning">Auto Alert Notification</span>
                        </div>
                        <div class="card-body pt-4">
                            @if (!empty($details['is_planned']) && empty($details['is_active']))
                                <!-- Active Schedule Box -->
                                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                                    <i class="mdi mdi-clock-alert-outline fs-2 me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1 text-dark fw-bold">Jadwal Pemeliharaan Sedang Aktif!</h6>
                                        <p class="mb-0 small text-dark">
                                            Notifikasi modal dan top alert banner akan otomatis muncul ke seluruh user 
                                            <strong>{{ $details['plan_warn_minutes'] ?? 30 }} menit</strong> sebelum waktu mulai.
                                        </p>
                                    </div>
                                </div>

                                <div class="p-3 bg-lighter rounded mb-4 border">
                                    <div class="row g-2 small">
                                        <div class="col-sm-4 text-muted">Jadwal Mulai:</div>
                                        <div class="col-sm-8 fw-bold text-dark fs-6">{{ $details['plan_start_time'] }}</div>

                                        <div class="col-sm-4 text-muted">Estimasi Selesai:</div>
                                        <div class="col-sm-8 fw-semibold text-primary">{{ $details['plan_end_time'] ?? '-' }}</div>

                                        <div class="col-sm-4 text-muted">Muncul Peringatan:</div>
                                        <div class="col-sm-8 fw-semibold text-warning">
                                            {{ $details['plan_warn_minutes'] ?? 30 }} menit sebelum mulai
                                        </div>

                                        <div class="col-sm-4 text-muted">Pesan Peringatan:</div>
                                        <div class="col-sm-8">{{ $details['plan_message'] }}</div>

                                        <div class="col-sm-4 text-muted">Auto-Activate:</div>
                                        <div class="col-sm-8">
                                            <span class="badge {{ !empty($details['auto_activate']) ? 'bg-label-success' : 'bg-label-secondary' }}">
                                                {{ !empty($details['auto_activate']) ? 'Ya (Otomatis Hard Block)' : 'Tidak (Manual)' }}
                                            </span>
                                        </div>

                                        <div class="col-sm-4 text-muted">Dijadwalkan Oleh:</div>
                                        <div class="col-sm-8">{{ $details['planned_by'] ?? '-' }} ({{ $details['planned_at'] ?? '-' }})</div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <form action="{{ route('developer.maintenance.toggle') }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <input type="hidden" name="action" value="cancel_plan">
                                        <button type="submit" class="btn btn-outline-danger w-100"
                                            onclick="return confirm('Batalkan jadwal pemeliharaan ini?')">
                                            <i class="mdi mdi-close-circle-outline me-1"></i> Batalkan Jadwal
                                        </button>
                                    </form>

                                    <form action="{{ route('developer.maintenance.toggle') }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <input type="hidden" name="action" value="activate">
                                        <input type="hidden" name="message" value="{{ $details['plan_message'] }}">
                                        <input type="hidden" name="end_time" value="{{ $details['plan_end_time'] }}">
                                        <button type="submit" class="btn btn-danger w-100"
                                            onclick="return confirm('Mulai Hard Maintenance SEKARANG tanpa menunggu jadwal?')">
                                            <i class="mdi mdi-lightning-bolt me-1"></i> Mulai Sekarang (Force Start)
                                        </button>
                                    </form>
                                </div>
                            @else
                                <!-- Schedule Form -->
                                <p class="text-muted small mb-3">
                                    Atur jadwal pemeliharaan di masa depan. Sistem akan otomatis menampilkan <strong>Modal Popup Peringatan</strong> dan <strong>Top Alert Countdown Bar</strong> di layar semua user sebelum maintenance dimulai.
                                </p>

                                <form action="{{ route('developer.maintenance.toggle') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="schedule">

                                    <!-- Waktu Mulai -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold d-flex justify-content-between align-items-center" for="planStartTime">
                                            <span>1. Waktu Mulai Maintenance <span class="text-danger">*</span></span>
                                            <span class="text-muted small">Pilih cepat:</span>
                                        </label>
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPlanStartRel(30)">+30 Menit Lagi</button>
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPlanStartRel(60)">+1 Jam Lagi</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setPlanStartTime('12:00')">12:00 WIB</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setPlanStartTime('13:00')">13:00 WIB</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setPlanStartTime('18:00')">18:00 WIB</button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary" onclick="setPlanStartTime('22:00')">22:00 WIB</button>
                                        </div>
                                        <input type="text" class="form-control" id="planStartTime" name="plan_start_time"
                                            placeholder="Contoh: 12:00 WIB atau 12:00" required value="{{ old('plan_start_time') }}">
                                    </div>

                                    <!-- Estimasi Selesai -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold d-flex justify-content-between align-items-center" for="planEndTime">
                                            <span>2. Estimasi Waktu Selesai (Opsional)</span>
                                            <span class="text-muted small">Pilih durasi:</span>
                                        </label>
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPlanEndDuration(15)">+15 Menit</button>
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPlanEndDuration(30)">+30 Menit</button>
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPlanEndDuration(45)">+45 Menit</button>
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPlanEndDuration(60)">+1 Jam</button>
                                        </div>
                                        <input type="text" class="form-control" id="planEndTime" name="plan_end_time"
                                            placeholder="Contoh: 12:30 WIB atau 12:30" value="{{ old('plan_end_time') }}">
                                    </div>

                                    <!-- Warning Lead Time -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="planWarnMinutes">
                                            3. Waktu Muncul Peringatan ke Pengguna
                                        </label>
                                        <select class="form-select" id="planWarnMinutes" name="plan_warn_minutes">
                                            <option value="15">15 Menit sebelum waktu mulai</option>
                                            <option value="30" selected>30 Menit sebelum waktu mulai (Direkomendasikan)</option>
                                            <option value="45">45 Menit sebelum waktu mulai</option>
                                            <option value="60">60 Menit (1 Jam) sebelum waktu mulai</option>
                                        </select>
                                        <small class="text-muted">Modal dan banner peringatan akan otomatis muncul di layar semua user saat waktu ini tercapai.</small>
                                    </div>

                                    <!-- Pesan Pengumuman -->
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="planMessage">
                                            4. Pesan Pengumuman Peringatan
                                        </label>
                                        <textarea class="form-control" id="planMessage" name="plan_message" rows="2"
                                            placeholder="Pesan yang tampil di modal & banner">{{ old('plan_message', 'Pemberitahuan: Sistem akan memasuki masa pemeliharaan (Maintenance). Mohon segera selesaikan dan simpan pekerjaan atau transaksi Anda.') }}</textarea>
                                    </div>

                                    <!-- Auto-Activate Checkbox -->
                                    <div class="form-check mb-4">
                                        <input class="form-check-input" type="checkbox" id="autoActivateCheck" name="auto_activate" value="1" checked>
                                        <label class="form-check-label fw-semibold" for="autoActivateCheck">
                                            Otomatis aktifkan Hard Maintenance Mode begitu waktu mulai tiba
                                        </label>
                                        <div class="text-muted small">Jika dicentang, seluruh user otomatis ter-redirect ke halaman maintenance saat jam mulai tiba tanpa Anda perlu mengklik apapun.</div>
                                    </div>

                                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold text-dark waves-effect waves-light shadow-sm">
                                        <i class="mdi mdi-calendar-check me-1"></i> Simpan & Jadwalkan Pemeliharaan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Info Guide Tab 1 -->
                <div class="col-lg-5 mb-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom pb-3">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-lightbulb-outline me-2 text-warning"></i>Cara Kerja Planning Maintenance
                            </h5>
                        </div>
                        <div class="card-body pt-4">
                            <div class="d-flex align-items-start mb-3">
                                <div class="badge bg-label-warning rounded p-2 me-3">
                                    <i class="mdi mdi-bell-ring-outline fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">1. Peringatan Otomatis (Early Warning)</h6>
                                    <p class="small text-muted mb-0">Pada waktu peringatan (misal jam 11:30), otomatis muncul modal popup dan top alert bar di atas navbar semua user.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-3">
                                <div class="badge bg-label-warning rounded p-2 me-3">
                                    <i class="mdi mdi-timer-sand fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">2. Countdown Real-Time</h6>
                                    <p class="small text-muted mb-0">User dapat melihat hitung mundur sisa waktu persiapan di top banner saat mereka sedang beraktivitas.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-3">
                                <div class="badge bg-label-danger rounded p-2 me-3">
                                    <i class="mdi mdi-shield-lock-outline fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">3. Auto Hard Block (Jam Mulai)</h6>
                                    <p class="small text-muted mb-0">Saat jam mulai (misal jam 12:00) tiba, sistem otomatis mengalihkan seluruh user ke Halaman Maintenance 503.</p>
                                </div>
                            </div>

                            <div class="d-flex align-items-start mb-4">
                                <div class="badge bg-label-success rounded p-2 me-3">
                                    <i class="mdi mdi-account-check-outline fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold">4. Developer Bebas Akses</h6>
                                    <p class="small text-muted mb-0">Akun Anda (Developer) tetap bebas mengakses web untuk deployment & verifikasi data.</p>
                                </div>
                            </div>

                            <div class="alert alert-info py-2 small mb-0">
                                <i class="mdi mdi-information me-1"></i>Anda dapat membatalkan jadwal kapan saja sebelum waktu mulai tiba.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: IMMEDIATE MAINTENANCE (EKSEKUSI)    -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="tab-immediate" role="tabpanel">
            <div class="row">
                <div class="col-lg-7 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-3">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-server-network me-2 text-primary"></i>Kontrol Langsung (Instant Hard Block)
                            </h5>
                            <span class="badge bg-label-primary">Immediate Action</span>
                        </div>
                        <div class="card-body pt-4">
                            @if ($details['is_active'])
                                <!-- Active Alert Box -->
                                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                    <i class="mdi mdi-alert-circle-outline fs-3 me-3"></i>
                                    <div>
                                        <h6 class="alert-heading mb-1 text-danger fw-bold">Hard Maintenance Sedang Berjalan!</h6>
                                        <p class="mb-0 small">Seluruh role selain Developer (Admin, Sales, Warehouse, dll) saat ini diblokir dan diarahkan ke halaman pemeliharaan.</p>
                                    </div>
                                </div>

                                <div class="p-3 bg-lighter rounded mb-4 border">
                                    <div class="row g-2 small">
                                        <div class="col-sm-4 text-muted">Status:</div>
                                        <div class="col-sm-8 fw-bold text-danger">AKTIF (Blocked)</div>

                                        <div class="col-sm-4 text-muted">Tema Tampilan:</div>
                                        <div class="col-sm-8 fw-semibold text-primary">
                                            {{ $templateNames[$activeTemplate] ?? $activeTemplate }}
                                        </div>

                                        <div class="col-sm-4 text-muted">Pesan:</div>
                                        <div class="col-sm-8 fw-semibold">{{ $details['message'] }}</div>

                                        <div class="col-sm-4 text-muted">Estimasi Selesai:</div>
                                        <div class="col-sm-8 fw-semibold text-primary">{{ $details['end_time'] ?? '-' }}</div>

                                        <div class="col-sm-4 text-muted">Diaktifkan Sejak:</div>
                                        <div class="col-sm-8">{{ $details['started_at'] ?? '-' }}</div>

                                        <div class="col-sm-4 text-muted">Diaktifkan Oleh:</div>
                                        <div class="col-sm-8">{{ $details['started_by'] ?? '-' }}</div>
                                    </div>
                                </div>

                                <form action="{{ route('developer.maintenance.toggle') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="deactivate">
                                    <button type="submit" class="btn btn-success btn-lg w-100 waves-effect waves-light"
                                        onclick="return confirm('Nonaktifkan Maintenance Mode? Seluruh user akan dapat mengakses sistem kembali.')">
                                        <i class="mdi mdi-power me-2"></i> Nonaktifkan Maintenance Mode (Kembalikan Normal)
                                    </button>
                                </form>
                            @else
                                <p class="text-muted mb-4 small">
                                    Aktifkan mode pemeliharaan secara <strong>langsung detik ini juga</strong> tanpa jadwal.
                                    Hanya akun dengan role <strong>Developer</strong> yang dapat mengakses sistem.
                                </p>

                                <form action="{{ route('developer.maintenance.toggle') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action" value="activate">

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="maintenanceMessage">
                                            Pesan Pengumuman untuk Pengguna
                                        </label>
                                        <textarea class="form-control" id="maintenanceMessage" name="message" rows="3"
                                            placeholder="Contoh: Sistem sedang dalam proses pemeliharaan & pembaruan dari Staging ke Production.">{{ old('message', 'Sistem sedang dalam proses pemeliharaan & pembaruan dari Staging ke Production.') }}</textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label fw-semibold d-flex justify-content-between align-items-center" for="maintenanceEndTime">
                                            <span>Estimasi Waktu Selesai (Opsional)</span>
                                            <span class="text-muted small">Pilih cepat:</span>
                                        </label>
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPresetMinutes(15)">+15 Menit</button>
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPresetMinutes(30)">+30 Menit</button>
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPresetMinutes(45)">+45 Menit</button>
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPresetMinutes(60)">+1 Jam</button>
                                            <button type="button" class="btn btn-xs btn-outline-primary" onclick="setPresetMinutes(120)">+2 Jam</button>
                                        </div>
                                        <input type="text" class="form-control" id="maintenanceEndTime" name="end_time"
                                            placeholder="Contoh: 13:30 atau 13:30 WIB"
                                            value="{{ old('end_time') }}">
                                    </div>

                                    <button type="submit" class="btn btn-danger btn-lg w-100 waves-effect waves-light"
                                        onclick="return confirm('Aktifkan Maintenance Mode SEKARANG? User selain Developer akan langsung diblokir.')">
                                        <i class="mdi mdi-alert-octagon-outline me-2"></i> Aktifkan Hard Maintenance Sekarang
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-5 mb-4">
                    <div class="card h-100">
                        <div class="card-header border-bottom pb-3">
                            <h5 class="card-title mb-0">
                                <i class="mdi mdi-eye-outline me-2 text-primary"></i>Preview & Tautan
                            </h5>
                        </div>
                        <div class="card-body pt-4">
                            <p class="small text-muted mb-4">
                                Lihat bagaimana halaman maintenance akan tampil di layar pengguna saat mode pemeliharaan aktif.
                            </p>

                            <div class="d-grid gap-2">
                                <a href="{{ route('maintenance.page', ['template' => 'animated']) }}" target="_blank" class="btn btn-outline-primary py-2 text-start d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-creation me-2 text-primary"></i> Preview Template 3 (Dynamic & Animated)</span>
                                    <i class="mdi mdi-open-in-new"></i>
                                </a>
                                <a href="{{ route('maintenance.page', ['template' => 'light']) }}" target="_blank" class="btn btn-outline-info py-2 text-start d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-white-balance-sunny me-2 text-warning"></i> Preview Template 2 (Light Clean)</span>
                                    <i class="mdi mdi-open-in-new"></i>
                                </a>
                                <a href="{{ route('maintenance.page', ['template' => 'dark']) }}" target="_blank" class="btn btn-outline-secondary py-2 text-start d-flex justify-content-between align-items-center">
                                    <span><i class="mdi mdi-weather-night me-2 text-info"></i> Preview Template 1 (Dark Glass)</span>
                                    <i class="mdi mdi-open-in-new"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: THEMES & TEMPLATES (PILIHAN DESAIN) -->
        <!-- ========================================== -->
        <div class="tab-pane fade" id="tab-templates" role="tabpanel">
            <div class="row">
                <!-- Template 3: Light Dynamic & Motion Animated (New Hero) -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 {{ $activeTemplate === 'animated' ? 'border-primary shadow-lg' : 'border' }}" style="border-width: 2px;">
                        <div class="card-header pb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-label-primary px-2 py-1"><i class="mdi mdi-star me-1"></i>REKOMENDASI</span>
                                @if ($activeTemplate === 'animated')
                                    <span class="badge bg-primary px-2 py-1">
                                        <i class="mdi mdi-check-circle me-1"></i> AKTIF
                                    </span>
                                @endif
                            </div>
                            <h5 class="card-title mb-1 fw-bold text-primary">
                                <i class="mdi mdi-creation me-1"></i> Template 3: Dynamic & Animated
                            </h5>
                            <small class="text-muted">Desain cerah dengan animasi orb melayang, radar wave, orbiting tools, dan countdown interaktif.</small>
                        </div>
                        <div class="card-body">
                            <!-- Visual Mockup -->
                            <div class="rounded-3 p-3 mb-3 text-center border" style="background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%);">
                                <div class="bg-white rounded-3 p-3 shadow-sm border mx-auto" style="max-width: 240px;">
                                    <span class="badge bg-warning text-dark mb-2" style="font-size: 8px;">ANIMATED LIVE</span>
                                    <div class="avatar avatar-sm bg-label-primary mx-auto mb-2">
                                        <i class="mdi mdi-cog-sync text-primary"></i>
                                    </div>
                                    <div class="fw-bold text-dark small mb-1" style="font-size: 11px;">Pembaruan Dinamis</div>
                                    <div class="d-flex justify-content-center gap-1 mt-1">
                                        <div class="badge bg-primary text-white font-monospace" style="font-size: 9px;">00 : 25 : 10</div>
                                    </div>
                                </div>
                            </div>

                            <ul class="list-unstyled small text-muted mb-4">
                                <li class="mb-2"><i class="mdi mdi-check text-success me-1"></i> Floating background mesh orbs (CSS animation)</li>
                                <li class="mb-2"><i class="mdi mdi-check text-success me-1"></i> Orbiting tools (wrench & sync) & pulsing radar waves</li>
                                <li class="mb-2"><i class="mdi mdi-check text-success me-1"></i> Shimmering gradient status pill & flying particles</li>
                                <li><i class="mdi mdi-check text-success me-1"></i> Kartu countdown interaktif dengan efek hover elevation</li>
                            </ul>

                            <div class="d-flex gap-2">
                                @if ($activeTemplate !== 'animated')
                                    <form action="{{ route('developer.maintenance.toggle') }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <input type="hidden" name="action" value="change_template">
                                        <input type="hidden" name="template" value="animated">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="mdi mdi-check-circle-outline me-1"></i> Gunakan
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-label-primary w-100 disabled" disabled>
                                        <i class="mdi mdi-check-all me-1"></i> Digunakan
                                    </button>
                                @endif

                                <a href="{{ route('maintenance.page', ['template' => 'animated']) }}" target="_blank" class="btn btn-outline-secondary" title="Buka Pratinjau">
                                    <i class="mdi mdi-eye-outline me-1"></i> Preview
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template 2: Light Clean & Elegant -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 {{ $activeTemplate === 'light' ? 'border-primary shadow-lg' : 'border' }}" style="border-width: 2px;">
                        <div class="card-header pb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-label-info px-2 py-1">CLEAN & MINIMALIST</span>
                                @if ($activeTemplate === 'light')
                                    <span class="badge bg-primary px-2 py-1">
                                        <i class="mdi mdi-check-circle me-1"></i> AKTIF
                                    </span>
                                @endif
                            </div>
                            <h5 class="card-title mb-1 fw-bold text-dark">
                                <i class="mdi mdi-white-balance-sunny me-1 text-warning"></i> Template 2: Light Clean & Elegant
                            </h5>
                            <small class="text-muted">Konsep warna cerah, bersih, statis, dan bernuansa korporat premium.</small>
                        </div>
                        <div class="card-body">
                            <!-- Visual Mockup -->
                            <div class="rounded-3 p-3 mb-3 text-center border" style="background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);">
                                <div class="bg-white rounded-3 p-3 shadow-sm border mx-auto" style="max-width: 240px;">
                                    <span class="badge bg-warning text-dark mb-2" style="font-size: 8px;">PEMELIHARAAN SISTEM</span>
                                    <div class="avatar avatar-sm bg-label-primary mx-auto mb-2">
                                        <i class="mdi mdi-server-network"></i>
                                    </div>
                                    <div class="fw-bold text-dark small mb-1" style="font-size: 11px;">Pembaruan Sistem</div>
                                    <div class="d-flex justify-content-center gap-1 mt-1">
                                        <div class="badge bg-light text-primary border font-monospace" style="font-size: 9px;">00 : 25 : 10</div>
                                    </div>
                                </div>
                            </div>

                            <ul class="list-unstyled small text-muted mb-4">
                                <li class="mb-2"><i class="mdi mdi-check text-success me-1"></i> Background cerah dengan ambient glow halus</li>
                                <li class="mb-2"><i class="mdi mdi-check text-success me-1"></i> Kotak hitung mundur putih kontras dengan angka biru</li>
                                <li class="mb-2"><i class="mdi mdi-check text-success me-1"></i> Tipografi Plus Jakarta Sans & Inter modern</li>
                                <li><i class="mdi mdi-check text-success me-1"></i> Desain bersih tanpa banyak animasi bergerak</li>
                            </ul>

                            <div class="d-flex gap-2">
                                @if ($activeTemplate !== 'light')
                                    <form action="{{ route('developer.maintenance.toggle') }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <input type="hidden" name="action" value="change_template">
                                        <input type="hidden" name="template" value="light">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="mdi mdi-check-circle-outline me-1"></i> Gunakan
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-label-primary w-100 disabled" disabled>
                                        <i class="mdi mdi-check-all me-1"></i> Digunakan
                                    </button>
                                @endif

                                <a href="{{ route('maintenance.page', ['template' => 'light']) }}" target="_blank" class="btn btn-outline-secondary" title="Buka Pratinjau">
                                    <i class="mdi mdi-eye-outline me-1"></i> Preview
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Template 1: Modern Dark Glassmorphism -->
                <div class="col-lg-4 mb-4">
                    <div class="card h-100 {{ $activeTemplate === 'dark' ? 'border-primary shadow-lg' : 'border' }}" style="border-width: 2px;">
                        <div class="card-header pb-2">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-label-dark px-2 py-1">DARK GLASS</span>
                                @if ($activeTemplate === 'dark')
                                    <span class="badge bg-primary px-2 py-1">
                                        <i class="mdi mdi-check-circle me-1"></i> AKTIF
                                    </span>
                                @endif
                            </div>
                            <h5 class="card-title mb-1 fw-bold text-dark">
                                <i class="mdi mdi-weather-night me-1 text-info"></i> Template 1: Dark Glassmorphism
                            </h5>
                            <small class="text-muted">Konsep tema gelap, glassmorphism dengan efek cahaya cybernetic.</small>
                        </div>
                        <div class="card-body">
                            <!-- Visual Mockup -->
                            <div class="rounded-3 p-3 mb-3 text-center border" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                                <div class="rounded-3 p-3 border mx-auto" style="max-width: 240px; background: rgba(30, 41, 59, 0.85); border-color: rgba(255,255,255,0.1) !important;">
                                    <span class="badge bg-danger mb-2" style="font-size: 8px;">MAINTENANCE</span>
                                    <div class="avatar avatar-sm bg-label-info mx-auto mb-2">
                                        <i class="mdi mdi-cog-sync text-info"></i>
                                    </div>
                                    <div class="fw-bold text-white small mb-1" style="font-size: 11px;">Sistem Diperbarui</div>
                                    <div class="d-flex justify-content-center gap-1 mt-1">
                                        <div class="badge bg-dark text-info border border-secondary font-monospace" style="font-size: 9px;">00 : 25 : 10</div>
                                    </div>
                                </div>
                            </div>

                            <ul class="list-unstyled small text-muted mb-4">
                                <li class="mb-2"><i class="mdi mdi-check text-success me-1"></i> Background gelap slate dengan efek glow biru neon</li>
                                <li class="mb-2"><i class="mdi mdi-check text-success me-1"></i> Kartu kaca glassmorphism dengan backdrop blur</li>
                                <li class="mb-2"><i class="mdi mdi-check text-success me-1"></i> Ikon roda gigi berputar halus (smooth animation)</li>
                                <li><i class="mdi mdi-check text-success me-1"></i> Cybernetic futuristic look</li>
                            </ul>

                            <div class="d-flex gap-2">
                                @if ($activeTemplate !== 'dark')
                                    <form action="{{ route('developer.maintenance.toggle') }}" method="POST" class="flex-grow-1">
                                        @csrf
                                        <input type="hidden" name="action" value="change_template">
                                        <input type="hidden" name="template" value="dark">
                                        <button type="submit" class="btn btn-dark w-100">
                                            <i class="mdi mdi-check-circle-outline me-1"></i> Gunakan
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-label-dark w-100 disabled" disabled>
                                        <i class="mdi mdi-check-all me-1"></i> Digunakan
                                    </button>
                                @endif

                                <a href="{{ route('maintenance.page', ['template' => 'dark']) }}" target="_blank" class="btn btn-outline-secondary" title="Buka Pratinjau">
                                    <i class="mdi mdi-eye-outline me-1"></i> Preview
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Background Music (BGM) & Audio Upload Card -->
                <div class="col-12 mt-3">
                    <div class="card border">
                        <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-3">
                            <div>
                                <h5 class="card-title mb-1 fw-bold text-dark">
                                    <i class="mdi mdi-music-note-eighth me-2 text-primary"></i>Pengaturan Background Music (BGM)
                                </h5>
                                <small class="text-muted">Putar musik santai yang menenangkan di halaman maintenance untuk kenyamanan pengguna saat menunggu.</small>
                            </div>
                            <span class="badge {{ !empty($details['bgm_enabled']) ? 'bg-label-success' : 'bg-label-secondary' }}">
                                {{ !empty($details['bgm_enabled']) ? '🎵 BGM Aktif' : 'BGM Nonaktif' }}
                            </span>
                        </div>
                        <div class="card-body pt-4">
                            <form action="{{ route('developer.maintenance.toggle') }}" method="POST" id="bgmForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="action" value="update_bgm">

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="bgmEnabledSwitch" name="bgm_enabled" value="1" {{ !empty($details['bgm_enabled']) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold text-dark" for="bgmEnabledSwitch">
                                                Aktifkan Floating Music Player di Halaman Maintenance
                                            </label>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="bgmTitleInput">Judul Musik / Label Tampilan</label>
                                            <input type="text" class="form-control" id="bgmTitleInput" name="bgm_title"
                                                value="{{ old('bgm_title', $details['bgm_title'] ?? 'Lofi Ambient Relaxing - Water Lily') }}"
                                                placeholder="Contoh: Lofi Chill - Coffee Break">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">File Musik Saat Ini</label>
                                            <div class="p-2 bg-lighter rounded border d-flex align-items-center justify-content-between">
                                                <span class="small font-monospace text-truncate me-2 text-primary" id="currentBgmPath">
                                                    <i class="mdi mdi-file-music-outline me-1"></i>{{ $details['bgm_url'] ?? '/assets/audio/Water_Lily.mp3' }}
                                                </span>
                                                <audio id="dev-test-audio" preload="none">
                                                    <source src="{{ asset(ltrim($details['bgm_url'] ?? 'assets/audio/Water_Lily.mp3', '/')) }}" type="audio/mpeg">
                                                </audio>
                                                <button type="button" class="btn btn-xs btn-outline-primary" id="btn-test-play" onclick="testPlayBgm()">
                                                    <i class="mdi mdi-play me-1" id="test-play-icon"></i> <span id="test-play-text">Putar</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="bgmFileInput">
                                                <i class="mdi mdi-upload me-1 text-primary"></i>Unggah File Audio / MP3 Baru (.mp3, .wav, .ogg)
                                            </label>
                                            <input type="file" class="form-control" id="bgmFileInput" name="bgm_file" accept=".mp3,.wav,.ogg,.m4a,audio/*">
                                            <div id="bgmFileStatus" class="small text-muted mt-1">
                                                Pilih file MP3 untuk mengganti musik halaman maintenance.
                                            </div>
                                        </div>

                                        <!-- Chunked Upload Progress Bar -->
                                        <div id="bgmProgressWrap" class="progress my-2" style="display: none; height: 20px; border-radius: 10px;">
                                            <div id="bgmProgressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-primary fw-bold small" role="progressbar" style="width: 0%">0%</div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-semibold" for="bgmUrlInput">Atau Masukkan Path / URL Audio</label>
                                            <input type="text" class="form-control" id="bgmUrlInput" name="bgm_url"
                                                value="{{ old('bgm_url', $details['bgm_url'] ?? '/assets/audio/Water_Lily.mp3') }}"
                                                placeholder="/assets/audio/Water_Lily.mp3">
                                        </div>

                                        <button type="submit" class="btn btn-primary w-100 fw-bold" id="btnSubmitBgm">
                                            <i class="mdi mdi-content-save-outline me-1" id="iconSubmitBgm"></i> 
                                            <span id="textSubmitBgm">Simpan Pengaturan Musik (BGM)</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-script')
    <script>
        function setPresetMinutes(mins) {
            const now = new Date();
            now.setMinutes(now.getMinutes() + mins);

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            const input = document.getElementById('maintenanceEndTime');
            if (input) {
                input.value = `${hours}:${minutes} WIB`;
            }
        }

        function setPlanStartRel(mins) {
            const now = new Date();
            now.setMinutes(now.getMinutes() + mins);

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');

            const input = document.getElementById('planStartTime');
            if (input) {
                input.value = `${hours}:${minutes} WIB`;
            }
        }

        function setPlanStartTime(timeStr) {
            const input = document.getElementById('planStartTime');
            if (input) {
                input.value = `${timeStr} WIB`;
            }
        }

        function setPlanEndDuration(mins) {
            const startInput = document.getElementById('planStartTime');
            let baseDate = new Date();

            if (startInput && startInput.value) {
                const match = startInput.value.match(/(\d{1,2})[:.](\d{2})/);
                if (match) {
                    baseDate.setHours(parseInt(match[1], 10), parseInt(match[2], 10), 0, 0);
                }
            }

            baseDate.setMinutes(baseDate.getMinutes() + mins);
            const hours = String(baseDate.getHours()).padStart(2, '0');
            const minutes = String(baseDate.getMinutes()).padStart(2, '0');

            const endInput = document.getElementById('planEndTime');
            if (endInput) {
                endInput.value = `${hours}:${minutes} WIB`;
            }
        }

        function testPlayBgm() {
            const audio = document.getElementById('dev-test-audio');
            const icon = document.getElementById('test-play-icon');
            const text = document.getElementById('test-play-text');
            if (!audio) return;

            if (audio.paused) {
                audio.play().then(() => {
                    if (icon) icon.className = 'mdi mdi-pause me-1';
                    if (text) text.textContent = 'Jeda';
                }).catch(() => {});
            } else {
                audio.pause();
                if (icon) icon.className = 'mdi mdi-play me-1';
                if (text) text.textContent = 'Putar';
            }
        }

        // Chunked Audio Uploader & Auto-fill Title
        const bgmInput = document.getElementById('bgmFileInput');
        const bgmStatus = document.getElementById('bgmFileStatus');
        const bgmForm = document.getElementById('bgmForm');

        if (bgmInput) {
            bgmInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    const rawName = file.name.replace(/\.[^/.]+$/, "");
                    const sizeMb = (file.size / (1024 * 1024)).toFixed(2);

                    const titleInput = document.getElementById('bgmTitleInput');
                    if (titleInput) {
                        titleInput.value = rawName;
                    }

                    if (bgmStatus) {
                        bgmStatus.innerHTML = `<span class="text-success fw-semibold"><i class="mdi mdi-check-circle me-1"></i>File terpilih: <strong>${file.name}</strong> (${sizeMb} MB) — Siap disimpan.</span>`;
                    }
                }
            });
        }

        if (bgmForm) {
            bgmForm.addEventListener('submit', async function(e) {
                const file = bgmInput && bgmInput.files ? bgmInput.files[0] : null;
                if (!file) {
                    // No new file chosen, submit regular form (for toggle / title / url update)
                    return true;
                }

                e.preventDefault();

                const submitBtn = document.getElementById('btnSubmitBgm');
                const submitText = document.getElementById('textSubmitBgm');
                const submitIcon = document.getElementById('iconSubmitBgm');
                const progressBar = document.getElementById('bgmProgressBar');
                const progressWrap = document.getElementById('bgmProgressWrap');

                if (submitBtn) submitBtn.disabled = true;
                if (submitIcon) submitIcon.className = 'mdi mdi-loading mdi-spin me-1';
                if (progressWrap) progressWrap.style.display = 'block';

                const chunkSize = 512 * 1024; // 512 KB per chunk
                const totalChunks = Math.ceil(file.size / chunkSize);
                const fileUuid = 'bgm_' + Date.now() + '_' + Math.random().toString(36).substring(2, 8);
                const bgmTitle = document.getElementById('bgmTitleInput') ? document.getElementById('bgmTitleInput').value : '';
                const csrfToken = document.querySelector('input[name="_token"]').value;

                try {
                    for (let i = 0; i < totalChunks; i++) {
                        const start = i * chunkSize;
                        const end = Math.min(start + chunkSize, file.size);
                        const chunkBlob = file.slice(start, end);

                        const formData = new FormData();
                        formData.append('_token', csrfToken);
                        formData.append('file_uuid', fileUuid);
                        formData.append('chunk_index', i);
                        formData.append('total_chunks', totalChunks);
                        formData.append('filename', file.name);
                        formData.append('bgm_title', bgmTitle);
                        formData.append('chunk_file', chunkBlob, 'chunk.bin');

                        const percent = Math.round(((i + 1) / totalChunks) * 100);
                        if (progressBar) {
                            progressBar.style.width = percent + '%';
                            progressBar.textContent = percent + '%';
                        }
                        if (submitText) {
                            submitText.textContent = `Mengunggah... ${percent}% (${i + 1}/${totalChunks})`;
                        }

                        const res = await fetch('{{ route("developer.maintenance.upload_chunk") }}', {
                            method: 'POST',
                            body: formData
                        });

                        const data = await res.json();
                        if (!data.success) {
                            throw new Error(data.message || 'Gagal mengunggah potongan audio.');
                        }

                        if (i === totalChunks - 1) {
                            if (submitText) submitText.textContent = 'Menyimpan Audio...';
                            if (progressBar) {
                                progressBar.className = 'progress-bar bg-success fw-bold';
                                progressBar.textContent = 'Selesai 100%';
                            }
                            
                            // Success reload
                            setTimeout(() => {
                                window.location.href = '{{ route("developer.maintenance.index") }}';
                            }, 600);
                            return;
                        }
                    }
                } catch (err) {
                    alert('Gagal mengunggah file audio: ' + err.message);
                    if (submitBtn) submitBtn.disabled = false;
                    if (submitIcon) submitIcon.className = 'mdi mdi-content-save-outline me-1';
                    if (submitText) submitText.textContent = 'Simpan Pengaturan Musik (BGM)';
                    if (progressWrap) progressWrap.style.display = 'none';
                }
            });
        }
    </script>
@endpush
