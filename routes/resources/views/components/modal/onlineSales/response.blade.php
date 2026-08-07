<form action="{{ @$response ? route('update.salon', @$response->id) : route('store.salon') }}" method="post"
    enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="response" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">CS Support</h4>
                        <form>
                            <div class="row mb-3">
                                <div class="col-md-{{ $salesID == 16 ? '8' : '12' }} col-12">
                                    <div class="row align-items-center">
                                        <div class="col-4 mb-3">
                                            <h5 class="text-start m-0">
                                                {{ $salesID == 16 ? 'Airend Center' : 'Part Compressor' }}</h5>
                                        </div>
                                        <div class="col-8 mb-3">
                                            <div class="input-group input-group-merge">
                                                <div class="form-floating form-floating-outline">
                                                    <input type="text" class="form-control" id="airendResponse"
                                                        name="airend" placeholder="Target" aria-label="Airend"
                                                        aria-describedby="basic-addon11"
                                                        oninput="validateFloatInputResponse(this)"
                                                        value="{{ @$response->airend ? str_replace('.', ',', $response->airend) : '0' }}">
                                                    <label for="basic-addon11">Airend</label>
                                                </div>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                        @if ($salesID == 16)
                                            <div class="col-4 mb-3">
                                                <h5 class="text-start m-0">Kojisha</h5>
                                            </div>
                                            <div class="col-8 mb-3">
                                                <div class="input-group input-group-merge">
                                                    <div class="form-floating form-floating-outline">
                                                        <input type="text" class="form-control" id="kojishaResponse"
                                                            name="kojisha" placeholder="Target" aria-label="Kojisha"
                                                            aria-describedby="basic-addon11"
                                                            oninput="validateFloatInputResponse(this)"
                                                            value="{{ @$response->kojisha ? str_replace('.', ',', $response->kojisha) : '0' }}">
                                                        <label for="basic-addon11">Kojisha</label>
                                                    </div>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if ($salesID == 16)
                                    <div class="col-md-4 col-12">
                                        <div class="card text-center bg-label-secondary h-100">
                                            <input type="text" name="average" id="averageResponse"
                                                value="{{ @$response->average ? str_replace('.', ',', $response->average) : '0' }}"
                                                hidden>
                                            <div class="card-body">
                                                <h5>Average</h5>
                                                <p id="averageResponseText">
                                                    {{ @$response->average ? str_replace('.', ',', $response->average) : '0' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <input type="text" name="type" id="type" value="Response" hidden>
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
