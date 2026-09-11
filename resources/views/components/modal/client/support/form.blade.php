<style>
    /* Ultra-smooth modal entrance and exit transition */
    .smooth-prospect-modal.modal.fade .modal-dialog {
        transform: scale(0.95) translateY(-20px);
        opacity: 0;
        transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.24s ease !important;
    }
    .smooth-prospect-modal.modal.show .modal-dialog {
        transform: scale(1) translateY(0) !important;
        opacity: 1 !important;
    }
    .smooth-prospect-modal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.22);
        overflow: hidden;
    }
    .smooth-prospect-modal .modal-header {
        background: linear-gradient(180deg, #fbfcfe 0%, #f4f6fa 100%);
        border-bottom: 1px solid #e7ebf0;
        padding: 1.1rem 1.75rem;
    }
    .smooth-prospect-modal .modal-body {
        padding: 1.5rem 1.75rem;
    }
    .smooth-prospect-modal .modal-footer {
        border-top: 1px solid #e7ebf0;
        padding: 1rem 1.75rem;
    }
    .modal-backdrop.fade {
        opacity: 0;
        transition: opacity 0.24s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
    .modal-backdrop.show {
        opacity: 0.45 !important;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
    }
    @media (min-width: 1200px) {
        #createProspect .modal-dialog {
            max-width: 1250px;
            width: 92%;
        }
    }
</style>

<form action="" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal fade smooth-prospect-modal" id="createProspect" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">
                        <i class="mdi mdi-account-plus-outline text-primary me-2"></i>Create Prospect
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h6>Company </h6>
                            <div class="row g-2 mb-3">
                                <div class="col-12 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="company" class="form-control" name="company"
                                            placeholder="PT xxxxxxx"
                                            value="{{ old('company', @$leads->company ?? '') }}">
                                        <label for="company">Company</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="email" id="emailAnimation" class="form-control" name="email"
                                            placeholder="company@email.com" value="{{ old('email', @$leads->email ?? '') }}">
                                        <label for="emailAnimation">Email</label>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="phone" id="phoneAnimation" class="form-control" name="phone"
                                            placeholder="081xxxxx" value="{{ old('phone', @$leads->phone ?? '') }}">
                                        <label for="phoneAnimation">Phone</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-4 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectMobile"
                                            aria-label="Default select example" name="mobile">
                                            <option disabled>----- Choose Mobile -----</option>
                                            <option value="WA"
                                                {{ old('mobile', @$leads->mobile) == 'WA' ? 'selected' : '' }}>
                                                WhatsApp</option>
                                            <option value="Phone Office"
                                                {{ old('mobile', @$leads->mobile) == 'Phone Office' ? 'selected' : '' }}>
                                                Phone
                                                Office</option>
                                        </select>
                                        <label for="selectMobile">Mobile</label>
                                    </div>
                                </div>
                                <div class="col-4 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectR/U" aria-label="Default select example"
                                            name="ru">
                                            <option disabled>----- Choose R/U -----</option>
                                            <option value="User"
                                                {{ old('ru', @$leads->ru) == 'User' ? 'selected' : '' }}>
                                                User
                                            </option>
                                            <option value="Reseller"
                                                {{ old('ru', @$leads->ru) == 'Reseller' ? 'selected' : '' }}>Reseller
                                            </option>
                                        </select>
                                        <label for="selectR/U">R/U</label>
                                    </div>
                                </div>
                                <div class="col-4 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectSource"
                                            aria-label="Default select example" name="source">
                                            <option disabled>----- Choose Source -----</option>
                                            <option value="IG"
                                                {{ old('source', @$leads->source) == 'IG' ? 'selected' : '' }}>Instagram
                                            </option>
                                            <option value="WhatsApp"
                                                {{ old('source', @$leads->source) == 'WhatsApp' ? 'selected' : '' }}>
                                                WhatsApp
                                            </option>
                                            <option value="LinkedIn"
                                                {{ old('source', @$leads->source) == 'LinkedIn' ? 'selected' : '' }}>
                                                LinkedIn
                                            </option>
                                            <option value="Website"
                                                {{ old('source', @$leads->source) == 'Website' ? 'selected' : '' }}>
                                                Website
                                            </option>
                                            <option value="Indotrading"
                                                {{ old('source', @$leads->source) == 'Indotrading' ? 'selected' : '' }}>
                                                Indotrading
                                            </option>
                                            <option value="Tokopedia"
                                                {{ old('source', @$leads->source) == 'Tokopedia' ? 'selected' : '' }}>
                                                Tokopedia
                                            </option>
                                            <option value="OLX"
                                                {{ old('source', @$leads->source) == 'OLX' ? 'selected' : '' }}>OLX
                                            </option>
                                            <option value="Google"
                                                {{ old('source', @$leads->source) == 'Google' ? 'selected' : '' }}>
                                                Google
                                            </option>
                                            <option value="Google Ads"
                                                {{ old('source', @$leads->source) == 'Google Ads' ? 'selected' : '' }}>
                                                Google Ads
                                            </option>
                                            <option value="Meta Ads"
                                                {{ old('source', @$leads->source) == 'Meta Ads' ? 'selected' : '' }}>
                                                Meta Ads
                                            </option>
                                            <option value="Facebook"
                                                {{ old('source', @$leads->source) == 'Facebook' ? 'selected' : '' }}>
                                                Facebook
                                            </option>
                                            <option value="Other"
                                                {{ old('source', @$leads->source) == 'Other' ? 'selected' : '' }}>Other
                                            </option>
                                        </select>
                                        <label for="selectSource">Source</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-3" id="domainWrapper" style="display:none;">
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="domainInput" name="source_detail"
                                            list="domainList" maxlength="100" placeholder="example.com"
                                            value="{{ old('source_detail', '') }}">
                                        <label for="domainInput">Website Domain (optional)</label>
                                    </div>
                                    <datalist id="domainList">
                                        @foreach ($domainList ?? [] as $d)
                                            <option value="{{ $d }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col mb-2">
                                    <select id="selectArea" class="form-select" name="area" style="width:100%">
                                        <option value=""></option>
                                        @php $selectedArea = old('area', @$leads->area ?? ''); @endphp
                                        @if($selectedArea)
                                            <option value="{{ $selectedArea }}" selected>{{ $selectedArea }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="row g-2 mb-3"> 
                                <div class="col-12 mb-2">
                                    <div class="form-floating form-floating-outline mb-2">
                                        <textarea class="form-control h-px-100" name="address" id="addressTextarea1"
                                            placeholder="Contoh: Jl Taman Kopo Indah 5 Kota..." required>{{ old('address', @$leads->address ?? '') }}</textarea>
                                        <label for="addressTextarea1">Address</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <h6> PIC </h6>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="nameAnimation" class="form-control" name="namePic"
                                            placeholder="xxxxxxx xxxxxxxx"
                                            value="{{ old('namePic', @$leads->pic->name_pic ?? '') }}">
                                        <label for="nameAnimation">Name</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="positionAnimation" class="form-control"
                                            name="position" placeholder="example: CEO"
                                            value="{{ old('position', @$leads->pic->position ?? '') }}">
                                        <label for="positionAnimation">Position</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="emailPicAnimation" class="form-control"
                                            name="emailPic" placeholder="xxxxxxxx@xxx.xx"
                                            value="{{ old('emailPic', @$leads->pic->email_pic ?? '') }}">
                                        <label for="emailPicAnimation">Email PIC</label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="phone" id="phonePicAnimation" class="form-control"
                                            name="phonePic" placeholder="08xxxxxxxxxx"
                                            value="{{ old('phonePic', @$leads->pic->phone_pic ?? '') }}">
                                        <label for="phonePicAnimation">Phone PIC</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        @php
                                            $defaultCategories = [
                                                'Service Compressor',
                                                'Rental Compressor',
                                                'Sparepart Compressor',
                                                'Instalasi Piping',
                                                'Air Audit',
                                                'Fire System',
                                                'HVAC System',
                                                'Unit Baru/Second',
                                            ];
                                            $customCategories = [];
                                            if (isset($categoryList)) {
                                                if (is_array($categoryList)) {
                                                    $customCategories = $categoryList;
                                                } elseif ($categoryList instanceof \Illuminate\Support\Collection) {
                                                    $customCategories = $categoryList->toArray();
                                                }
                                            }
                                            $categoriesToDisplay = array_values(array_unique(array_merge($defaultCategories, $customCategories)));
                                            $selectedCategory = old('category', @$leads->category ?? '');
                                        @endphp
                                        <select class="form-select @error('category') is-invalid @enderror" id="category"
                                            aria-label="Category Selection" name="category" required>
                                            <option value="" disabled {{ empty($selectedCategory) ? 'selected' : '' }}>----- Choose Category -----</option>
                                            @foreach ($categoriesToDisplay as $cat)
                                                <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>
                                                    {{ $cat }}
                                                </option>
                                            @endforeach
                                            <option value="__add_new__" {{ $selectedCategory === '__add_new__' ? 'selected' : '' }} style="font-weight: 700; color: #696cff;">
                                                ➕ + Tambah Kategori Baru...
                                            </option>
                                        </select>
                                        <label for="category">Category</label>
                                    </div>
                                    @error('category')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror

                                    {{-- Custom New Category Input Box --}}
                                    <div id="newCategoryWrapper" class="mt-2" style="{{ old('category') === '__add_new__' ? 'display: block;' : 'display: none;' }}">
                                        <div class="p-2 border rounded-3 bg-light">
                                            <label for="new_category" class="form-label text-primary fw-semibold small mb-1">
                                                <i class="mdi mdi-plus-box-outline me-1"></i>Nama Kategori Baru:
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <input type="text" id="new_category" name="new_category"
                                                    class="form-control @error('new_category') is-invalid @enderror"
                                                    placeholder="Ketik nama kategori baru..."
                                                    value="{{ old('new_category') }}">
                                                <button type="button" class="btn btn-outline-secondary" id="cancelNewCategoryBtn" title="Batal">
                                                    <i class="mdi mdi-close"></i> Batal
                                                </button>
                                            </div>
                                            @error('new_category')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                            <div class="text-muted small mt-1" style="font-size: 0.75rem;">
                                                <i class="mdi mdi-information-outline me-1"></i>Kategori baru ini akan otomatis tersimpan & tersedia di pilihan berikutnya.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="unit" class="form-control" name="unit"
                                            placeholder="Contoh: KAESER SK 21"
                                            value="{{ old('unit', @$leads->unit ?? '') }}">
                                        <label for="unit">Unit Existing</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-floating form-floating-outline mb-4">
                                    <textarea class="form-control h-px-100" name="prospect" id="prosp" placeholder="Contoh: Oil Filter ....."></textarea>
                                    <label for="prosp">Prospect</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- @empty($leads)
                    <div class="divider divider-dark mx-3">
                        <div class="divider-text"><span class="fw-semibold">Personal In Charge</span></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="nameAnimation" class="form-control" name="namePic"
                                    placeholder="xxxxxxx xxxxxxxx"
                                    value="{{ old('namePic', @$leads->pic->name_pic ?? '') }}">
                                <label for="nameAnimation">Name</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="positionAnimation" class="form-control" name="position"
                                    placeholder="example: CEO"
                                    value="{{ old('position', @$leads->pic->position ?? '') }}">
                                <label for="positionAnimation">Position</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="emailPicAnimation" class="form-control" name="emailPic"
                                    placeholder="xxxxxxxx@xxx.xx"
                                    value="{{ old('emailPic', @$leads->pic->email_pic ?? '') }}">
                                <label for="emailPicAnimation">Email PIC</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="phonePicAnimation" class="form-control" name="phonePic"
                                    placeholder="08xxxxxxxxxx"
                                    value="{{ old('phonePic', @$leads->pic->phone_pic ?? '') }}">
                                <label for="phonePicAnimation">Phone PIC</label>
                            </div>
                        </div>
                    </div>
                    @endempty --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('category');
        const newCategoryWrapper = document.getElementById('newCategoryWrapper');
        const newCategoryInput = document.getElementById('new_category');
        const cancelNewCategoryBtn = document.getElementById('cancelNewCategoryBtn');
        let previousCategory = '';

        if (categorySelect && newCategoryWrapper) {
            categorySelect.addEventListener('focus', function() {
                if (this.value !== '__add_new__') {
                    previousCategory = this.value;
                }
            });

            categorySelect.addEventListener('change', function() {
                if (this.value === '__add_new__') {
                    $(newCategoryWrapper).slideDown(220, function() {
                        if (newCategoryInput) {
                            newCategoryInput.focus();
                            newCategoryInput.setAttribute('required', 'required');
                        }
                    });
                } else {
                    previousCategory = this.value;
                    $(newCategoryWrapper).slideUp(180);
                    if (newCategoryInput) {
                        newCategoryInput.removeAttribute('required');
                    }
                }
            });

            if (cancelNewCategoryBtn) {
                cancelNewCategoryBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    categorySelect.value = previousCategory || '';
                    $(newCategoryWrapper).slideUp(180);
                    if (newCategoryInput) {
                        newCategoryInput.value = '';
                        newCategoryInput.removeAttribute('required');
                    }
                });
            }
        }

        @if ($errors->any())
            const createModalEl = document.getElementById('createProspect');
            if (createModalEl && typeof bootstrap !== 'undefined') {
                const createModal = bootstrap.Modal.getOrCreateInstance(createModalEl);
                createModal.show();
            }
        @endif
    });
</script>
