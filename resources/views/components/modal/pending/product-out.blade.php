<form action="{{ route('pending-po.connect_out', $pending->id) }}" method="post" enctype="multipart/form-data">
    @method('PATCH')
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="inputProductOut" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">

                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">
                            {{ $pending->quote->invoice[0]->no_invoice ?? $pending->quote->pic->client->company }}</h4>
                        <div class="row">
                            <div class="col">

                                <div class="form-floating form-floating-outline mb-2">
                                    <select id="product-dropdown" class="select2 form-select invoice-item-product"
                                        data-allow-clear="true" name="product" data-id="1">
                                        <option selected disabled>---------Pilih No Invoice Product Out--------</option>
                                        @forelse ($allproductOut as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->invoice }}
                                            </option>
                                        @empty
                                            <option value="" disabled>No Product Out</option>
                                        @endforelse
                                    </select>
                                    <label for="exampleFormControlSelect1">Choose Product Out</label>
                                </div>
                            </div>
                        </div>
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
