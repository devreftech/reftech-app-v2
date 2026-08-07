<form action="{{ route('pending-po.deliveryEdit', $pending->id) }}" method="post" enctype="multipart/form-data">
    @method('PATCH')
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="deliveryEdit" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">

                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ $pending->quote->invoice[0]->no_invoice ?? $pending->quote->pic->client->company }}</h4>
                            <div class="row">
                                <div class="col">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <select class="form-select" tabindex="0" id="deliveryChange" name="delivery">
                                            <option value="1" {{ $pending->delivery == '1' ? 'selected' : '' }}>
                                                JNE / J&T / Cargo
                                            </option>
                                            <option value="2" {{ $pending->delivery == '2' ? 'selected' : '' }}>
                                                Send By Technician
                                            </option>
                                            <option value="3" {{ $pending->delivery == '3' ? 'selected' : '' }}>
                                                Taken Directly
                                            </option>
                                            <option value="4" {{ $pending->delivery == '4' ? 'selected' : '' }}>
                                                Other
                                            </option>
                                        </select>
                                        <label for="deliveryChange">Delivery</label>
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
