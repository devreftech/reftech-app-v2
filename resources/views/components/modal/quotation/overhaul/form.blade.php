<form action="{{ route('quotation.choose-machine') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal modal-l fade animate__animated" id="chooseMachine" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Choose Machine</h4>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <div class="form-floating form-floating-outline">
                                            <select id="select2Basic" class="select2 form-select form-select-lg"
                                                data-allow-clear="true" name="machine">
                                                @foreach ($machine as $machines)
                                                    <option value="{{ $machines->id }}">
                                                        {{ $machines->brand }} {{ $machines->sku }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <label for="select2Basic">Choose Machine</label>
                                    </div>
                                    {{-- <div class="form-floating form-floating-outline">
                                        <div class="form-floating form-floating-outline mb-4">
                                            <select class="form-select" id="exampleFormControlSelect1"
                                                aria-label="Default select example" name="id_mention">
                                                @foreach ($user as $users)
                                                    @if ($users->id != Auth::id())
                                                        <option value="{{ $users->id }}">{{ $users->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <label for="exampleFormControlSelect1">User To</label>
                                        </div>
                                    </div> --}}
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
