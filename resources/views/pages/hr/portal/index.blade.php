@extends('layouts.sales.app')
@section('title', 'Portal Mandiri Karyawan (ESS) - ' . ($employee->user?->name ?? 'HRM'))

@section('content')
@php
    $empName = $employee->user?->name ?? ($employee->nik ? 'Karyawan ' . $employee->nik : 'Karyawan #' . $employee->id);
    $words = array_filter(explode(' ', trim($empName)));
    $inits = '';
    foreach (array_slice($words, 0, 2) as $w) {
        $inits .= strtoupper(substr($w, 0, 1));
    }
    if (empty($inits)) $inits = 'KR';
@endphp

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-xs border-0 mb-4" role="alert">
        <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-xs border-0 mb-4" role="alert">
        <i class="mdi mdi-alert-circle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- ── HERO PROFILE & CLOCK WIDGET ───────────────────────────────────── --}}
<div class="card border-0 shadow-sm mb-4 overflow-hidden">
    <div style="height: 90px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);"></div>
    <div class="card-body px-4 pb-4 pt-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-end gap-3" style="margin-top: -45px;">
            <div class="d-flex align-items-end gap-3">
                <div class="position-relative">
                    @if ($employee->user?->image && file_exists(public_path($employee->user->image)))
                        <img src="{{ asset($employee->user->image) }}" alt="{{ $empName }}"
                             class="rounded-circle shadow" style="width: 95px; height: 95px; object-fit: cover; border: 4px solid #fff; background: #fff;">
                    @else
                        <div class="rounded-circle shadow d-inline-flex align-items-center justify-content-center bg-label-primary text-primary fw-bold"
                             style="width: 95px; height: 95px; font-size: 2rem; border: 4px solid #fff; background: #fff;">
                            {{ $inits }}
                        </div>
                    @endif
                </div>
                <div class="mb-1">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <h4 class="fw-bold text-heading mb-0">{{ $empName }}</h4>
                        <span class="badge bg-label-primary">{{ $employee->employment_status }}</span>
                    </div>
                    <div class="text-muted small mt-1">
                        <span><i class="mdi mdi-domain me-1"></i>{{ $employee->department?->name ?? '-' }}</span> &bull;
                        <span><i class="mdi mdi-briefcase me-1"></i>{{ $employee->position?->name ?? '-' }}</span> &bull;
                        <span><i class="mdi mdi-barcode me-1"></i>NIK: {{ $employee->nik ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Clock-In Widget --}}
            <div class="p-3 rounded bg-light border text-center text-md-end w-100 w-md-auto">
                <div class="small text-muted mb-1 d-flex align-items-center justify-content-center justify-content-md-end gap-1">
                    <i class="mdi mdi-calendar-today text-primary"></i>
                    <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                    <span class="fw-bold text-dark font-monospace ms-2" id="liveTimeClock"></span>
                </div>

                {{-- WiFi Network Status Indicator --}}
                @if ($employee->can_online_attendance && isset($isWifiRestrictionEnabled) && $isWifiRestrictionEnabled)
                    <div class="mb-2 d-flex align-items-center justify-content-center justify-content-md-end">
                        @if ($isWifiVerified)
                            <span class="badge bg-label-success fs-8 d-inline-flex align-items-center shadow-2xs" title="IP Terdeteksi: {{ $clientIp }}">
                                <i class="mdi mdi-wifi-check me-1"></i> WiFi Kantor Terverifikasi
                            </span>
                        @else
                            <span class="badge bg-label-danger fs-8 d-inline-flex align-items-center shadow-2xs" title="IP Terdeteksi: {{ $clientIp }}. Hubungkan ke WiFi kantor untuk presensi.">
                                <i class="mdi mdi-wifi-alert me-1"></i> Di Luar Jaringan WiFi Kantor
                            </span>
                        @endif
                    </div>
                @endif

                <div class="d-flex align-items-center justify-content-center justify-content-md-end gap-2 mt-1">
                    @if (!$employee->can_online_attendance)
                        <span class="badge bg-label-secondary py-2 px-3 fs-7">
                            <i class="mdi mdi-shield-account-outline me-1"></i> Bebas Presensi Online
                        </span>
                    @elseif (!$todayAttendance || !$todayAttendance->clock_in)
                        <form action="{{ route('hr.portal.clockin') }}" method="POST">
                            @csrf
                            <input type="hidden" name="work_type" value="WFO">
                            <button type="submit" class="btn btn-success shadow-xs px-3" {{ (isset($isWifiRestrictionEnabled) && $isWifiRestrictionEnabled && !$isWifiVerified) ? 'disabled title="Hubungkan ke WiFi Kantor untuk Clock In"' : '' }}>
                                <i class="mdi mdi-clock-in me-1"></i> Presensi Masuk (Clock In)
                            </button>
                        </form>
                    @elseif ($todayAttendance && !$todayAttendance->clock_out)
                        <div class="small me-2 text-success fw-semibold">
                            <i class="mdi mdi-check-circle me-1"></i>Masuk: {{ substr($todayAttendance->clock_in, 0, 5) }}
                        </div>
                        <form action="{{ route('hr.portal.clockout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning shadow-xs px-3" {{ (isset($isWifiRestrictionEnabled) && $isWifiRestrictionEnabled && !$isWifiVerified) ? 'disabled title="Hubungkan ke WiFi Kantor untuk Clock Out"' : '' }}>
                                <i class="mdi mdi-clock-out me-1"></i> Presensi Pulang (Clock Out)
                            </button>
                        </form>
                    @else
                        <span class="badge bg-label-success py-2 px-3 fs-7">
                            <i class="mdi mdi-check-all me-1"></i> Presensi Lengkap ({{ substr($todayAttendance->clock_in, 0, 5) }} - {{ substr($todayAttendance->clock_out, 0, 5) }})
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── STATS MINI ROW ────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Sisa Kuota Cuti {{ date('Y') }}</span>
                <h4 class="fw-bold text-primary mb-0">{{ $leaveBalance->remaining_quota }} Hari</h4>
                <div class="text-muted small mt-1" style="font-size:0.75rem;">Dari kuota {{ $leaveBalance->total_quota }} hari</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Kehadiran Bulan Ini</span>
                <h4 class="fw-bold text-success mb-0">{{ $monthAttendances->where('status', 'Hadir')->count() }} Hari</h4>
                <div class="text-muted small mt-1" style="font-size:0.75rem;">Status hadir tercatat</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Slip Gaji Tersedia</span>
                <h4 class="fw-bold text-info mb-0">{{ $myPayslips->count() }} Periode</h4>
                <div class="text-muted small mt-1" style="font-size:0.75rem;">Arsip slip digital</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
                <span class="text-muted small fw-semibold d-block mb-1">Alat Kerja Dipinjamkan</span>
                <h4 class="fw-bold text-warning mb-0">{{ $myAssets->count() }} Unit</h4>
                <div class="text-muted small mt-1" style="font-size:0.75rem;">Fasilitas operasional</div>
            </div>
        </div>
    </div>
