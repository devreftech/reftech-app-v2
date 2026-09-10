@extends('layouts.sales.app')
@section('title', 'Proyek HVAC: ' . $project->project_code)

@section('content')
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('hvac.project.index') }}" class="btn btn-sm btn-icon btn-light">
                    <i class="mdi mdi-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0">{{ $project->project_code }} - {{ $project->project_name }}</h4>
                @switch($project->status)
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
            </div>
            <p class="text-muted mb-0">
                <i class="mdi mdi-domain me-1"></i>{{ $project->customer_display_name }}
                @if($project->location)
                    &bull; <i class="mdi mdi-map-marker-outline me-1"></i>{{ $project->location }}
                @endif
                &bull; <i class="mdi mdi-weather-sunny me-1"></i>Ambient Outdoor: {{ $project->design_outdoor_temp }}°C / {{ $project->design_outdoor_rh }}% RH
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('hvac.project.edit', $project->id) }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-pencil-outline me-1"></i> Edit Proyek
            </a>
            <a href="{{ route('hvac.room.create', $project->id) }}" class="btn btn-primary">
                <i class="mdi mdi-plus me-1"></i> Tambah Ruangan Baru
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

    <!-- Total Project Capacity Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body p-3">
                    <small class="text-muted text-uppercase fw-semibold">Total Cooling Load</small>
                    <div class="fs-3 fw-bold text-primary">{{ number_format($totalProjectLoadBtuh, 0) }}</div>
                    <small class="text-muted">BTU/h (Semua Ruangan)</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-info border-4">
                <div class="card-body p-3">
                    <small class="text-muted text-uppercase fw-semibold">Total Kapasitas kW</small>
                    <div class="fs-3 fw-bold text-info">{{ number_format($totalProjectLoadKw, 2) }}</div>
                    <small class="text-muted">kW Cooling</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-success border-4">
                <div class="card-body p-3">
                    <small class="text-muted text-uppercase fw-semibold">Total Kapasitas TR</small>
                    <div class="fs-3 fw-bold text-success">{{ number_format($totalProjectLoadTr, 2) }}</div>
                    <small class="text-muted">Ton of Refrigeration</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 border-start border-warning border-4">
                <div class="card-body p-3">
                    <small class="text-muted text-uppercase fw-semibold">Total Estimasi PK</small>
                    <div class="fs-3 fw-bold text-warning">{{ number_format($totalProjectLoadPk, 1) }}</div>
                    <small class="text-muted">Horse Power / PK AC</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms List Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-0 fw-bold">
                    <i class="mdi mdi-door-sliding text-primary me-2"></i>Daftar Ruangan dalam Proyek ({{ $project->rooms->count() }})
                </h5>
            </div>
            <a href="{{ route('hvac.room.create', $project->id) }}" class="btn btn-sm btn-primary">
                <i class="mdi mdi-plus me-1"></i> Tambah Ruangan
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Ruangan</th>
                        <th class="text-center">Mode Perhitungan</th>
                        <th>Dimensi &amp; Luas</th>
                        <th>Kondisi Ruangan (T / RH)</th>
                        <th class="text-end">Design Load</th>
                        <th class="text-center">Kapasitas</th>
                        <th>Rekomendasi Unit AC</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($project->rooms as $room)
                        @php
                            $res = $room->result;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('hvac.room.result', $room->id) }}" class="fw-bold text-dark">
                                    {{ $room->room_name }}
                                </a>
                                @if($room->room_type)
                                    <small class="text-muted d-block">{{ $room->room_type }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($room->calculation_mode == 'quick')
                                    <span class="badge bg-label-warning"><i class="mdi mdi-flash me-1"></i>Quick (Sales)</span>
                                @else
                                    <span class="badge bg-label-primary"><i class="mdi mdi-ruler-square me-1"></i>Detailed Engineering</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $room->length }}m × {{ $room->width }}m × {{ $room->height }}m</div>
                                <small class="text-muted">{{ number_format($room->floor_area, 1) }} m² / {{ number_format($room->room_volume, 1) }} m³</small>
                            </td>
                            <td>
                                <div><i class="mdi mdi-thermometer-lines text-danger"></i> {{ $room->indoor_temp }}°C / {{ $room->indoor_rh }}% RH</div>
                                <small class="text-muted">Safety: +{{ $room->safety_factor_percent }}%</small>
                            </td>
                            <td class="text-end">
                                @if($res)
                                    <span class="fw-bold text-primary fs-6">{{ number_format($res->design_load_btuh, 0) }}</span>
                                    <small class="text-muted d-block">BTU/h</small>
                                @else
                                    <span class="text-muted fst-italic">Belum dihitung</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($res)
                                    <span class="badge bg-light-success text-success font-monospace">{{ number_format($res->design_load_pk, 1) }} PK</span>
                                    <small class="text-muted d-block">{{ number_format($res->design_load_tr, 2) }} TR</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($res && $res->recommended_ac_model)
                                    <span class="badge bg-light-primary text-primary fw-semibold">{{ $res->recommended_unit_qty }}x {{ $res->recommended_ac_model }}</span>
                                    <small class="text-muted d-block">{{ number_format($res->recommended_ac_capacity_btuh, 0) }} BTU/h ({{ $res->recommended_ac_pk }} PK)</small>
                                @elseif($res)
                                    <span class="text-muted font-monospace">Estimasi {{ number_format($res->design_load_pk, 1) }} PK</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('hvac.room.result', $room->id) }}" class="btn btn-outline-primary" title="Lihat Hasil & Breakdown">
                                        <i class="mdi mdi-chart-donut"></i>
                                    </a>
                                    <a href="{{ route('hvac.room.print', $room->id) }}" target="_blank" class="btn btn-outline-secondary" title="Cetak Engineering Sheet">
                                        <i class="mdi mdi-printer"></i>
                                    </a>
                                    <a href="{{ route('hvac.room.edit', $room->id) }}" class="btn btn-outline-warning" title="Edit Parameter">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </a>
                                    <form action="{{ route('hvac.room.destroy', $room->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus ruangan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Hapus Ruangan">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="mdi mdi-door-closed fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada ruangan pada proyek ini.
                                <div class="mt-2">
                                    <a href="{{ route('hvac.room.create', $project->id) }}" class="btn btn-sm btn-primary">
                                        <i class="mdi mdi-plus me-1"></i> Tambahkan Ruangan Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
