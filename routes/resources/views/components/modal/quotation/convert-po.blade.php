<form action="{{ route('convert-po.quotation', $quote->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="convertPo" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Convert To PO {{ $quote->no_quote }}</h4>
                        <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div>
                        <form>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="date" id="po_date" name="po_date"
                                            value="{{ old('po_date', @$quote->po_date ?? now()->format('Y-m-d')) }}">
                                        <label for="po_date">Date PO</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectWeek" aria-label="Default select example"
                                            name="week">
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
                                {{-- <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text" id="type" name="type"
                                            value="{{ $quote->type == 'Sparepart' && $quote->quote_for == 'Sparepart' ? 'Non Project' : 'Project' }}"
                                            disabled>
                                        <label for="po_date">Date PO</label>
                                    </div>
                                </div> --}}
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectType" aria-label="Default select example"
                                            name="type">
                                            <option disabled>----- Choose Type -----</option>
                                            <option value="Non Project"
                                                {{ $quote->type == 'Sparepart' && $quote->quote_for == 'Sparepart' ? 'selected' : '' }}>
                                                Non Project</option>
                                            <option value="Project"
                                                {{ $quote->type != 'Sparepart' && $quote->quote_for != 'Sparepart' ? 'selected' : '' }}>
                                                Project</option>
                                        </select>
                                        <label for="selectType">Type</label>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select class="form-select" id="selectEkspidisi"
                                            aria-label="Default select example" name="ekspidisi">
                                            <option>----- Choose Ekspidisi -----</option>
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
                                        <input type="text" class="form-control form-control-lg" id="NoPending"
                                            placeholder="xxx/xx/xx/xxxx" aria-describedby="floatingInputFilledHelp"
                                            name="no_pending" value="{{ $noPending ?? '' }}">
                                        <label for="NoPending">No Pending</label>
                                    </div>
                                </div>
                                <div class="col-12  mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control form-control-lg" id="title"
                                            placeholder="Put your title here"
                                            aria-describedby="floatingInputFilledHelp" name="title">
                                        <label for="title">Title</label>
                                    </div>
                                </div>
                                <div class="col-12  mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <textarea name="note" id="" cols="30" class="form-control h-px-100" rows="10">-</textarea>
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
            $('#selectEkspidisi').change(function() {
                $('.charged').toggle($(this).val() == '1');
            });
        });
    </script>
@endpush
