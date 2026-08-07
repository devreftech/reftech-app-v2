<form action="{{ route('invoice.pph_manual', $invoice->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="addPphManual" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Add PPH Manual</h4>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <div class="input-group">
                                            <span class="input-group-text">Rp. </span>
                                            <input type="text" class="form-control invoice-item-pph-manual-label"
                                                id="pphManualLabel" name="pphLabel" placeholder="Put PPH Here"
                                                data-type="currency" min="0" pattern="^[0-9]\d{0,2}(\.\d{3})*$"
                                                @focus="focused = true" @blur="focused = false"
                                                value="{{ old('pph') }}">
                                            <input class="form-control invoice-item-pph-manual" type="number"
                                                name="pph" id="pphManual" value="{{ old('pph') }}" hidden>
                                        </div>
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
