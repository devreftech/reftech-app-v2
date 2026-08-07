<form action="{{ route('product-set.store') }}" method="post"
    enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf

    @if (@$product)
        @method('patch')
    @endif
    <div class="modal animate__animated animate__fadeIn"
        id="createProduct" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">
                        Create Product Set
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
                                <input type="text" id="commodity" class="form-control" name="commodity"
                                    placeholder="W XXX" value="{{ old('commodity', @$product->commodity ?? '') }}">
                                <label for="commodity">Commodity</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="detail_desc" class="form-control" name="detail_desc"
                                    placeholder="Short Description"
                                    value="{{ old('detail_desc', @$product->detail_desc ?? '') }}">
                                <label for="detail_desc">Short Descroption</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <textarea type="text" id="description" class="form-control h-px-100" name="description" placeholder="xxxxxxx"
                                    cols="30" rows="10">{{ old('description', @$product->description ?? '') }}</textarea>
                                <label for="description">Description</label>
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