</div>

{{-- ── TABBED ESS CONTENT ─────────────────────────────────────────────── --}}
<div class="nav-align-top">
    <ul class="nav nav-tabs nav-fill mb-3" role="tablist">
        <li class="nav-item">
            <button type="button" class="nav-link active py-2" role="tab" data-bs-toggle="tab" data-bs-target="#tabEssAttendance">
                <i class="mdi mdi-calendar-clock-outline me-2"></i> Presensi Saya
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link py-2" role="tab" data-bs-toggle="tab" data-bs-target="#tabEssLeave">
                <i class="mdi mdi-calendar-remove-outline me-2"></i> Cuti &amp; Izin
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link py-2" role="tab" data-bs-toggle="tab" data-bs-target="#tabEssPayslip">
                <i class="mdi mdi-cash-multiple me-2"></i> Slip Gaji
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link py-2" role="tab" data-bs-toggle="tab" data-bs-target="#tabEssClaims">
                <i class="mdi mdi-receipt-text-outline me-2"></i> Klaim Biaya
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link py-2" role="tab" data-bs-toggle="tab" data-bs-target="#tabEssAssets">
                <i class="mdi mdi-laptop me-2"></i> Alat Kerja
            </button>
        </li>
    </ul>

    <div class="tab-content p-0 bg-transparent border-0">
        {{-- TAB 1: Presensi --}}
        <div class="tab-pane fade show active" id="tabEssAttendance" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold">Riwayat Kehadiran Bulan Ini ({{ \Carbon\Carbon::now()->translatedFormat('F Y') }})</h6>
                    <span class="badge bg-label-primary">{{ $monthAttendances->count() }} Hari Tercatat</span>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Tipe Kerja</th>
                                <th>Status</th>
                                <th>Keterlambatan / Lembur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($monthAttendances as $att)
                                <tr>
                                    <td>
                                        <span class="fw-semibold text-heading">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d M Y') }}</span>
                                    </td>
                                    <td>
                                        @if ($att->clock_in)
                                            <span class="badge bg-label-dark font-monospace">{{ substr($att->clock_in, 0, 5) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($att->clock_out)
                                            <span class="badge bg-label-dark font-monospace">{{ substr($att->clock_out, 0, 5) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-label-secondary small">{{ $att->work_type }}</span></td>
                                    <td>
                                        <span class="badge bg-label-{{ $att->status === 'Hadir' ? 'success' : ($att->status === 'Izin' ? 'info' : 'warning') }}">
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($att->late_minutes > 0)
                                            <span class="badge bg-label-warning me-1">Telat {{ $att->late_minutes }}m</span>
                                        @endif
                                        @if ($att->overtime_minutes > 0)
                                            <span class="badge bg-label-info">Lembur {{ round($att->overtime_minutes / 60, 1) }}j</span>
                                        @endif
                                        @if ($att->late_minutes == 0 && $att->overtime_minutes == 0)
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat presensi bulan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 2: Cuti --}}
        <div class="tab-pane fade" id="tabEssLeave" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold">Riwayat Pengajuan Cuti &amp; Izin</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEssNewLeave">
                        <i class="mdi mdi-plus me-1"></i> Ajukan Cuti Baru
                    </button>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Cuti</th>
                                <th>Tanggal Pelaksanaan</th>
                                <th>Durasi</th>
                                <th>Alasan</th>
                                <th>Status</th>
                                <th>Catatan HR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($myLeaves as $l)
                                @php
                                    $badge = match ($l->status) {
                                        'Approved' => 'success',
                                        'Rejected' => 'danger',
                                        default => 'warning',
                                    };
                                @endphp
                                <tr>
                                    <td><span class="badge bg-label-info">{{ $l->leaveType->name }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($l->start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($l->end_date)->format('d M Y') }}</td>
                                    <td><strong>{{ $l->total_days }} Hari</strong></td>
                                    <td>{{ $l->reason }}</td>
                                    <td><span class="badge bg-label-{{ $badge }}">{{ $l->status }}</span></td>
                                    <td><span class="small text-muted">{{ $l->rejection_note ?: '-' }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada pengajuan cuti yang diajukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 3: Slip Gaji --}}
        <div class="tab-pane fade" id="tabEssPayslip" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold">Arsip Slip Gaji Elektronik</h6>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Slip</th>
                                <th>Periode Gaji</th>
                                <th>Gaji Pokok</th>
                                <th>Tunjangan</th>
                                <th>Potongan</th>
                                <th>Gaji Bersih (Net)</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($myPayslips as $slip)
                                @php
                                    $totDeduct = $slip->deduction_absence + $slip->deduction_bpjs + $slip->deduction_other;
                                @endphp
                                <tr>
                                    <td class="font-monospace fw-bold text-primary">{{ $slip->slip_number }}</td>
                                    <td>{{ $slip->payroll->title }}</td>
                                    <td class="font-monospace">Rp {{ number_format($slip->basic_salary, 0, ',', '.') }}</td>
                                    <td class="font-monospace text-info">+Rp {{ number_format($slip->total_allowance, 0, ',', '.') }}</td>
                                    <td class="font-monospace text-danger">-Rp {{ number_format($totDeduct, 0, ',', '.') }}</td>
                                    <td class="font-monospace fw-bold text-success">Rp {{ number_format($slip->net_salary, 0, ',', '.') }}</td>
                                    <td><span class="badge bg-label-{{ $slip->payment_status === 'Paid' ? 'success' : 'secondary' }}">{{ $slip->payment_status }}</span></td>
                                    <td class="text-end">
                                        <a href="{{ route('hr.payrolls.slip', $slip->id) }}" target="_blank" class="btn btn-sm btn-label-primary">
                                            <i class="mdi mdi-printer me-1"></i> Buka Slip
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">Belum ada arsip slip gaji digital.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 4: Klaim --}}
        <div class="tab-pane fade" id="tabEssClaims" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom py-3 bg-light d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold">Riwayat Klaim Reimbursement Saya</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalEssNewClaim">
                        <i class="mdi mdi-plus me-1"></i> Ajukan Klaim
                    </button>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Klaim</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($myReimbursements as $claim)
                                <tr>
                                    <td class="font-monospace text-primary fw-semibold">{{ $claim->claim_number }}</td>
                                    <td><span class="badge bg-label-secondary">{{ $claim->claim_type }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($claim->event_date)->format('d M Y') }}</td>
                                    <td class="font-monospace fw-bold">Rp {{ number_format($claim->amount, 0, ',', '.') }}</td>
                                    <td>{{ $claim->description }}</td>
                                    <td>
                                        <span class="badge bg-label-{{ $claim->status === 'Paid' ? 'success' : ($claim->status === 'Approved' ? 'info' : 'warning') }}">
                                            {{ $claim->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pengajuan klaim biaya.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TAB 5: Aset --}}
        <div class="tab-pane fade" id="tabEssAssets" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header border-bottom py-3 bg-light">
                    <h6 class="card-title mb-0 fw-bold">Daftar Alat Kerja &amp; Inventaris Yang Anda Pegang</h6>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Alat / Fasilitas</th>
                                <th>Kode &amp; Serial Number</th>
                                <th>Kondisi Fisik</th>
                                <th>Tanggal Serah Terima</th>
                                <th>Catatan Kelengkapan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($myAssets as $asset)
                                <tr>
                                    <td><div class="fw-bold text-heading">{{ $asset->asset_name }}</div></td>
                                    <td><span class="font-monospace text-primary">{{ $asset->asset_code ?: '-' }}</span> (S/N: {{ $asset->serial_number ?: '-' }})</td>
                                    <td><span class="badge bg-label-success">{{ $asset->condition }}</span></td>
                                    <td>{{ \Carbon\Carbon::parse($asset->handover_date)->format('d M Y') }}</td>
                                    <td><span class="small text-muted">{{ $asset->notes ?: 'Lengkap' }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Tidak ada catatan peminjaman alat kerja saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Mandiri Cuti --}}
