<form action="{{ route('product-in.update', $product->id) }}" method="post" enctype="multipart/form-data">
    {{-- {{ csrf_token() }} --}}
    @if (@$product)
        @method('PATCH')
    @endif
    @csrf
    <div class="modal animate__animated animate__fadeIn" id="{{ 'editPrice-' . $product->id }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">
                        {{ 'Edit Price ' . $product->invoice }}
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2 mt-1">
                    <div class="row mt-2 gy-4">
                        @foreach ($detail as $item)
                            <div class="col-6 col-lg-4 mb-3">
                                <div class="text-nowrap align-top">
                                    <p class="mb-0 fw-semibold" style="font-size: 16px">
                                        {{ $item->detailProduct->replacement }}
                                    </p>
                                    <pre class="mb-0"
                                        style="font-size: 12px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">{{ $products->detailProduct->product->commodity }}</pre>
                                </div>
                            </div>
                            <div class="col-6 col-lg-8 mb-3">
                                <div class="input-group" data-price="{{$item->id}}">
                                    <span class="input-group-text">Rp. </span>
                                    <input type="text" class="form-control invoice-item-modal-label" id="modal-label"
                                        data-id="{{$item->id}}" min="0" placeholder="Put modal Here" data-type="currency"
                                        pattern="^[0-9]\d{0,2}(\.\d{3})*$" @focus="focused = true"
                                        @blur="focused = false" value="{{ old('modal['.$item->id.']', @$item->modal ? number_format($item->modal, 0, '', '.') : '') }}">
                                    <input class="form-control invoice-item-modal" type="number" name="modal[{{$item->id}}]"
                                        id="modal-{{$item->id}}" value="{{ old('modal['.$item->id.']', @$item->modal ? $item->modal : '') }}" hidden>
                                </div>
                            </div>
                        @endforeach
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
