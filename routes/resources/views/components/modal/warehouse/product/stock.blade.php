<form action="{{ route('stock.update', $product->id) }}" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @csrf

    @if (@$product)
        @method('patch')
    @endif
    <div class="modal animate__animated animate__fadeIn" id="{{ 'updateStock-' . $product->id }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">
                        {{ 'Update Stock' . @$product->commodity }}
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
                        <div class="col-4 mb-2">
                            <p>First Stock</p>
                        </div>
                        <div class="col-8 mb-2">
                            <div class="row">
                                <div class="col-8">
                                    <div class="form-floating form-floating-outline">
                                        <input type="number" id="first_stock" class="form-control" name="first_stock"
                                            value="{{ old('first_stock', $product->first_stock) }}">
                                        <label for="first_stock">First Stock</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="date" id="Date" name="date"
                                            value="{{ old('date', $product->date ?? now()->format('Y-m-d')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-4 mb-2">
                            <p>Recent Stock</p>
                        </div>
                        <div class="col-8 mb-2">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="number" id="stock" class="form-control recent-office-stock-label"
                                    name="stock" value="{{ old('stock', $product->stock) }}" disabled>
                                <input type="number" id="stock" class="form-control recent-office-stock"
                                    name="office_recent_stock" value="{{ old('stock', $product->stock) }}" hidden>
                                <label for="Recent Stock">Recent Office Stock</label>
                            </div>
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="number" id="stock" class="form-control recent-warehouse-stock-label"
                                    name="stock" value="{{ old('stock', $product->warehouse_stock) }}" disabled>
                                <input type="number" id="stock" class="form-control recent-warehouse-stock"
                                    name="warehouse_recent_stock" value="{{ old('stock', $product->warehouse_stock) }}"
                                    hidden>
                                <label for="Recent Stock">Recent Warehouse Stock</label>
                            </div>
                            <div class="form-floating form-floating-outline">
                                <input type="number" id="pending_stock" class="form-control" name="pending_recent_stock"
                                    value="{{ old('pending_stock', $product->pending_stock) }}">
                                <label for="Recent Stock">Recent Pending Stock</label>
                            </div>
                        </div>
                    </div>
                    @php
                        $i = 0;
                    @endphp
                    @forelse ($details as $detail)
                        <div class="row g-2 mb-3">
                            <div class="col-1"></div>
                            <div class="col-3 mb-2 d-flex align-item-center">
                                <p style="margin: auto">{{ $detail->replacement }}</p>
                            </div>
                            <div class="col-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" id="stock" class="form-control office-stock"
                                        name="office_stock[]" data-id="{{ $i }}"
                                        value="{{ old('stock', $detail->stock) }}">
                                    <label for="Recent Stock"> Office Stock</label>
                                </div>
                                <div class="form-floating form-floating-outline">
                                    <input type="number" id="stock" class="form-control warehouse-stock"
                                        name="warehouse_stock[]" data-id="{{ $i }}"
                                        value="{{ old('stock', $detail->warehouse_stock) }}">
                                    <label for="warehouse"> Warehouse Stock</label>
                                </div>
                            </div>
                            <div class="col-2"></div>
                        </div>
                        <hr>
                        @php
                            $i++;
                        @endphp
                    @empty
                        <p> Anda Belum memiliki Replacement. </p>
                    @endforelse
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

@push('script')
    <script>
        $(() => {
            $('.office-stock').on('keyup change', function() {
                var total = 0;
                $('.office-stock').each(function() {
                    total += parseInt($(this).val());
                });
                $('.recent-office-stock-label').val(total);
                $('.recent-office-stock').val(total);
            });
            $('.warehouse-stock').on('keyup change', function() {
                var total = 0;
                $('.warehouse-stock').each(function() {
                    total += parseInt($(this).val());
                });
                $('.recent-warehouse-stock-label').val(total);
                $('.recent-warehouse-stock').val(total);
            });
        });
    </script>
@endpush
