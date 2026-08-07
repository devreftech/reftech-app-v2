<form action="{{ route('payment.addPph', $payment->id) }}" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal modal-lg animate__animated animate__fadeIn" id="addPPH" tabindex="-1" style="display: none;"
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
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="input-group input-group-merge">
                                <span class="input-group-text">Rp.</span>
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control invoice-item-pph-label" id="pphLabel"
                                        data-id="1" name="harga" placeholder="Put pph Here" data-type="currency"
                                        min="0" pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                        @blur="focused = false" value="{{ old('pph', number_format($payment->pph, 0, '', '.')) }}">
                                    <input class="form-control invoice-item-pph" type="number" name="pph"
                                        id="pph" value="{{ old('pph', $payment->pph) }}" hidden>
                                    <label for="descAnimation">PPH</label>
                                </div>
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
