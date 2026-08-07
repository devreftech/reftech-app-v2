<form
    action="{{ route('purchase.add_pph', $purchase->id) }}"
    method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal animate__animated animate__fadeIn" id="addPph" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5">Add PPH
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
                    @foreach ($dPurchase as $product)
                        <div class="row g-2 mb-3">
                            <div class="col-8">
                                <p class="fw-medium">
                                    {{ $product->product }}
                                </p>
                            </div>
                            <div class="col-4">
                                <div class="input-group input-group-merge">
                                    <input type="number" class="form-control" placeholder="2" name="pph[]"
                                        aria-label="Amount (to the nearest dollar)" value="0">
                                    <span class="input-group-text">%</span>
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
