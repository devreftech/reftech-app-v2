@extends('layouts.sales.app')
@section('title', 'Kelengkapan Data Finance - Tools')
@section('no-container') @endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <style>
        .tool-finance-card {
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.25s ease-in-out;
        }
        .tool-finance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(149, 157, 165, 0.15);
        }
        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .nav-pills .nav-link {
            border-radius: 10px;
            padding: 0.6rem 1.25rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .nav-pills .nav-link.active {
            box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
        }
        .tool-avatar {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e0e0e0;
        }
        .pic-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            background-color: #f5f5f9;
            font-size: 0.8125rem;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header & Breadcrumb -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance /</span> Kelengkapan Data Finance Tools
            </h4>
            <p class="text-muted mb-0">
                Integrasi master aset tetap untuk tools teknisi yang telah diserahterimakan di operasional lapangan.
            </p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('fixed.index', ['type' => 'Tools']) }}" class="btn btn-outline-secondary waves-effect">
                <i class="mdi mdi-cube-outline me-1"></i> Lihat Daftar Master Fixed Asset
            </a>
        </div>
    </div>

    <!-- Alert / Information Callout -->
    <div class="alert alert-primary alert-dismissible d-flex align-items-center mb-4" role="alert">
        <i class="mdi mdi-information-outline mdi-24px me-3 text-primary"></i>
        <div class="d-flex flex-column">
            <span class="fw-semibold">Alur Sinkronisasi Data Tools:</span>
            <small class="text-body">
                Tools yang di-assign ke teknisi via Management Tools Teknisi secara otomatis terdaftar di sini. 
                Lengkapi Akun Aktiva, Akun Penyusutan, dan Nilai Perolehan agar tercatat sah dalam Laporan Keuangan dan Jurnal Penyusutan Otomatis.
            </small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- KPI Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card tool-finance-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted fw-medium d-block mb-1">Total Tools Terdaftar</span>
                            <h3 class="card-title mb-1 text-dark fw-bold">{{ number_format($countBelum + $countSudah) }}</h3>
                            <small class="text-muted">Total unit tools yang terdata</small>
                        </div>
                        <div class="stat-icon-wrapper bg-label-primary">
                            <i class="mdi mdi-tools mdi-24px text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card tool-finance-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted fw-medium d-block mb-1">Belum Lengkap Akuntansi</span>
                            <h3 class="card-title mb-1 text-danger fw-bold">{{ number_format($countBelum) }}</h3>
                            <small class="text-danger fw-semibold">
                                <i class="mdi mdi-alert-circle-outline me-1"></i> Perlu penetapan akun aktiva
                            </small>
                        </div>
                        <div class="stat-icon-wrapper bg-label-danger">
                            <i class="mdi mdi-file-alert-outline mdi-24px text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card tool-finance-card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <span class="text-muted fw-medium d-block mb-1">Sudah Terverifikasi Lengkap</span>
                            <h3 class="card-title mb-1 text-success fw-bold">{{ number_format($countSudah) }}</h3>
                            <small class="text-success fw-semibold">
                                <i class="mdi mdi-check-decagram-outline me-1"></i> Siap disusutkan otomatis
                            </small>
                        </div>
                        <div class="stat-icon-wrapper bg-label-success">
                            <i class="mdi mdi-check-all mdi-24px text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs & Main Table Card -->
    <div class="card shadow-sm">
        <div class="card-header border-bottom pb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <ul class="nav nav-pills gap-2 mb-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link {{ $status == 'belum' ? 'active' : 'bg-light text-dark' }}"
                            href="{{ route('tool-finance.index', ['status' => 'belum']) }}">
                            <i class="mdi mdi-alert-circle-outline me-1"></i> Belum Lengkap
                            <span class="badge {{ $status == 'belum' ? 'bg-white text-danger' : 'bg-danger text-white' }} ms-2">
                                {{ $countBelum }}
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $status == 'sudah' ? 'active' : 'bg-light text-dark' }}"
                            href="{{ route('tool-finance.index', ['status' => 'sudah']) }}">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Sudah Lengkap
                            <span class="badge {{ $status == 'sudah' ? 'bg-white text-success' : 'bg-success text-white' }} ms-2">
                                {{ $countSudah }}
                            </span>
                        </a>
                    </li>
                </ul>
                <div class="text-muted small">
                    Menampilkan <strong>{{ count($tools) }}</strong> data status <strong>{{ $status == 'belum' ? 'Belum Lengkap' : 'Sudah Lengkap' }}</strong>
                </div>
            </div>
        </div>

        <div class="card-body pt-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tool-finance-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama Tools / Spesifikasi</th>
                            <th>Teknisi (PIC)</th>
                            <th class="text-center">Qty</th>
                            <th>Tgl Serah Terima</th>
                            <th>Akun Aktiva</th>
                            <th class="text-end">Nilai / Harga Beli</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tools as $idx => $tool)
                            @php
                                $master = $tool->toolsMaster;
                                $pic = $tool->pic;
                                $aktiva = $tool->aktiva;
                                $hasAktiva = !empty($tool->id_aktiva);
                                $imgSrc = $tool->foto_awal ? asset($tool->foto_awal) : ($master?->foto_referensi ? asset($master->foto_referensi) : null);
                            @endphp
                            <tr>
                                <td class="text-muted small">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if ($imgSrc)
                                            <img src="{{ $imgSrc }}" alt="Tool" class="tool-avatar me-2 shadow-xs" 
                                                 onerror="this.style.display='none'">
                                        @else
                                            <div class="stat-icon-wrapper bg-label-secondary me-2" style="width: 38px; height: 38px; border-radius: 6px;">
                                                <i class="mdi mdi-wrench-outline text-secondary"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-dark">
                                                {{ $master->nama_tools ?? ($tool->desc ?: 'Tools Tanpa Nama') }}
                                            </h6>
                                            <small class="text-muted d-block">
                                                {{ $master->kategori ?? 'Tools' }} 
                                                @if($master?->spesifikasi) • {{ Str::limit($master->spesifikasi, 35) }} @endif
                                            </small>
                                            @if($tool->code)
                                                <span class="badge bg-label-secondary font-monospace mt-1" style="font-size: 0.725rem;">
                                                    {{ $tool->code }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($pic)
                                        <div class="pic-badge">
                                            <i class="mdi mdi-account-circle text-primary"></i>
                                            <span class="fw-medium text-dark">{{ $pic->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-label-dark fw-bold px-2 py-1">
                                        {{ $tool->qty ?: 1 }}
                                    </span>
                                </td>
                                <td>
                                    @if ($tool->tanggal_serah_terima)
                                        <span class="text-body fw-medium">
                                            {{ \Carbon\Carbon::parse($tool->tanggal_serah_terima)->format('d M Y') }}
                                        </span>
                                        <small class="text-muted d-block">
                                            {{ \Carbon\Carbon::parse($tool->tanggal_serah_terima)->diffForHumans() }}
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($hasAktiva && $aktiva)
                                        <span class="badge bg-label-success text-start text-wrap py-2 px-2" style="max-width: 200px;">
                                            <i class="mdi mdi-book-check-outline me-1"></i> {{ $aktiva->code }} - {{ $aktiva->name }}
                                        </span>
                                    @else
                                        <span class="badge bg-label-danger py-2 px-2">
                                            <i class="mdi mdi-alert-circle-outline me-1"></i> Belum Ditetapkan
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if ($tool->total)
                                        <span class="fw-bold text-dark">
                                            Rp {{ number_format($tool->total, 0, ',', '.') }}
                                        </span>
                                    @elseif($master?->harga_referensi)
                                        <span class="text-muted small" title="Harga Referensi Master">
                                            (Ref: Rp {{ number_format($master->harga_referensi, 0, ',', '.') }})
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($hasAktiva)
                                        <span class="badge bg-success">
                                            <i class="mdi mdi-check me-1"></i> Lengkap
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="mdi mdi-clock-outline me-1"></i> Belum Lengkap
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('fixed.edit', ['fixed' => $tool->id, 'redirect_to' => route('tool-finance.index', ['status' => $status])]) }}" 
                                       class="btn btn-sm {{ $status == 'belum' ? 'btn-primary' : 'btn-outline-primary' }} d-inline-flex align-items-center gap-1">
                                        <i class="mdi {{ $status == 'belum' ? 'mdi-playlist-edit' : 'mdi-pencil-outline' }}"></i>
                                        <span>{{ $status == 'belum' ? 'Lengkapi' : 'Edit' }}</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="stat-icon-wrapper bg-label-success mb-3" style="width: 64px; height: 64px; border-radius: 50%;">
                                            <i class="mdi mdi-check-all mdi-36px text-success"></i>
                                        </div>
                                        <h5 class="fw-bold mb-1">
                                            {{ $status == 'belum' ? 'Semua Data Tools Sudah Lengkap!' : 'Belum Ada Data Tools Lengkap' }}
                                        </h5>
                                        <p class="text-muted small mb-0" style="max-width: 420px;">
                                            {{ $status == 'belum' ? 'Seluruh tools yang diserahkan ke teknisi telah memiliki penetapan akun aktiva dan nilai finansial.' : 'Silakan buka tab "Belum Lengkap" untuk melengkapi data akuntansi tools teknisi.' }}
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script>
        $(document).ready(function() {
            if ($('#tool-finance-table tbody tr td').length > 1) {
                $('#tool-finance-table').DataTable({
                    pageLength: 25,
                    lengthMenu: [10, 25, 50, 100],
                    ordering: true,
                    responsive: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Cari tools, teknisi, akun...",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ s/d _END_ dari _TOTAL_ tools",
                        infoEmpty: "Data tidak ditemukan",
                        zeroRecords: "Tidak ada tools yang sesuai dengan pencarian",
                        paginate: {
                            first: '<i class="mdi mdi-chevron-double-left"></i>',
                            previous: '<i class="mdi mdi-chevron-left"></i>',
                            next: '<i class="mdi mdi-chevron-right"></i>',
                            last: '<i class="mdi mdi-chevron-double-right"></i>'
                        }
                    }
                });
            }
        });
    </script>
@endpush
