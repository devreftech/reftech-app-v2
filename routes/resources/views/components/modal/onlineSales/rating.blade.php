<form action="{{ @$rating ? route('update.salon', @$rating->id) : route('store.salon') }}" method="post"
    enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="rating" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Store Rating</h4>
                        <form>
                            <div class="row align-items-center">
                                <h5 class="text-center mb-3">Target 5.0</h5>
                                <div class="col-4 mb-3">
                                    <h5 class="text-start m-0">
                                        {{ $salesID == 16 ? 'Airend Center' : 'Part Compressor' }}</h5>
                                </div>
                                <div class="col-8 mb-3">
                                    <input class="form-control form-control-lg" type="text" placeholder="Target"
                                        id="airendRating" name="airend" oninput="validateFloatInputRating(this)"
                                        maxlength="4"
                                        value="{{ @$rating->airend ? str_replace('.', ',', $rating->airend) : '0' }}">
                                </div>
                                @if ($salesID == 16)
                                    <div class="col-4 mb-3">
                                        <h5 class="text-start m-0">Kojisha</h5>
                                    </div>
                                    <div class="col-8 mb-3">
                                        <input class="form-control form-control-lg" type="text" placeholder="Target"
                                            id="kojishaRating" name="kojisha" oninput="validateFloatInputRating(this)"
                                            maxlength="4"
                                            value="{{ @$rating->kojisha ? str_replace('.', ',', $rating->kojisha) : '0' }}">
                                    </div>
                                    <div class="col-12 mb-3">
                                        <div class="card text-center bg-label-secondary">
                                            <div class="card-body">
                                                <input type="text" name="average" id="averageRating"
                                                    value="{{ @$rating->average ? str_replace('.', ',', $rating->average) : '0' }}"
                                                    hidden>
                                                <h5>Average</h5>
                                                <p id="averageRatingText">
                                                    {{ @$rating->average ? str_replace('.', ',', $rating->average) : '0' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <input type="text" name="type" id="type" value="Rating" hidden>
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
