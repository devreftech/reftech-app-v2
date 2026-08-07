<form action="{{ route('status.change.quotation', $quote->id) }}" method="patch" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="changeStatus-{{ $quote->id }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">

                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">{{ $quote->no_quote }}</h4>
                        <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div>
                        <form>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-floating form-floating-outline mb-3">
                                        <select class="form-select" tabindex="0" id="statusChange" name="status">
                                            <option value="20" {{ $quote->status == '20' ? 'selected' : '' }}>Send
                                                WA/Email <small class="text-muted">20%</small></option>
                                            <option value="30" {{ $quote->status == '30' ? 'selected' : '' }}>
                                                Inquiry Accepted <small class="text-muted">30%</small></option>
                                            <option value="40" {{ $quote->status == '40' ? 'selected' : '' }}>
                                                Progress Follow Up <small class="text-muted">40%</small>
                                            </option>
                                            <option value="60" {{ $quote->status == '60' ? 'selected' : '' }}>
                                                Negotiation/Revisi <small class="text-muted">60%</small>
                                            </option>
                                            <option value="80" {{ $quote->status == '80' ? 'selected' : '' }}>Hot
                                                Prospect<small class="text-muted">80%</small>
                                            </option>
                                            <option value="90" {{ $quote->status == '90' ? 'selected' : '' }}>Hold
                                                <small class="text-muted">90%</small>
                                            </option>
                                            <option value="100" {{ $quote->status == '100' ? 'selected' : '' }}>Done
                                                PO <small class="text-muted">100%</small>
                                            </option>
                                            <option value="0" {{ $quote->status == '0' ? 'selected' : '' }}>Loss
                                                <small class="text-muted">0%</small>
                                            </option>
                                        </select>
                                        <label for="statusChange">Status</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectWeek" aria-label="Default select example"
                                            name="week" id="week" disabled>
                                            <option disabled>----- Choose Week -----</option>
                                            <option value="1">Week 1</option>
                                            <option value="2">Week 2</option>
                                            <option value="3">Week 3</option>
                                            <option value="4">Week 4</option>
                                            <option value="5">Week 5</option>
                                        </select>
                                        <label for="selectWeek">Week</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text" id="type" name="type"
                                            value="{{ $quote->type == 'Sparepart' && $quote->quote_for == 'Sparepart' ? 'Non Project' : 'Project' }}"
                                            disabled>
                                        <label for="po_date">Date PO</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectEkspedisi"
                                            aria-label="Default select example" name="ekspidisi" disabled>
                                            <option disabled>----- Choose Ekspidisi -----</option>
                                            <option value="1">JNT / JNE / Cargo</option>
                                            <option value="2">Send By Technian</option>
                                            <option value="3">Taken Directly</option>
                                            <option value="4">Others</option>
                                        </select>
                                        <label for="selectEkspidisi">Ekspidisi</label>
                                    </div>
                                </div>
                                <div class="col-12 charged" style="display:none;">
                                    <div class="card shadow-none bg-transparent border border-label-secondary mb-3">
                                        <div class="card-body">
                                            <p class="fw-semibold text-start d-block mb-0">Charged to</p>
                                            <div class="form-check form-check-inline mt-3">
                                                <input class="form-check-input" type="radio" name="charged"
                                                    id="inlineRadio1" value="1">
                                                <label class="form-check-label" for="inlineRadio1">Company</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="charged"
                                                    id="inlineRadio2" value="2">
                                                <label class="form-check-label" for="inlineRadio2">Customer</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12  mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control-lg" id="noPending"
                                            placeholder="xxx/xx/xx/xxxx" aria-describedby="floatingInputFilledHelp"
                                            name="no_pending" value="{{ $noPending }}" disabled>
                                        <label for="NoPending">No Pending</label>
                                    </div>
                                </div>
                                <div class="col-12  mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control-lg" id="title"
                                            placeholder="Put your title here"
                                            aria-describedby="floatingInputFilledHelp" name="title" dsiabled>
                                        <label for="title">Title</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="note" class="form-control" name="note"
                                            placeholder="Put Note Here....." value="-">
                                        <label for="note">Note</label>
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
        $(document).ready(function() {
            function toggleWeekField() {
                if ($('#statusChange').val() === '100') {
                    $('#selectWeek').prop('disabled', false);
                    $('#selectEkspedisi').prop('disabled', false);
                    $('#noPending').prop('disabled', false);
                    $('#title').prop('disabled', false);
                } else {
                    $('#selectWeek').prop('disabled', true).val('');
                    $('#selectEkspedisi').prop('disabled', true).val('');
                    $('#noPending').prop('disabled', true);
                    $('#title').prop('disabled', true);
                }
            }

            toggleWeekField();

            $('#statusChange').on('change', toggleWeekField);
        });

        $('#selectEkspedisi').change(function() {
            $('.charged').toggle($(this).val() == '1');
        });
    </script>
@endpush
