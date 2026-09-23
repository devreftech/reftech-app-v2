<form action="{{ @$existing ? route('existing.update', @$existing->id) : route('existing.store') }}" method="post"
    enctype="multipart/form-data">
    @csrf

    @if (@$existing)
        @method('patch')
    @endif
    <div class="modal fade"
        id="{{ @$existing ? 'updateExisting' . strval(@$existing->id) : 'createExisting' }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
                {{-- Modal Header --}}
                <div class="modal-header bg-lighter py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm rounded-3 bg-label-primary d-flex align-items-center justify-content-center">
                            <i class="mdi {{ @$existing ? 'mdi-domain-edit' : 'mdi-domain-plus' }} fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">
                                {{ @$existing ? 'Edit Data Customer Existing' : 'Tambah Customer Existing Baru' }}
                            </h5>
                            <small class="text-muted">Perbarui data profil, kontak, unit mesin, dan wilayah customer</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 py-3">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible mb-3 py-2 px-3 small" role="alert">
                            <div class="fw-bold mb-1"><i class="mdi mdi-alert-circle-outline me-1"></i> Terdapat kesalahan pengisian:</div>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    {{-- Section 1: Identitas & Klasifikasi Perusahaan --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-1 mb-2 text-primary fw-bold small text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="mdi mdi-office-building-outline me-1"></i> Identitas &amp; Klasifikasi Perusahaan
                        </div>
                        <div class="p-3 rounded-3 bg-light border border-light-subtle">
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="companyInput{{ @$existing->id ?? 'Create' }}" class="form-control bg-white" name="company"
                                            placeholder="PT / CV Nama Perusahaan" value="{{ old('company', @$existing->company ?? '') }}" required>
                                        <label for="companyInput{{ @$existing->id ?? 'Create' }}">Nama Perusahaan (Company) <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="unitInput{{ @$existing->id ?? 'Create' }}" class="form-control bg-white" name="unit"
                                            placeholder="Contoh: Comp 22kW, Dry 15HP" value="{{ old('unit', @$existing->unit ?? '') }}">
                                        <label for="unitInput{{ @$existing->id ?? 'Create' }}">Unit Mesin Utama</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select bg-white" id="selectRU{{ @$existing->id ?? 'Create' }}" name="ru">
                                            <option value="" disabled {{ !old('ru', @$existing->ru) ? 'selected' : '' }}>-- Pilih Tipe R/U --</option>
                                            <option value="User" {{ old('ru', @$existing->ru) == 'User' ? 'selected' : '' }}>End User</option>
                                            <option value="Reseller" {{ old('ru', @$existing->ru) == 'Reseller' ? 'selected' : '' }}>Reseller</option>
                                        </select>
                                        <label for="selectRU{{ @$existing->id ?? 'Create' }}">Status R/U</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select bg-white" id="selectVia{{ @$existing->id ?? 'Create' }}" name="info">
                                            <option value="" disabled {{ !old('info', @$existing->info) ? 'selected' : '' }}>-- Pilih Entitas (Via) --</option>
                                            <option value="Reftech" {{ old('info', @$existing->info) == 'Reftech' ? 'selected' : '' }}>Reftech</option>
                                            <option value="Kojisha" {{ old('info', @$existing->info) == 'Kojisha' ? 'selected' : '' }}>Kojisha</option>
                                        </select>
                                        <label for="selectVia{{ @$existing->id ?? 'Create' }}">Entitas / Via</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Kontak & Media Komunikasi --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center gap-1 mb-2 text-primary fw-bold small text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="mdi mdi-phone-message-outline me-1"></i> Kontak &amp; Saluran Komunikasi
                        </div>
                        <div class="p-3 rounded-3 bg-light border border-light-subtle">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input type="email" id="emailInput{{ @$existing->id ?? 'Create' }}" class="form-control bg-white" name="email"
                                            placeholder="info@perusahaan.com" value="{{ old('email', @$existing->email ?? '') }}">
                                        <label for="emailInput{{ @$existing->id ?? 'Create' }}"><i class="mdi mdi-email-outline me-1"></i> Email Kantor</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="phoneInput{{ @$existing->id ?? 'Create' }}" class="form-control bg-white" name="phone"
                                            placeholder="022-xxxxxx / 021-xxxxxx" value="{{ old('phone', @$existing->phone ?? '') }}">
                                        <label for="phoneInput{{ @$existing->id ?? 'Create' }}"><i class="mdi mdi-phone-outline me-1"></i> Telepon Kantor</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select bg-white" id="selectMobile{{ @$existing->id ?? 'Create' }}" name="mobile">
                                            <option value="" disabled {{ !old('mobile', @$existing->mobile) ? 'selected' : '' }}>-- Pilih Media Mobile --</option>
                                            <option value="WA" {{ old('mobile', @$existing->mobile) == 'WA' ? 'selected' : '' }}>WhatsApp</option>
                                            <option value="Phone Office" {{ old('mobile', @$existing->mobile) == 'Phone Office' ? 'selected' : '' }}>Phone Office</option>
                                        </select>
                                        <label for="selectMobile{{ @$existing->id ?? 'Create' }}"><i class="mdi mdi-cellphone-message me-1"></i> Media Komunikasi</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select bg-white" id="selectSource{{ @$existing->id ?? 'Create' }}" name="source">
                                            <option value="" disabled {{ !old('source', @$existing->source) ? 'selected' : '' }}>-- Pilih Lead Source --</option>
                                            <option value="IG" {{ old('source', @$existing->source) == 'IG' ? 'selected' : '' }}>Instagram</option>
                                            <option value="LinkedIn" {{ old('source', @$existing->source) == 'LinkedIn' ? 'selected' : '' }}>LinkedIn</option>
                                            <option value="Website" {{ old('source', @$existing->source) == 'Website' ? 'selected' : '' }}>Website</option>
                                            <option value="Iklan" {{ old('source', @$existing->source) == 'Iklan' ? 'selected' : '' }}>Iklan / Ads</option>
                                            <option value="Google" {{ old('source', @$existing->source) == 'Google' ? 'selected' : '' }}>Google Search</option>
                                            <option value="Other" {{ old('source', @$existing->source) == 'Other' ? 'selected' : '' }}>Other / Rekomendasi</option>
                                        </select>
                                        <label for="selectSource{{ @$existing->id ?? 'Create' }}"><i class="mdi mdi-source-branch me-1"></i> Sumber Lead (Source)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 3: Wilayah & Alamat Lengkap --}}
                    <div class="mb-2">
                        <div class="d-flex align-items-center gap-1 mb-2 text-primary fw-bold small text-uppercase" style="letter-spacing: 0.5px;">
                            <i class="mdi mdi-map-marker-radius-outline me-1"></i> Wilayah &amp; Alamat Pabrik / Kantor
                        </div>
                        <div class="p-3 rounded-3 bg-light border border-light-subtle">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label mb-1 fw-semibold text-dark small" for="selectAreaExisting{{ @$existing->id ?? 'Create' }}">
                                        Wilayah / Area (Kota / Kabupaten)
                                    </label>
                                    @php $selectedArea = old('area', @$existing->area ?? ''); @endphp
                                    <select id="selectAreaExisting{{ @$existing->id ?? 'Create' }}" class="form-select select-area-existing" name="area" style="width: 100%;" data-placeholder="Ketik minimal 2 huruf untuk cari kota/kabupaten...">
                                        @if ($selectedArea)
                                            <option value="{{ $selectedArea }}" selected="selected">{{ $selectedArea }}</option>
                                        @endif
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating form-floating-outline">
                                        <textarea class="form-control bg-white h-px-100" name="address" id="addressTextarea{{ @$existing->id ?? 'Create' }}"
                                            placeholder="Contoh: Jl. Industri No. 123...">{{ old('address', @$existing->address ?? '') }}</textarea>
                                        <label for="addressTextarea{{ @$existing->id ?? 'Create' }}">Alamat Lengkap Pabrik / Kantor</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Hidden preserved values --}}
                    <input type="hidden" name="npwp" value="{{ old('npwp', @$existing->npwp ?? '0') }}">
                    <input type="hidden" name="subAddress" value="{{ old('subAddress', @$existing->subAddress ?? '') }}">

                    {{-- Section 4: PIC Baru (Hanya saat form Create New Customer) --}}
                    @empty($existing)
                        <div class="mt-3">
                            <div class="d-flex align-items-center gap-1 mb-2 text-primary fw-bold small text-uppercase" style="letter-spacing: 0.5px;">
                                <i class="mdi mdi-account-tie-outline me-1"></i> Data Kontak PIC Awal
                            </div>
                            <div class="p-3 rounded-3 bg-light border border-light-subtle">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="namePicCreate" class="form-control bg-white" name="namePic"
                                                placeholder="Nama Lengkap PIC" value="{{ old('namePic', @$existing->pic->name_pic ?? '') }}">
                                            <label for="namePicCreate">Nama PIC</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="positionPicCreate" class="form-control bg-white" name="position"
                                                placeholder="Contoh: Manager Maintenance / Purchasing" value="{{ old('position', @$existing->pic->position ?? '') }}">
                                            <label for="positionPicCreate">Jabatan PIC</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="email" id="emailPicCreate" class="form-control bg-white" name="emailPic"
                                                placeholder="pic@perusahaan.com" value="{{ old('emailPic', @$existing->pic->email_pic ?? '') }}">
                                            <label for="emailPicCreate">Email PIC</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" id="phonePicCreate" class="form-control bg-white" name="phonePic"
                                                placeholder="0812xxxxxxxx" value="{{ old('phonePic', @$existing->pic->phone_pic ?? '') }}">
                                            <label for="phonePicCreate">No. HP / WhatsApp PIC</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endempty
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer bg-lighter py-3 px-4 border-top">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 shadow-xs">
                        <i class="mdi mdi-content-save-check-outline me-1"></i> {{ @$existing ? 'Simpan Perubahan' : 'Buat Customer Baru' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .select-area-existing + .select2-container,
        .select2-container--default.select2-container {
            width: 100% !important;
        }
        .select-area-existing + .select2-container .select2-selection--single {
            height: 48px !important;
            border: 1px solid #d9dee3 !important;
            border-radius: 8px !important;
            background-color: #fff !important;
            position: relative !important;
            display: block !important;
        }
        .select-area-existing + .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 46px !important;
            padding-left: 14px !important;
            padding-right: 40px !important;
            color: #566a7f !important;
            font-size: 0.9375rem !important;
            display: block !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }
        .select-area-existing + .select2-container .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
            position: absolute !important;
            top: 0 !important;
            right: 10px !important;
            width: 20px !important;
        }
        .select-area-existing + .select2-container .select2-selection--single .select2-selection__clear {
            cursor: pointer !important;
            float: right !important;
            font-weight: bold !important;
            margin-right: 10px !important;
            color: #888 !important;
            font-size: 1.1rem !important;
            line-height: 46px !important;
        }
        .select2-container--open {
            z-index: 9999 !important;
        }
        .select2-dropdown {
            z-index: 9999 !important;
            border: 1px solid #d9dee3 !important;
            border-radius: 8px !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.12) !important;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script>
        $(function () {
            $('.select-area-existing').each(function () {
                var $this = $(this);
                if ($this.hasClass('select2-hidden-accessible')) {
                    $this.select2('destroy');
                }

                var initialText = @json($selectedArea ?? '');

                $this.select2({
                    placeholder: 'Ketik minimal 2 huruf untuk cari kota/kabupaten...',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $this.closest('.modal'),
                    minimumInputLength: 2,
                    language: {
                        inputTooShort: function () { return 'Ketik minimal 2 karakter...'; },
                        searching: function () { return 'Mencari...'; },
                        noResults: function () { return 'Kota/Kabupaten tidak ditemukan'; }
                    },
                    ajax: {
                        url: '{{ route('kota.search') }}',
                        dataType: 'json',
                        delay: 300,
                        data: function (params) { return { q: params.term }; },
                        processResults: function (data) { return { results: data }; },
                        cache: true
                    }
                });

                if (initialText) {
                    if (!$this.find("option[value='" + initialText + "']").length) {
                        var newOption = new Option(initialText, initialText, true, true);
                        $this.append(newOption);
                    }
                    $this.val(initialText).trigger('change');
                }

                var $modal = $this.closest('.modal');
                if ($modal.length) {
                    $modal.on('shown.bs.modal', function () {
                        if (initialText && !$this.val()) {
                            $this.val(initialText).trigger('change');
                        }
                    });
                }
            });

            $(document).on('select2:open', function () {
                setTimeout(function () {
                    var searchField = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchField) searchField.focus();
                }, 50);
            });
        });
    </script>
@endpush
