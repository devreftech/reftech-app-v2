@extends('layouts.sales.app')
@section('title', 'Edit Profil & Pengaturan Akun - ' . ($user->name ?? Auth::user()->name))

@section('content')
    @php
        $targetUser = ($user && $user->exists) ? $user : Auth::user();
        $isClientVendor = ($targetUser->role === 'Client Vendor');
        $userAvatar = $targetUser->image ? url('/') . '/' . $targetUser->image : asset('assets/img/avatars/1.png');
        $rawPhone = $targetUser->phone ?? '';
        $phoneNum = preg_replace('/^\+62|^62|^0/', '', $rawPhone);
        $employee = $targetUser->employee;
    @endphp

    @if ($isClientVendor)
        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        {{-- ── CLIENT VENDOR SIMPLIFIED SETTING VIEW ─────────────────────────── --}}
        {{-- ══════════════════════════════════════════════════════════════════════ --}}

        {{-- Breadcrumb Navigation --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 text-muted small">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}" class="text-muted"><i class="mdi mdi-home-outline me-1"></i>Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Pengaturan Profil &amp; Akun</li>
                </ol>
            </nav>
            <a href="{{ url('/') }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="mdi mdi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-account-cog-outline text-primary"></i>
                    Pengaturan Profil Client Vendor
                </h4>
                <p class="text-muted small mb-0">Kelola nama akun, email login, serta password akun Anda dengan mudah.</p>
            </div>
        </div>

        {{-- Validation Error Alerts --}}
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-start gap-2">
                    <i class="mdi mdi-alert-circle-outline fs-5 mt-0.5"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">Periksa Kembali Data Formulir</h6>
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Session Success Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-check-circle-outline fs-5"></i>
                    <div class="fw-semibold">{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- MAIN FORM CARD FOR CLIENT VENDOR --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <form action="{{ route('profile.update', $targetUser->id) }}" method="POST" id="formClientVendorSettings">
                @csrf
                @method('patch')

                {{-- Section Informasi Akun --}}
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-account-edit-outline fs-5"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold text-dark">Informasi Akun</h6>
                </div>

                <div class="card-body p-4 border-bottom">
                    <div class="row g-3">
                        {{-- Nama Lengkap / Vendor --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark" for="name">
                                Nama Lengkap / Client Vendor <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
                                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name"
                                    value="{{ old('name', $targetUser->name) }}" placeholder="Contoh: PT. Reftech Vendor" required />
                            </div>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- E-mail --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark" for="email">
                                Alamat E-mail <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email"
                                    value="{{ old('email', $targetUser->email) }}" placeholder="vendor@example.com" required />
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section Ganti Password --}}
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-warning rounded p-1 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-shield-key-outline fs-5"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold text-dark">Keamanan &amp; Ganti Password</h6>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark" for="password">
                                Password Baru
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" autocomplete="new-password" placeholder="············" aria-describedby="togglePasswordVisibility">
                                <span class="input-group-text cursor-pointer" id="togglePasswordVisibility" title="Lihat/Sembunyikan Password">
                                    <i class="mdi mdi-eye-off-outline"></i>
                                </span>
                            </div>
                            <small class="text-muted">Biarkan kosong jika Anda tidak ingin mengubah password akun.</small>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Footer Action Buttons --}}
                <div class="card-footer bg-light-subtle py-3 px-4 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="text-muted small">
                        <i class="mdi mdi-information-outline me-1"></i> Pastikan alamat email yang digunakan valid untuk login.
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary px-3">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-xs fw-semibold">
                            <i class="mdi mdi-content-save-check-outline me-1 fs-5"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

    @else

        {{-- ══════════════════════════════════════════════════════════════════════ --}}
        {{-- ── STANDARD EMPLOYEE SETTING VIEW ─────────────────────────────────── --}}
        {{-- ══════════════════════════════════════════════════════════════════════ --}}

        {{-- Breadcrumb Navigation --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 text-muted small">
                    <li class="breadcrumb-item">
                        <a href="{{ url('/') }}" class="text-muted"><i class="mdi mdi-home-outline me-1"></i>Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('profile.show', $targetUser->id) }}" class="text-muted">Profil Karyawan</a>
                    </li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Edit Profil &amp; Pengaturan</li>
                </ol>
            </nav>
            <a href="{{ route('profile.show', $targetUser->id) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="mdi mdi-arrow-left"></i> Kembali ke Profil
            </a>
        </div>

        {{-- Page Header --}}
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <div>
                <h4 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-account-cog-outline text-primary"></i>
                    Pengaturan Profil &amp; Akun
                </h4>
                <p class="text-muted small mb-0">Kelola informasi data diri, foto avatar, banner profil, kontak, serta keamanan akun Anda.</p>
            </div>
        </div>

        {{-- Validation Error Alerts --}}
        @if (isset($errors) && $errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-start gap-2">
                    <i class="mdi mdi-alert-circle-outline fs-5 mt-0.5"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">Periksa Kembali Data Formulir</h6>
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Session Success Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-check-circle-outline fs-5"></i>
                    <div class="fw-semibold">{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- MAIN FORM CARD --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <form action="{{ route('profile.update', $targetUser->id) }}" method="POST" enctype="multipart/form-data" id="formAccountSettings">
                @csrf
                @method('patch')

                {{-- SECTION 1: FOTO AVATAR & BANNER PROFIL --}}
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-primary rounded p-1 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-image-album fs-5"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold text-dark">Foto Profil &amp; Banner Sampul</h6>
                </div>

                <div class="card-body p-4 border-bottom">
                    <div class="row g-4">
                        {{-- Avatar Upload Box --}}
                        <div class="col-12 col-lg-6">
                            <div class="p-3 border rounded-3 bg-light-subtle h-100">
                                <label class="form-label fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                                    <i class="mdi mdi-account-box-outline text-primary"></i> Foto Avatar
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative flex-shrink-0">
                                        <img src="{{ $userAvatar }}" alt="{{ $targetUser->name }}"
                                            class="rounded-circle shadow-sm border border-2 border-white object-cover" 
                                            id="uploadedAvatar" style="width: 88px; height: 88px; object-fit: cover;">
                                    </div>
                                    <div class="button-wrapper">
                                        <label for="uploadAvatarInput" class="btn btn-primary btn-sm mb-1.5 shadow-xs waves-effect waves-light cursor-pointer">
                                            <i class="mdi mdi-upload me-1"></i> Pilih Foto Baru
                                            <input type="file" id="uploadAvatarInput" class="account-file-input" name="image" hidden accept="image/png, image/jpeg, image/jpg, image/webp">
                                        </label>
                                        <div class="text-muted small">
                                            Format JPG, PNG, atau WEBP. Maks. 5MB.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Banner Upload Box --}}
                        <div class="col-12 col-lg-6">
                            <div class="p-3 border rounded-3 bg-light-subtle h-100">
                                <label class="form-label fw-bold text-dark mb-2 d-flex align-items-center gap-1.5">
                                    <i class="mdi mdi-panorama-outline text-primary"></i> Banner Sampul Profil
                                </label>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 border overflow-hidden shadow-xs flex-shrink-0" 
                                        style="width: 140px; height: 75px; background: {{ $targetUser->banner ? 'url(' . asset($targetUser->banner) . ') center/cover no-repeat' : 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6366f1 100%)' }};" 
                                        id="settingBannerPreview"></div>
                                    <div class="button-wrapper">
                                        <label for="uploadBannerInput" class="btn btn-outline-primary btn-sm mb-1.5 shadow-xs waves-effect cursor-pointer">
                                            <i class="mdi mdi-camera-outline me-1"></i> Pilih Banner
                                            <input type="file" id="uploadBannerInput" name="banner" hidden accept="image/png, image/jpeg, image/jpg, image/webp">
                                        </label>
                                        <div class="text-muted small">
                                            Rekomendasi rasio 4:1 atau 16:9. Maks. 5MB.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: DATA PRIBADI & KONTAK --}}
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-info rounded p-1 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-card-account-details-outline fs-5"></i>
                    </div>
                    <h6 class="card-title mb-0 fw-bold text-dark">Informasi Pribadi &amp; Kontak</h6>
                </div>

                <div class="card-body p-4 border-bottom">
                    <div class="row g-3">
                        {{-- Nama Lengkap --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark" for="name">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="mdi mdi-account-outline"></i></span>
                                <input class="form-control @error('name') is-invalid @enderror" type="text" id="name" name="name"
                                    value="{{ old('name', $targetUser->name) }}" placeholder="Contoh: Budi Santoso" required />
                            </div>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- E-mail --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark" for="email">
                                Alamat E-mail <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="mdi mdi-email-outline"></i></span>
                                <input class="form-control @error('email') is-invalid @enderror" type="email" id="email" name="email"
                                    value="{{ old('email', $targetUser->email) }}" placeholder="budi@example.com" required />
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nomor Telepon / WA --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark" for="phone">
                                Nomor WhatsApp / HP <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text fw-bold text-muted bg-light">+62</span>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                                    value="{{ old('phone', $phoneNum) }}" placeholder="8123456789" required pattern="[0-9]*" inputmode="numeric" />
                            </div>
                            <small class="text-muted">Masukkan tanpa angka 0 atau +62 di depan (Contoh: 8123456789)</small>
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark" for="birthday">
                                Tanggal Lahir
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="mdi mdi-cake-variant-outline"></i></span>
                                <input class="form-control @error('birthday') is-invalid @enderror" type="date" id="birthday" name="birthday"
                                    value="{{ old('birthday', $targetUser->birthday ? \Carbon\Carbon::parse($targetUser->birthday)->format('Y-m-d') : '') }}" />
                            </div>
                            @error('birthday')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Alamat Domisili --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark" for="address">
                                Alamat Tinggal / Domisili
                            </label>
                            <textarea class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Tuliskan alamat lengkap tempat tinggal..." name="address" id="address">{{ old('address', $targetUser->address) }}</textarea>
                            @error('address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: KEAMANAN & GANTI PASSWORD --}}
                <div class="card-header bg-transparent py-3 border-bottom d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-warning rounded p-1 d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-shield-key-outline fs-5"></i>
                    </div>
                    <div>
                        <h6 class="card-title mb-0 fw-bold text-dark">Keamanan Akun &amp; Password</h6>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark" for="password">
                                Password Baru
                            </label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text"><i class="mdi mdi-lock-outline"></i></span>
                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                                    name="password" autocomplete="new-password" placeholder="············" aria-describedby="togglePasswordVisibility">
                                <span class="input-group-text cursor-pointer" id="togglePasswordVisibility" title="Lihat/Sembunyikan Password">
                                    <i class="mdi mdi-eye-off-outline"></i>
                                </span>
                            </div>
                            <small class="text-muted">Biarkan kosong jika Anda tidak ingin mengubah password akun.</small>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- CARD FOOTER ACTIONS --}}
                <div class="card-footer bg-light-subtle py-3 px-4 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="text-muted small">
                        <i class="mdi mdi-information-outline me-1"></i> Pastikan semua perubahan sudah sesuai sebelum menekan simpan.
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('profile.show', $targetUser->id) }}" class="btn btn-outline-secondary px-3">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4 shadow-xs fw-semibold">
                            <i class="mdi mdi-content-save-check-outline me-1 fs-5"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @endif
