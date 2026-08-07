<form action="{{ @$existing ? route('existing.update', @$existing->id) : route('existing.store') }}" method="post"
    enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf

    @if (@$existing)
        @method('patch')
    @endif
    <div class="modal animate__animated animate__fadeIn"
        id="{{ @$existing ? 'updateExisting' . strval(@$existing->id) : 'createExisting' }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">{{ @$existing ? 'Update Data' : 'Create New' }}
                        Existing
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
                        {{-- <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectSales" name="sales"
                                    aria-label="Default select example">
                                    <option disabled>----- Choose Sales -----</option>
                                    @foreach ($sales as $saless)
                                        <option value="{{ $saless->id }}"
                                            {{ $saless->id == Auth::user()->id ? 'selected' : '' }}>{{ $saless->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="selectSales">Sales</label>
                            </div>
                        </div> --}}
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="company" class="form-control" name="company"
                                    placeholder="Mr/Mss xxxx" value="{{ old('company', @$existing->company ?? '') }}">
                                <label for="company">Company</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectVia" aria-label="Default select example"
                                    name="info">
                                    <option disabled>----- Choose Via -----</option>
                                    <option value="Reftech"
                                        {{ old('info', @$existing->info) == 'Reftech' ? 'selected' : '' }}>
                                        Reftech
                                    </option>
                                    <option value="Kojisha"
                                        {{ old('info', @$existing->info) == 'Kojisha' ? 'selected' : '' }}>Kojisha
                                    </option>
                                </select>
                                <label for="selectSource">Via</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="email" class="form-control" name="email"
                                    placeholder="xxxx@xxx.xx" value="{{ old('email', @$existing->email ?? '') }}">
                                <label for="email">Email</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="phoneAnimation" class="form-control" name="phone"
                                    placeholder="081xxxxx" value="{{ old('phone', @$existing->phone ?? '') }}">
                                <label for="phoneAnimation">Phone</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="unitsiteAnimation" class="form-control" name="unit"
                                    placeholder="XXX-21" value="{{ old('unit', @$existing->unit ?? '') }}">
                                <label for="unitsiteAnimation">Unit</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectR/U" aria-label="Default select example"
                                    name="ru">
                                    <option disabled>----- Choose R/U -----</option>
                                    <option value="User" {{ old('ru', @$existing->ru) == 'User' ? 'selected' : '' }}>
                                        User
                                    </option>
                                    <option value="Reseller"
                                        {{ old('ru', @$existing->ru) == 'Reseller' ? 'selected' : '' }}>Reseller
                                    </option>
                                </select>
                                <label for="selectSource">R/U</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectSource" aria-label="Default select example"
                                    name="source">
                                    <option disabled>----- Choose Source -----</option>
                                    <option value="IG"
                                        {{ old('source', @$existing->source) == 'IG' ? 'selected' : '' }}>Instagram
                                    </option>
                                    <option value="LinkedIn"
                                        {{ old('source', @$existing->source) == 'LinkedIn' ? 'selected' : '' }}>
                                        LinkedIn
                                    </option>
                                    <option value="Website"
                                        {{ old('source', @$existing->source) == 'Website' ? 'selected' : '' }}>Website
                                    </option>
                                    <option value="Iklan"
                                        {{ old('source', @$existing->source) == 'Iklan' ? 'selected' : '' }}>Iklan
                                    </option>
                                    <option value="Google"
                                        {{ old('source', @$existing->source) == 'Google' ? 'selected' : '' }}>Google
                                    </option>
                                    <option value="Other"
                                        {{ old('source', @$existing->source) == 'Other' ? 'selected' : '' }}>Other
                                    </option>
                                </select>
                                <label for="selectSource">Source</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectMobile" aria-label="Default select example"
                                    name="mobile">
                                    <option disabled>----- Choose Mobile -----</option>
                                    <option value="WA"
                                        {{ old('mobile', @$existing->mobile) == 'WA' ? 'selected' : '' }}>
                                        WhatsApp</option>
                                    <option value="Phone Office"
                                        {{ old('mobile', @$existing->mobile) == 'Phone Office' ? 'selected' : '' }}>
                                        Phone
                                        Office</option>
                                </select>
                                <label for="selectMobile">Mobile</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="machineAnimation" class="form-control"
                                    placeholder="123xxxxxxxx" name="npwp"
                                    value="{{ old('npwp', @$existing->npwp ?? '') }}">
                                <label for="npwpAnimation">NPWP</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="areaAnimation" class="form-control"
                                    placeholder="Contoh: Bandung" name="area"
                                    value="{{ old('area', @$existing->area ?? '') }}">
                                <label for="areaAnimation">Area</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control h-px-100" name="address" id="addressTextarea1"
                                    placeholder="Contoh: Jl Taman Kopo Indah 5 Kota...">{{ old('address', @$existing->address ?? '') }}</textarea>
                                <label for="addressTextarea1">Address</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control h-px-100" name="subAddress" id="addressTextarea2"
                                    placeholder="Contoh: Jl Taman Kopo Indah 5 Kota...">{{ old('subAddress', @$existing->subAddress ?? '') }}</textarea>
                                <label for="addressTextarea2">Sub Address</label>
                            </div>
                        </div>
                    </div>
                    @empty($existing)
                        <div class="divider divider-dark mx-3">
                            <div class="divider-text"><span class="fw-semibold">Personal In Charge</span></div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="nameAnimation" class="form-control" name="namePic"
                                        placeholder="xxxxxxx xxxxxxxx"
                                        value="{{ old('namePic', @$existing->pic->name_pic ?? '') }}">
                                    <label for="nameAnimation">Name</label>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="positionAnimation" class="form-control" name="position"
                                        placeholder="example: CEO"
                                        value="{{ old('position', @$existing->pic->position ?? '') }}">
                                    <label for="positionAnimation">Position</label>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="emailPicAnimation" class="form-control" name="emailPic"
                                        placeholder="xxxxxxxx@xxx.xx"
                                        value="{{ old('emailPic', @$existing->pic->email_pic ?? '') }}">
                                    <label for="emailPicAnimation">Email PIC</label>
                                </div>
                            </div>
                            <div class="col mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="phone" id="phonePicAnimation" class="form-control" name="phonePic"
                                        placeholder="08xxxxxxxxxx"
                                        value="{{ old('phonePic', @$existing->pic->phone_pic ?? '') }}">
                                    <label for="phonePicAnimation">Phone PIC</label>
                                </div>
                            </div>
                        </div>
                    @endempty
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
