<form action="{{ route('machine.update', $machine->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @if ($machine)
        @method('patch')
    @endif
    <div class="modal animate__animated animate__fadeIn" id="editMachine-{{ $machine->id }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Edit Machine
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
                                    value="{{ $existing->id }}" hidden>
                                {{-- <select class="select2 form-select" data-allow-clear="true" name="unit"
                                    data-id="1">
                                    <option> ---- Choose Uniit Here ---- </option>
                                    @foreach ($unit as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $machine->id_unit == $item->id ? 'selected' : '' }}>
                                            {{ $machine->serial->brand ?? 'No brand' }} - {{ $item->sku }} ||
                                            {{ $machine->bar }} - {{ $machine->air_cap }}
                                        </option>
                                    @endforeach
                                </select> --}}
                                <select class="select2 form-select" data-allow-clear="true" name="unit"
                                    data-id="1">
                                    <option> ---- Choose Uniit Here ---- </option>
                                    @foreach ($unit as $item)
                                        <option value="{{ $item->id }}" {{ $machine->id_unit == $item->id ? 'selected' : '' }}>
                                            {{ $item->brand }} - {{ $item->unit->sku ?? '-' }} ||
                                            {{ $item->bar ?? '-' }} - {{ $item->air_cap ?? '-' }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="Unit" class="mb-2">Unit</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="descAnimation" class="form-control" name="desc"
                                    placeholder="Example: CEO" value="{{ old('desc', @$machine->desc ?? '') }}">
                                <label for="descAnimation">Description</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="serialAnimation" class="form-control" name="serial"
                                    placeholder="Example: CEO" value="{{ old('serial', @$machine->serial ?? '') }}">
                                <label for="serialAnimation">Serial Number</label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="tagAnimation" class="form-control" name="tag"
                                    placeholder="Example: CEO" value="{{ old('tag', @$machine->tag ?? '') }}">
                                <label for="tagAnimation">Tag Number</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-6 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="loactionAnimation" class="form-control" name="location"
                                    placeholder="Example: CEO"
                                    value="{{ old('location', @$machine->location ?? '') }}">
                                <label for="loactionAnimation">Location</label>
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
    </div>
</form>
