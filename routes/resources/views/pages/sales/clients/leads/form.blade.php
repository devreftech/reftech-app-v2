<form action="{{ @$leads ? route('leads.update', @$leads->id) : route('leads.store') }}" method="post"
    enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf

    @if (@$leads)
        @method('patch')
    @endif
    <div class="modal animate__animated animate__fadeIn"
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
                        @if (Auth::user()->id == '1' || Auth::user()->id == '16' || Auth::user()->id == '23')
                            <div class="col-6 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="company" class="form-control" name="company"
                                        placeholder="PT xxxxxxx" value="{{ old('company', @$leads->company ?? '') }}">
                                    <label for="company">Company</label>
                                </div>
                            </div>
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
                                    <label for="selectSource">Via</label>
                                </div>
                            </div>
                            @if (empty($leads))
                                <div class="col mb-2">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectWeek" aria-label="Default select example"
                                            name="week" {{ @$leads ? 'disabled' : '' }}>
                                            <option disabled>----- Choose Week -----</option>
                                            <option value="1">Week 1</option>
                                            <option value="2">Week 2</option>
                                            <option value="3">Week 3</option>
                                            <option value="4">Week 4</option>
                                            <option value="5">Week 5</option>
                                        </select>
                                        <label for="selectWeek">Week</label>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="col-12 mb-2">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="company" class="form-control" name="company"
                                        placeholder="PT xxxxxxx" value="{{ old('company', @$leads->company ?? '') }}">
                                    <label for="company">Company</label>
                                </div>
                            </div>
                        @endif
                    </div>
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
                                <input type="phone" id="phoneAnimation" class="form-control" name="phone"
                                    placeholder="081xxxxx" value="{{ old('phone', @$leads->phone ?? '') }}">
                                <label for="phoneAnimation">Phone</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="unitsiteAnimation" class="form-control" name="unit"
                                    placeholder="xxx-21" value="{{ old('unit', @$leads->unit ?? '') }}">
                                <label for="unitsiteAnimation">Unit</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectR/U" aria-label="Default select example"
                                    name="ru">
                                    <option disabled>----- Choose R/U -----</option>
                                    <option value="User" {{ old('ru', @$leads->ru) == 'User' ? 'selected' : '' }}>
                                        User
                                    </option>
                                    <option value="Reseller"
                                        {{ old('ru', @$leads->ru) == 'Reseller' ? 'selected' : '' }}>Reseller
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
                                        {{ old('source', @$leads->source) == 'IG' ? 'selected' : '' }}>Instagram
                                    </option>
                                    <option value="LinkedIn"
                                        {{ old('source', @$leads->source) == 'LinkedIn' ? 'selected' : '' }}>LinkedIn
                                    </option>
                                    <option value="Website"
                                        {{ old('source', @$leads->source) == 'Website' ? 'selected' : '' }}>Website
                                    </option>
                                    <option value="Iklan"
                                        {{ old('source', @$leads->source) == 'Iklan' ? 'selected' : '' }}>Iklan
                                    </option>
                                    <option value="Google"
                                        {{ old('source', @$leads->source) == 'Google' ? 'selected' : '' }}>Google
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
                    {{-- Domain detail (muncul hanya ketika source = Website) --}}
                    @php
                        $wsPredefined = ['reftech.id', 'rentalkompresor.com', 'hvac.co.id', 'chiller.co.id'];
                        $wsStored = old('source_detail', @$leads->source_detail ?? '');
                        $wsOther  = old('source_detail_other', '');
                        if ($wsStored && !in_array($wsStored, $wsPredefined)) {
                            $wsOther  = $wsStored;
                            $wsStored = 'Other';
                        }
                    @endphp
                    <div class="row g-2 mb-3" id="divSourceDetail" style="display:none">
                        <div class="col-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectSourceDetail" name="source_detail">
                                    <option value="" disabled {{ $wsStored == '' ? 'selected' : '' }}>----- Pilih Domain -----</option>
                                    <option value="reftech.id" {{ $wsStored == 'reftech.id' ? 'selected' : '' }}>reftech.id</option>
                                    <option value="rentalkompresor.com" {{ $wsStored == 'rentalkompresor.com' ? 'selected' : '' }}>rentalkompresor.com</option>
                                    <option value="hvac.co.id" {{ $wsStored == 'hvac.co.id' ? 'selected' : '' }}>hvac.co.id</option>
                                    <option value="chiller.co.id" {{ $wsStored == 'chiller.co.id' ? 'selected' : '' }}>chiller.co.id</option>
                                    <option value="Other" {{ $wsStored == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                <label>Domain Website</label>
                            </div>
                        </div>
                        <div class="col-6 mb-2" id="divSourceDetailOther" style="display:none">
                            <div class="form-floating form-floating-outline">
                                <input type="text" class="form-control" id="inputSourceDetailOther"
                                       name="source_detail_other" placeholder="Ketik domain..."
                                       value="{{ $wsOther }}">
                                <label>Domain Lainnya</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="machineAnimation" class="form-control"
                                    placeholder="123xxxxxxxx" name="npwp"
                                    value="{{ old('npwp', @$leads->npwp ?? '') }}">
                                <label for="npwpAnimation">NPWP</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="areaAnimation" class="form-control"
                                    placeholder="Contoh: Bandung" name="area"
                                    value="{{ old('area', @$leads->area ?? '') }}">
                                <label for="areaAnimation">Area</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control h-px-100" name="address" id="addressTextarea1"
                                    placeholder="Contoh: Jl Taman Kopo Indah 5 Kota...">{{ old('address', @$leads->address ?? '') }}</textarea>
                                <label for="addressTextarea1">NPWP Address</label>
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
@push('page-script')
<script>
$(function () {
    function syncSourceDetail() {
        var src = $('#selectSource').val();
        if (src === 'Website') {
            $('#divSourceDetail').show();
            if ($('#selectSourceDetail').val() === 'Other') {
                $('#divSourceDetailOther').show();
            }
        }
    }
    syncSourceDetail();

    $('#selectSource').on('change', function () {
        var isWebsite = $(this).val() === 'Website';
        $('#divSourceDetail').toggle(isWebsite);
        if (!isWebsite) {
            $('#selectSourceDetail').val('');
            $('#divSourceDetailOther').hide();
        }
    });

    $('#selectSourceDetail').on('change', function () {
        $('#divSourceDetailOther').toggle($(this).val() === 'Other');
    });
});
</script>
@endpush
