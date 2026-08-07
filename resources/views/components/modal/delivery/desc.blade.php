<form action="{{ route('delivery.change_desc', $delivery->id) }}" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal animate__animated animate__fadeIn" id="descView" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5">Description To Image Link Product
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
                    @foreach ($dDelivery as $product)
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <h5 class="fw-medium">
                                    {{ $product->pn->brand }} {{ $product->pn->pn }}
                                </h5>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="checker[]" value="{{$product->id}}" id="defaultCheck-{{$product->id}}" {{$product->view == '1' ? 'checked' : ''}}>
                                    <label class="form-check-label" for="defaultCheck-{{$product->id}}">
                                        No Description
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
