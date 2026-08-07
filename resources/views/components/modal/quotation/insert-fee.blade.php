<form action="{{ route('insert_fee.quotation', $quote->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="insertFee" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Insert Fee of {{ $quote->no_quote }}</h4>
                        <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div>
                        <form>
                            <div class="row">
                                @php
                                    $row = 0;
                                @endphp
                                @foreach ($dquote as $detail)
                                    @php
                                        $row ++;
                                    @endphp
                                    <div class="col-4">
                                        <p class="text-start">Fee {{ $detail->product }}</p>
                                    </div>
                                    <div class="col-8 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <div class="input-group" data-price="{{ $row }}">
                                                <span class="input-group-text">Rp. </span>
                                                <input type="text" class="form-control invoice-item-price-label"
                                                    id="priceLabel-{{ $row }}" data-id="{{ $row }}"
                                                    name="harga[]" placeholder="Put Fee Here" data-type="currency"
                                                    min="0" pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                    @focus="focused = true" @blur="focused = false"
                                                    value="{{ old('price[]', $detail->fee != 0 ? number_format($detail->fee, 0, ',', '.') : 0) }}">
                                                <input class="form-control invoice-item-price" type="number"
                                                    name="fee[]" id="price-{{ $row }}"
                                                    value="{{ old('price[]', $detail->fee != 0 ? $detail->fee : 0) }}" hidden>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="col-4">
                                    <h5 class="text-start">Total</h5>
                                </div>
                                <div class="col-8 mb-3">
                                    <div class="form-floating-outline">
                                        <input type="text" id="totalLabel"
                                            class="form-control form-control-sm invoice-item-total-label" value="Rp {{ old('total[]', $quote->fee != 0 ? number_format($quote->fee, 0, ',', '.') : 0) }}" disabled>
                                        <input class="form-control invoice-item-total" type="number" name="total"
                                            id="total" value="{{ old('total[]', $quote->fee != 0 ? $quote->fee : 0) }}" hidden>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
