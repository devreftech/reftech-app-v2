<style>
    /* Ultra-smooth modal entrance and backdrop */
    .smooth-user-modal.modal.fade .modal-dialog {
        transform: scale(0.96) translateY(-16px);
        opacity: 0;
        transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.28s ease !important;
    }
    .smooth-user-modal.modal.show .modal-dialog {
        transform: scale(1) translateY(0) !important;
        opacity: 1 !important;
    }
    .modal-backdrop.fade {
        opacity: 0;
        transition: opacity 0.25s ease-out !important;
    }
    .modal-backdrop.show {
        opacity: 0.55 !important;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
    .smooth-user-modal .modal-xl {
        max-width: 1140px;
    }
    @media (min-width: 1400px) {
        .smooth-user-modal .modal-xl {
            max-width: 1220px;
        }
    }
    .smooth-user-modal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 24px 64px rgba(15, 23, 42, 0.2), 0 0 1px rgba(15, 23, 42, 0.12);
        overflow: hidden;
    }
    .smooth-user-modal .modal-header {
        background: linear-gradient(180deg, #fbfcfe 0%, #f4f6fa 100%);
        border-bottom: 1px solid #e7ebf0;
        padding: 1.1rem 1.75rem;
    }
    .smooth-user-modal .modal-body {
        padding: 1.5rem 1.75rem;
        background: #ffffff;
        max-height: calc(88vh - 110px);
        overflow-y: auto;
    }
    .smooth-user-modal .modal-footer {
        background: #f8f9fc;
        border-top: 1px solid #e7ebf0;
        padding: 0.9rem 1.75rem;
    }
    .panel-left-sidebar {
        background: #f8fafd;
        border: 1px solid #e3e8f3;
        border-radius: 14px;
        padding: 1.35rem;
        height: 100%;
    }
    .form-section-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
        margin-bottom: 0.35rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px dashed #e2e5eb;
    }
    .form-section-header.first-header {
        margin-top: 0;
    }
    .form-section-badge {
        width: 26px;
        height: 26px;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        background: rgba(105, 108, 255, 0.12);
        color: #696cff;
    }
    .form-section-title {
        font-weight: 700;
        font-size: 0.82rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin: 0;
        color: #4b5563;
    }
    .target-sales-card {
        background: #f6f8fd;
        border: 1px solid #dce4f9;
        border-radius: 12px;
        padding: 1.15rem;
        margin-top: 1rem;
        transition: all 0.3s ease;
    }
    .avatar-wrapper-box {
        background: #ffffff;
        border: 1px dashed #d5dced;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        margin-bottom: 1.25rem;
    }
</style>

