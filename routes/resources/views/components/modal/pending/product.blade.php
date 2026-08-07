<form action="{{ route('pending-po.productEdit', $pending->id) }}" method="post" enctype="multipart/form-data">
    @method('PATCH')
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="productEdit" tabindex="-1" style="display: none;"
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
                                                $no = 1;
                                            @endphp
                                            @foreach ($detQuotation as $item)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    {{-- <td>
                                                        @if ($item->id_equivalent == '0')
                                                            -
                                                        @else
                                                            {{ $item->equivalent->brand }} {{ $item->equivalent->pn }}
                                                        @endif
                                                    </td> --}}
                                                    @php
                                                        if (@$item) {
                                                            $stock = $item->equivalent->product->stock;
                                                        } else {
                                                            $stock = 0;
                                                        }
                                                        $title =
                                                            'BDG (' .
                                                            $stock .
                                                            ') | BKS (' .
                                                            $item->equivalent->product->warehouse_stock .
                                                            ')';
                                                    @endphp
                                                    <td class="text-start">
                                                        <pre class="mb-0"
                                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $title }}">{{ $item->equivalent->product->go == 'Genuine' ? 'G' : 'R' }} - {{ $item->equivalent->brand }} {{ $item->equivalent->pn }}</pre>
                                                    </td>

                                                    <td>{{ $item->qty }} {{ $item->info_qty }}</td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <select class="form-select" tabindex="0" id="statusChange"
                                                                name="status[]">
                                                                <option value="1"
                                                                    {{ $item->status == '1' ? 'selected' : '' }}>
                                                                    On Check
                                                                </option>
                                                                <option value="2"
                                                                    {{ $item->status == '2' ? 'selected' : '' }}>
                                                                    Ready Stock
                                                                </option>
                                                                <option value="3"
                                                                    {{ $item->status == '3' ? 'selected' : '' }}>
                                                                    Kurang
                                                                </option>
                                                                <option value="4"
                                                                    {{ $item->status == '4' ? 'selected' : '' }}>
                                                                    Pre-Order
                                                                </option>
                                                                <option value="5"
                                                                    {{ $item->status == '5' ? 'selected' : '' }}>
                                                                    Delivery Process
                                                                </option>
                                                                <option value="6"
                                                                    {{ $item->status == '6' ? 'selected' : '' }}>
                                                                    Done
                                                                </option>
                                                                <option value="7"
                                                                    {{ $item->status == '7' ? 'selected' : '' }}>
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
                                                                value="{{ @$item->bdg }}"></input>
                                                            <label for="exampleFormControlinput1">Bandung</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" class="form-control"
                                                                id="exampleFormControlinput1" name="bks[]"
                                                                placeholder="Stock..."
                                                                value="{{ @$item->bks }}"></input>
                                                            <label for="exampleFormControlTextarea1">Bekasi</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <textarea class="form-control" id="exampleFormControlTextarea1" name="note[]" placeholder="Comments here...">{{ @$item->note }}</textarea>
                                                            <label for="exampleFormControlTextarea1">Note</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @php
                                                    $no++;
                                                @endphp
                                            @endforeach
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
