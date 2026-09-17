@php
    $employee = $employee ?? null;
    $statusOptions = [
        'Tetap' => ['color' => 'success', 'desc' => 'Pegawai tetap / permanen'],
        'Kontrak' => ['color' => 'info', 'desc' => 'Perjanjian kerja waktu tertentu (PKWT)'],
        'Probation' => ['color' => 'warning', 'desc' => 'Masa percobaan kerja'],
        'Perlu Verifikasi' => ['color' => 'danger', 'desc' => 'Perlu verifikasi data HR'],
        'Resign' => ['color' => 'secondary', 'desc' => 'Sudah berhenti / keluar'],
    ];

    // Data user dipakai untuk autofill saat "Akun User" dipilih
    $userAutofillMap = $users->mapWithKeys(function ($user) {
        return [
            $user->id => [
                'name' => $user->name,
                'nik' => $user->nip,
                'address' => $user->address,
                'phone' => $user->phone,
                'birthday' => $user->birthday ? \Illuminate\Support\Carbon::parse($user->birthday)->format('Y-m-d') : null,
            ],
        ];
    });
@endphp

{{-- Validation Errors --}}
@if (isset($errors) && $errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-xs border-0 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="mdi mdi-alert-circle fs-4 me-2 text-danger"></i>
            <div>
                <strong>Periksa kembali formulir:</strong>
                <ul class="mb-0 ps-3 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="d-flex flex-column gap-4">

    {{-- SECTION 1: AKUN & INFORMASI PRIBADI --}}
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3 bg-light">
            <h6 class="card-title mb-0 fw-bold d-flex align-items-center text-heading">
                <i class="mdi mdi-account-cog-outline text-primary fs-5 me-2"></i>
                1. Akun Sistem & Data Personal
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                {{-- Akun User --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">Tautkan Akun Login Sistem <span class="text-muted fw-normal">(Opsional)</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="mdi mdi-account-circle-outline"></i></span>
                        <select name="user_id" id="employeeUserSelect" class="form-select">
                            <option value="">- Tidak tertaut ke akun user (Buruh / Non-Login) -</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected(old('user_id', $employee?->user_id ?? request('user_id')) == $user->id)>
                                    {{ $user->name }} &bull; {{ $user->email }} ({{ $user->role ?? 'Staf' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-text mt-1 text-muted small">
                        <i class="mdi mdi-information-outline text-primary me-1"></i>
                        Memilih akun login akan otomatis mengisi NIK, alamat, telepon, dan tanggal lahir bila masih kosong.
                    </div>
                </div>

                {{-- NIK --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">NIK (Nomor Induk Karyawan)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="mdi mdi-card-account-details-outline"></i></span>
                        <input type="text" name="nik" id="employeeNikInput" value="{{ old('nik', $employee?->nik) }}" 
                               class="form-control font-monospace" placeholder="Contoh: NIK2024001" maxlength="30">
                    </div>
                </div>

                {{-- No. Telepon --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">No. Telepon / WhatsApp</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="mdi mdi-phone-outline"></i></span>
                        <input type="text" name="phone" id="employeePhoneInput" value="{{ old('phone', $employee?->phone) }}" 
                               class="form-control" placeholder="Contoh: 081234567890" maxlength="20">
                    </div>
                </div>

                {{-- Tanggal Lahir --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Tanggal Lahir</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="mdi mdi-cake-variant-outline"></i></span>
                        <input type="date" name="birthday" id="employeeBirthdayInput" 
                               value="{{ old('birthday', optional($employee?->birthday)->format('Y-m-d')) }}" class="form-control">
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Alamat Domisili</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="mdi mdi-map-marker-outline"></i></span>
                        <textarea name="address" id="employeeAddressInput" class="form-control" rows="1" 
                                  placeholder="Alamat tempat tinggal...">{{ old('address', $employee?->address) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: ORGANISASI & PENEMPATAN --}}
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3 bg-light">
            <h6 class="card-title mb-0 fw-bold d-flex align-items-center text-heading">
                <i class="mdi mdi-domain text-primary fs-5 me-2"></i>
                2. Struktur Organisasi & Penempatan
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                {{-- Departemen --}}
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Departemen</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="mdi mdi-sitemap-outline"></i></span>
                        <select name="id_department" id="employeeDeptSelect" class="form-select">
                            <option value="">- Belum Ditentukan -</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" @selected(old('id_department', $employee?->id_department) == $department->id)>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Posisi / Jabatan --}}
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Posisi / Jabatan</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="mdi mdi-briefcase-outline"></i></span>
                        <select name="id_position" id="employeePosSelect" class="form-select">
                            <option value="">- Belum Ditentukan -</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}" @selected(old('id_position', $employee?->id_position) == $position->id)>
                                    {{ $position->name }} (Lvl {{ $position->level }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Tanggal Masuk (Join Date) --}}
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Tanggal Masuk (Join Date)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="mdi mdi-calendar-check-outline"></i></span>
                        <input type="date" name="join_date" id="employeeJoinDateInput" 
                               value="{{ old('join_date', optional($employee?->join_date)->format('Y-m-d')) }}" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 3: STATUS KEPEGAWAIAN & KONTRAK --}}
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3 bg-light">
            <h6 class="card-title mb-0 fw-bold d-flex align-items-center text-heading">
                <i class="mdi mdi-file-sign text-primary fs-5 me-2"></i>
                3. Status Kepegawaian & Masa Kontrak
            </h6>
        </div>
        <div class="card-body p-4">
            {{-- Status Radio Options --}}
            <label class="form-label fw-semibold d-block mb-2">
                Status Kepegawaian <span class="text-danger">*</span>
            </label>
            <div class="row g-2 mb-3">
                @php
                    $currentStatus = old('employment_status', $employee?->employment_status ?? 'Kontrak');
                @endphp
                @foreach ($statusOptions as $st => $cfg)
                    <div class="col-12 col-sm-6 col-md">
                        <label class="border rounded p-2 d-flex align-items-center justify-content-between h-100 cursor-pointer status-option-card {{ $currentStatus === $st ? 'border-primary bg-label-primary' : 'bg-white' }}"
                               style="cursor: pointer; transition: all 0.15s ease;">
                            <div class="d-flex align-items-center gap-2">
                                <input type="radio" name="employment_status" value="{{ $st }}" 
                                       class="form-check-input status-radio-input" @checked($currentStatus === $st) required>
                                <span class="fw-semibold small text-heading">{{ $st }}</span>
                            </div>
                            <span class="status-dot bg-{{ $cfg['color'] }}"></span>
                        </label>
                    </div>
                @endforeach
            </div>

            {{-- Dynamic Contract & Resign Dates --}}
            <div class="p-3 bg-light rounded border mt-3">
                <div class="row g-3">
                    {{-- Mulai Kontrak --}}
                    <div class="col-12 col-md-4" id="contractStartWrapper">
                        <label class="form-label fw-medium small">Mulai Kontrak (PKWT)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-muted"><i class="mdi mdi-calendar-start-outline"></i></span>
                            <input type="date" name="contract_start_date" id="contractStartInput" 
                                   value="{{ old('contract_start_date', optional($employee?->contract_start_date)->format('Y-m-d')) }}" 
                                   class="form-control bg-white">
                        </div>
                    </div>

                    {{-- Berakhir Kontrak --}}
                    <div class="col-12 col-md-4" id="contractEndWrapper">
                        <label class="form-label fw-medium small">Berakhir Kontrak (PKWT)</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-muted"><i class="mdi mdi-calendar-end-outline"></i></span>
                            <input type="date" name="contract_end_date" id="contractEndInput" 
                                   value="{{ old('contract_end_date', optional($employee?->contract_end_date)->format('Y-m-d')) }}" 
                                   class="form-control bg-white">
                        </div>
                    </div>

                    {{-- Tanggal Resign --}}
                    <div class="col-12 col-md-4" id="resignDateWrapper">
                        <label class="form-label fw-medium small text-danger">Tanggal Resign / Keluar</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white text-danger"><i class="mdi mdi-account-minus-outline"></i></span>
                            <input type="date" name="resign_date" id="resignDateInput" 
                                   value="{{ old('resign_date', optional($employee?->resign_date)->format('Y-m-d')) }}" 
                                   class="form-control bg-white">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 4: KEBIJAKAN PRESENSI & ABSENSI ONLINE --}}
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom py-3 bg-light">
            <h6 class="card-title mb-0 fw-bold d-flex align-items-center text-heading">
                <i class="mdi mdi-clock-check-outline text-primary fs-5 me-2"></i>
                4. Kebijakan Presensi &amp; Absensi Online
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="p-3 border rounded bg-light">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                    <div class="me-3">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="fw-bold text-heading">Izinkan / Wajibkan Absensi Online (ESS)</span>
                            <span class="badge bg-label-info font-monospace fs-8">Portal &amp; Navbar Clock In</span>
                        </div>
                        <p class="text-muted small mb-0">
                            Jika diaktifkan, karyawan dapat melakukan <strong>Clock In</strong> dan <strong>Clock Out</strong> secara mandiri melalui Portal Karyawan &amp; tombol cepat di Top Navbar. Nonaktifkan jika karyawan masuk kategori bebas absensi online (misal: Direksi / Manajer Khusus).
                        </p>
                    </div>
                    <div class="form-check form-switch form-switch-lg mb-0 align-self-start align-self-sm-center">
                        <input type="hidden" name="can_online_attendance" value="0">
                        <input class="form-check-input cursor-pointer" type="checkbox" name="can_online_attendance" value="1" id="canOnlineAttendanceToggle"
                               style="width: 3.2rem; height: 1.8rem;"
                               @checked(old('can_online_attendance', $employee ? $employee->can_online_attendance : true))>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions Card Footer --}}
        <div class="card-footer border-top py-3 d-flex flex-wrap align-items-center justify-content-between gap-2 bg-white">
            <div class="text-muted small">
                <i class="mdi mdi-check-circle-outline text-success me-1"></i>
                Pastikan data yang diinputkan sudah sesuai sebelum menyimpan.
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ $employee ? route('employees.show', $employee->id) : route('employees.index') }}" class="btn btn-label-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary shadow-sm px-4">
                    <i class="mdi mdi-content-save-outline me-1"></i>
                    {{ $employee ? 'Simpan Perubahan' : 'Simpan Karyawan' }}
                </button>
            </div>
        </div>
    </div>

</div>

{{-- Autofill & UI Scripts --}}
<script>
    (function () {
        var userAutofillMap = @json($userAutofillMap);

        var fieldMap = {
            nik: 'employeeNikInput',
            address: 'employeeAddressInput',
            phone: 'employeePhoneInput',
            birthday: 'employeeBirthdayInput',
        };

        // Autofill on user select
        document.getElementById('employeeUserSelect')?.addEventListener('change', function () {
            var data = userAutofillMap[this.value];
            if (!data) return;

            var filledCount = 0;
            Object.keys(fieldMap).forEach(function (key) {
                var el = document.getElementById(fieldMap[key]);
                if (el && !el.value && data[key]) {
                    el.value = data[key];
                    filledCount++;
                }
            });

            // Update live preview name if on create page
            var previewNameEl = document.getElementById('previewEmployeeName');
            if (previewNameEl && data.name) {
                previewNameEl.textContent = data.name;
            }
        });

        // Trigger change on initial load if user already pre-selected
        var initUserSelect = document.getElementById('employeeUserSelect');
        if (initUserSelect && initUserSelect.value) {
            initUserSelect.dispatchEvent(new Event('change'));
        }

        // Interactive status option styling & highlight
        var statusRadios = document.querySelectorAll('.status-radio-input');
        statusRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.status-option-card').forEach(function (card) {
                    card.classList.remove('border-primary', 'bg-label-primary');
                    card.classList.add('bg-white');
                });
                var parentCard = this.closest('.status-option-card');
                if (parentCard) {
                    parentCard.classList.remove('bg-white');
                    parentCard.classList.add('border-primary', 'bg-label-primary');
                }

                // Contract/Resign highlights
                var val = this.value;
                var contractStart = document.getElementById('contractStartWrapper');
                var contractEnd = document.getElementById('contractEndWrapper');
                var resignWrap = document.getElementById('resignDateWrapper');

                if (val === 'Kontrak') {
                    contractStart?.classList.add('border-start', 'border-info', 'ps-2');
                    contractEnd?.classList.add('border-start', 'border-info', 'ps-2');
                } else {
                    contractStart?.classList.remove('border-start', 'border-info', 'ps-2');
                    contractEnd?.classList.remove('border-start', 'border-info', 'ps-2');
                }

                if (val === 'Resign') {
                    resignWrap?.classList.add('border-start', 'border-danger', 'ps-2');
                } else {
                    resignWrap?.classList.remove('border-start', 'border-danger', 'ps-2');
                }
            });
        });
    })();
</script>
