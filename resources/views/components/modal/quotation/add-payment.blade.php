<form action="{{ route('add_payment.quotation', $quote->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="addPayment" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Add Payment {{ $quote->no_quote }}</h4>
                        <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div>
                        <form>
                            <div class="row">
                                {{-- <div class="col-8 mb-3">
                                    <div class="card-body">
                                        <div class="
                                        b-3">
                                            <input class="form-control h-100" type="file" id="formFile"
                                                name="file" accept=".pdf, .jpg, .jpeg, .png">
                                        </div>
                                    </div>
                                </div> --}}
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectType" aria-label="Default select example"
                                            name="method">
                                            <option disabled>---Method---</option>
                                            <option value="Transfer">
                                                Bank Transfer
                                            </option>
                                            <option value="Giro">
                                                Giro
                                            </option>
                                            <option value="Cash">
                                                Cash
                                            </option>
                                            <option value="Escrow">
                                                Escrow
                                            </option>
                                        </select>
                                        <label for="exampleFormControlSelect1">Type</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectType" aria-label="Default select example"
                                            name="type">
                                            <option disabled>---Type---</option>
                                            <option value="CBD">
                                                Cash Before Delivery
                                            </option>
                                            <option value="COD">
                                                Cash On Delivery
                                            </option>
                                            <option value="DP">
                                                DP
                                            </option>
                                            <option value="BP">
                                                BP
                                            </option>
                                            <option value="Tempo">
                                                Tempo
                                            </option>
                                        </select>
                                        <label for="exampleFormControlSelect1">Type</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp. </span>
                                            <input type="text" class="form-control invoice-item-amount-label"
                                                id="amountLabel" name="harga[]" placeholder="Put Fee Here"
                                                data-type="currency" min="0" pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                @focus="focused = true" @blur="focused = false"
                                                value="{{ old('amount') }}">
                                            <input class="form-control invoice-item-amount" type="number"
                                                name="amount" id="amount" value="{{ old('amount') }}" hidden>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-4 mb-3">
                                    <div class="input-group h-100">
                                        <input type="number" class="form-control" placeholder="Tempo" id="tempo"
                                            aria-label="tempo" name="tempo" aria-describedby="basic-addon43" disabled>
                                        <span class="input-group-text" id="basic-addon43">Days</span>
                                    </div>
                                </div> --}}
                                <div class="col-8 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <textarea class="form-control h-px-100" placeholder="Write your note here...." name="note" id="note"></textarea>
                                        <label for="note">Note</label>
                                    </div>
                                </div>
                                <div class="col-4 mb-3">
                                    <div class="input-group h-px-100">
                                        <input type="number" class="form-control" placeholder="Percent"
                                            aria-label="Percent" name="percent" aria-describedby="basic-addon43">
                                        <span class="input-group-text" id="basic-addon43">%</span>
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
@push('script')
    <script>
        $('#selectType').on('change', function() {
            console.log($(this).val());
            if ($(this).val() === 'Tempo') {
                $('#tempo').prop('disabled', false); // enable
            } else {
                $('#tempo').prop('disabled', true).val(''); // disable + kosongin
            }
        });
    </script>
@endpush
