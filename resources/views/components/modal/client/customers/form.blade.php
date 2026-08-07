<form action="{{route('customers.update', $customers->id)}}" method="post"
    enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf

    @if ($customers)
        @method('patch')
    @endif
    <div class="modal animate__animated animate__fadeIn"
        id="{{'updateCustomers-' . strval($customers->id)}}" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">{{ 'Update Data '}} Customers
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
                                <select class="form-select" id="selectSales" name="sales"
                                    aria-label="Default select example">
                                    <option disabled>----- Choose Sales -----</option>
                                    @foreach ($sales as $saless)
                                        <option value="{{ $saless->id }}"
                                            {{ @$customers->id_sales == $saless->id ? 'selected' : '' }}>{{ $saless->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="selectSales">Sales</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="company" class="form-control" name="company"
                                    placeholder="Mr/Mss xxxx" value="{{ old('company', @$customers->company ?? '') }}">
                                <label for="company">Company</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="email" id="email" class="form-control" name="email"
                                    placeholder="xxxx@xxx.xx" value="{{ old('email', @$customers->email ?? '') }}">
                                <label for="email">Email</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="phoneAnimation" class="form-control" name="phone"
                                    placeholder="081xxxxx" value="{{ old('phone', @$customers->phone ?? '') }}">
                                <label for="phoneAnimation">Phone</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="unitsiteAnimation" class="form-control" name="unit"
                                    placeholder="XXX-21" value="{{ old('unit', @$customers->unit ?? '') }}">
                                <label for="unitsiteAnimation">Unit</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectR/U" aria-label="Default select example"
                                    name="ru">
                                    <option disabled>----- Choose R/U -----</option>
                                    <option value="User" {{ old('ru', @$customers->ru) == 'User' ? 'selected' : '' }}>
                                        User
                                    </option>
                                    <option value="Reseller"
                                        {{ old('ru', @$customers->ru) == 'Reseller' ? 'selected' : '' }}>Reseller
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
                                        {{ old('source', @$customers->source) == 'IG' ? 'selected' : '' }}>Instagram
                                    </option>
                                    <option value="LinkedIn"
                                        {{ old('source', @$customers->source) == 'LinkedIn' ? 'selected' : '' }}>LinkedIn
                                    </option>
                                    <option value="Website"
                                        {{ old('source', @$customers->source) == 'Website' ? 'selected' : '' }}>Website
                                    </option>
                                    <option value="Iklan"
                                        {{ old('source', @$customers->source) == 'Iklan' ? 'selected' : '' }}>Iklan
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
                                        {{ old('mobile', @$customers->mobile) == 'WA' ? 'selected' : '' }}>
                                        WhatsApp</option>
                                    <option value="Phone Office"
                                        {{ old('mobile', @$customers->mobile) == 'Phone Office' ? 'selected' : '' }}>Phone
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
                                    placeholder="Contoh: Copco Atlas" name="machine"
                                    value="{{ old('machine', @$customers->machine ?? '') }}">
                                <label for="machineAnimation">Machine</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="areaAnimation" class="form-control"
                                    placeholder="Contoh: Bandung" name="area"
                                    value="{{ old('area', @$customers->area ?? '') }}">
                                <label for="areaAnimation">Area</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control h-px-100" name="address" id="addressTextarea1"
                                    placeholder="Contoh: Jl Taman Kopo Indah 5 Kota...">{{ old('address', @$customers->address ?? '') }}</textarea>
                                <label for="addressTextarea1">Address</label>
                            </div>
                        </div>
                    </div>
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

