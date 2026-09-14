@extends('layouts.sales.app')
@section('title', 'Pengaturan PIN Keamanan - Finance Reftech')

@push('after-style')
<style>
    .security-card {
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .security-status-badge {
        font-size: 13px;
        padding: 6px 14px;
        border-radius: 30px;
    }
    .pin-display-dots {
        letter-spacing: 6px;
        font-size: 20px;
        font-family: monospace;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance /</span> Pengaturan PIN Keamanan
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-shield-lock me-1"></i> Kelola kode PIN otorisasi untuk proteksi akses menu <strong>Kas &amp; Bank</strong> dan <strong>Petty Cash</strong>
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('finance.security.lock') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm shadow-sm">
                    <i class="mdi mdi-lock me-1"></i> Kunci Sesi Vault Sekarang
                </button>
            </form>
            <a href="{{ route('bank.index') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="mdi mdi-bank me-1"></i> Buka Kas &amp; Bank
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="mdi mdi-information-outline me-1"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- Left Column: Status Keamanan --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm security-card h-100 bg-white">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-shield-check text-success me-1"></i> Status Keamanan Vault
                    </h6>
                    <span class="badge bg-label-success security-status-badge">
                        <i class="mdi mdi-check-circle me-1"></i> Proteksi Aktif
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="text-center py-3">
                        <div class="avatar-initial rounded-circle bg-label-primary p-3 d-inline-flex mb-3" style="width: 72px; height: 72px;">
                            <i class="mdi mdi-lock-outline fs-1"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">PIN Otoritas Finance</h5>
                        <p class="text-muted small mb-3">
                            Setiap staf (Accounting, Admin, Finance) wajib memasukkan PIN ini untuk membuka data saldo bank dan kas kecil.
                        </p>
                        <div class="p-3 bg-light rounded border d-inline-block px-4">
                            <small class="text-muted d-block text-uppercase" style="font-size: 11px;">Format Standar:</small>
                            <span class="fw-bold text-dark pin-display-dots">&bull;&bull;&bull;&bull;&bull;&bull;</span>
                            <small class="text-muted d-block" style="font-size: 10px;">(6 Digit Angka Rahasia)</small>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="vstack gap-2 small">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Menu Terproteksi:</span>
                            <span class="fw-semibold text-dark">Kas &amp; Bank, Petty Cash</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Terakhir Diperbarui:</span>
                            <span class="fw-semibold text-dark">
                                {{ $vault->updated_at ? $vault->updated_at->translatedFormat('d F Y, H:i') : 'PIN Bawaan Sistem' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Diperbarui Oleh:</span>
                            <span class="fw-semibold text-dark">
                                {{ $vault->updater ? $vault->updater->name : 'Sistem Reftech' }}
                            </span>
                        </div>
                        @if($isDeveloper)
                            <div class="d-flex justify-content-between align-items-center mt-2 p-2 bg-label-warning rounded">
                                <span><i class="mdi mdi-code-tags me-1"></i> Bypass Developer:</span>
                                <strong class="text-warning">121212</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Form Ubah PIN --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm security-card h-100 bg-white">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold text-dark">
                        <i class="mdi mdi-key-change text-primary me-1"></i> Ubah / Perbarui PIN Keamanan
                    </h6>
                    <small class="text-muted">Hanya role Finance Manager dan Developer yang memiliki akses untuk mengubah PIN ini</small>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('finance.security.update_pin') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="current_pin">
                                Masukkan PIN Saat Ini <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
                                <input type="password" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                                       name="current_pin" id="current_pin"
                                       class="form-control @error('current_pin') is-invalid @enderror"
                                       placeholder="6 digit PIN lama" required autocomplete="current-password">
                            </div>
                            @error('current_pin')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Jika belum pernah diubah, PIN default adalah <strong>123456</strong>.</small>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="new_pin">
                                    PIN Baru (6 Digit) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="mdi mdi-key-variant"></i></span>
                                    <input type="password" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                                           name="new_pin" id="new_pin"
                                           class="form-control @error('new_pin') is-invalid @enderror"
                                           placeholder="6 digit angka baru" required autocomplete="new-password">
                                </div>
                                @error('new_pin')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="new_pin_confirmation">
                                    Ulangi PIN Baru <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="mdi mdi-check-all"></i></span>
                                    <input type="password" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                                           name="new_pin_confirmation" id="new_pin_confirmation"
                                           class="form-control @error('new_pin_confirmation') is-invalid @enderror"
                                           placeholder="Ketik ulang PIN baru" required autocomplete="new-password">
                                </div>
                                @error('new_pin_confirmation')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded border mb-4">
                            <div class="d-flex align-items-start gap-2">
                                <i class="mdi mdi-shield-alert-outline text-warning fs-5 mt-1"></i>
                                <div class="small text-muted">
                                    <strong>Perhatian:</strong> Setelah PIN berhasil diperbarui, seluruh staf Accounting, Admin, dan Finance lainnya harus memasukkan PIN baru ini saat membuka menu Kas &amp; Bank serta Petty Cash.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-label-secondary">Reset</button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="mdi mdi-content-save-outline me-1"></i> Simpan &amp; Terapkan PIN Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
