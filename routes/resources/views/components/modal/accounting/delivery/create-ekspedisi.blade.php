<form action="{{ route('delivery.store', $invoice->id) }}" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf

    {{-- @if (@$product)
        @method('patch')
    @endif --}}
    <div class="modal animate__animated animate__fadeIn" id="doEkspedisi" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">
                        {{ 'Create Delivery Order Ekspedisi' . $invoice->no_invoice }}
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
                    <div class="row g-2 my-3">
                        <div class="col-8">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select" id="selectAddress"
                                    aria-label="Default select example" name="destination" data-allow-clear="true">
                                    <option value="1"
                                        {{ old('address', $invoice->invoiceTo) == '1' ? 'selected' : '' }}>
                                        {{ $quote->pic->client->address }}
                                    </option>
                                    <option value="2"
                                        {{ old('address', $invoice->invoiceTo) == '2' ? 'selected' : '' }}>
                                        {{ $quote->pic->client->subAddress }}</option>
                                </select>
                                <label for="selectAddress">Choose Address</label>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="date" id="html5-date-input" name="date"
                                    value="{{ @$invoice->date }}">
                                <label for="html5-date-input">Date</label>
                            </div>
                            <input type="text" name="type" id="type" value="ekspedisi" hidden>
                            <input type="number" name="id_invoice" id="id_invoice" value="{{ $invoice->id }}" hidden>
                        </div>
                    </div>
                    @if ($invoice->quote->type == 'Sparepart')
                        @php
                            $i = 0;
                        @endphp
                        @forelse ($dquote as $product)
                            <hr>
                            <div class="row g-2 mb-3">
                                <div class="col-1"></div>
                                <div class="col-3 mb-2 d-flex align-item-center">
                                    <p style="margin: auto">{{ $product->equivalent->brand }}
                                        {{ $product->equivalent->pn }}
                                    </p>
                                </div>
                                <div class="col-6 mb-2">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <div class="input-group">
                                            <input type="number" id="stock" class="form-control" name="qty[]"
                                                value="0" max="{{ $product->qty }}" min="0">
                                            <span class="input-group-text"
                                                id="basic-addon43">{{ $product->info_qty }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <p>max : {{ $product->qty }}</p>
                                </div>
                            </div>
                            @php
                                $i++;
                            @endphp
                        @empty
                            <p> Anda Belum memiliki pn. </p>
                        @endforelse
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