<div class="modal fade" id="modalEssNewLeave" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('hr.leaves.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold">Ajukan Cuti / Izin Mandiri</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Jenis Cuti</label>
                        <select name="leave_type_id" class="form-select" required>
                            @foreach ($leaveTypes as $lt)
                                <option value="{{ $lt->id }}">{{ $lt->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tanggal Berakhir</label>
                        <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Alasan</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Alasan keperluan..." required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Lampiran (Surat Dokter jika sakit)</label>
                        <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Kirim Permohonan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Mandiri Klaim --}}
<div class="modal fade" id="modalEssNewClaim" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('hr.reimbursements.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow">
            @csrf
            <input type="hidden" name="employee_id" value="{{ $employee->id }}">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title fw-bold">Ajukan Klaim Reimbursement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold">Kategori Biaya</label>
                        <select name="claim_type" class="form-select" required>
                            <option value="BBM / Bensin">BBM / Bensin</option>
                            <option value="Tol / Parkir">Tol / Parkir</option>
                            <option value="Akomodasi / Hotel">Akomodasi / Hotel</option>
                            <option value="Konsumsi Lapangan">Konsumsi Lapangan</option>
                            <option value="Medis / Pengobatan">Medis / Pengobatan</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">Tanggal Nota</label>
                        <input type="date" name="event_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nominal Biaya (IDR)</label>
                        <input type="number" name="amount" class="form-control" placeholder="100000" min="1000" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Keperluan / Keterangan</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Tujuan pengeluaran..." required></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Upload Foto Struk / Nota</label>
                        <input type="file" name="receipt_image" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Kirim Klaim Biaya</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Live time ticker
    function updateLiveClock() {
        var now = new Date();
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var seconds = String(now.getSeconds()).padStart(2, '0');
        var el = document.getElementById('liveTimeClock');
        if (el) el.textContent = hours + ':' + minutes + ':' + seconds + ' WIB';
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();
</script>
@endsection
