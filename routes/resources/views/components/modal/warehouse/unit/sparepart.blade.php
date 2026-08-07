<form action="{{ route('unit-sparepart.store', $product->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal animate__animated animate__fadeIn" id="createSparepart" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Create Sparepart of {{ $product->sku }}
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
                                <select class="select2 form-select form-select-lg invoice-item-client"
                                    data-allow-clear="true" name="id_equivalent" id="selectclient">
                                    <option selected>----- Select Sparepart -----</option>
                                    @foreach ($equivalent as $item)
                                        <option value="{{ $item->id }}">
                                            @if ($item->product)
                                                {{ $item->brand }} {{ $item->pn }} - {{ $item->product->detail_desc }} ||
                                                {{ $item->product->category }}
                                                ({{ $item->product->go == 'Replacement' ? 'R' : 'G' }})
                                            @else
                                                {{ $item->pn }} - [Product Not Found]
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <label for="select2Basic">Sparepart</label>
                            </div>
                        </div>
                        <div class="col-2 col-md-4">
                            <div class="form-floating form-floating-outline mb-4">
                                <input type="number" class="form-control invoice-item-qty mb-3" placeholder="Min 1"
                                    name="qty" id="qty" min="1"
                                    value="{{ old('qty', @$sparepart->qty) }}">
                                <label for="qty">Quantity</label>
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
