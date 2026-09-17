@extends('layouts.sales.app')
@section('title', 'Edit Karyawan - ' . ($employee->user?->name ?? $employee->nik ?? 'HRM'))

@section('content')
@php
    $statusColors = [
        'Tetap' => 'success',
        'Kontrak' => 'info',
        'Probation' => 'warning',
        'Resign' => 'secondary',
        'Perlu Verifikasi' => 'danger',
    ];
    $statusColor = $statusColors[$employee->employment_status] ?? 'secondary';

    $empName = $employee->user?->name ?? ($employee->nik ? 'Karyawan ' . $employee->nik : 'Karyawan #' . $employee->id);
    $words = array_filter(explode(' ', trim($empName)));
    $inits = '';
    foreach (array_slice($words, 0, 2) as $w) {
        $inits .= strtoupper(substr($w, 0, 1));
    }
    if (empty($inits)) $inits = 'KR';

    // Calculate tenure if join_date exists
    $tenure = null;
    if ($employee->join_date) {
        try {
            $joinCarbon = \Illuminate\Support\Carbon::parse($employee->join_date);
            $tenure = $joinCarbon->diffForHumans(null, true);
        } catch (\Exception $e) {
            $tenure = null;
        }
    }
@endphp

{{-- Header & Breadcrumb --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 text-muted small">
                <li class="breadcrumb-item">
                    <a href="{{ route('employees.index') }}" class="text-muted">
                        <i class="mdi mdi-account-group-outline me-1"></i>HR Management
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('employees.index') }}" class="text-muted">Data Karyawan</a>
                </li>
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Edit Karyawan</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-heading">
                Edit Profil: {{ $empName }}
            </h4>
            <span class="badge bg-label-{{ $statusColor }} px-2 py-1">
                <i class="mdi mdi-circle-small me-1"></i>{{ $employee->employment_status ?? 'Status Belum Diatur' }}
            </span>
            @if ($employee->nik)
                <span class="badge bg-label-secondary font-monospace">
                    NIK: {{ $employee->nik }}
                </span>
            @endif
        </div>
    </div>

    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-label-primary shadow-xs">
            <i class="mdi mdi-account-eye-outline me-1"></i> Lihat Profil Detail
        </a>
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary shadow-xs">
            <i class="mdi mdi-arrow-left me-1"></i> Hub Karyawan
        </a>
    </div>
</div>

{{-- Main Grid Layout --}}
<div class="row g-4">
    {{-- Left Sidebar: Sticky Quick Profile & Summary --}}
    <div class="col-12 col-lg-4 col-xl-3">
        <div class="d-flex flex-column gap-3" style="position: sticky; top: 1rem;">
            
            {{-- Profile Card --}}
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body pt-4 pb-3">
                    {{-- Avatar --}}
                    <div class="position-relative d-inline-block mb-3">
                        @if ($employee->user?->image && file_exists(public_path($employee->user->image)))
                            <img src="{{ asset($employee->user->image) }}" alt="{{ $empName }}"
                                 class="rounded-circle shadow-sm"
                                 style="width: 90px; height: 90px; object-fit: cover; border: 3px solid #fff;">
                        @else
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-label-primary shadow-xs"
                                 style="width: 90px; height: 90px; font-size: 1.85rem; font-weight: 700;">
                                {{ $inits }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 p-1 bg-{{ $statusColor }} border border-white rounded-circle"
                              title="Status: {{ $employee->employment_status }}"
                              style="width: 16px; height: 16px;"></span>
                    </div>

                    <h5 class="fw-bold text-heading mb-1">{{ $empName }}</h5>
                    <p class="text-muted small mb-2">
                        {{ $employee->position?->name ?? 'Belum ada jabatan' }}
                    </p>

                    <div class="d-flex flex-wrap justify-content-center gap-1 mb-3">
                        <span class="badge bg-label-info">
                            <i class="mdi mdi-domain me-1"></i>{{ $employee->department?->name ?? 'Tanpa Departemen' }}
                        </span>
                        <span class="badge bg-label-{{ $statusColor }}">
                            {{ $employee->employment_status }}
                        </span>
                    </div>

                    <hr class="my-3 opacity-25">

                    {{-- Compact Meta Details --}}
                    <div class="text-start small d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="mdi mdi-card-account-details-outline me-1"></i> NIK</span>
                            <span class="fw-semibold font-monospace">{{ $employee->nik ?? '-' }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="mdi mdi-email-outline me-1"></i> Email</span>
                            <span class="fw-semibold text-truncate ms-2" style="max-width: 140px;" title="{{ $employee->user?->email ?? '-' }}">
                                {{ $employee->user?->email ?? '-' }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="mdi mdi-phone-outline me-1"></i> Telepon</span>
                            <span class="fw-semibold">{{ $employee->phone ?? '-' }}</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted"><i class="mdi mdi-calendar-check-outline me-1"></i> Masuk</span>
                            <span class="fw-semibold">
                                {{ $employee->join_date ? \Illuminate\Support\Carbon::parse($employee->join_date)->format('d/m/Y') : '-' }}
                            </span>
                        </div>

                        @if ($tenure)
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="mdi mdi-timer-sand me-1"></i> Masa Kerja</span>
                                <span class="badge bg-label-success">{{ $tenure }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="text-muted"><i class="mdi mdi-shield-account-outline me-1"></i> Akun Login</span>
                            @if ($employee->user_id)
                                <span class="badge bg-label-success">
                                    <i class="mdi mdi-check-circle-outline me-1"></i>Terhubung
                                </span>
                            @else
                                <span class="badge bg-label-secondary">
                                    <i class="mdi mdi-link-variant-off me-1"></i>Non-User
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Helper Guidance Card --}}
            <div class="card shadow-sm border-0 bg-label-primary">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-2 text-primary d-flex align-items-center">
                        <i class="mdi mdi-lightbulb-on-outline me-1 fs-5"></i> Catatan HR
                    </h6>
                    <ul class="mb-0 ps-3 small text-muted">
                        <li class="mb-1">Perubahan status ke <strong>Kontrak</strong> mewajibkan tanggal akhir kontrak.</li>
                        <li class="mb-1">Status <strong>Resign</strong> akan otomatis mengarsipkan data dari status aktif.</li>
                        <li>Gaji pokok & tunjangan disimpan dalam format nominal rupiah utuh.</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    {{-- Right Column: Form Sections --}}
    <div class="col-12 col-lg-8 col-xl-9">
        <form action="{{ route('employees.update', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('pages.hr.employees._form')
        </form>
    </div>
</div>
@endsection
