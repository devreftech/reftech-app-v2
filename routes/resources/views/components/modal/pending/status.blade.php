<form action="{{ route('pending-po.statusEdit', $pending->id) }}" method="post" enctype="multipart/form-data">
    @method('PATCH')
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="statusEdit" tabindex="-1"
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
                                        <select class="form-select" tabindex="0" id="statusChange" name="status">
                                            <option value="1" {{ $pending->status == '1' ? 'selected' : '' }}>
                                                On Check
                                            </option>
                                            <option value="2" {{ $pending->status == '2' ? 'selected' : '' }}>
                                                Ready Stock
                                            </option>
                                            <option value="3" {{ $pending->status == '3' ? 'selected' : '' }}>
                                                Kurang
                                            </option>
                                            <option value="4" {{ $pending->status == '4' ? 'selected' : '' }}>
                                                Pre-Order
                                            </option>
                                            <option value="5" {{ $pending->status == '5' ? 'selected' : '' }}>
                                                Delivery Process
                                            </option>
                                            <option value="6" {{ $pending->status == '6' ? 'selected' : '' }}>
                                                Done
                                            </option>
                                            <option value="7" {{ $pending->status == '7' ? 'selected' : '' }}>
                                                Cancel
                                            </option>
                                        </select>
                                        <label for="statusChange">Status</label>
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
