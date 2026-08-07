<form action="{{ route('unit-reftech.edit', $unitr->id) }}" method="post" enctype="multipart/form-data">
    @method('PATCH')
    @csrf
    <div class="modal animate__animated animate__fadeIn" id="editUnit-{{ $unitr->id }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Create New Machine
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
                    <div class="divider divider-dark mx-3">
                        <div class="divider-text"><span class="fw-semibold">Machine</span></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-6 mb-2">
                            <div class="form-floating form-floating-outline mb-2">
                                <input type="text" id="id_client" class="form-control" name="id_client"
                                    value="1" hidden>
                                <select class="select2 form-select" data-allow-clear="true" name="unit"
                                    data-id="1" disabled>
                                    <option> ---- Choose Uniit Here ---- </option>
                                    @foreach ($unit as $machine)
                                        <option value="{{ $machine->id }}"
                                            {{ $unitr->id_unit == $machine->id ? 'selected' : '' }}>
                                            {{ $machine->brand }} - {{ $machine->unit->sku ?? '-' }} ||
                                            {{ $machine->bar ?? '-' }} - {{ $machine->air_cap ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="Unit" class="mb-2">Unit</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select invoice-item-info" id="status"
                                    aria-label="Default select example" name="status">
                                    <option disabled>----- Info Status -----</option>
                                    <option value="Ready" {{ @$unitr->status == 'Ready' ? 'selected' : '' }}>Ready
                                    </option>
                                    <option value="On Rental" {{ @$unitr->status == 'On Rental' ? 'selected' : '' }}>
                                        On Rental
                                    </option>
                                    <option value="Sold" {{ @$unitr->status == 'Sold' ? 'selected' : '' }}>Sold
                                    </option>
                                    <option value="Service" {{ @$unitr->status == 'Service' ? 'selected' : '' }}>
                                        Service
                                    </option>
                                </select>
                                <label for="exampleFormControlSelect1">Status</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="tagAnimation" class="form-control" name="tag"
                                    placeholder="Example: Second - Rental" value="{{ $unitr->tag }}">
                                <label for="tagAnimation">Keterangan</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="descAnimation" class="form-control" name="desc"
                                    placeholder="Example: CEO" value="{{ $unitr->desc }}">
                                <label for="descAnimation">Url Google Drive</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select invoice-item-info" id="status_unit"
                                    aria-label="Default select example" name="status_unit">
                                    <option disabled>----- Info Unit -----</option>
                                    <option value="Baru" {{ @$product->status_unit == 'Baru' ? 'selected' : '' }}>Baru
                                    </option>
                                    <option value="Second" {{ @$product->status_unit == 'Second' ? 'selected' : '' }}>
                                        Second
                                    </option>
                                </select>
                                <label for="exampleFormControlSelect1">Status Unit</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-2">
                            <label for="priceAnimation">Price</label>
                            <div class="input-group form-floating form-floating-outline" data-price="1">
                                <span class="input-group-text">Rp. </span>
                                <input type="text" class="form-control harga-label" id="harga-label"
                                    data-id="{{ $unitr->id }}" min="12" placeholder="Put harga Here"
                                    data-type="currency" pattern="^[1-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                    @blur="focused = false" value="{{ old('price', $unitr->price ?? '') }}">
                                <input class="form-control harga" type="number" name="total"
                                    id="harga{{ $unitr->id }}" value="{{ old('price', $unitr->price ?? '') }}"
                                    hidden>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label for="priceAnimation">Price Rental</label>
                            <div class="input-group form-floating form-floating-outline" data-price="1">
                                <span class="input-group-text">Rp. </span>
                                <input type="text" class="form-control harga-rental-label" id="hargaRental"
                                    data-id="{{ $unitr->id }}" min="12" placeholder="Put harga-rental Here"
                                    data-type="currency" pattern="^[1-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                    @blur="focused = false"
                                    value="{{ old('price_rental', $unitr->price_rental ?? '') }}">
                                <input class="form-control harga-rental" type="number" name="totalRental"
                                    id="hargaRental{{ $unitr->id }}"
                                    value="{{ old('price_rental', $unitr->price_rental ?? '') }}" hidden>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label for="priceAnimation">Best Price</label>
                            <div class="input-group form-floating form-floating-outline" data-price="1">
                                <span class="input-group-text">Rp. </span>
                                <input type="text" class="form-control harga-best-label" id="hargabest"
                                    data-id="{{ $unitr->id }}" min="12" placeholder="Put harga-best Here"
                                    data-type="currency" pattern="^[1-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                    @blur="focused = false"
                                    value="{{ old('price_best', $unitr->price_best ?? '') }}">
                                <input class="form-control harga-best" type="number" name="totalBest"
                                    id="totalBest{{ $unitr->id }}"
                                    value="{{ old('price_best', $unitr->price_best ?? '') }}" hidden>
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
