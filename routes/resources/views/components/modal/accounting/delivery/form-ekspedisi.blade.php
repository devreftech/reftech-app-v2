<form action="{{ route('invoice.form_ekspedisi', $invoice->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal modal-xl fade animate__animated" id="changeDate" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Change Date and Address of {{ $invoice->no_invoice }}
                        </h4>
                        <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select class="select2 form-select" id="selectAddress"
                                            aria-label="Default select example" name="destination"
                                            data-allow-clear="true">
                                            <option value="1"
                                                {{ old('address', $invoice->doTo) == '1' ? 'selected' : '' }}>
                                                {{ $quote->pic->client->address }}
                                            </option>
                                            <option value="2"
                                                {{ old('address', $invoice->doTo) == '2' ? 'selected' : '' }}>
                                                {{ $quote->pic->client->subAddress }}</option>
                                        </select>
                                        <label for="selectAddress">Choose Address</label>
                                    </div>
                                </div>
                                <div class="col-12  mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="date" id="html5-date-input" name="date"
                                            value="{{ @$invoice->dateDo }}">
                                        <label for="html5-date-input">Date</label>
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
