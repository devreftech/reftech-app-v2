<form action="{{ route('invoice.due_date', $invoice->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="dueDate" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Due Date {{ $invoice->no_invoice }}</h4>
                        {{-- <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div> --}}
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="date" id="html5-date-input" name="date"
                                            value="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">
                                        <label for="html5-date-input">Date</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="basic-addon43">Due Date</label>
                                    <div class="form-floating form-floating-outline">
                                        <div class="input-group input-group-lg">
                                            <input type="number" class="form-control" name="due_date"
                                                placeholder="Due Date" aria-label="Due Date"
                                                aria-describedby="basic-addon43">
                                            <span class="input-group-text" id="basic-addon43">Days</span>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-12 mb-3">
                                    <label for="basic-addon43">Extends Date</label>
                                    <div class="form-floating form-floating-outline">
                                        <div class="input-group input-group-lg">
                                            <input type="number" class="form-control" name="extends"
                                                placeholder="Extends Date" aria-label="Due Date"
                                                aria-describedby="basic-addon43">
                                            <span class="input-group-text" id="basic-addon43">Days</span>
                                        </div>
                                    </div>
                                </div> --}}
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
