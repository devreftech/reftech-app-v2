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
                                                        $bdgStock = $item->equivalent->product->stock ?? 0;
                                                        $bksStock = $item->equivalent->product->warehouse_stock ?? 0;
                                                        $totalStock = $bdgStock + $bksStock;
                                                        
                                                        // Default selection logic:
                                                        $selectedStatus = $item->status;
                                                        if ($item->status == '1' || is_null($item->status)) {
                                                            $selectedStatus = $totalStock >= $item->qty ? '2' : '3';
                                                        }

                                                        // Auto allocation logic:
                                                        $defaultBdg = $item->bdg;
                                                        $defaultBks = $item->bks;
                                                        if (($item->status == '1' || is_null($item->status)) && ($item->bdg == 0 && $item->bks == 0)) {
                                                            if ($bdgStock >= $item->qty) {
                                                                $defaultBdg = $item->qty;
                                                                $defaultBks = 0;
                                                            } elseif ($totalStock >= $item->qty) {
                                                                $defaultBdg = $bdgStock;
                                                                $defaultBks = $item->qty - $bdgStock;
                                                            } else {
                                                                $defaultBdg = $bdgStock;
                                                                $defaultBks = $bksStock;
                                                            }
                                                        }
                                                        
                                                        $title = 'BDG (' . $bdgStock . ') | BKS (' . $bksStock . ')';
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
                                                                    {{ $selectedStatus == '1' ? 'selected' : '' }}>
                                                                    On Check
                                                                </option>
                                                                <option value="2"
                                                                    {{ $selectedStatus == '2' ? 'selected' : '' }}>
                                                                    Ready Stock
                                                                </option>
                                                                <option value="3"
                                                                    {{ $selectedStatus == '3' ? 'selected' : '' }}>
                                                                    Kurang
                                                                </option>
                                                                <option value="4"
                                                                    {{ $selectedStatus == '4' ? 'selected' : '' }}>
                                                                    Pre-Order
                                                                </option>
                                                                <option value="5"
                                                                    {{ $selectedStatus == '5' ? 'selected' : '' }}>
                                                                    Delivery Process
                                                                </option>
                                                                <option value="6"
                                                                    {{ $selectedStatus == '6' ? 'selected' : '' }}>
                                                                    Done
                                                                </option>
                                                                <option value="7"
                                                                    {{ $selectedStatus == '7' ? 'selected' : '' }}>
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
                                                                value="{{ $defaultBdg }}"></input>
                                                            <label for="exampleFormControlinput1">Bandung</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" class="form-control"
                                                                id="exampleFormControlinput1" name="bks[]"
                                                                placeholder="Stock..."
                                                                value="{{ $defaultBks }}"></input>
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
