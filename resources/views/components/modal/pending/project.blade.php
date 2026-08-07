<form action="{{ route('pending-po.projectEdit', $pending->id) }}" method="post" enctype="multipart/form-data">
    @method('PATCH')
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="replacementEdit" tabindex="-1" style="display: none;"
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
                                <div class="table text-nowrap h-100" style="height: fit-content">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th style="width: 25%">Item</th>
                                                {{-- <th>Desc</th> --}}
                                                <th>Qty</th>
                                                <th style="width: 15%">Status</th>
                                                <th style="width: 10%">BDG</th>
                                                <th style="width: 10%">BKS</th>
                                                <th style="width: 20%">Note</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            @php
                                                $abjad = 64;
                                            @endphp
                                            @foreach ($subQuote as $subJudul)
                                                @php
                                                    $no = 1;
                                                    $abjad++;
                                                @endphp
                                                <tr style="font-size: 17px border-bottom:none !important;"
                                                    class="border-top">
                                                    <td class="align-top"
                                                        style="border-bottom:none !important; background-color: #f0f0f0;">
                                                        <p class="fw-bold mb-0">{{ chr($abjad) }}</p>
                                                    </td>
                                                    <td class="text-nowrap align-top" colspan="6"
                                                        style="border-bottom:none !important; background-color: #f0f0f0;">
                                                        <p class="text-start fw-bold mb-0">{{ $subJudul->subtitle }}</p>
                                                    </td>
                                                </tr>
                                                @foreach ($subJudul->detail as $product)
                                                    <tr>
                                                        <td>{{ $no }}</td>
                                                        <td>
                                                            <div class="form-floating form-floating-outline mb-2">
                                                                <select class="select2 form-select"
                                                                    data-allow-clear="true" name="equivalent[]"
                                                                    data-id="1">
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
                                                                <label for="Equivalent"
                                                                    class="mb-2">Equivalent</label>
                                                            </div>
                                                        </td>

                                                        <td>{{ $product->qty }} {{ $product->info_qty }}</td>
                                                        <td>
                                                            <div class="form-floating form-floating-outline">
                                                                <select class="form-select" tabindex="0"
                                                                    id="statusChange" name="status[]">
                                                                    <option value="1"
                                                                        {{ @$product->pending[0]->status == '1' ? 'selected' : '' }}>
                                                                        On Check
                                                                    </option>
                                                                    <option value="2"
                                                                        {{ @$product->pending[0]->status == '2' ? 'selected' : '' }}>
                                                                        Ready Stock
                                                                    </option>
                                                                    <option value="3"
                                                                        {{ @$product->pending[0]->status == '3' ? 'selected' : '' }}>
                                                                        Kurang
                                                                    </option>
                                                                    <option value="4"
                                                                        {{ @$product->pending[0]->status == '4' ? 'selected' : '' }}>
                                                                        Pre-Order
                                                                    </option>
                                                                    <option value="5"
                                                                        {{ @$product->pending[0]->status == '5' ? 'selected' : '' }}>
                                                                        Delivery Process
                                                                    </option>
                                                                    <option value="6"
                                                                        {{ @$product->pending[0]->status == '6' ? 'selected' : '' }}>
                                                                        Done
                                                                    </option>
                                                                    <option value="7"
                                                                        {{ @$product->pending[0]->status == '7' ? 'selected' : '' }}>
                                                                        Cancel
                                                                    </option>
                                                                </select>
                                                                <label for="statusChange">Status</label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="number" class="form-control"
                                                                    id="exampleFormControlinput1" name="bdg[]"
                                                                    placeholder="Stock..."
                                                                    value="{{ @$product->pending[0]->bdg }}"></input>
                                                                <label for="exampleFormControlinput1">Bandung</label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-floating form-floating-outline">
                                                                <input type="number" class="form-control"
                                                                    id="exampleFormControlinput1" name="bks[]"
                                                                    placeholder="Stock..."
                                                                    value="{{ @$product->pending[0]->bks }}"></input>
                                                                <label for="exampleFormControlTextarea1">Bekasi</label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-floating form-floating-outline">
                                                                <textarea class="form-control" id="exampleFormControlTextarea1" name="note[]" placeholder="Comments here...">{{ @$product->pending[0]->note }}</textarea>
                                                                <label for="exampleFormControlTextarea1">Note</label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @php
                                                        $no++;
                                                    @endphp
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer mt-4 border-0">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
