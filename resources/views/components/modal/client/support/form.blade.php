 <form action="" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal modal-xl animate__animated animate__fadeIn" id="createProspect" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Create Prospect
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
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <textarea class="form-control h-px-100" name="address" id="addressTextarea1"
                                            placeholder="Contoh: Jl Taman Kopo Indah 5 Kota...">{{ old('address', @$leads->address ?? '') }}</textarea>
                                        <label for="addressTextarea1">Address</label>
                                    </div>
                                </div>
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline mb-4">
                                        <textarea class="form-control h-px-100" name="subAddress" id="addressTextarea2"
                                            placeholder="Contoh: Jl Taman Kopo Indah 5 Kota...">{{ old('subAddress', @$leads->subAddress ?? '') }}</textarea>
                                        <label for="addressTextarea2">Sub Address</label>
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
                                        <select class="form-select" id="category"
                                            aria-label="Default select example" name="category">
                                            <option disabled>----- Choose Category -----</option>
                                            <option value="Service Compressor"
                                                {{ old('category', @$leads->category) == 'Service Compressor' ? 'selected' : '' }}>
                                                Service Compressor
                                            </option>
                                            <option value="Rental Compressor"
                                                {{ old('category', @$leads->category) == 'Rental Compressor' ? 'selected' : '' }}>
                                                Rental Compressor
                                            </option>
                                            <option value="Sparepart Compressor"
                                                {{ old('category', @$leads->category) == 'Sparepart Compressor' ? 'selected' : '' }}>
                                                Sparepart Compressor
                                            </option>
                                            <option value="Instalasi Piping"
                                                {{ old('category', @$leads->category) == 'Instalasi Piping' ? 'selected' : '' }}>
                                                Instalasi Piping
                                            </option>
                                            <option value="Air Audit"
                                                {{ old('category', @$leads->category) == 'Air Audit' ? 'selected' : '' }}>
                                                Air Audit
                                            </option>
                                            <option value="Fire System"
                                                {{ old('category', @$leads->category) == 'Fire System' ? 'selected' : '' }}>
                                                Fire System
                                            </option>
                                            <option value="HVAC System"
                                                {{ old('category', @$leads->category) == 'HVAC System' ? 'selected' : '' }}>
                                                HVAC System
                                            </option>
                                            <option value="Unit Baru/Second"
                                                {{ old('category', @$leads->category) == 'Unit Baru/Second' ? 'selected' : '' }}>
                                                Unit Baru/Second
                                            </option>
                                        </select>
                                        <label for="category">Category</label>
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
    </div>
</form>
