<form action="{{ route('product-in.return', $product->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="productReturn" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">

                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Return Barang </h4>
                        <form>
                            <div class="card">
                                <div class="table-responsive text-nowrap h-100">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th style="width: 40%">Item</th>
                                                {{-- <th>Desc</th> --}}
                                                <th>Qty</th>
                                                <th style="width: 40%">Note</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0">
                                            @php
                                                $no = 1;
                                            @endphp
                                            @foreach ($detProduct as $products)
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td class="text-nowrap align-top text-start">
                                                        <p class="mb-0 fw-semibold" style="font-size: 15px">
                                                            {{ $products->detailProduct->replacement }}
                                                        </p>
                                                        <pre class="mb-0"
                                                            style="font-size: 12px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $products->detailProduct->product->description }}</pre>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <input type="number" class="form-control"
                                                                id="exampleFormControlinput1" name="qty[]"
                                                                placeholder="Stock..." min="0"
                                                                value="0"></input>
                                                            <label for="exampleFormControlinput1">Qty</label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-floating form-floating-outline">
                                                            <textarea class="form-control" id="exampleFormControlTextarea1" name="note[]" placeholder="Note here..."></textarea>
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
