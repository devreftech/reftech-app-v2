<form action="{{ route('invoice.confirm_payment', $invoice->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="confirmPayment" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Confirm Payment {{ $invoice->no_invoice }}</h4>
                        {{-- <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div> --}}
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <textarea class="form-control" name="note" id="note"></textarea>
                                        {{-- <input class="form-control form-control-sm" type="textarea"
                                            placeholder="Put No Invoice Here ...." id="invoice" name="invoice"> --}}
                                        <label for="invoice">Note</label>
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
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</form>
