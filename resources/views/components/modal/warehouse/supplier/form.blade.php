@php
    $isEdit = isset($supplier) && !empty($supplier->id);
    $modalId = $isEdit ? 'updateSupplier-' . $supplier->id : 'createSupplier';
    $formAction = $isEdit ? route('supplier.update', $supplier->id) : route('supplier.store');
    $currentInfo = old('info', @$supplier->info ?? 'Lokal');
    $currentType = old('type', @$supplier->type ?? 'Company');
@endphp

<form action="{{ $formAction }}" method="post" enctype="multipart/form-data" class="supplier-modal-form" id="form-{{ $modalId }}">
    @csrf
    @if ($isEdit)
        @method('patch')
    @endif

    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content border-0 shadow-lg rounded-3 overflow-hidden">
                {{-- Modal Header with Premium Accent --}}
                <div class="modal-header border-bottom py-3 px-4 bg-white d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md flex-shrink-0">
                            <span class="avatar-initial rounded-3 bg-label-primary shadow-xs">
                                <i class="mdi {{ $isEdit ? 'mdi-domain-edit' : 'mdi-domain-plus' }} fs-4"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="{{ $modalId }}Label">
                                {{ $isEdit ? 'Perbarui Data Supplier' : 'Tambah Mitra Supplier Baru' }}
                            </h5>
                            <p class="text-muted small mb-0">
                                {{ $isEdit ? 'Ubah profil, kontak, dan status legalitas mitra supplier' : 'Registrasi profil vendor, legalitas, klasifikasi pengadaan & PIC' }}
                            </p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body p-4 bg-white">
                    @if ($errors->any())
                        <div class="alert alert-solid-danger alert-dismissible fade show py-2 px-3 mb-3 small" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="mdi mdi-alert-circle me-2 fs-5"></i>
                                <div>
                                    <strong class="d-block mb-1">Terdapat kesalahan input:</strong>
                                    <ul class="mb-0 ps-3">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Dynamic Live Preview Card --}}
                    <div class="card bg-label-primary border-0 rounded-3 p-3 mb-4 supplier-preview-box">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar avatar-sm rounded-circle bg-white text-primary shadow-xs d-flex align-items-center justify-content-center fw-bold">
                                    <i class="mdi {{ $currentType == 'Individual' ? 'mdi-account' : 'mdi-domain' }} preview-type-icon"></i>
                                </div>
                                <div>
                                    <span class="small text-muted d-block" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Preview Mitra</span>
                                    <h6 class="fw-bold text-primary mb-0 preview-supplier-name" style="font-size: 0.95rem;">
                                        {{ old('supplier', @$supplier->supplier ?: 'Nama Supplier / PT / CV / Toko...') }}
                                    </h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge bg-success rounded-pill px-2.5 py-1 preview-info-badge">
                                    <i class="mdi mdi-map-marker-outline me-1"></i>{{ $currentInfo }}
                                </span>
                                <span class="badge {{ $currentType == 'Individual' ? 'bg-warning' : 'bg-dark' }} rounded-pill px-2.5 py-1 preview-type-badge">
                                    {{ $currentType == 'Individual' ? 'Perorangan' : 'Company' }}
                                </span>
                                <span class="badge bg-white text-secondary rounded-pill px-2.5 py-1 border preview-code-badge font-monospace {{ old('code', @$supplier->code) ? '' : 'd-none' }}">
                                    {{ old('code', @$supplier->code ?: '') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 1: Identitas & Klasifikasi Mitra --}}
                    <div class="section-card border rounded-3 p-3 mb-3 bg-body">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <span class="badge bg-label-primary rounded-pill p-1 px-2">
                                <i class="mdi mdi-office-building-cog-outline me-1"></i>1
                            </span>
                            <h6 class="fw-bold text-dark mb-0 font-14">Identitas & Klasifikasi Mitra</h6>
                        </div>

                        <div class="row g-3">
                            {{-- Nama Supplier --}}
                            <div class="col-12 col-md-8">
                                <label class="form-label fw-semibold text-dark small" for="supplier-input-{{ $modalId }}">
                                    <span class="supplier-label-text">{{ $currentType == 'Individual' ? 'Nama Supplier / Toko / Perorangan' : 'Nama Supplier / Badan Usaha' }}</span> 
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="mdi {{ $currentType == 'Individual' ? 'mdi-account-outline' : 'mdi-domain' }} supplier-name-icon"></i>
                                    </span>
                                    <input type="text" 
                                           id="supplier-input-{{ $modalId }}" 
                                           class="form-control border-start-0 ps-1 input-supplier-name" 
                                           name="supplier"
                                           placeholder="{{ $currentType == 'Individual' ? 'Contoh: Toko Maju Jaya / Bpk. Hendra' : 'Contoh: PT Sumber Rezeki Makmur / CV Berkah Teknik' }}" 
                                           value="{{ old('supplier', @$supplier->supplier ?? '') }}" 
                                           required 
                                           autocomplete="off">
                                </div>
                                <div class="form-text text-muted supplier-helper-text" style="font-size: 0.75rem;">
                                    {{ $currentType == 'Individual' ? 'Nama toko retail, bengkel, atau perorangan penyedia.' : 'Cantumkan bentuk badan usaha resmi (PT, CV, UD, dll).' }}
                                </div>
                            </div>

                            {{-- Kode Supplier --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label fw-semibold text-dark small" for="code-input-{{ $modalId }}">
                                    Kode Supplier
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="mdi mdi-barcode-scan"></i>
                                    </span>
                                    <input type="text" 
                                           id="code-input-{{ $modalId }}" 
                                           class="form-control border-start-0 ps-1 text-uppercase font-monospace input-supplier-code" 
                                           name="code"
                                           placeholder="SUP-001" 
                                           value="{{ old('code', @$supplier->code ?? '') }}" 
                                           autocomplete="off">
                                </div>
                                <div class="form-text text-muted" style="font-size: 0.75rem;">Singkatan / kode identifikasi internal.</div>
                            </div>

                            {{-- Klasifikasi Pengadaan (Lokal / Import) --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark small d-block mb-2">
                                    Klasifikasi Pengadaan <span class="text-danger">*</span>
                                </label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="classification-option-card d-flex align-items-center gap-2 p-2 border rounded-3 cursor-pointer w-100 {{ $currentInfo == 'Lokal' ? 'selected-option border-success bg-label-success' : 'bg-white' }}" style="cursor: pointer; transition: all 0.2s ease;">
                                            <input type="radio" name="info" value="Lokal" class="form-check-input mt-0 radio-supplier-info" {{ $currentInfo == 'Lokal' ? 'checked' : '' }} required>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold small text-dark"><i class="mdi mdi-map-marker-radius-outline text-success me-1"></i>Lokal</span>
                                                <span class="text-muted" style="font-size: 0.7rem;">Dalam Negeri</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="classification-option-card d-flex align-items-center gap-2 p-2 border rounded-3 cursor-pointer w-100 {{ $currentInfo == 'Import' ? 'selected-option border-info bg-label-info' : 'bg-white' }}" style="cursor: pointer; transition: all 0.2s ease;">
                                            <input type="radio" name="info" value="Import" class="form-check-input mt-0 radio-supplier-info" {{ $currentInfo == 'Import' ? 'checked' : '' }} required>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold small text-dark"><i class="mdi mdi-airplane-takeoff text-info me-1"></i>Import</span>
                                                <span class="text-muted" style="font-size: 0.7rem;">Luar Negeri</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- Tipe Badan Usaha --}}
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold text-dark small d-block mb-2">
                                    Tipe Entitas Bisnis <span class="text-danger">*</span>
                                </label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="type-option-card d-flex align-items-center gap-2 p-2 border rounded-3 cursor-pointer w-100 {{ $currentType == 'Company' ? 'selected-option border-primary bg-label-primary' : 'bg-white' }}" style="cursor: pointer; transition: all 0.2s ease;">
                                            <input type="radio" name="type" value="Company" class="form-check-input mt-0 radio-supplier-type" {{ $currentType == 'Company' ? 'checked' : '' }} required>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold small text-dark"><i class="mdi mdi-domain text-primary me-1"></i>Perusahaan</span>
                                                <span class="text-muted" style="font-size: 0.7rem;">PT / CV / Firma</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="type-option-card d-flex align-items-center gap-2 p-2 border rounded-3 cursor-pointer w-100 {{ $currentType == 'Individual' ? 'selected-option border-warning bg-label-warning' : 'bg-white' }}" style="cursor: pointer; transition: all 0.2s ease;">
                                            <input type="radio" name="type" value="Individual" class="form-check-input mt-0 radio-supplier-type" {{ $currentType == 'Individual' ? 'checked' : '' }} required>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold small text-dark"><i class="mdi mdi-account-outline text-warning me-1"></i>Perorangan</span>
                                                <span class="text-muted" style="font-size: 0.7rem;">Toko / Individu</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: Kontak & Legalitas Perpajakan --}}
                    <div class="section-card border rounded-3 p-3 mb-3 bg-body">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <span class="badge bg-label-info rounded-pill p-1 px-2">
                                <i class="mdi mdi-phone-classic me-1"></i>2
                            </span>
                            <h6 class="fw-bold text-dark mb-0 font-14 section-2-title">
                                {{ $currentType == 'Individual' ? 'Kontak & Komunikasi' : 'Komunikasi & Legalitas Pajak' }}
                            </h6>
                        </div>

                        <div class="row g-3">
                            {{-- Telepon Kantor --}}
                            <div class="col-12 {{ $currentType == 'Individual' ? 'col-md-12' : 'col-md-4' }} supplier-phone-col">
                                <label class="form-label fw-semibold text-dark small" for="phone-input-{{ $modalId }}">
                                    <span class="phone-label-text">{{ $currentType == 'Individual' ? 'No. Telepon / WhatsApp' : 'No. Telepon / Hotline' }}</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="mdi mdi-phone-outline"></i>
                                    </span>
                                    <input type="text" 
                                           id="phone-input-{{ $modalId }}" 
                                           class="form-control border-start-0 ps-1" 
                                           name="phone"
                                           placeholder="{{ $currentType == 'Individual' ? 'Contoh: 081234567890' : 'Contoh: 021-xxxxxxx / 0812xxxx' }}" 
                                           value="{{ old('phone', @$supplier->phone ?? '') }}" 
                                           autocomplete="off">
                                </div>
                            </div>

                            {{-- Email Kantor (Hanya untuk Perusahaan) --}}
                            <div class="col-12 col-md-4 company-only-field {{ $currentType == 'Individual' ? 'd-none' : '' }}">
                                <label class="form-label fw-semibold text-dark small" for="email-input-{{ $modalId }}">
                                    Email Resmi Perusahaan
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="mdi mdi-email-outline"></i>
                                    </span>
                                    <input type="email" 
                                           id="email-input-{{ $modalId }}" 
                                           class="form-control border-start-0 ps-1 input-company-email" 
                                           name="email"
                                           placeholder="procurement@vendor.com" 
                                           value="{{ old('email', @$supplier->email ?? '') }}" 
                                           {{ $currentType == 'Individual' ? 'disabled' : '' }}
                                           autocomplete="off">
                                </div>
                            </div>

                            {{-- NPWP Perusahaan (Hanya untuk Perusahaan) --}}
                            <div class="col-12 col-md-4 company-only-field {{ $currentType == 'Individual' ? 'd-none' : '' }}">
                                <label class="form-label fw-semibold text-dark small" for="npwp-input-{{ $modalId }}">
                                    NPWP (Wajib Pajak)
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="mdi mdi-card-account-details-outline"></i>
                                    </span>
                                    <input type="text" 
                                           id="npwp-input-{{ $modalId }}" 
                                           class="form-control border-start-0 ps-1 font-monospace input-company-npwp" 
                                           name="npwp"
                                           placeholder="00.000.000.0-000.000" 
                                           value="{{ old('npwp', @$supplier->npwp ?? '') }}" 
                                           {{ $currentType == 'Individual' ? 'disabled' : '' }}
                                           autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 3: Lokasi & Alamat Operasional --}}
                    <div class="section-card border rounded-3 p-3 mb-3 bg-body">
                        <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                            <span class="badge bg-label-warning rounded-pill p-1 px-2">
                                <i class="mdi mdi-map-marker-radius me-1"></i>3
                            </span>
                            <h6 class="fw-bold text-dark mb-0 font-14">Lokasi & Alamat Operasional</h6>
                        </div>

                        <div class="row g-3">
                            {{-- Baris 1: Wilayah / Kota / Negara (Boleh diisi / opsional) --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark small" for="selectArea-{{ $modalId }}">
                                    <span class="area-label-text">
                                        {{ $currentInfo == 'Import' ? 'Negara Asal Supplier' : 'Wilayah / Kota / Kabupaten' }}
                                    </span>
                                    <span class="text-muted fw-normal font-11">(Opsional)</span>
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="mdi {{ $currentInfo == 'Import' ? 'mdi-earth' : 'mdi-map-marker-radius-outline' }} area-icon"></i>
                                    </span>
                                    <div class="flex-grow-1 select2-area-wrapper">
                                        <select id="selectArea-{{ $modalId }}" 
                                                class="form-select select2-area-supplier border-start-0" 
                                                name="area" 
                                                style="width: 100%;">
                                            <option value=""></option>
                                            @php $selectedArea = old('area', @$supplier->area ?? ''); @endphp
                                            @if ($selectedArea)
                                                <option value="{{ $selectedArea }}" selected>{{ $selectedArea }}</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="form-text text-muted area-helper-text" style="font-size: 0.75rem;">
                                    {{ $currentInfo == 'Import' ? 'Pilih negara asal pemasok import atau ketik nama negara.' : 'Ketik minimal 2 huruf untuk mencari kota & kabupaten di seluruh Indonesia.' }}
                                </div>
                            </div>

                            {{-- Baris 2: Alamat Lengkap / Lokasi Gudang / Toko --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold text-dark small" for="address-input-{{ $modalId }}">
                                    Alamat Lengkap / Lokasi Gudang / Toko
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0 text-muted align-items-start pt-2">
                                        <i class="mdi mdi-map-marker-outline"></i>
                                    </span>
                                    <textarea id="address-input-{{ $modalId }}" 
                                              class="form-control border-start-0 ps-1" 
                                              name="address" 
                                              rows="2"
                                              placeholder="Jl. Raya Industri No. XX, Kawasan Industri..." 
                                              style="min-height: 70px;">{{ old('address', @$supplier->address ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 4: Kontak PIC Utama (Khusus Create Supplier Baru - Opsional) --}}
                    @if (!$isEdit)
                        <div class="section-card border rounded-3 p-3 bg-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-label-success rounded-pill p-1 px-2">
                                        <i class="mdi mdi-account-tie-outline me-1"></i>4
                                    </span>
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0 font-14">Kontak PIC Utama (Person In Charge)</h6>
                                        <small class="text-muted font-11">Daftarkan langsung PIC penghubung (Opsional)</small>
                                    </div>
                                </div>
                                <button type="button" 
                                        class="btn btn-sm btn-label-secondary waves-effect py-1 px-2.5 font-12" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#collapsePic-{{ $modalId }}" 
                                        aria-expanded="false">
                                    <i class="mdi mdi-unfold-more-horizontal me-1"></i><span class="btn-pic-toggle-text">Isi PIC</span>
                                </button>
                            </div>

                            <div class="collapse" id="collapsePic-{{ $modalId }}">
                                <div class="pt-3 border-top mt-2">
                                    <div class="row g-3">
                                        {{-- Nama PIC --}}
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-dark small" for="namePic-{{ $modalId }}">
                                                Nama Lengkap PIC
                                            </label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text bg-light border-end-0 text-muted">
                                                    <i class="mdi mdi-account-outline"></i>
                                                </span>
                                                <input type="text" 
                                                       id="namePic-{{ $modalId }}" 
                                                       class="form-control border-start-0 ps-1" 
                                                       name="namePic" 
                                                       placeholder="Contoh: Bpk. Bambang Wijaya" 
                                                       value="{{ old('namePic') }}" 
                                                       autocomplete="off">
                                            </div>
                                        </div>

                                        {{-- Jabatan PIC --}}
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-dark small" for="position-{{ $modalId }}">
                                                Jabatan / Posisi
                                            </label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text bg-light border-end-0 text-muted">
                                                    <i class="mdi mdi-badge-account-outline"></i>
                                                </span>
                                                <input type="text" 
                                                       id="position-{{ $modalId }}" 
                                                       class="form-control border-start-0 ps-1" 
                                                       name="position" 
                                                       placeholder="Contoh: Sales Manager / Key Account" 
                                                       value="{{ old('position') }}" 
                                                       autocomplete="off">
                                            </div>
                                        </div>

                                        {{-- Phone PIC --}}
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-dark small" for="phonePic-{{ $modalId }}">
                                                No. Handphone / WhatsApp PIC
                                            </label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text bg-light border-end-0 text-muted">
                                                    <i class="mdi mdi-whatsapp"></i>
                                                </span>
                                                <input type="text" 
                                                       id="phonePic-{{ $modalId }}" 
                                                       class="form-control border-start-0 ps-1" 
                                                       name="phonePic" 
                                                       placeholder="0812xxxxxxxx" 
                                                       value="{{ old('phonePic') }}" 
                                                       autocomplete="off">
                                            </div>
                                        </div>

                                        {{-- Email PIC --}}
                                        <div class="col-12 col-md-6">
                                            <label class="form-label fw-semibold text-dark small" for="emailPic-{{ $modalId }}">
                                                Email PIC
                                            </label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text bg-light border-end-0 text-muted">
                                                    <i class="mdi mdi-email-outline"></i>
                                                </span>
                                                <input type="email" 
                                                       id="emailPic-{{ $modalId }}" 
                                                       class="form-control border-start-0 ps-1" 
                                                       name="emailPic" 
                                                       placeholder="bambang@vendor.com" 
                                                       value="{{ old('emailPic') }}" 
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer border-top py-3 px-4 bg-light d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <span class="text-danger">*</span> Kolom wajib diisi
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-label-secondary px-3 waves-effect" data-bs-dismiss="modal">
                            <i class="mdi mdi-close me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary px-4 waves-effect waves-light shadow-sm btn-submit-supplier">
                            <i class="mdi {{ $isEdit ? 'mdi-check-circle-outline' : 'mdi-content-save-outline' }} me-1"></i> 
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan Mitra Supplier' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('after-style')
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
<style>
    .classification-option-card:hover, .type-option-card:hover {
        border-color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.04) !important;
    }
    .classification-option-card.selected-option, .type-option-card.selected-option {
        box-shadow: 0 0 0 1px currentColor;
    }
    .supplier-preview-box {
        transition: all 0.2s ease;
        border: 1px dashed rgba(var(--bs-primary-rgb), 0.3) !important;
    }
    .select2-container--default .select2-selection--single {
        border: 1px solid #d9dee3 !important;
        height: 38px !important;
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
</style>
@endpush

@push('after-script')
<script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
<script>
    $(document).ready(function () {
        var modalEl = $('#{{ $modalId }}');
        if (!modalEl.length) return;

        var nameInput = modalEl.find('.input-supplier-name');
        var codeInput = modalEl.find('.input-supplier-code');
        var previewName = modalEl.find('.preview-supplier-name');
        var previewCodeBadge = modalEl.find('.preview-code-badge');
        var previewInfoBadge = modalEl.find('.preview-info-badge');
        var previewTypeBadge = modalEl.find('.preview-type-badge');
        var previewTypeIcon = modalEl.find('.preview-type-icon');
        var supplierLabelText = modalEl.find('.supplier-label-text');
        var supplierNameIcon = modalEl.find('.supplier-name-icon');
        var supplierHelperText = modalEl.find('.supplier-helper-text');
        var section2Title = modalEl.find('.section-2-title');
        var phoneCol = modalEl.find('.supplier-phone-col');
        var phoneLabelText = modalEl.find('.phone-label-text');
        var companyFields = modalEl.find('.company-only-field');
        var emailInput = modalEl.find('.input-company-email');
        var npwpInput = modalEl.find('.input-company-npwp');
        
        // Area Select2 & Labels
        var areaSelect = modalEl.find('#selectArea-{{ $modalId }}');
        var areaLabelText = modalEl.find('.area-label-text');
        var areaIcon = modalEl.find('.area-icon');
        var areaHelperText = modalEl.find('.area-helper-text');

        // Master List of World Countries (Import Suppliers)
        var importCountries = [
            "China", "Singapore", "Japan", "Taiwan", "Germany (Jerman)", "United States (USA)",
            "South Korea (Korea Selatan)", "Malaysia", "Thailand", "Vietnam", "Italy (Italia)",
            "United Kingdom (UK)", "Netherlands (Belanda)", "Australia", "India", "Switzerland (Swiss)",
            "France (Prancis)", "Spain (Spanyol)", "Turkey (Turki)", "United Arab Emirates (UAE)",
            "Hong Kong", "Austria", "Sweden (Swedia)", "Denmark", "Belgium (Belgia)", "Poland (Polandia)",
            "Canada", "Brazil", "Mexico", "Russia", "New Zealand", "Saudi Arabia", "Philippines (Filipina)"
        ];

        // Initialize / Reconfigure Area Select2 based on Lokal vs Import
        function initAreaSelect2(infoType) {
            var currentVal = areaSelect.val() || @json(old('area', @$supplier->area ?? ''));

            if (areaSelect.data('select2')) {
                areaSelect.select2('destroy');
            }

            if (infoType === 'Import') {
                areaLabelText.text('Negara Asal Supplier');
                areaIcon.attr('class', 'mdi mdi-earth area-icon');
                areaHelperText.text('Pilih negara asal pemasok import atau ketik negara/kota asal.');

                areaSelect.empty().append('<option value=""></option>');
                
                importCountries.forEach(function(country) {
                    var isSelected = (currentVal && currentVal.toLowerCase() === country.toLowerCase());
                    var opt = new Option(country, country, false, isSelected);
                    areaSelect.append(opt);
                });

                if (currentVal && !importCountries.some(function(c) { return c.toLowerCase() === currentVal.toLowerCase(); })) {
                    var customOpt = new Option(currentVal, currentVal, true, true);
                    areaSelect.append(customOpt);
                }

                areaSelect.select2({
                    placeholder: 'Pilih / cari negara pemasok (Opsional)...',
                    allowClear: true,
                    tags: true,
                    width: '100%',
                    dropdownParent: modalEl
                });

                if (currentVal) {
                    areaSelect.val(currentVal).trigger('change');
                }
            } else {
                areaLabelText.text('Wilayah / Kota / Kabupaten');
                areaIcon.attr('class', 'mdi mdi-map-marker-radius-outline area-icon');
                areaHelperText.text('Ketik minimal 2 huruf untuk mencari kota & kabupaten di seluruh Indonesia.');

                areaSelect.empty().append('<option value=""></option>');
                if (currentVal) {
                    areaSelect.append(new Option(currentVal, currentVal, true, true));
                }

                areaSelect.select2({
                    placeholder: 'Ketik min. 2 huruf cari kota/kabupaten (Opsional)...',
                    allowClear: true,
                    tags: true,
                    width: '100%',
                    dropdownParent: modalEl,
                    minimumInputLength: 2,
                    language: {
                        inputTooShort: function () { return 'Ketik minimal 2 karakter...'; },
                        searching: function () { return 'Mencari kota & kabupaten...'; },
                        noResults: function () { return 'Kota/Kabupaten tidak ditemukan'; }
                    },
                    ajax: {
                        url: '{{ route("kota.search") }}',
                        dataType: 'json',
                        delay: 250,
                        data: function (params) { return { q: params.term }; },
                        processResults: function (data) { return { results: data }; },
                        cache: true
                    }
                });

                if (currentVal) {
                    areaSelect.val(currentVal).trigger('change');
                }
            }
        }

        // Initialize when modal is shown or ready
        modalEl.on('shown.bs.modal', function () {
            var activeInfo = modalEl.find('.radio-supplier-info:checked').val() || '{{ $currentInfo }}';
            initAreaSelect2(activeInfo);
        });

        // Also run immediately on page ready
        var initialInfo = modalEl.find('.radio-supplier-info:checked').val() || '{{ $currentInfo }}';
        initAreaSelect2(initialInfo);

        // Dynamic Name Input Listener
        if (nameInput.length && previewName.length) {
            nameInput.on('input', function () {
                var val = $(this).val().trim();
                previewName.text(val ? val : 'Nama Supplier / PT / CV / Toko...');
            });
        }

        // Dynamic Code Input Listener
        if (codeInput.length && previewCodeBadge.length) {
            codeInput.on('input', function () {
                var val = $(this).val().trim().toUpperCase();
                if (val) {
                    previewCodeBadge.text(val).removeClass('d-none');
                } else {
                    previewCodeBadge.addClass('d-none');
                }
            });
        }

        // Classification Radio Option Cards (Lokal vs Import)
        modalEl.find('.radio-supplier-info').on('change', function () {
            modalEl.find('.classification-option-card').removeClass('selected-option border-success bg-label-success border-info bg-label-info').addClass('bg-white');

            var parentCard = $(this).closest('.classification-option-card');
            if (this.value === 'Lokal') {
                parentCard.addClass('selected-option border-success bg-label-success').removeClass('bg-white');
                if (previewInfoBadge.length) {
                    previewInfoBadge.attr('class', 'badge bg-success rounded-pill px-2.5 py-1 preview-info-badge')
                        .html('<i class="mdi mdi-map-marker-outline me-1"></i>Lokal');
                }
            } else {
                parentCard.addClass('selected-option border-info bg-label-info').removeClass('bg-white');
                if (previewInfoBadge.length) {
                    previewInfoBadge.attr('class', 'badge bg-info rounded-pill px-2.5 py-1 preview-info-badge')
                        .html('<i class="mdi mdi-airplane-takeoff me-1"></i>Import');
                }
            }

            // Switch select2 options to Kota/Kabupaten (Lokal) or Countries (Import)
            initAreaSelect2(this.value);
        });

        // Function to toggle Company vs Individual
        function applyTypeChange(typeVal) {
            var isIndividual = (typeVal === 'Individual');

            // Toggle Company-only fields (Email & NPWP)
            if (isIndividual) {
                companyFields.addClass('d-none');
                if (emailInput.length) { emailInput.prop('disabled', true).val(''); }
                if (npwpInput.length) { npwpInput.prop('disabled', true).val(''); }
            } else {
                companyFields.removeClass('d-none');
                if (emailInput.length) { emailInput.prop('disabled', false); }
                if (npwpInput.length) { npwpInput.prop('disabled', false); }
            }

            // Adjust Phone input column width & labels
            if (phoneCol.length) {
                if (isIndividual) {
                    phoneCol.removeClass('col-md-4').addClass('col-md-12');
                } else {
                    phoneCol.removeClass('col-md-12').addClass('col-md-4');
                }
            }

            if (phoneLabelText.length) {
                phoneLabelText.text(isIndividual ? 'No. Telepon / WhatsApp' : 'No. Telepon / Hotline');
            }

            if (section2Title.length) {
                section2Title.text(isIndividual ? 'Kontak & Komunikasi' : 'Komunikasi & Legalitas Pajak');
            }

            if (supplierLabelText.length) {
                supplierLabelText.text(isIndividual ? 'Nama Supplier / Toko / Perorangan' : 'Nama Supplier / Badan Usaha');
            }

            if (supplierHelperText.length) {
                supplierHelperText.text(isIndividual 
                    ? 'Nama toko retail, bengkel, atau perorangan penyedia.' 
                    : 'Cantumkan bentuk badan usaha resmi (PT, CV, UD, dll).');
            }

            if (supplierNameIcon.length) {
                supplierNameIcon.attr('class', 'mdi ' + (isIndividual ? 'mdi-account-outline' : 'mdi-domain') + ' supplier-name-icon');
            }

            if (previewTypeIcon.length) {
                previewTypeIcon.attr('class', 'mdi ' + (isIndividual ? 'mdi-account' : 'mdi-domain') + ' preview-type-icon');
            }

            if (previewTypeBadge.length) {
                if (isIndividual) {
                    previewTypeBadge.attr('class', 'badge bg-warning rounded-pill px-2.5 py-1 preview-type-badge').text('Perorangan');
                } else {
                    previewTypeBadge.attr('class', 'badge bg-dark rounded-pill px-2.5 py-1 preview-type-badge').text('Company');
                }
            }
        }

        // Type Radio Option Cards
        modalEl.find('.radio-supplier-type').on('change', function () {
            modalEl.find('.type-option-card').removeClass('selected-option border-primary bg-label-primary border-warning bg-label-warning').addClass('bg-white');

            var parentCard = $(this).closest('.type-option-card');
            if (this.value === 'Company') {
                parentCard.addClass('selected-option border-primary bg-label-primary').removeClass('bg-white');
            } else {
                parentCard.addClass('selected-option border-warning bg-label-warning').removeClass('bg-white');
            }

            applyTypeChange(this.value);
        });

        // Toggle PIC collapse button text
        var collapsePicEl = modalEl.find('#collapsePic-{{ $modalId }}');
        var togglePicBtnText = modalEl.find('.btn-pic-toggle-text');
        if (collapsePicEl.length && togglePicBtnText.length) {
            collapsePicEl.on('show.bs.collapse', function () {
                togglePicBtnText.text('Tutup PIC');
            });
            collapsePicEl.on('hide.bs.collapse', function () {
                togglePicBtnText.text('Isi PIC');
            });
        }
    });
</script>
@endpush
