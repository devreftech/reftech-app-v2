@extends('layouts.sales.app')
@section('title', 'Management Tools & Jadwal Audit')
@section('content')
    <style>
        .card-stat-widget {
            border: 1px solid rgba(0, 0, 0, 0.06);
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-stat-widget:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .nav-pills .nav-link {
            border-radius: 8px;
            padding: 9px 18px;
            font-weight: 500;
        }

        .nav-pills .nav-link.active {
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
        }

        .period-card {
            border-left: 4px solid #696cff;
            border-radius: 10px;
            background: #fff;
            transition: all 0.2s;
        }

        .period-card.closed {
            border-left-color: #8592a3;
        }

        .period-card.active-open {
            border-left-color: #71dd37;
            background: rgba(113, 221, 55, 0.02);
        }

        .pulse-badge {
            animation: pulse-animation 2s infinite;
        }

        @keyframes pulse-animation {
            0% {
                box-shadow: 0 0 0 0 rgba(113, 221, 55, 0.5);
            }
            70% {
                box-shadow: 0 0 0 8px rgba(113, 221, 55, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(113, 221, 55, 0);
            }
        }
    </style>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-check-circle-outline fs-4 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-alert-circle-outline fs-4 me-2"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="fw-bold mb-1">Terjadi kesalahan validasi:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 py-3 mb-2">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-tools text-primary"></i> Management Tools &amp; Pengaturan Audit
            </h4>
            <p class="text-muted mb-0">
                Kelola penugasan tools ke teknisi, jadwal periode self-audit, dan trigger audit langsung.
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            {{-- Tombol Trigger Cepat --}}
            <button type="button" class="btn btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#triggerQuickAuditModal">
                <i class="mdi mdi-lightning-bolt me-1"></i> Trigger Audit Sekarang
            </button>
            {{-- Tombol Atur Jadwal Periode --}}
            <button type="button" class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addPeriodModal">
                <i class="mdi mdi-calendar-plus me-1"></i> Atur Periode Audit
            </button>
            {{-- Tombol Tambah Teknisi --}}
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addTechnicianModal">
                <i class="mdi mdi-account-plus me-1"></i> Add Technician
            </button>
        </div>
    </div>

    {{-- KPI / Stat Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Teknisi Ditugaskan --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat-widget h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Teknisi Pemegang Tools</span>
                        <h3 class="fw-bold my-1 text-primary">{{ $stats['total_holding_tools'] }} <span class="text-muted fs-6 fw-normal">/ {{ $stats['total_technicians'] }}</span></h3>
                        <small class="text-muted">Teknisi dengan tools aktif</small>
                    </div>
                    <div class="stat-icon-wrapper bg-label-primary text-primary">
                        <i class="mdi mdi-account-hard-hat"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Tools Aktif --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat-widget h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Total Tools Aktif</span>
                        <h3 class="fw-bold my-1 text-info">{{ $stats['total_active_tools'] }}</h3>
                        <small class="text-muted">Unit aset tools di lapangan</small>
                    </div>
                    <div class="stat-icon-wrapper bg-label-info text-info">
                        <i class="mdi mdi-wrench-outline"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Status Periode Audit --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat-widget h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Periode Audit Aktif</span>
                        <div class="my-1">
                            @if ($currentActivePeriod && $currentActivePeriod->status == 'Open')
                                <span class="badge bg-success pulse-badge px-2 py-1 fs-6">
                                    <i class="mdi mdi-check-circle-outline me-1"></i> OPEN
                                </span>
                                <span class="fw-semibold text-dark ms-1">{{ $currentActivePeriod->short_semester_label }} {{ $currentActivePeriod->tahun }}</span>
                            @else
                                <span class="badge bg-secondary px-2 py-1 fs-6">CLOSED</span>
                                <span class="text-muted ms-1">Tidak ada audit aktif</span>
                            @endif
                        </div>
                        <small class="text-muted">
                            @if ($currentActivePeriod)
                                {{ \Carbon\Carbon::parse($currentActivePeriod->tanggal_mulai)->format('d M') }} - {{ \Carbon\Carbon::parse($currentActivePeriod->tanggal_selesai)->format('d M Y') }}
                            @else
                                Belum ada jadwal
                            @endif
                        </small>
                    </div>
                    <div class="stat-icon-wrapper {{ $currentActivePeriod && $currentActivePeriod->status == 'Open' ? 'bg-label-success text-success' : 'bg-label-secondary text-secondary' }}">
                        <i class="mdi mdi-calendar-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Progres Kepatuhan Audit --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-stat-widget h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted text-uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Kepatuhan Audit Periode</span>
                        <h3 class="fw-bold my-1 text-success">{{ $stats['submitted_count'] }} <span class="text-muted fs-6 fw-normal">/ {{ $stats['total_audits_count'] }}</span></h3>
                        <small class="text-muted">{{ $stats['verified_count'] }} Terverifikasi • {{ $stats['draft_count'] }} Draft</small>
                    </div>
                    <div class="stat-icon-wrapper bg-label-success text-success">
                        <i class="mdi mdi-clipboard-check-outline"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Tabs Navigation --}}
    <div class="card mb-4">
        <div class="card-header border-bottom py-2">
            <ul class="nav nav-pills card-header-pills" id="mainTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab == 'technicians' ? 'active' : '' }}" id="tab-technicians-btn" data-bs-toggle="pill" data-bs-target="#tab-technicians" type="button" role="tab" aria-selected="{{ $activeTab == 'technicians' ? 'true' : 'false' }}">
                        <i class="mdi mdi-account-group-outline me-1"></i> Penugasan Tools per Teknisi
                        <span class="badge bg-white text-primary ms-2">{{ $technicians->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab == 'periods' ? 'active' : '' }}" id="tab-periods-btn" data-bs-toggle="pill" data-bs-target="#tab-periods" type="button" role="tab" aria-selected="{{ $activeTab == 'periods' ? 'true' : 'false' }}">
                        <i class="mdi mdi-calendar-range me-1"></i> Jadwal &amp; Pengaturan Periode Audit
                        <span class="badge bg-white text-primary ms-2">{{ $periods->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-0">
            <div class="tab-content p-0" id="mainTabsContent">

                {{-- TAB 1: DAFTAR TEKNISI & TOOLS --}}
                <div class="tab-pane fade {{ $activeTab == 'technicians' ? 'show active' : '' }}" id="tab-technicians" role="tabpanel">
                    <div class="p-3 border-bottom bg-light d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div class="position-relative" style="max-width: 320px; width: 100%;">
                            <i class="mdi mdi-magnify position-absolute top-50 translate-middle-y text-muted" style="left: 12px; font-size: 1.2rem;"></i>
                            <input type="text" id="searchTechnicianInput" class="form-control ps-5" placeholder="Cari nama atau kode teknisi...">
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <i class="mdi mdi-information-outline text-primary"></i>
                            Klik "Kelola Tools" untuk menambah, transfer, atau me-retire tools milik teknisi.
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="technicianTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35%;">Teknisi</th>
                                    <th style="width: 15%;">Kode Teknisi</th>
                                    <th style="width: 25%;">Total Tools Aktif</th>
                                    <th style="width: 25%;" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($technicians as $technician)
                                    <tr class="technician-row">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-3">
                                                    <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                                                        {{ strtoupper(substr($technician->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <a href="{{ route('tool-assignment.show', $technician->id) }}" class="fw-semibold text-dark text-decoration-none hover-primary">
                                                        {{ $technician->name }}
                                                    </a>
                                                    <div class="text-muted small">{{ $technician->role ?? 'Technician' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-secondary font-monospace">{{ $technician->code ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if ($technician->tools_assigned_count > 0)
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-label-success fs-6 fw-bold px-2 py-1">
                                                        {{ $technician->tools_assigned_count }} Tools
                                                    </span>
                                                    @if ($technician->toolsAssigned->isNotEmpty())
                                                        <small class="text-muted d-none d-md-inline text-truncate" style="max-width: 220px;" title="{{ $technician->toolsAssigned->pluck('toolsMaster.nama_tools')->filter()->implode(', ') }}">
                                                            ({{ $technician->toolsAssigned->pluck('toolsMaster.nama_tools')->filter()->take(2)->implode(', ') }}{{ $technician->tools_assigned_count > 2 ? '...' : '' }})
                                                        </small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="badge bg-label-warning">0 Tools</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="{{ route('tool-assignment.show', $technician->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="mdi mdi-wrench-outline me-1"></i> Kelola Tools
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="visually-hidden">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('tool-assignment.show', $technician->id) }}">
                                                            <i class="mdi mdi-eye-outline me-2 text-primary"></i> Lihat &amp; Tambah Tools
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('tool-assignment.remove-technician', $technician->id) }}" method="post"
                                                            onsubmit="return confirm('Hapus {{ $technician->name }} dari daftar penugasan tools? Tools yang sudah terdaftar tidak akan dihapus.');">
                                                            @csrf
                                                            @method('delete')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="mdi mdi-account-remove-outline me-2"></i> Hapus dari Penugasan
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-account-alert-outline fs-1 d-block mb-2 text-secondary"></i>
                                            Belum ada teknisi yang ditambahkan ke penugasan tools.<br>
                                            <button type="button" class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#addTechnicianModal">
                                                <i class="mdi mdi-plus"></i> Tambah Teknisi Sekarang
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: PENGATURAN & JADWAL PERIODE AUDIT --}}
                <div class="tab-pane fade {{ $activeTab == 'periods' ? 'show active' : '' }}" id="tab-periods" role="tabpanel">
                    <div class="p-3 border-bottom bg-light d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="mdi mdi-clock-check-outline text-primary me-1"></i> Daftar Jadwal &amp; Pengaturan Periode Self-Audit
                            </h6>
                            <small class="text-muted">
                                Atur rentang tanggal pelaksanaan audit tools serta trigger pengaktifan audit secara manual maupun otomatis.
                            </small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#triggerQuickAuditModal">
                                <i class="mdi mdi-lightning-bolt me-1"></i> Trigger Audit Sekarang
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPeriodModal">
                                <i class="mdi mdi-plus me-1"></i> Buat Periode Baru
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 25%;">Periode Audit</th>
                                    <th style="width: 25%;">Rentang Waktu (Window)</th>
                                    <th style="width: 15%;">Status</th>
                                    <th style="width: 20%;">Progres Teknisi</th>
                                    <th style="width: 15%;" class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $todayStr = \Carbon\Carbon::today()->toDateString();
                                @endphp
                                @forelse ($periods as $period)
                                    @php
                                        $isDateActive = $period->tanggal_mulai <= $todayStr && $period->tanggal_selesai >= $todayStr;
                                        $isUpcoming = $period->tanggal_mulai > $todayStr;
                                        $isPast = $period->tanggal_selesai < $todayStr;

                                        $audits = $period->audits;
                                        $totalAudits = $audits->count();
                                        $draftCount = $audits->where('status_submit', 'Draft')->count();
                                        $subCount = $audits->where('status_submit', 'Submitted')->count();
                                        $verCount = $audits->where('status_submit', 'Verified')->count();
                                        $rejCount = $audits->where('status_submit', 'Rejected')->count();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark fs-6">{{ $period->period_title }}</div>
                                            <small class="text-muted">{{ $period->semester_label }}</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <i class="mdi mdi-calendar-range text-primary"></i>
                                                <span class="fw-semibold">
                                                    {{ \Carbon\Carbon::parse($period->tanggal_mulai)->format('d M Y') }}
                                                </span>
                                                <span class="text-muted">s/d</span>
                                                <span class="fw-semibold">
                                                    {{ \Carbon\Carbon::parse($period->tanggal_selesai)->format('d M Y') }}
                                                </span>
                                            </div>
                                            <div>
                                                @if ($isDateActive)
                                                    <span class="badge bg-label-success" style="font-size: 0.7rem;">
                                                        <i class="mdi mdi-circle-medium"></i> Sedang Berlangsung
                                                    </span>
                                                @elseif ($isUpcoming)
                                                    <span class="badge bg-label-info" style="font-size: 0.7rem;">
                                                        <i class="mdi mdi-clock-outline"></i> Akan Datang
                                                    </span>
                                                @else
                                                    <span class="badge bg-label-secondary" style="font-size: 0.7rem;">
                                                        <i class="mdi mdi-history"></i> Telah Berakhir
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @if ($period->status == 'Open')
                                                <form action="{{ route('tool-assignment.period.toggle-status', $period->id) }}" method="post" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-label-success border-0 px-2 py-1" title="Klik untuk Tutup Periode">
                                                        <i class="mdi mdi-lock-open-outline me-1"></i> Open (Aktif)
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('tool-assignment.period.toggle-status', $period->id) }}" method="post" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-label-secondary border-0 px-2 py-1" title="Klik untuk Buka Periode">
                                                        <i class="mdi mdi-lock-outline me-1"></i> Closed (Tutup)
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($totalAudits > 0)
                                                <div class="d-flex align-items-center gap-1 mb-1">
                                                    <span class="badge bg-success" title="Verified">{{ $verCount }} Verif</span>
                                                    <span class="badge bg-warning" title="Submitted (Menunggu Verifikasi)">{{ $subCount }} Submit</span>
                                                    <span class="badge bg-secondary" title="Draft">{{ $draftCount }} Draft</span>
                                                    @if ($rejCount > 0)
                                                        <span class="badge bg-danger" title="Rejected">{{ $rejCount }} Tolak</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted">Total {{ $totalAudits }} teknisi</small>
                                            @else
                                                <span class="text-muted small italic">Draft belum digenerate</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                {{-- Trigger Button --}}
                                                <form action="{{ route('tool-assignment.period.trigger', $period->id) }}" method="post" class="d-inline"
                                                    onsubmit="return confirm('⚡ Aktifkan waktu audit dan sinkronkan form self-audit untuk semua teknisi pemegang tools aktif pada periode {{ $period->period_title }}?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Trigger / Sinkronkan Audit Sekarang">
                                                        <i class="mdi mdi-lightning-bolt"></i> Trigger
                                                    </button>
                                                </form>

                                                {{-- Edit Button --}}
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPeriodModal-{{ $period->id }}" title="Edit Tanggal & Status">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>

                                                {{-- Dropdown Actions --}}
                                                <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('tool-audit-summary.index', ['period_id' => $period->id]) }}">
                                                            <i class="mdi mdi-chart-box-outline me-2 text-primary"></i> Lihat Summary Rekap
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('tool-assignment.period.toggle-status', $period->id) }}" method="post">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class="mdi mdi-toggle-switch-outline me-2"></i> {{ $period->status == 'Open' ? 'Tutup Periode (Close)' : 'Buka Periode (Open)' }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                    @if ($subCount == 0 && $verCount == 0)
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <form action="{{ route('tool-assignment.period.delete', $period->id) }}" method="post"
                                                                onsubmit="return confirm('Yakin ingin menghapus periode {{ $period->period_title }}?');">
                                                                @csrf
                                                                @method('delete')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="mdi mdi-trash-can-outline me-2"></i> Hapus Periode
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="mdi mdi-calendar-blank-outline fs-1 d-block mb-2 text-secondary"></i>
                                            Belum ada periode audit yang dibuat.<br>
                                            <button type="button" class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#addPeriodModal">
                                                <i class="mdi mdi-plus"></i> Atur Periode Audit Baru
                                            </button>
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

    {{-- MODAL 1: ATUR / TAMBAH PERIODE AUDIT BARU --}}
    <div class="modal fade" id="addPeriodModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('tool-assignment.period.store') }}" method="post" id="addPeriodForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i class="mdi mdi-calendar-plus text-primary"></i> Atur Jadwal Periode Audit
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Mode Selector --}}
                        <div class="bg-light p-2 rounded-3 mb-4 d-flex gap-2">
                            <input type="hidden" name="mode" id="periodGenerationMode" value="full_year">
                            <button type="button" class="btn btn-sm btn-primary flex-fill fw-semibold" id="btnModeFullYear" onclick="setPeriodMode('full_year')">
                                <i class="mdi mdi-calendar-multiselect me-1"></i> Generate 1 Tahun Penuh (4 Kuartal Sekaligus)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary flex-fill fw-semibold" id="btnModeSingle" onclick="setPeriodMode('single')">
                                <i class="mdi mdi-calendar-today me-1"></i> Kuartal Tunggal (1 Periode Saja)
                            </button>
                        </div>

                        {{-- SEKSI 1: GENERATE 1 TAHUN PENUH (SEMUA 4 KUARTAL) --}}
                        <div id="sectionFullYear">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3 pb-3 border-bottom">
                                <div style="max-width: 260px; width: 100%;">
                                    <label class="form-label fw-bold mb-1">Pilih Tahun Pelaksanaan <span class="text-danger">*</span></label>
                                    @php
                                        $currentYear = date('Y');
                                    @endphp
                                    <select class="form-select" name="tahun" id="fullYearSelect" onchange="calculateFullYearQuarters()">
                                        @for ($y = $currentYear - 1; $y <= $currentYear + 2; $y++)
                                            <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>Tahun {{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="form-label small text-muted mb-1 d-block">Preset Rentang Tanggal Semua Kuartal:</label>
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-secondary" onclick="applyFullYearPreset('10days')">
                                            10 Hari Terakhir Kuartal
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="applyFullYearPreset('14days')">
                                            14 Hari Terakhir
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="applyFullYearPreset('fullmonth')">
                                            Bulan Penuh
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <p class="text-muted small mb-3">
                                <i class="mdi mdi-information-outline text-primary me-1"></i>
                                Sistem akan membuat jadwal untuk <strong>4 Kuartal (Q1 s/d Q4)</strong> sekaligus. Anda dapat menyesuaikan tanggal masing-masing kuartal di bawah ini:
                            </p>

                            <div class="row g-3">
                                {{-- Q1 --}}
                                <div class="col-12 col-md-6">
                                    <div class="border rounded-3 p-3 bg-light-subtle h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-label-primary fs-6 fw-bold">Q1 (Triwulan I)</span>
                                            <span class="text-muted small">Akhir Maret</span>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small mb-1">Mulai</label>
                                                <input type="date" class="form-control form-control-sm" name="quarters[1][tanggal_mulai]" id="q1_start" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small mb-1">Selesai</label>
                                                <input type="date" class="form-control form-control-sm" name="quarters[1][tanggal_selesai]" id="q1_end" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label small mb-1">Status</label>
                                            <select class="form-select form-select-sm" name="quarters[1][status]" id="q1_status">
                                                <option value="Open">Open (Aktifkan Sekarang)</option>
                                                <option value="Closed" selected>Closed (Draft / Belum Aktif)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Q2 --}}
                                <div class="col-12 col-md-6">
                                    <div class="border rounded-3 p-3 bg-light-subtle h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-label-primary fs-6 fw-bold">Q2 (Triwulan II)</span>
                                            <span class="text-muted small">Akhir Juni</span>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small mb-1">Mulai</label>
                                                <input type="date" class="form-control form-control-sm" name="quarters[2][tanggal_mulai]" id="q2_start" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small mb-1">Selesai</label>
                                                <input type="date" class="form-control form-control-sm" name="quarters[2][tanggal_selesai]" id="q2_end" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label small mb-1">Status</label>
                                            <select class="form-select form-select-sm" name="quarters[2][status]" id="q2_status">
                                                <option value="Open">Open (Aktifkan Sekarang)</option>
                                                <option value="Closed" selected>Closed (Draft / Belum Aktif)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Q3 --}}
                                <div class="col-12 col-md-6">
                                    <div class="border rounded-3 p-3 bg-light-subtle h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-label-primary fs-6 fw-bold">Q3 (Triwulan III)</span>
                                            <span class="text-muted small">Akhir September</span>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small mb-1">Mulai</label>
                                                <input type="date" class="form-control form-control-sm" name="quarters[3][tanggal_mulai]" id="q3_start" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small mb-1">Selesai</label>
                                                <input type="date" class="form-control form-control-sm" name="quarters[3][tanggal_selesai]" id="q3_end" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label small mb-1">Status</label>
                                            <select class="form-select form-select-sm" name="quarters[3][status]" id="q3_status">
                                                <option value="Open">Open (Aktifkan Sekarang)</option>
                                                <option value="Closed" selected>Closed (Draft / Belum Aktif)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Q4 --}}
                                <div class="col-12 col-md-6">
                                    <div class="border rounded-3 p-3 bg-light-subtle h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-label-primary fs-6 fw-bold">Q4 (Triwulan IV)</span>
                                            <span class="text-muted small">Akhir Desember</span>
                                        </div>
                                        <div class="row g-2 mb-2">
                                            <div class="col-6">
                                                <label class="form-label small mb-1">Mulai</label>
                                                <input type="date" class="form-control form-control-sm" name="quarters[4][tanggal_mulai]" id="q4_start" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small mb-1">Selesai</label>
                                                <input type="date" class="form-control form-control-sm" name="quarters[4][tanggal_selesai]" id="q4_end" required>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label small mb-1">Status</label>
                                            <select class="form-select form-select-sm" name="quarters[4][status]" id="q4_status">
                                                <option value="Open">Open (Aktifkan Sekarang)</option>
                                                <option value="Closed" selected>Closed (Draft / Belum Aktif)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- SEKSI 2: KUARTAL TUNGGAL --}}
                        <div id="sectionSingleQuarter" style="display: none;">
                            <p class="text-muted small mb-3">
                                Atur atau sesuaikan tanggal untuk 1 kuartal spesifik.
                            </p>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Tahun <span class="text-danger">*</span></label>
                                    <select class="form-select" name="single_tahun" id="newPeriodYear">
                                        @for ($y = $currentYear - 1; $y <= $currentYear + 2; $y++)
                                            <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Kuartal / Triwulan <span class="text-danger">*</span></label>
                                    @php
                                        $currentMonth = (int)date('m');
                                        $defaultQ = ceil($currentMonth / 3);
                                    @endphp
                                    <select class="form-select" name="semester" id="newPeriodSemester">
                                        <option value="1" {{ $defaultQ == 1 ? 'selected' : '' }}>Q1 (Triwulan I - Akhir Maret)</option>
                                        <option value="2" {{ $defaultQ == 2 ? 'selected' : '' }}>Q2 (Triwulan II - Akhir Juni)</option>
                                        <option value="3" {{ $defaultQ == 3 ? 'selected' : '' }}>Q3 (Triwulan III - Akhir September)</option>
                                        <option value="4" {{ $defaultQ == 4 ? 'selected' : '' }}>Q4 (Triwulan IV - Akhir Desember)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted mb-1">Preset Cepat Rentang Tanggal:</label>
                                <div class="d-flex flex-wrap gap-1">
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="applyQuarterPreset()">
                                        10 Hari Terakhir Kuartal
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="applyCustomPreset(0, 10)">
                                        Hari Ini s/d 10 Hari ke Depan
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="applyCustomPreset(0, 14)">
                                        2 Minggu ke Depan
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="applyMonthPreset()">
                                        Bulan Penuh
                                    </button>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal_mulai" id="newPeriodStart">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal_selesai" id="newPeriodEnd">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status Awal <span class="text-danger">*</span></label>
                                <select class="form-select" name="status">
                                    <option value="Open" selected>Open (Langsung Aktif)</option>
                                    <option value="Closed">Closed (Draft / Belum Aktif)</option>
                                </select>
                            </div>

                            <div class="form-check form-switch mt-3 p-2 bg-light rounded">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="trigger_now" value="1" id="triggerNowCheck" checked>
                                <label class="form-check-label fw-semibold text-dark small" for="triggerNowCheck">
                                    ⚡ Langsung trigger dan buat form self-audit untuk seluruh teknisi aktif
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitPeriod">
                            <i class="mdi mdi-calendar-check me-1"></i> Simpan &amp; Terapkan 4 Kuartal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 2: TRIGGER CEPAT AUDIT SEKARANG --}}
    <div class="modal fade" id="triggerQuickAuditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                @if ($currentActivePeriod)
                    <form action="{{ route('tool-assignment.period.trigger', $currentActivePeriod->id) }}" method="post">
                        @csrf
                        <div class="modal-header bg-label-warning py-3">
                            <h5 class="modal-title text-warning-emphasis">
                                <i class="mdi mdi-lightning-bolt me-1"></i> Trigger Waktu Audit Sekarang
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center my-2">
                                <div class="avatar avatar-lg bg-label-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                    <i class="mdi mdi-play-circle-outline fs-1 text-warning"></i>
                                </div>
                                <h5 class="fw-bold mb-1">Aktifkan Periode: {{ $currentActivePeriod->period_title }}</h5>
                                <p class="text-muted small">
                                    Rentang: {{ \Carbon\Carbon::parse($currentActivePeriod->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($currentActivePeriod->tanggal_selesai)->format('d M Y') }}
                                </p>
                            </div>
                            <div class="alert alert-secondary small mb-0">
                                <i class="mdi mdi-information-outline text-primary me-1"></i>
                                Tindakan ini akan:
                                <ul class="mb-0 mt-1 ps-3">
                                    <li>Mengubah status periode menjadi <strong>Open</strong>.</li>
                                    <li>Secara instan men-generate form self-audit (Draft) untuk <strong>semua teknisi</strong> yang memegang tools aktif.</li>
                                    <li>Teknisi dapat langsung mengisi audit melalui menu <strong>Audit Tools</strong> di dashboard mereka.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="mdi mdi-lightning-bolt me-1"></i> Ya, Aktifkan Audit Sekarang
                            </button>
                        </div>
                    </form>
                @else
                    <div class="modal-header">
                        <h5 class="modal-title">Trigger Audit Tools</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <p class="text-muted mb-3">Belum ada periode audit yang dibuat. Silakan atur periode baru terlebih dahulu.</p>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#addPeriodModal">
                            <i class="mdi mdi-calendar-plus me-1"></i> Buat Periode Audit
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- MODAL 3: ADD TECHNICIAN --}}
    <div class="modal fade" id="addTechnicianModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('tool-assignment.add-technician') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center gap-2">
                            <i class="mdi mdi-account-plus text-primary"></i> Tambah Teknisi ke Penugasan Tools
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih User / Teknisi <span class="text-danger">*</span></label>
                            <select class="form-select" name="user_id" required>
                                <option value="" disabled selected>-- Pilih User --</option>
                                @foreach ($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role ?? 'User' }}) - {{ $user->code ?? 'No Code' }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">
                                User yang ditambahkan akan dapat menerima penugasan tools dan mengikuti siklus self-audit.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-plus me-1"></i> Tambahkan Teknisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL 4: EDIT PERIODE AUDIT (RENDER DI ROOT AGAR TIDAK TERJEBAK DI DALAM CARD/TABLE) --}}
    @foreach ($periods as $period)
        <div class="modal fade" id="editPeriodModal-{{ $period->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form action="{{ route('tool-assignment.period.update', $period->id) }}" method="post">
                        @csrf
                        @method('patch')
                        <div class="modal-header">
                            <h5 class="modal-title d-flex align-items-center gap-2">
                                <i class="mdi mdi-calendar-edit text-primary"></i> Edit Jadwal Periode Audit
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info py-2 px-3 mb-3 small">
                                <strong>Periode:</strong> {{ $period->period_title }} ({{ $period->semester_label }})
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal_mulai" value="{{ $period->tanggal_mulai }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="tanggal_selesai" value="{{ $period->tanggal_selesai }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Status Periode <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" required>
                                    <option value="Open" {{ $period->status == 'Open' ? 'selected' : '' }}>Open (Audit Terbuka untuk Teknisi)</option>
                                    <option value="Closed" {{ $period->status == 'Closed' ? 'selected' : '' }}>Closed (Ditutup)</option>
                                </select>
                            </div>

                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="sync_technicians" value="1" id="syncTech-{{ $period->id }}" checked>
                                <label class="form-check-label small" for="syncTech-{{ $period->id }}">
                                    Sinkronkan &amp; generate draft audit untuk seluruh teknisi dengan tools aktif
                                </label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach


    {{-- Javascript Helpers --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Search filter for technician table
            const searchInput = document.getElementById('searchTechnicianInput');
            if (searchInput) {
                searchInput.addEventListener('keyup', function () {
                    const filter = this.value.toLowerCase();
                    const rows = document.querySelectorAll('#technicianTable tbody tr.technician-row');
                    rows.forEach(function (row) {
                        const text = row.textContent.toLowerCase();
                        row.style.display = text.includes(filter) ? '' : 'none';
                    });
                });
            }

            // URL hash / tab switching
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            const hash = window.location.hash;

            if (tabParam === 'periods' || hash === '#periods' || hash === '#tab-periods') {
                const periodsTabBtn = document.getElementById('tab-periods-btn');
                if (periodsTabBtn) {
                    const bsTab = new bootstrap.Tab(periodsTabBtn);
                    bsTab.show();
                }
            }

            // Update URL hash when tab is clicked
            const tabButtons = document.querySelectorAll('#mainTabs button[data-bs-toggle="pill"]');
            tabButtons.forEach(btn => {
                btn.addEventListener('shown.bs.tab', function (e) {
                    const targetId = e.target.getAttribute('data-bs-target');
                    if (targetId === '#tab-periods') {
                        history.replaceState(null, null, '?tab=periods');
                    } else {
                        history.replaceState(null, null, window.location.pathname);
                    }
                });
            });

            // Inisialisasi perhitungan 4 kuartal 1 tahun
            calculateFullYearQuarters();

            // Inisialisasi preset kuartal pada modal tambah periode (mode single)
            const yearSelect = document.getElementById('newPeriodYear');
            const semesterSelect = document.getElementById('newPeriodSemester');
            if (yearSelect && semesterSelect) {
                yearSelect.addEventListener('change', applyQuarterPreset);
                semesterSelect.addEventListener('change', applyQuarterPreset);
                applyQuarterPreset();
            }
        });

        function setPeriodMode(mode) {
            const modeInput = document.getElementById('periodGenerationMode');
            const btnFullYear = document.getElementById('btnModeFullYear');
            const btnSingle = document.getElementById('btnModeSingle');
            const secFullYear = document.getElementById('sectionFullYear');
            const secSingle = document.getElementById('sectionSingleQuarter');
            const btnSubmit = document.getElementById('btnSubmitPeriod');

            modeInput.value = mode;

            if (mode === 'full_year') {
                btnFullYear.classList.remove('btn-outline-secondary');
                btnFullYear.classList.add('btn-primary');
                btnSingle.classList.remove('btn-primary');
                btnSingle.classList.add('btn-outline-secondary');

                secFullYear.style.display = 'block';
                secSingle.style.display = 'none';

                // Set required attributes
                document.getElementById('q1_start').required = true;
                document.getElementById('q1_end').required = true;
                document.getElementById('q2_start').required = true;
                document.getElementById('q2_end').required = true;
                document.getElementById('q3_start').required = true;
                document.getElementById('q3_end').required = true;
                document.getElementById('q4_start').required = true;
                document.getElementById('q4_end').required = true;

                document.getElementById('newPeriodStart').required = false;
                document.getElementById('newPeriodEnd').required = false;

                btnSubmit.innerHTML = '<i class="mdi mdi-calendar-check me-1"></i> Simpan &amp; Terapkan 4 Kuartal';
            } else {
                btnSingle.classList.remove('btn-outline-secondary');
                btnSingle.classList.add('btn-primary');
                btnFullYear.classList.remove('btn-primary');
                btnFullYear.classList.add('btn-outline-secondary');

                secFullYear.style.display = 'none';
                secSingle.style.display = 'block';

                document.getElementById('q1_start').required = false;
                document.getElementById('q1_end').required = false;
                document.getElementById('q2_start').required = false;
                document.getElementById('q2_end').required = false;
                document.getElementById('q3_start').required = false;
                document.getElementById('q3_end').required = false;
                document.getElementById('q4_start').required = false;
                document.getElementById('q4_end').required = false;

                document.getElementById('newPeriodStart').required = true;
                document.getElementById('newPeriodEnd').required = true;

                btnSubmit.innerHTML = '<i class="mdi mdi-check me-1"></i> Simpan Jadwal Kuartal';
                applyQuarterPreset();
            }
        }

        function calculateFullYearQuarters(presetType = '10days') {
            const yearSelect = document.getElementById('fullYearSelect');
            if (!yearSelect) return;

            const year = parseInt(yearSelect.value) || new Date().getFullYear();
            const formatYMD = (d) => {
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${d.getFullYear()}-${mm}-${dd}`;
            };

            const quartersDef = [
                { id: 1, month: 3 },  // Q1: Maret
                { id: 2, month: 6 },  // Q2: Juni
                { id: 3, month: 9 },  // Q3: September
                { id: 4, month: 12 }, // Q4: Desember
            ];

            const todayStr = formatYMD(new Date());

            quartersDef.forEach(q => {
                const lastDate = new Date(year, q.month, 0);
                let startDate;

                if (presetType === 'fullmonth') {
                    startDate = new Date(year, q.month - 1, 1);
                } else if (presetType === '14days') {
                    startDate = new Date(year, q.month - 1, lastDate.getDate() - 13);
                } else {
                    // Default: 10 days
                    startDate = new Date(year, q.month - 1, lastDate.getDate() - 9);
                }

                const startInput = document.getElementById(`q${q.id}_start`);
                const endInput = document.getElementById(`q${q.id}_end`);
                const statusSelect = document.getElementById(`q${q.id}_status`);

                if (startInput) startInput.value = formatYMD(startDate);
                if (endInput) endInput.value = formatYMD(lastDate);

                // Auto-set status Open if today is within quarter date range
                if (statusSelect) {
                    const startStr = formatYMD(startDate);
                    const endStr = formatYMD(lastDate);
                    if (todayStr >= startStr && todayStr <= endStr) {
                        statusSelect.value = 'Open';
                    }
                }
            });
        }

        function applyFullYearPreset(presetType) {
            calculateFullYearQuarters(presetType);
        }

        function applyQuarterPreset() {
            const year = parseInt(document.getElementById('newPeriodYear').value) || new Date().getFullYear();
            const semester = parseInt(document.getElementById('newPeriodSemester').value) || 1;

            let endMonth = 3;
            if (semester === 2) endMonth = 6;
            if (semester === 3) endMonth = 9;
            if (semester === 4) endMonth = 12;

            // Last day of month
            const lastDate = new Date(year, endMonth, 0);
            const startDate = new Date(year, endMonth - 1, lastDate.getDate() - 9);

            const formatYMD = (d) => {
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${d.getFullYear()}-${mm}-${dd}`;
            };

            const startEl = document.getElementById('newPeriodStart');
            const endEl = document.getElementById('newPeriodEnd');
            if (startEl) startEl.value = formatYMD(startDate);
            if (endEl) endEl.value = formatYMD(lastDate);
        }

        function applyCustomPreset(startOffsetDays, durationDays) {
            const today = new Date();
            const start = new Date(today);
            start.setDate(today.getDate() + startOffsetDays);

            const end = new Date(start);
            end.setDate(start.getDate() + durationDays - 1);

            const formatYMD = (d) => {
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${d.getFullYear()}-${mm}-${dd}`;
            };

            const startEl = document.getElementById('newPeriodStart');
            const endEl = document.getElementById('newPeriodEnd');
            if (startEl) startEl.value = formatYMD(start);
            if (endEl) endEl.value = formatYMD(end);
        }

        function applyMonthPreset() {
            const year = parseInt(document.getElementById('newPeriodYear').value) || new Date().getFullYear();
            const semester = parseInt(document.getElementById('newPeriodSemester').value) || 1;

            let month = 3;
            if (semester === 2) month = 6;
            if (semester === 3) month = 9;
            if (semester === 4) month = 12;

            const firstDate = new Date(year, month - 1, 1);
            const lastDate = new Date(year, month, 0);

            const formatYMD = (d) => {
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${d.getFullYear()}-${mm}-${dd}`;
            };

            const startEl = document.getElementById('newPeriodStart');
            const endEl = document.getElementById('newPeriodEnd');
            if (startEl) startEl.value = formatYMD(firstDate);
            if (endEl) endEl.value = formatYMD(lastDate);
        }
    </script>
@endsection
