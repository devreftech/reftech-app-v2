@extends('layouts.sales.app')
@section('title', 'HVAC Cooling Load Projects')

@section('content')
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="mdi mdi-air-conditioner text-primary me-2"></i>HVAC Cooling Load Calculation
            </h4>
            <p class="text-muted mb-0">Modul kalkulasi beban pendinginan ruangan (Quick Sales & Detailed Engineering) untuk menentukan kapasitas AC (BTU/h, kW, TR, PK).</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('hvac.master.index') }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-database-cog-outline me-1"></i> Master Data & Katalog AC
            </a>
            <a href="{{ route('hvac.project.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus me-1"></i> Buat Proyek Baru
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
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stat KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="avatar bg-light-primary text-primary rounded p-2">
                        <i class="mdi mdi-folder-outline fs-3"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                        <small class="text-muted">Total Proyek</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="avatar bg-light-warning text-warning rounded p-2">
                        <i class="mdi mdi-file-edit-outline fs-3"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['draft'] }}</div>
                        <small class="text-muted">Draft</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="avatar bg-light-info text-info rounded p-2">
                        <i class="mdi mdi-calculator-variant-outline fs-3"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['calculated'] }}</div>
                        <small class="text-muted">Selesai Hitung</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="avatar bg-light-success text-success rounded p-2">
                        <i class="mdi mdi-check-decagram-outline fs-3"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['approved'] }}</div>
                        <small class="text-muted">Disetujui / Quoted</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-3 border-bottom">
            <form method="GET" action="{{ route('hvac.project.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari kode proyek, nama, client, atau lokasi..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="calculated" {{ $status == 'calculated' ? 'selected' : '' }}>Calculated</option>
                        <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="quoted" {{ $status == 'quoted' ? 'selected' : '' }}>Quoted</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="mdi mdi-filter-variant me-1"></i> Filter</button>
                    @if($search || $status)
                        <a href="{{ route('hvac.project.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-responsive" style="min-height: 260px;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode Proyek</th>
                        <th>Nama Proyek / Gedung</th>
                        <th>Customer / Client</th>
                        <th>Sales / PIC</th>
                        <th class="text-center">Jml Ruangan</th>
                        <th class="text-end">Total Beban (BTU/h)</th>
                        <th class="text-center">Estimasi Kapasitas</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $p)
                        @php
                            $roomCount = $p->rooms->count();
                            $totalBtuh = $p->rooms->sum(fn($r) => $r->result?->design_load_btuh ?? 0);
                            $totalPk   = $p->rooms->sum(fn($r) => $r->result?->design_load_pk ?? 0);
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('hvac.project.show', $p->id) }}" class="fw-bold text-primary">
                                    {{ $p->project_code }}
                                </a>
                                <div class="small text-muted">{{ $p->created_at?->format('d M Y') }}</div>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $p->project_name }}</span>
                                @if($p->location)
                                    <div class="small text-muted"><i class="mdi mdi-map-marker-outline"></i> {{ $p->location }}</div>
                                @endif
                            </td>
                            <td>
                                @if($p->client)
                                    <span class="badge bg-light-primary text-primary">{{ $p->client->company }}</span>
                                @else
                                    <span class="text-dark">{{ $p->customer_name ?: '-' }}</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $p->sales?->name ?: '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-secondary rounded-pill px-2 py-1">
                                    {{ $roomCount }} Ruangan
                                </span>
                            </td>
                            <td class="text-end">
                                @if($totalBtuh > 0)
                                    <span class="fw-bold text-dark">{{ number_format($totalBtuh, 0) }}</span>
                                    <small class="text-muted d-block">BTU/h</small>
                                @else
                                    <span class="text-muted fst-italic">Belum dihitung</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($totalPk > 0)
                                    <span class="badge bg-label-info font-monospace">{{ number_format($totalPk, 1) }} PK</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @switch($p->status)
                                    @case('calculated')
                                        <span class="badge bg-label-info">Calculated</span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-label-success">Approved</span>
                                        @break
                                    @case('quoted')
                                        <span class="badge bg-label-primary">Quoted</span>
                                        @break
                                    @default
                                        <span class="badge bg-label-warning">Draft</span>
                                @endswitch
                            </td>
                            <td class="text-center text-nowrap">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('hvac.project.show', $p->id) }}" class="btn btn-sm btn-icon btn-label-primary" title="Detail & Ruangan">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </a>
                                    <a href="{{ route('hvac.project.edit', $p->id) }}" class="btn btn-sm btn-icon btn-label-warning" title="Edit Proyek">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </a>
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-icon btn-light" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" data-bs-popper-config='{"strategy":"fixed"}' aria-expanded="false" title="Menu Lainnya">
                                            <i class="mdi mdi-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('hvac.project.show', $p->id) }}">
                                                    <i class="mdi mdi-eye-outline me-2 text-primary"></i> Detail &amp; Ruangan
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('hvac.project.edit', $p->id) }}">
                                                    <i class="mdi mdi-pencil-outline me-2 text-warning"></i> Edit Proyek
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('hvac.room.create', $p->id) }}">
                                                    <i class="mdi mdi-plus-circle-outline me-2 text-success"></i> Tambah Ruangan
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <form action="{{ route('hvac.project.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus proyek HVAC ini beserta seluruh ruangan di dalamnya?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="mdi mdi-trash-can-outline me-2"></i> Hapus Proyek
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="mdi mdi-air-conditioner fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada proyek HVAC Cooling Load yang dibuat.
                                <div class="mt-2">
                                    <a href="{{ route('hvac.project.create') }}" class="btn btn-sm btn-primary">
                                        <i class="mdi mdi-plus me-1"></i> Buat Proyek Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
            <div class="card-footer d-flex justify-content-end p-3">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
@endsection
