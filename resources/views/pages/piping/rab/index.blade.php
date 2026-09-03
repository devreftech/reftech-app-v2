@extends('layouts.sales.app')
@section('title', 'Estimasi / RAB Proyek Piping')

@section('content')
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="mdi mdi-calculator-variant text-primary me-2"></i>Estimasi / RAB Proyek Piping</h4>
            <p class="text-muted mb-0">Kalkulator estimasi HPP material perpipaan, tenaga kerja, margin laba, dan konversi ke Smart Quote.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('piping-materials.index') }}" class="btn btn-outline-secondary">
                <i class="mdi mdi-pipe me-1"></i> Master Material Piping
            </a>
            <a href="{{ route('piping-rab.create') }}" class="btn btn-primary">
                <i class="mdi mdi-plus me-1"></i> Buat RAB Proyek Baru
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

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="avatar bg-light-primary text-primary rounded p-2">
                        <i class="mdi mdi-file-document-multiple fs-3"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['total'] }}</div>
                        <small class="text-muted">Total Proyek RAB</small>
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
                        <small class="text-muted">Status Draft / Review</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="avatar bg-light-success text-success rounded p-2">
                        <i class="mdi mdi-check-decagram fs-3"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['converted'] }}</div>
                        <small class="text-muted">Sudah Jadi Smart Quote</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="avatar bg-light-info text-info rounded p-2">
                        <i class="mdi mdi-currency-usd fs-3"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold text-success">Rp {{ number_format($stats['total_selling_value'], 0, ',', '.') }}</div>
                        <small class="text-muted">Total Nilai Estimasi Jual</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0 fw-semibold">Daftar Estimasi RAB Piping</h5>
            </div>
            <form action="{{ route('piping-rab.index') }}" method="GET" class="d-flex gap-2" style="max-width: 380px;">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Status --</option>
                    <option value="Draft" {{ $status === 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Approved" {{ $status === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Converted" {{ $status === 'Converted' ? 'selected' : '' }}>Converted ke Quotation</option>
                </select>
                <div class="input-group input-group-merge">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="No. RAB / Client / Proyek..." value="{{ $search }}">
                    <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="mdi mdi-magnify"></i></button>
                </div>
                @if($search || $status)
                    <a href="{{ route('piping-rab.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. RAB & Tanggal</th>
                        <th>Klien & Lokasi Plant</th>
                        <th>Nama Proyek</th>
                        <th>Sales Rep</th>
                        <th class="text-end">Total HPP Internal</th>
                        <th class="text-end">Estimasi Margin</th>
                        <th class="text-end">Nilai Jual (Selling Price)</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rabs as $rab)
                        @php
                            $marginPercent = $rab->total_hpp > 0 ? round(($rab->total_margin / $rab->total_hpp) * 100, 1) : 0;
                            $statusBadge = match($rab->status) {
                                'Draft'     => 'warning',
                                'Approved'  => 'info',
                                'Converted' => 'success',
                                default     => 'secondary',
                            };
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('piping-rab.show', $rab->id) }}" class="fw-bold text-primary">
                                    {{ $rab->no_rab }}
                                </a>
                                @if($rab->revision_number > 0)
                                    <span class="badge bg-label-info ms-1">Rev {{ $rab->revision_number }}</span>
                                @endif
                                <div class="text-muted small">{{ $rab->rab_date ? $rab->rab_date->format('d M Y') : '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $rab->client ? $rab->client->company : 'Klien Non-Database / Umum' }}</div>
                                <small class="text-muted"><i class="mdi mdi-map-marker-outline me-1"></i>{{ $rab->location_plant ?: '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $rab->project_name }}</div>
                                <small class="text-muted">{{ $rab->sections->count() }} Section Area</small>
                            </td>
                            <td>
                                <span class="fw-semibold">{{ $rab->sales ? $rab->sales->name : '-' }}</span>
                            </td>
                            <td class="text-end fw-semibold text-muted">
                                Rp {{ number_format($rab->total_hpp, 0, ',', '.') }}
                            </td>
                            <td class="text-end">
                                <div class="fw-bold text-success">Rp {{ number_format($rab->total_margin, 0, ',', '.') }}</div>
                                <small class="badge bg-label-success" style="font-size: 10px;">+{{ $marginPercent }}%</small>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-primary fs-6">Rp {{ number_format($rab->total_selling_price, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-{{ $statusBadge }}">{{ $rab->status }}</span>
                                @if($rab->status === 'Converted' && $rab->converted_quotation_id)
                                    <a href="{{ route('unit-quotation.show', $rab->converted_quotation_id) }}" class="d-block small text-primary text-decoration-underline mt-1" title="Buka Smart Quote">
                                        <i class="mdi mdi-open-in-new"></i> Lihat Quote
                                    </a>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('piping-rab.show', $rab->id) }}" class="btn btn-outline-primary" title="Detail & Kalkulasi HPP">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </a>
                                    <a href="{{ route('piping-rab.edit', $rab->id) }}" class="btn btn-outline-secondary" title="Edit RAB">
                                        <i class="mdi mdi-pencil-outline"></i>
                                    </a>
                                    <form action="{{ route('piping-rab.revise', $rab->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Buat revisi baru dari RAB ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-info" title="Buat Revisi (Rev Baru)">
                                            <i class="mdi mdi-source-branch"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="mdi mdi-calculator-variant-outline fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada data Estimasi RAB Piping. Klik tombol <strong>Buat RAB Proyek Baru</strong> untuk mulai menghitung.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rabs->hasPages())
            <div class="card-footer bg-transparent py-3 border-top">
                {{ $rabs->links() }}
            </div>
        @endif
    </div>
@endsection
