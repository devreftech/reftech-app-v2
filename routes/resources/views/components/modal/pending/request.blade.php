<form action="{{ route('purchase-request.store', $pending->id) }}" method="post" enctype="multipart/form-data">
    {{-- @method('PATCH') --}}
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="purchaseReq" tabindex="-1" style="display: none;"
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
                                                <th style="width: 35%">Item</th>
                                                {{-- <th>Desc</th> --}}
                                                <th>Qty</th>
                                                <th style="width: 35%">Note</th>
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
                                                        $title = 'BDG (' . $item->equivalent->product->stock . ') | BKS (' . $item->equivalent->product->warehouse_stock . ')';
                                                    @endphp
                                                    <td class="text-start">
                                                        <pre class="mb-0"
                                                            style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"
                                                            data-bs-toggle="tooltip" 
                                                            data-bs-placement="top" 
                                                            title="{{ $title }}">{{$item->equivalent->product->go == "Genuine" ? 'G' : 'R'}} - {{ $item->equivalent->brand }} {{ $item->equivalent->pn }}</pre>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" class="form-control"
                                                                id="exampleFormControlinput1" name="qty[]"
                                                                placeholder="Stock..."
                                                                value="{{ @$item->bdg }}" min="0"></input>
                                                            <label for="exampleFormControlinput1">Qty</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <textarea class="form-control" id="exampleFormControlTextarea1" name="note[]" placeholder="Text Note here..."></textarea>
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
