<form action="{{ route('invoice.date', $invoice->id) }}" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf
    <div class="modal animate__animated animate__fadeIn" id="changeDate" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title text-center" id="exampleModalLabel5">Change Date {{$invoice->no_invoice}}
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="row g-2 mb-3">
                        <div class="col-12">
                            <div class="mb-3">
                                <input class="form-control" type="date" id="date" name="date"
                                    value="{{ old('estimated_date', @$invoice->date ?? now()->format('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select" id="selectAddress"
                                    aria-label="Default select example" name="destination"
                                    data-allow-clear="true">
                                    <option value="1"
                                        {{ old('address', $invoice->invoiceTo) == '1' ? 'selected' : '' }}>
                                        {{ $quote->pic->client->address }}</option>
                                    <option value="2"
                                        {{ old('address', $invoice->invoiceTo) == '2' ? 'selected' : '' }}>
                                        {{ $quote->pic->client->subAddress }}</option>
                                </select>
                                <label for="selectAddress">Choose Address</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
