@extends('layouts.sales.app')
@section('title', 'Portal Mandiri Karyawan - HRM')

@section('content')
<div class="card border-0 shadow-sm text-center py-5">
    <div class="card-body">
        <div class="avatar avatar-xl rounded-circle bg-label-warning mx-auto mb-3 d-flex align-items-center justify-content-center">
            <i class="mdi mdi-account-question-outline fs-1 text-warning"></i>
        </div>
        <h4 class="fw-bold text-heading mb-2">Profil Karyawan Belum Terhubung</h4>
        <p class="text-muted mx-auto" style="max-width: 500px;">
            Akun login Anda (<strong>{{ $user->name }}</strong>) belum ditautkan ke data kepegawaian HR.
            Fitur Portal Mandiri (ESS) seperti presensi mandiri, permohonan cuti, dan slip gaji hanya dapat diakses oleh staf yang terdaftar di modul HR.
        </p>
        <div class="mt-4">
            <a href="{{ route('employees.create') }}?user_id={{ $user->id }}" class="btn btn-primary px-4 shadow-xs me-2">
                <i class="mdi mdi-account-plus-outline me-1"></i> Hubungkan ke Data Karyawan
            </a>
            <a href="{{ url('/') }}" class="btn btn-label-secondary px-3">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
