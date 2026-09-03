@extends('layouts.sales.app')
@section('title', 'Schematic Diagram Proyek')

@section('content')
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="mdi mdi-vector-polyline text-primary me-2"></i>Schematic & P&ID Diagram</h4>
            <p class="text-muted mb-0">Pembuat dan manajemen diagram skematik sistem refrigerasi, alur kompresor, receiver tank, dan perpipaan proyek.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('schematics.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus me-1"></i> Buat Diagram Baru
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

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-primary rounded me-3 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-vector-polyline fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Diagram</span>
                        <h5 class="mb-0 fw-bold">{{ $stats['total'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-info rounded me-3 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-snowflake fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Refrigeration</span>
                        <h5 class="mb-0 fw-bold">{{ $stats['refrigeration'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-success rounded me-3 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-air-filter fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Compressed Air</span>
                        <h5 class="mb-0 fw-bold">{{ $stats['compressed'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="avatar avatar-md bg-label-warning rounded me-3 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-pipe fs-4"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">P&ID System</span>
                        <h5 class="mb-0 fw-bold">{{ $stats['pid'] }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('schematics.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Cari judul, nomor skematik, atau proyek..." value="{{ $search }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">-- Semua Jenis Sistem --</option>
                        <option value="Refrigeration System" {{ $type === 'Refrigeration System' ? 'selected' : '' }}>Refrigeration System</option>
                        <option value="Compressed Air System" {{ $type === 'Compressed Air System' ? 'selected' : '' }}>Compressed Air System</option>
                        <option value="Piping & Instrumentation (P&ID)" {{ $type === 'Piping & Instrumentation (P&ID)' ? 'selected' : '' }}>Piping & Instrumentation (P&ID)</option>
                        <option value="Other" {{ $type === 'Other' ? 'selected' : '' }}>Lainnya / General</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="client_id" class="form-select">
                        <option value="">-- Semua Klien --</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ $clientId == $c->id ? 'selected' : '' }}>{{ $c->company }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="mdi mdi-filter-outline me-1"></i>Filter</button>
                    @if($search || $type || $clientId)
                        <a href="{{ route('schematics.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="mdi mdi-refresh"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Schematics Cards Grid -->
    <div class="row g-4">
        @forelse($schematics as $sch)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                    <!-- Thumbnail Preview -->
                    <div class="bg-light border-bottom position-relative text-center overflow-hidden" style="height: 180px;">
                        @if($sch->preview_image)
                            <img src="{{ $sch->preview_image }}" alt="{{ $sch->title }}" class="img-fluid h-100 w-100" style="object-fit: contain; padding: 8px;">
                        @else
                            <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                <i class="mdi mdi-vector-polyline fs-1 opacity-25"></i>
                                <span class="small mt-1 opacity-75">Canvas Kosong</span>
                            </div>
                        @endif
                        <span class="badge bg-dark bg-opacity-75 position-absolute top-0 start-0 m-2 font-monospace" style="font-size: 11px;">
                            {{ $sch->schematic_number }}
                        </span>
                        <span class="badge {{ $sch->status === 'Final' ? 'bg-success' : 'bg-warning' }} position-absolute top-0 end-0 m-2">
                            {{ $sch->status }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-3 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h6 class="fw-bold mb-0 text-truncate" title="{{ $sch->title }}">
                                    <a href="{{ route('schematics.edit', $sch->id) }}" class="text-dark">{{ $sch->title }}</a>
                                </h6>
                            </div>
                            
                            <div class="text-muted small mb-2">
                                <i class="mdi mdi-tag-outline me-1"></i>{{ $sch->diagram_type }}
                            </div>

                            @if($sch->client)
                                <div class="small fw-semibold text-primary mb-1 text-truncate">
                                    <i class="mdi mdi-domain me-1"></i>{{ $sch->client->company }}
                                </div>
                            @endif

                            @if($sch->project_name)
                                <div class="small text-secondary mb-2 text-truncate">
                                    <i class="mdi mdi-briefcase-outline me-1"></i>{{ $sch->project_name }}
                                </div>
                            @endif
                        </div>

                        <!-- Card Footer Info & Actions -->
                        <div class="pt-3 border-top mt-3 d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="font-size: 11px;">
                                <i class="mdi mdi-clock-outline me-1"></i>{{ $sch->updated_at->diffForHumans() }}
                            </span>
                            <div class="btn-group">
                                <a href="{{ route('schematics.edit', $sch->id) }}" class="btn btn-sm btn-primary">
                                    <i class="mdi mdi-pencil-outline me-1"></i>Buka Editor
                                </a>
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <form method="POST" action="{{ route('schematics.duplicate', $sch->id) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="mdi mdi-content-copy me-2 text-info"></i>Duplikasi
                                            </button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('schematics.destroy', $sch->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus skematik ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="mdi mdi-trash-can-outline me-2"></i>Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="mdi mdi-vector-polyline text-muted opacity-50" style="font-size: 64px;"></i>
                        <h5 class="fw-bold mt-3 mb-1">Belum Ada Skematik Diagram</h5>
                        <p class="text-muted mb-3">Mulai buat skematik sistem kompresor, tangki receiver, dan perpipaan proyek Anda secara online.</p>
                        <a href="{{ route('schematics.create') }}" class="btn btn-primary">
                            <i class="mdi mdi-plus me-1"></i> Buat Skematik Pertama
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $schematics->links() }}
    </div>
@endsection
