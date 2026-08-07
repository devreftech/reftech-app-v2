<form action="{{ route('purchase-request.store-project', $pending->id) }}" method="post" enctype="multipart/form-data">
    {{-- @method('PATCH') --}}
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="purchaseReqPrj" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">

                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">
                            {{ $pending->quote->invoice[0]?->no_invoice ?? $pending->quote->pic->client->company }}</h4>
                        <form>
                            <div class="card">
                                <div class="table-responsive text-nowrap h-100">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 35%">Item</th>
                                                {{-- <th>Desc</th> --}}
                                                <th>Qty</th>
                                                <th style="width: 35%">Note</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            <tr>
                                                <td class="text-start">
                                                    <div class="form-floating form-floating-outline mb-2">
                                                        <select class="select2 form-select select-project" data-allow-clear="true"
                                                            name="id_equivalent" data-id="1">
                                                            <option> ---- Choose Equivalent Here ---- </option>
                                                            @foreach ($serial as $replacement)
                                                                <option value="{{ $replacement->id }}"
                                                                    {{ $product->pending[0]->id_equivalent == $replacement->id ? 'selected' : '' }}>
                                                                    {{ $replacement->brand }}
                                                                    {{ $replacement->pn }} -
                                                                    {{ $replacement->product?->go == 'Replacement' ? 'R' : 'G' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <label for="Equivalent" class="mb-2">Equivalent</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="number" class="form-control"
                                                            id="exampleFormControlinput1" name="qty"
                                                            placeholder="Stock..." value="{{ @$item->bdg }}"
                                                            min="0"></input>
                                                        <label for="exampleFormControlinput1">Qty</label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-floating form-floating-outline">
                                                        <textarea class="form-control" id="exampleFormControlTextarea1" name="note" placeholder="Text Note here..."></textarea>
                                                        <label for="exampleFormControlTextarea1">Note</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