@endsection

@push('after-style')
<style>
    /* ── Nav Pills Custom ────────────── */
    .nav-pills-custom .nav-link {
        border-radius: 8px;
        padding: 10px 18px;
        font-weight: 500;
        color: #566a7f;
        transition: all 0.2s ease;
        background: #ffffff;
        border: 1px solid #e0e4e8;
    }
    .nav-pills-custom .nav-link:hover {
        background: rgba(105, 108, 255, 0.08);
        color: #696cff;
        border-color: #696cff;
    }
    .nav-pills-custom .nav-link.active {
        background: #696cff;
        color: #ffffff;
        border-color: #696cff;
        box-shadow: 0 4px 12px rgba(105, 108, 255, 0.35);
    }
    .nav-pills-custom .nav-link.active .badge {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>
@endpush

@push('after-script')
<script>
    $(document).ready(function() {
        // Toggle password visibility
        $("#togglePasswordVisibility").on('click', function() {
            var icon = $(this).find('i');
            var passwordInput = $('#password');
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('mdi-eye-outline').addClass('mdi-eye-off-outline');
            }
        });

        // Live preview avatar image
        $('#uploadAvatarInput').on('change', function(e) {
            const [file] = e.target.files;
            if (file) {
                $('#uploadedAvatar').attr('src', URL.createObjectURL(file));
            }
        });

        // Live preview banner image
        $('#uploadBannerInput').on('change', function(e) {
            const [file] = e.target.files;
            if (file) {
                $('#settingBannerPreview').css('background-image', `url('${URL.createObjectURL(file)}')`);
            }
        });

        // Restrict phone input to numbers
        $('#phone').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
</script>
@endpush
