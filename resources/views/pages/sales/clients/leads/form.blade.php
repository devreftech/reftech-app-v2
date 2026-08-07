<form action="{{ @$leads ? route('leads.update', @$leads->id) : route('leads.store') }}" method="post"
    enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf

    @if (@$leads)
        @method('patch')
    @endif
    <div class="modal fade"
        id="{{ @$leads ? 'updateLeads' . strval(@$leads->id) : 'createLeads' }}" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">{{ @$leads ? 'Update Data' : 'Create New' }} Leads
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="company" class="form-control" name="company"
                                    placeholder="PT xxxxxxx" value="{{ old('company', @$leads->company ?? '') }}">
                                <label for="company">Company</label>
                            </div>
                        </div>
                        <div class="col mb-2 d-flex align-items-center">
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="ru" id="ruUser" value="User"
                                    autocomplete="off" required
                                    {{ old('ru', @$leads->ru) == 'User' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="ruUser">User</label>

                                <input type="radio" class="btn-check" name="ru" id="ruReseller" value="Reseller"
                                    autocomplete="off" required
                                    {{ old('ru', @$leads->ru) == 'Reseller' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="ruReseller">Reseller</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="phoneAnimation" class="form-control" name="phone"
                                    placeholder="081xxxxx" value="{{ old('phone', @$leads->phone ?? '') }}">
                                <label for="phoneAnimation">Phone</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="unitsiteAnimation" class="form-control" name="unit"
                                    placeholder="xxx-21" value="{{ old('unit', @$leads->unit ?? '') }}">
                                <label for="unitsiteAnimation">Unit</label>
                            </div>
                        </div>
                        @if (Auth::user()->id == '1' || Auth::user()->id == '16' || Auth::user()->id == '23')
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" id="selectVia" aria-label="Default select example"
                                        name="info">
                                        <option disabled>----- Choose Via -----</option>
                                        <option value="Reftech"
                                            {{ old('info', @$leads->info) == 'Reftech' ? 'selected' : '' }}>
                                            Reftech
                                        </option>
                                        <option value="Kojisha"
                                            {{ old('info', @$leads->info) == 'Kojisha' ? 'selected' : '' }}>Kojisha
                                        </option>
                                    </select>
                                    <label for="selectVia">Via</label>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select" id="selectSource" aria-label="Default select example"
                                    name="source">
                                    <option value="" disabled>----- Choose Source -----</option>
                                    <option value="Direct Whatsapp"
                                        {{ old('source', @$leads->source) == 'Direct Whatsapp' ? 'selected' : '' }}>
                                        Direct Whatsapp
                                    </option>
                                    <option value="Canvasing"
                                        {{ old('source', @$leads->source) == 'Canvasing' ? 'selected' : '' }}>
                                        Canvasing
                                    </option>
                                    <option value="Instagram"
                                        {{ old('source', @$leads->source) == 'Instagram' ? 'selected' : '' }}>
                                        Instagram
                                    </option>
                                    <option value="LinkedIn"
                                        {{ old('source', @$leads->source) == 'LinkedIn' ? 'selected' : '' }}>LinkedIn
                                    </option>
                                    <option value="Other"
                                        {{ old('source', @$leads->source) == 'Other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                <label for="selectSource">Source</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select id="selectArea" class="select2 form-select" name="area">
                                    <option value=""></option>
                                    @php $selectedArea = old('area', @$leads->area ?? ''); @endphp
                                    @if ($selectedArea)
                                        <option value="{{ $selectedArea }}" selected>{{ $selectedArea }}</option>
                                    @endif
                                </select>
                                <label for="selectArea">Area</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12 mb-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control h-px-100" name="address" id="addressTextarea1"
                                    placeholder="Contoh: Jl Taman Kopo Indah 5 Kota...">{{ old('address', @$leads->address ?? '') }}</textarea>
                                <label for="addressTextarea1">Office / Factory Address</label>
                            </div>
                        </div>
                    </div>
                    @if (!empty($leads))
                        <div class="row g-2 mb-3">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="email" class="form-control" name="email"
                                        placeholder="xxxx@xxx.xx" value="{{ old('email', @$leads->email ?? '') }}">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" id="selectMobile" aria-label="Default select example"
                                        name="mobile">
                                        <option disabled>----- Choose Mobile -----</option>
                                        <option value="WA"
                                            {{ old('mobile', @$leads->mobile) == 'WA' ? 'selected' : '' }}>
                                            WhatsApp</option>
                                        <option value="Phone Office"
                                            {{ old('mobile', @$leads->mobile) == 'Phone Office' ? 'selected' : '' }}>Phone
                                            Office</option>
                                    </select>
                                    <label for="selectMobile">Mobile</label>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="npwp" value="{{ old('npwp', @$leads->npwp ?? '') }}">
                        <input type="hidden" name="subAddress" value="{{ old('subAddress', @$leads->subAddress ?? '') }}">
                    @endif
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
    </div>
</form>

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script>
        $(function () {
            $('#selectSource').select2({
                placeholder: '----- Choose Source -----',
                width: '100%',
                dropdownParent: $('#selectSource').closest('.modal')
            });

            $('#selectArea').select2({
                placeholder: 'Area',
                width: '100%',
                dropdownParent: $('#selectArea').closest('.modal'),
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

            @if ($errors->any() && old('company') !== null)
                var leadsModalEl = document.getElementById('{{ @$leads ? 'updateLeads' . strval(@$leads->id) : 'createLeads' }}');
                if (leadsModalEl) {
                    bootstrap.Modal.getOrCreateInstance(leadsModalEl).show();
                }
            @endif
        });
    </script>
@endpush
