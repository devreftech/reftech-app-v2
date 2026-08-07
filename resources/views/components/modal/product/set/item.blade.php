<form action="{{ route('product-set.store_item', $productSet->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="createItemReplacement" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">

                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">
                            New Item Product Set</h4>
                        <div class="form-floating form-floating-outline mb-2">
                            <select class="select2 form-select w-100" data-allow-clear="true" name="replacement"
                                data-id="1">
                                <option> ---- Choose Equivalent Here ---- </option>
                                @foreach ($replacement as $item)
                                    <option value="{{ $item->id }}">
                                        {{ $item->replacement }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="Equivalent" class="mb-2">Equivalent</label>
                        </div>
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