<form action="{{ @$users ? route('employee.update', @$users->id) : route('employee.store') }}" method="post"
    enctype="multipart/form-data">
    @csrf

    @if (@$users)
        @method('patch')
    @endif

    <div class="modal fade smooth-user-modal" id="{{ @$users ? 'updateUsers-' . @$users->id : 'createUsers' }}"
        tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="mdi mdi-account-cog-outline text-primary fs-4"></i>
                            {{ @$users ? 'Update Akun: ' . @$users->name : 'Create Account (Tambah User Baru)' }}
                        </h5>
                        <small class="text-muted">
                            {{ @$users ? 'Perbarui data hak akses dan profil karyawan' : 'Pilih role akun, form akan menyesuaikan jabatan, area, dan target secara otomatis' }}
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-4">
                        <!-- KOLOM KIRI: FOTO & HAK AKSES AKUN (LOGIN) -->
                        <div class="col-lg-4 col-md-5">
                            <div class="panel-left-sidebar">
                                <!-- Upload Foto Profil -->
                                <div class="avatar-wrapper-box">
                                    <div class="position-relative d-inline-block mb-2">
                                        @if (@$users && @$users->image)
                                            <img src="{{ url('') . '/' . @$users->image }}" alt="user-avatar"
                                                class="rounded-circle shadow-sm border" id="uploadedAvatar{{ @$users ? '-' . @$users->id : '' }}"
                                                style="width: 88px; height: 88px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('asset/profile/profile.jpg') }}" alt="user-avatar"
                                                class="rounded-circle shadow-sm border" id="uploadedAvatar{{ @$users ? '-' . @$users->id : '' }}"
                                                style="width: 88px; height: 88px; object-fit: cover;">
                                        @endif
                                    </div>
                                    <div class="d-flex justify-content-center gap-2">
                                        <label for="upload{{ @$users ? '-' . @$users->id : '' }}" class="btn btn-primary btn-xs px-3 py-1 waves-effect waves-light m-0">
                                            <span><i class="mdi mdi-camera-outline me-1"></i> Ganti Foto</span>
                                            <input type="file" id="upload{{ @$users ? '-' . @$users->id : '' }}" class="account-file-input" name="image"
                                                hidden="" accept="image/png, image/jpeg, image/jpg">
                                        </label>
                                        <button type="button" class="btn btn-outline-secondary btn-xs px-2 py-1 account-image-reset waves-effect m-0">
                                            <span>Reset</span>
                                        </button>
                                    </div>
                                    <div class="text-muted small mt-1" style="font-size: 0.72rem;">JPG / PNG maks 2MB (Opsional)</div>
                                </div>

                                <!-- Hak Akses & Kredensial -->
                                <div class="form-section-header first-header mb-3">
                                    <span class="form-section-badge"><i class="mdi mdi-shield-account-outline"></i></span>
                                    <h6 class="form-section-title">Hak Akses &amp; Login</h6>
                                </div>

                                <div class="row g-3">
                                    <!-- Role -->
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select user-role-select fw-semibold text-primary" id="roleSelect-{{ @$users ? @$users->id : 'create' }}"
                                                name="role" required>
                                                @php
                                                    $currentRole = old('role', @$users->role ?? 'Sales');
                                                @endphp
                                                <option value="Sales" {{ $currentRole == 'Sales' ? 'selected' : '' }}>Sales</option>
                                                <option value="Admin" {{ $currentRole == 'Admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="Developer" {{ $currentRole == 'Developer' ? 'selected' : '' }}>Developer</option>
                                                <option value="Project Manager" {{ $currentRole == 'Project Manager' ? 'selected' : '' }}>Project Manager</option>
                                                <option value="Accounting" {{ $currentRole == 'Accounting' ? 'selected' : '' }}>Accounting</option>
                                                <option value="Finance Manager" {{ $currentRole == 'Finance Manager' ? 'selected' : '' }}>Finance Manager</option>
                                                <option value="Logistic" {{ $currentRole == 'Logistic' ? 'selected' : '' }}>Logistic</option>
                                                <option value="Technician" {{ $currentRole == 'Technician' ? 'selected' : '' }}>Technician</option>
                                                <option value="Coordinator" {{ $currentRole == 'Coordinator' ? 'selected' : '' }}>Service Coordinator</option>
                                                <option value="ServiceM" {{ $currentRole == 'ServiceM' ? 'selected' : '' }}>Service Admin</option>
                                                <option value="Supervisor" {{ $currentRole == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                                                <option value="Support" {{ $currentRole == 'Support' ? 'selected' : '' }}>Technical Support</option>
                                                <option value="Client" {{ $currentRole == 'Client' ? 'selected' : '' }}>Client</option>
                                            </select>
                                            <label for="roleSelect-{{ @$users ? @$users->id : 'create' }}">Pilih Role Akun</label>
                                        </div>
                                    </div>

                                    <!-- Status Akun -->
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <select class="form-select" id="activeSelect-{{ @$users ? @$users->id : 'create' }}" name="active" required>
                                                <option value="1" {{ old('active', @$users->active ?? '1') == '1' ? 'selected' : '' }}>Active (Aktif)</option>
                                                <option value="0" {{ old('active', @$users->active) == '0' ? 'selected' : '' }}>Non Active (Non-Aktif)</option>
                                            </select>
                                            <label for="activeSelect-{{ @$users ? @$users->id : 'create' }}">Status Akun</label>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="col-12">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control" type="email" id="email-{{ @$users ? @$users->id : 'create' }}" name="email"
                                                value="{{ old('email', @$users->email ?? '') }}"
                                                placeholder="user@reftech.id" required />
                                            <label for="email-{{ @$users ? @$users->id : 'create' }}">Alamat Email</label>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="col-12">
                                        <div class="input-group input-group-merge">
                                            <div class="form-floating form-floating-outline">
                                                <input type="password" id="password-{{ @$users ? @$users->id : 'create' }}"
                                                    class="form-control" name="password" autocomplete="new-password"
                                                    placeholder="············" {{ empty($users) ? 'required' : '' }}>
                                                <label for="password-{{ @$users ? @$users->id : 'create' }}">
                                                    {{ empty($users) ? 'Password Akun' : 'Password (Opsional)' }}
                                                </label>
                                            </div>
                                            <span class="input-group-text cursor-pointer toggle-password-visibility"
                                                data-target="#password-{{ @$users ? @$users->id : 'create' }}">
                                                <i class="mdi mdi-eye-off-outline"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: DATA PROFIL, PENEMPATAN, & TARGET SALES -->
                        <div class="col-lg-8 col-md-7">
                            <!-- Section 1: Profil & Biodata -->
                            <div class="form-section-header first-header">
                                <span class="form-section-badge"><i class="mdi mdi-card-account-details-outline"></i></span>
                                <h6 class="form-section-title">Data Profil &amp; Identitas</h6>
                            </div>

                            <div class="row g-3 mt-1">
                                <!-- NIP -->
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text" id="nip-{{ @$users ? @$users->id : 'create' }}" name="nip"
                                            value="{{ old('nip', @$users->nip ?? '') }}" placeholder="Contoh: 61256996" required />
                                        <label for="nip-{{ @$users ? @$users->id : 'create' }}">NIP (Nomor Induk Pegawai)</label>
                                    </div>
                                </div>

                                <!-- Nama Lengkap -->
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control user-name-input" type="text" id="name-{{ @$users ? @$users->id : 'create' }}" name="name"
                                            value="{{ old('name', @$users->name ?? '') }}" placeholder="Contoh: Budi Santoso" required />
                                        <label for="name-{{ @$users ? @$users->id : 'create' }}">Nama Lengkap</label>
                                    </div>
                                </div>

                                <!-- No HP / WA -->
                                <div class="col-md-6">
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text fw-medium">+62</span>
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control phone-number-input" pattern="[0-9]*"
                                                placeholder="8123456789" id="phone-{{ @$users ? @$users->id : 'create' }}" name="phone"
                                                value="{{ old('phone', @$users->phone ? (str_starts_with(@$users->phone, '+62') ? substr(@$users->phone, 3) : @$users->phone) : '') }}" required>
                                            <label for="phone-{{ @$users ? @$users->id : 'create' }}">No. WhatsApp / HP</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tanggal Lahir -->
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="date" id="birthday-{{ @$users ? @$users->id : 'create' }}" name="birthday"
                                            value="{{ old('birthday', @$users->birthday ?? '1995-01-01') }}">
                                        <label for="birthday-{{ @$users ? @$users->id : 'create' }}">Tanggal Lahir</label>
                                    </div>
                                </div>

                                <!-- Alamat -->
                                <div class="col-12">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" placeholder="Alamat lengkap domisili tempat tinggal"
                                            name="address" id="address-{{ @$users ? @$users->id : 'create' }}"
                                            value="{{ old('address', @$users->address ?? '') }}" required>
                                        <label for="address-{{ @$users ? @$users->id : 'create' }}">Alamat Domisili</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Penempatan & Organisasi -->
                            <div class="form-section-header mt-3">
                                <span class="form-section-badge"><i class="mdi mdi-briefcase-outline"></i></span>
                                <h6 class="form-section-title">Penempatan &amp; Jabatan</h6>
                            </div>

                            <div class="row g-3 mt-1">
                                <!-- Jabatan / Position -->
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control user-position-input"
                                            id="position-{{ @$users ? @$users->id : 'create' }}" name="position"
                                            placeholder="Contoh: Sales Engineer"
                                            value="{{ old('position', @$users->detailUser[0]->position ?? '') }}" required />
                                        <label for="position-{{ @$users ? @$users->id : 'create' }}" id="positionLabel-{{ @$users ? @$users->id : 'create' }}">
                                            Jabatan / Posisi
                                        </label>
                                    </div>
                                </div>

                                <!-- Area Kerja -->
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control user-area-input"
                                            id="area-{{ @$users ? @$users->id : 'create' }}" name="area"
                                            placeholder="Contoh: Surabaya / Head Office"
                                            value="{{ old('area', @$users->detailUser[0]->area ?? '') }}" required />
                                        <label for="area-{{ @$users ? @$users->id : 'create' }}" id="areaLabel-{{ @$users ? @$users->id : 'create' }}">
                                            Area Kerja
                                        </label>
                                    </div>
                                </div>

                                <!-- Kode Karyawan / Inisial -->
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control text-uppercase user-code-input" type="text"
                                            id="code-{{ @$users ? @$users->id : 'create' }}" name="code"
                                            value="{{ old('code', @$users->code ?? '') }}"
                                            placeholder="Contoh: RZA" required />
                                        <label for="code-{{ @$users ? @$users->id : 'create' }}" id="codeLabel-{{ @$users ? @$users->id : 'create' }}">
                                            Kode Karyawan / Inisial
                                        </label>
                                    </div>
                                </div>

                                <!-- Tanggal Masuk -->
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="date"
                                            id="date_in-{{ @$users ? @$users->id : 'create' }}" name="date_in"
                                            value="{{ old('date_in', @$users->date_in ?? now()->format('Y-m-d')) }}">
                                        <label for="date_in-{{ @$users ? @$users->id : 'create' }}">Tanggal Masuk (Entry Date)</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Notifikasi Role Non-Sales -->
                            <div id="roleNoticeNonSales-{{ @$users ? @$users->id : 'create' }}"
                                class="alert alert-light border border-dashed d-flex align-items-center gap-2 mt-3 py-2 px-3 mb-0"
                                style="display: none;">
                                <i class="mdi mdi-information-outline text-info fs-5"></i>
                                <span class="text-muted small mb-0">
                                    Role <strong class="notice-role-name text-dark">User</strong> merupakan fungsi operasional internal dan tidak menggunakan metrik target penjualan.
                                </span>
                            </div>

                            <!-- Section 3: Target Bulanan (HANYA UNTUK ROLE SALES) -->
                            <div id="inputTarget-{{ @$users ? @$users->id : 'create' }}" class="target-sales-card" style="display: none;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 text-primary fw-bold d-flex align-items-center gap-1 font-size-14">
                                        <i class="mdi mdi-target-account fs-5"></i> Target Bulanan Penjualan
                                    </h6>
                                    <span class="badge bg-label-primary font-weight-bold">Khusus Role Sales</span>
                                </div>
                                <div class="row g-2">
                                    <div class="col-sm-4 col-6">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control form-control-sm" type="number" id="dc-{{ @$users ? @$users->id : 'create' }}" name="dc" min="0"
                                                value="{{ old('dc', @$users->target[0]->dc ?? '') }}" placeholder="0" />
                                            <label for="dc-{{ @$users ? @$users->id : 'create' }}">Daily Call</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control form-control-sm" type="number" id="crm-{{ @$users ? @$users->id : 'create' }}" name="crm" min="0"
                                                value="{{ old('crm', @$users->target[0]->crm ?? '') }}" placeholder="0" />
                                            <label for="crm-{{ @$users ? @$users->id : 'create' }}">CRM</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control form-control-sm" type="number" id="leads-{{ @$users ? @$users->id : 'create' }}" name="leads" min="0"
                                                value="{{ old('leads', @$users->target[0]->leads ?? '') }}" placeholder="0" />
                                            <label for="leads-{{ @$users ? @$users->id : 'create' }}">Leads</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control form-control-sm" type="number" id="quote-{{ @$users ? @$users->id : 'create' }}" name="quote" min="0"
                                                value="{{ old('quote', @$users->target[0]->quote ?? '') }}" placeholder="0" />
                                            <label for="quote-{{ @$users ? @$users->id : 'create' }}">Quotation</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control form-control-sm" type="number" id="po-{{ @$users ? @$users->id : 'create' }}" name="po" min="0"
                                                value="{{ old('po', @$users->target[0]->po ?? '') }}" placeholder="0" />
                                            <label for="po-{{ @$users ? @$users->id : 'create' }}">Purchase Order</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-12">
                                        <div class="input-group form-floating form-floating-outline">
                                            <span class="input-group-text fw-bold" style="font-size: 0.8rem;">Rp</span>
                                            <div class="form-floating form-floating-outline">
                                                <input type="text" class="form-control form-control-sm total-label" id="total-label-{{ @$users ? @$users->id : 'create' }}"
                                                    placeholder="0"
                                                    value="{{ old('total', @$users->target[0]->total ? number_format($users->target[0]->total, 0, '', '.') : '') }}">
                                                <label for="total-label-{{ @$users ? @$users->id : 'create' }}">Target Omset</label>
                                            </div>
                                            <input class="form-control total" type="number" name="total" id="total-{{ @$users ? @$users->id : 'create' }}"
                                                value="{{ old('total', @$users->target[0]->total ?? '') }}" hidden>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light d-flex align-items-center gap-1">
                        <i class="mdi mdi-content-save-check-outline me-1"></i>
                        {{ @$users ? 'Simpan Perubahan' : 'Simpan Akun Baru' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
