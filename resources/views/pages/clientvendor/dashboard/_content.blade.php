{{-- Dashboard Client Vendor View --}}
<div class="row gy-4 mb-4">
    <!-- Welcome & Hero Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; border-radius: 16px; overflow: hidden; position: relative;">
            <div class="card-body p-4 p-md-5 position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-lg-8 col-md-7">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary px-3 py-1 rounded-pill fs-7 fw-semibold shadow-xs">
                                <i class="mdi mdi-domain me-1"></i> Client Vendor Portal
                            </span>
                            <span class="badge bg-label-info px-2 py-1 rounded-pill fs-8">
                                {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                            </span>
                        </div>
                        <h2 class="text-white fw-bold mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
                        <p class="text-white-50 mb-4 fs-6" style="max-width: 600px;">
                            Kelola dan pantau seluruh pelaporan proyek harian (Daily Project Report) dengan mudah dan terorganisir.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('project-reports.create') }}" class="btn btn-primary rounded-pill px-4 py-2 waves-effect waves-light shadow-sm d-flex align-items-center gap-2">
                                <i class="mdi mdi-plus-circle-outline fs-5"></i>
                                <span class="fw-semibold">Buat Daily Report Baru</span>
                            </a>
                            <a href="#section-project-reports" class="btn btn-outline-light rounded-pill px-3 py-2 waves-effect d-flex align-items-center gap-2">
                                <i class="mdi mdi-table-eye fs-5"></i>
                                <span>Lihat Tabel Laporan</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-5 text-center text-md-end mt-4 mt-md-0">
                        <img src="{{ asset('assets') }}/img/illustrations/faq-illustration.png" 
                             alt="Daily Project Report" class="img-fluid" style="max-height: 160px; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.3));">
                    </div>
                </div>
            </div>
            <!-- Background Decorative Shape -->
            <div class="position-absolute end-0 top-0 bottom-0 opacity-10 pointer-events-none" style="width: 300px; background: radial-gradient(circle, #6366f1 0%, transparent 70%);"></div>
        </div>
    </div>

    <!-- Quick Stat KPI Cards -->
    <div class="col-sm-6 col-xl-3">
        <div class="card clean-card h-100 border-0 shadow-xs">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-primary rounded-circle">
                            <i class="mdi mdi-clipboard-text-clock-outline fs-4"></i>
                        </div>
                    </div>
                    <span class="badge bg-label-primary rounded-pill">Total Semua</span>
                </div>
                <h3 class="fw-bold mb-1 text-dark">{{ number_format($dailyReportsTotal ?? 0) }}</h3>
                <small class="text-muted">Total Daily Project Reports</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card clean-card h-100 border-0 shadow-xs">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-info rounded-circle">
                            <i class="mdi mdi-calendar-month-outline fs-4"></i>
                        </div>
                    </div>
                    <span class="badge bg-label-info rounded-pill">Bulan Ini</span>
                </div>
                <h3 class="fw-bold mb-1 text-info">{{ number_format($dailyReportsThisMonth ?? 0) }}</h3>
                <small class="text-muted">Laporan Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card clean-card h-100 border-0 shadow-xs">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-success rounded-circle">
                            <i class="mdi mdi-check-decagram-outline fs-4"></i>
                        </div>
                    </div>
                    <span class="badge bg-label-success rounded-pill">Completed</span>
                </div>
                <h3 class="fw-bold mb-1 text-success">{{ number_format($dailyReportsCompleted ?? 0) }}</h3>
                <small class="text-muted">Laporan Selesai / Disetujui</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card clean-card h-100 border-0 shadow-xs">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="avatar">
                        <div class="avatar-initial bg-label-warning rounded-circle">
                            <i class="mdi mdi-file-edit-outline fs-4"></i>
                        </div>
                    </div>
                    <span class="badge bg-label-warning rounded-pill">Draft</span>
                </div>
                <h3 class="fw-bold mb-1 text-warning">{{ number_format($dailyReportsDraft ?? 0) }}</h3>
                <small class="text-muted">Laporan Status Draft</small>
            </div>
        </div>
    </div>
</div>

<!-- Main Section: Daily Project Reports Datatable -->
<div class="row" id="section-project-reports">
    <div class="col-12">
        <div class="card clean-card border-0 shadow-sm">
            <div class="card-header pb-3 d-flex align-items-center justify-content-between flex-wrap gap-3 border-bottom">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-md">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="mdi mdi-clipboard-text-outline fs-3"></i>
                        </span>
                    </div>
                    <div>
                        <h5 class="card-title mb-0 fw-bold">Daftar Daily Project Reports</h5>
                        <small class="text-muted">Tabel seluruh data laporan proyek harian beserta status dan aksinya</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('project-reports.create') }}" class="btn btn-primary rounded-pill px-3 waves-effect waves-light shadow-xs">
                        <i class="mdi mdi-plus me-1"></i> Buat Laporan Harian
                    </a>
                </div>
            </div>

            <div class="card-datatable table-responsive p-3">
                <table class="datatable-project-reports table table-hover border-top">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">#</th>
                            <th>Judul Proyek / Job & Kontrak</th>
                            <th>Tanggal & Hari</th>
                            <th>Dibuat Oleh</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Loaded via AJAX DataTables --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <style>
        .clean-card {
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            border-radius: 16px !important;
        }
        .clean-card:hover {
            border-color: rgba(105, 108, 255, 0.3) !important;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="{{ asset('assets') }}/includes/table-project-reports.js?v={{ file_exists(public_path('assets/includes/table-project-reports.js')) ? filemtime(public_path('assets/includes/table-project-reports.js')) : time() }}"></script>
@endpush
