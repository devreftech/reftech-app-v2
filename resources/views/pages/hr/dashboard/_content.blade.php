<!-- Header Banner -->
<div class="card bg-primary text-white border-0 shadow-sm mb-4" style="border-radius: 14px; background: linear-gradient(135deg, #696cff 0%, #3f42c2 100%) !important;">
    <div class="card-body p-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
            <div>
                <span class="badge bg-white text-primary mb-2 fw-semibold px-3 py-1">Enterprise HRMS Suite</span>
                <h3 class="text-white fw-bold mb-1">Human Resource Management Hub</h3>
                <p class="mb-0 text-white-50">Sentralisasi manajemen data karyawan, absensi real-time, cuti, payroll terotomatisasi, dan portal mandiri.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                @if (Auth::user()?->employee)
                <a href="{{ route('hr.portal.index') }}" class="btn btn-light text-primary fw-semibold">
                    <i class="mdi mdi-account-circle-outline me-1"></i> Portal Mandiri Saya
                </a>
                @endif
                <a href="{{ route('employees.create') }}" class="btn btn-warning text-dark fw-semibold">
                    <i class="mdi mdi-account-plus-outline me-1"></i> Tambah Karyawan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Top KPI Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card hr-stat-card">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Total Karyawan</span>
                    <h4 class="mb-0 fw-bold mt-1 text-primary">{{ $totalEmployees ?? 0 }}</h4>
                    <span class="text-success small fw-semibold"><i class="mdi mdi-check-circle-outline"></i> {{ $activeEmployees ?? 0 }} Karyawan Aktif</span>
                </div>
                <div class="hr-action-icon bg-label-primary">
                    <i class="mdi mdi-account-group-outline text-primary"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card hr-stat-card">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Hadir Hari Ini</span>
                    <h4 class="mb-0 fw-bold mt-1 text-success">{{ $todayAttendances ?? 0 }}</h4>
                    <span class="text-warning small fw-semibold"><i class="mdi mdi-clock-alert-outline"></i> {{ $lateToday ?? 0 }} Terlambat</span>
                </div>
                <div class="hr-action-icon bg-label-success">
                    <i class="mdi mdi-calendar-check-outline text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card hr-stat-card">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Approval Cuti</span>
                    <h4 class="mb-0 fw-bold mt-1 {{ ($pendingLeaves ?? 0) > 0 ? 'text-danger' : 'text-muted' }}">{{ $pendingLeaves ?? 0 }}</h4>
                    <span class="text-muted small">Menunggu Review HR</span>
                </div>
                <div class="hr-action-icon bg-label-warning">
                    <i class="mdi mdi-calendar-clock-outline text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card hr-stat-card">
            <div class="card-body d-flex align-items-center justify-content-between p-3">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Klaim Reimburse</span>
                    <h4 class="mb-0 fw-bold mt-1 {{ ($pendingReimbursements ?? 0) > 0 ? 'text-info' : 'text-muted' }}">{{ $pendingReimbursements ?? 0 }}</h4>
                    <span class="text-muted small">Rp {{ number_format($pendingReimbursementAmount ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="hr-action-icon bg-label-info">
                    <i class="mdi mdi-receipt-text-outline text-info"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Navigation to All HR Modules -->
<h5 class="fw-bold mb-3"><i class="mdi mdi-view-grid-outline me-1 text-primary"></i> Modul &amp; Operasional HR</h5>
<div class="row g-3 mb-4">
    <!-- 1. Hub Karyawan -->
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('employees.index') }}" class="card hr-action-card p-3">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="hr-action-icon bg-label-primary">
                    <i class="mdi mdi-account-group-outline text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Hub Karyawan</h6>
                    <small class="text-muted">Direktori &amp; Master Data</small>
                </div>
            </div>
            <p class="text-muted small mb-0">Kelola data profil, NIK, jabatan, departemen, dan dokumen karyawan.</p>
        </a>
    </div>

    <!-- 2. Presensi & Absensi -->
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('hr.attendances.index') }}" class="card hr-action-card p-3">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="hr-action-icon bg-label-success">
                    <i class="mdi mdi-calendar-clock-outline text-success"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Presensi &amp; Kehadiran</h6>
                    <small class="text-muted">Log Harian &amp; Shift</small>
                </div>
            </div>
            <p class="text-muted small mb-0">Monitor absensi masuk/pulang, keterlambatan, dan pencatatan lokasi kerja.</p>
        </a>
    </div>

    <!-- 3. Cuti & Izin -->
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('hr.leaves.index') }}" class="card hr-action-card p-3">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="hr-action-icon bg-label-warning">
                    <i class="mdi mdi-calendar-remove-outline text-warning"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Cuti &amp; Perizinan</h6>
                    <small class="text-muted">Workflow Approval</small>
                </div>
            </div>
            <p class="text-muted small mb-0">Kelola kuota cuti tahunan, izin khusus, dan persetujuan HR.</p>
        </a>
    </div>

    <!-- 4. Payroll Engine -->
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('hr.payrolls.index') }}" class="card hr-action-card p-3">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="hr-action-icon bg-label-info">
                    <i class="mdi mdi-cash-multiple text-info"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Payroll &amp; Gaji</h6>
                    <small class="text-muted">Batch Generate &amp; Slip</small>
                </div>
            </div>
            <p class="text-muted small mb-0">Kalkulasi gaji otomatis, BPJS, tunjangan, PPh, dan cetak slip digital.</p>
        </a>
    </div>

    <!-- 5. Bonus Semesteran -->
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('hr.bonuses.index') }}" class="card hr-action-card p-3">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="hr-action-icon bg-label-primary">
                    <i class="mdi mdi-gift-outline text-primary"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Bonus Semesteran</h6>
                    <small class="text-muted">Reward &amp; Insentif</small>
                </div>
            </div>
            <p class="text-muted small mb-0">Distribusi alokasi bonus berkala, insentif performa, dan integrasi pengeluaran.</p>
        </a>
    </div>

    <!-- 6. Reimbursement -->
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('hr.reimbursements.index') }}" class="card hr-action-card p-3">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="hr-action-icon bg-label-danger">
                    <i class="mdi mdi-receipt-text-outline text-danger"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Reimbursement</h6>
                    <small class="text-muted">Klaim Operasional</small>
                </div>
            </div>
            <p class="text-muted small mb-0">Pengajuan klaim dinas, bensin, medis, lampiran nota, dan status pencairan.</p>
        </a>
    </div>

    <!-- 7. Alat Kerja -->
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('hr.assets.index') }}" class="card hr-action-card p-3">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="hr-action-icon bg-label-secondary">
                    <i class="mdi mdi-laptop text-secondary"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Inventaris Alat Kerja</h6>
                    <small class="text-muted">Asset Tracking</small>
                </div>
            </div>
            <p class="text-muted small mb-0">Pendataan laptop, kendaraan, alat teknisi, serial number, dan kondisi aset.</p>
        </a>
    </div>

    <!-- 8. Evaluasi Kinerja -->
    <div class="col-md-6 col-lg-3">
        <a href="{{ route('hr.evaluations.index') }}" class="card hr-action-card p-3">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="hr-action-icon bg-label-dark">
                    <i class="mdi mdi-star-circle-outline text-dark"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Evaluasi Kinerja</h6>
                    <small class="text-muted">KPI &amp; Review Berkala</small>
                </div>
            </div>
            <p class="text-muted small mb-0">Review performa karyawan, penilaian skor KPI, rating, dan rekomendasi karir.</p>
        </a>
    </div>
