<form action="{{ route('payment.addCost', $payment->id) }}" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal modal-lg animate__animated animate__fadeIn" id="addCost" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5">Add Cost
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
                                    <input type="text" class="form-control invoice-item-cost-label" id="costLabel"
                                        data-id="1" name="harga" placeholder="Put cost Here" data-type="currency"
                                        min="0" pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                        @blur="focused = false" value="{{ old('cost', number_format($payment->cost, 0, '', '.')) }}">
                                    <input class="form-control invoice-item-cost" type="number" name="cost"
                                        id="cost" value="{{ old('cost', $payment->cost) }}" hidden>
                                    <label for="descAnimation">Cost</label>
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
