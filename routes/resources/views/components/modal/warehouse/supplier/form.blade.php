<form action="{{ @$supplier ? route('supplier.update', @$supplier->id) : route('supplier.store') }}" method="post"
    enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf

    @if (@$supplier)
        @method('patch')
    @endif
    <div class="modal animate__animated animate__fadeIn"
        id="{{ @$supplier ? 'updateSupplier-' . strval(@$supplier->id) : 'createSupplier' }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">{{ @$supplier ? 'Update Data' : 'Create New' }}
                        Supplier
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
                        <div class="col-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="supplier" class="form-control" name="supplier"
                                    placeholder="PT xxxxxxx" value="{{ old('supplier', @$supplier->supplier ?? '') }}">
                                <label for="supplier">Supplier</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectInfo" aria-label="Default select example"
                                    name="info">
                                    <option disabled>----- Choose Info -----</option>
                                    <option value="Lokal"
                                        {{ old('info', @$supplier->info) == 'Lokal' ? 'selected' : '' }}>
                                        Lokal
                                    </option>
                                    <option value="Import"
                                        {{ old('info', @$supplier->info) == 'Import' ? 'selected' : '' }}>Import
                                    </option>
                                </select>
                                <label for="selectSource">Info</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="code" class="form-control" name="code"
                                    placeholder="X-XXX" value="{{ old('code', @$supplier->code ?? '') }}">
                                <label for="code">Code</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="email" class="form-control" name="email"
                                    placeholder="xxxx@xxx.xx" value="{{ old('email', @$supplier->email ?? '') }}">
                                <label for="email">Email</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="phoneAnimation" class="form-control" name="phone"
                                    placeholder="081xxxxx" value="{{ old('phone', @$supplier->phone ?? '') }}">
                                <label for="phoneAnimation">Phone</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="machineAnimation" class="form-control"
                                    placeholder="123xxxxxxxx" name="npwp"
                                    value="{{ old('npwp', @$supplier->npwp ?? '') }}">
                                <label for="npwpAnimation">NPWP</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="areaAnimation" class="form-control"
                                    placeholder="Contoh: Bandung" name="area"
                                    value="{{ old('area', @$supplier->area ?? '') }}">
                                <label for="areaAnimation">Area</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline mb-4">
                                <textarea class="form-control h-px-100" name="address" id="addressTextarea1"
                                    placeholder="Contoh: Jl Taman Kopo Indah 5 Kota...">{{ old('address', @$supplier->address ?? '') }}</textarea>
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