</div>

<!-- Live Feeds Row -->
<div class="row g-4">
    <!-- Presensi Terkini Hari Ini -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-2">
                <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-clock-outline text-success me-1"></i> Presensi Masuk Hari Ini</h6>
                <a href="{{ route('hr.attendances.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Karyawan</th>
                                <th>Jam Masuk</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAttendances ?? [] as $att)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $att->employee?->user?->image ? asset('storage/'.$att->employee->user->image) : asset('assets/img/avatars/1.png') }}" class="avatar-sm">
                                        <div>
                                            <div class="fw-semibold text-dark">{{ $att->employee?->user?->name ?? 'Karyawan #'.$att->employee_id }}</div>
                                            <small class="text-muted">{{ $att->employee?->nik ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($att->clock_in)->format('H:i') }}</span>
                                </td>
                                <td>
                                    @if($att->status === 'present' || $att->status === 'Hadir')
                                        <span class="badge bg-label-success">Tepat Waktu</span>
                                    @elseif($att->status === 'late' || ($att->late_minutes ?? 0) > 0)
                                        <span class="badge bg-label-warning">Terlambat</span>
                                    @else
                                        <span class="badge bg-label-secondary">{{ ucfirst($att->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-calendar-blank-outline fs-3 d-block mb-1"></i>
                                    Belum ada presensi tercatat hari ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengajuan Cuti & Izin Terbaru -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header d-flex align-items-center justify-content-between pb-2">
                <h6 class="card-title mb-0 fw-bold"><i class="mdi mdi-calendar-clock-outline text-warning me-1"></i> Pengajuan Cuti Terbaru</h6>
                <a href="{{ route('hr.leaves.index') }}" class="btn btn-sm btn-outline-primary">Kelola Cuti</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Karyawan</th>
                                <th>Tipe Cuti</th>
                                <th>Durasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentLeaves ?? [] as $leave)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $leave->employee?->user?->name ?? 'Karyawan' }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-label-info">{{ $leave->leaveType?->name ?? 'Cuti' }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $leave->total_days }} Hari</span>
                                </td>
                                <td>
                                    @if(strtolower($leave->status) === 'approved')
                                        <span class="badge bg-label-success">Disetujui</span>
                                    @elseif(strtolower($leave->status) === 'pending')
                                        <span class="badge bg-label-warning">Pending</span>
                                    @else
                                        <span class="badge bg-label-danger">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="mdi mdi-calendar-check-outline fs-3 d-block mb-1"></i>
                                    Tidak ada permohonan cuti aktif.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
