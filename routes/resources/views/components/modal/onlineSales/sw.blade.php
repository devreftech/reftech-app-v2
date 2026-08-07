<form action="{{ @$sw ? route('update.salon', @$sw->id) : route('store.salon') }}" method="post"
    enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="SWin" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Status WA</h4>
                        <form>
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <h5 class="mb-4">{{$salesID == 16 ? '6' : '3'}} SW / Days</h5>
                                </div>
                                <div class="col-4 mb-3">
                                    <h5 class="text-start m-0">{{$salesID == 16 ? 'Airend Center' : 'Part Compressor'}}</h5>
                                </div>
                                <div class="col-8 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="number" id="airend" name="airend"
                                            max="3" min="0" oninput="validateMaxInput(this)"
                                            value="{{ @$sw->airend }}">
                                        <label for="airend">Airend</label>
                                    </div>
                                </div>
                                @if ($salesID == 16)
                                    <div class="col-4 mb-3">
                                        <h5 class="text-start m-0">Kojisha</h5>
                                    </div>
                                    <div class="col-8 mb-3">
                                        <div class="form-floating form-floating-outline">
                                            <input class="form-control" type="number" id="kojisha" name="kojisha"
                                                max="3" min="0" oninput="validateMaxInput(this)"
                                                value="{{ @$sw->kojisha }}">
                                            <label for="kojisha">Kojisha</label>
                                        </div>
                                    </div>
                                @endif
                                <input type="text" name="type" id="type" value="SW" hidden>
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
