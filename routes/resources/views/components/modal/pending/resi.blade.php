<form action="{{ route('pending-po.resiEdit', $pending->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal modal-xl fade animate__animated" id="resiEdit" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input class="form-control" type="date" name="date" id="date_resi"
                                        value="{{ now()->format('Y-m-d') }}">
                                    <label for="date_resi">Date</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input class="form-control" type="text" placeholder="JNE/JNT" id="kurir"
                                        name="kurir">
                                    <label for="kurir">Courier/Vendor</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input class="form-control" type="text" placeholder="JNE/JNT" id="no_track"
                                        name="no_track">
                                    <label for="no_track">Tracking Number</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input class="form-control" name="file" type="file" id="formFile" accept="image/*,.pdf">
                                    <label for="formFile">Upload Receipt</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <label for="price">Total Cost (IDR)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp.</span>
                                        <input type="text"
                                            class="form-control form-control-lg invoice-item-price-label" name="harga[]"
                                            placeholder="Put Cost Here" data-type="currency" min="0"
                                            pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                            @blur="focused = false">
                                        <input class="form-control form-control-lg invoice-item-price" type="number"
                                            name="cost" id="price" hidden>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card shadow-none bg-transparent border border-label-secondary mb-3">
                                    <div class="card-body">
                                        <p class="fw-semibold text-start d-block mb-0">Charged to</p>
                                        <div class="form-check form-check-inline mt-3">
                                            <input class="form-check-input" type="radio" name="charged"
                                                id="inlineRadio1" value="1" {{ $pending->id == 1 ? 'selected' : '' }}>
                                            <label class="form-check-label" for="inlineRadio1">Company</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="charged"
                                                id="inlineRadio2" value="2" {{ $pending->id == 2 ? 'selected' : '' }}>
                                            <label class="form-check-label" for="inlineRadio2">Customer</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        let formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        });

        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }
        $(".invoice-item-price-label").on('keyup', function() {
            var input = $(this);
            var input_val = input.val();

            // original length
            var original_len = input_val.length;

            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // send updated string to input
            input.val(input_val);
            var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
            $(`#price`).val(nomorInt);
        });
        
        
    </script>
@endpush
