@extends('layouts.sales.app')
@section('title', 'Tambah Karyawan Baru - HRM')

@section('content')
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
                <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Tambah Karyawan</li>
            </ol>
        </nav>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-heading">
                Tambah Karyawan Baru
            </h4>
            <span class="badge bg-label-primary px-2 py-1">
                <i class="mdi mdi-account-plus-outline me-1"></i>Onboarding Form
            </span>
        </div>
        <p class="text-muted small mb-0 mt-1">
            Daftarkan personil baru, tentukan struktur penempatan divisi & jabatan, serta kelola status masa kerja.
        </p>
    </div>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary shadow-xs">
            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Hub Karyawan
        </a>
    </div>
</div>

{{-- Main Grid Layout --}}
<div class="row g-4">
    {{-- Left Column: Onboarding Guide & Live Preview --}}
    <div class="col-12 col-lg-4 col-xl-3">
        <div class="d-flex flex-column gap-3" style="position: sticky; top: 1rem;">
            
            {{-- Preview Card --}}
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body pt-4 pb-3">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center bg-label-primary shadow-xs mb-3"
                         style="width: 84px; height: 84px;">
                        <i class="mdi mdi-account-plus fs-1 text-primary"></i>
                    </div>

                    <h5 class="fw-bold text-heading mb-1" id="previewEmployeeName">Karyawan Baru</h5>
                    <p class="text-muted small mb-3">Pratinjau Data Pegawai Baru</p>

                    <div class="p-2 rounded bg-light text-start small mb-3">
                        <div class="d-flex align-items-center text-muted mb-1">
                            <i class="mdi mdi-shield-check-outline text-success me-2"></i>
                            <span>Autofill Akun Login Aktif</span>
                        </div>
                        <div class="d-flex align-items-center text-muted">
                            <i class="mdi mdi-lightning-bolt text-warning me-2"></i>
                            <span>Deteksi Cepat Kontrak PKWT</span>
                        </div>
                    </div>

                    <hr class="my-3 opacity-25">

                    {{-- Onboarding Checklist --}}
                    <div class="text-start small">
                        <h6 class="fw-bold text-heading mb-2 fs-7 text-uppercase">Tahapan Pendaftaran:</h6>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2 text-muted">
                            <li class="d-flex align-items-start">
                                <i class="mdi mdi-numeric-1-circle text-primary me-2 fs-6"></i>
                                <span>Pilih akun user sistem atau isi identitas NIK & kontak.</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="mdi mdi-numeric-2-circle text-primary me-2 fs-6"></i>
                                <span>Tentukan penempatan departemen & posisi jabatan.</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="mdi mdi-numeric-3-circle text-primary me-2 fs-6"></i>
                                <span>Pilih status ikatan kerja (Tetap, Kontrak, Probation).</span>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="mdi mdi-numeric-4-circle text-primary me-2 fs-6"></i>
                                <span>Input skema kompensasi gaji & tunjangan tetap.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Policy Hint Card --}}
            <div class="card shadow-sm border-0 bg-label-info">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-2 text-info d-flex align-items-center">
                        <i class="mdi mdi-information-outline me-1 fs-5"></i> Tips HR
                    </h6>
                    <p class="small text-muted mb-0">
                        Jika pegawai belum memiliki akun login portal, Anda tetap dapat menyimpannya tanpa menautkan user. Akun portal dapat ditautkan kemudian saat profil user dibuat.
                    </p>
                </div>
            </div>

        </div>
    </div>

    {{-- Right Column: Form Sections --}}
    <div class="col-12 col-lg-8 col-xl-9">
        <form action="{{ route('employees.store') }}" method="POST">
            @csrf
            @include('pages.hr.employees._form')
        </form>
    </div>
</div>
@endsection
