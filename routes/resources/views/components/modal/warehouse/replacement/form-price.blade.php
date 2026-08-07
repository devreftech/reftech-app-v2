<form action="{{ route('product.replacement.update', $detail->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    <div class="modal animate__animated animate__fadeIn" id="editReplacement-{{ $detail->id }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5"> Edit Price Replacement {{ $detail->replacement }}
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
                        <div class="col">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="replacement" class="form-control" name="replacement"
                                    placeholder="......" value="{{ $detail->replacement }}">
                                <label for="replacement">Replacement</label>
                            </div>
                        </div>
                        @if (Auth::user()->role == 'Admin')
                            <div class="col mb-2">
                                <div class="col mb-2">
                                    <div class="input-group form-floating form-floating-outline" data-price="1">
                                        <span class="input-group-text">Rp. </span>
                                        <input type="text" class="form-control invoice-item-modal-label"
                                            id="modal-label" data-id="{{ $detail->id }}" min="0"
                                            placeholder="Put modal Here" data-type="currency"
                                            pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                            @blur="focused = false"
                                            value="{{ old('modal', @$detail->modal ? number_format($detail->modal, 0, ',', '.') : '') }}">
                                        <input class="form-control invoice-item-modal" type="number" name="modal"
                                            id="modal-{{ $detail->id }}"
                                            value="{{ old('modal', @$detail->modal ?? '') }}" hidden>
                                    </div>
                                </div>
                            </div>
                        @endif
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
