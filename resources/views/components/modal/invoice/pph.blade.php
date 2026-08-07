<form action="{{ $quote->type == 'Sparepart' ? route('invoice.pph', $invoice->id) : route('invoice.pph_service', $invoice->id) }}" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal animate__animated animate__fadeIn" id="addPph" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5">Add PPH {{ $invoice->no_invoice }}
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
                    @if ($quote->type == 'Sparepart')
                        @foreach ($dquote as $product)
                            <div class="row g-2 mb-3">
                                <div class="col-8">
                                    <p class="fw-medium">
                                        {{ $product->equivalent->brand }} {{ $product->equivalent->pn }}
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
                    @else
                    @php
                        $row = 0;
                    @endphp
                        @foreach ($subQuote as $product)
                            @foreach ($product->detail as $details)
                            @php
                                $row++;
                            @endphp
                                <div class="row g-2 mb-3">
                                    <div class="col-8">
                                        <p class="fw-medium">
                                            {{ $details->product }}
                                        </p>
                                    </div>
                                    <div class="col-4">
                                        <div class="input-group input-group-merge">
                                            <input type="number" class="form-control" placeholder="2" name="pph[{{$row}}]"
                                                aria-label="Amount (to the nearest dollar)" value="0">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
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
</form>
