<form action="{{ route('add_mention.quotation', $quote->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal modal-xl fade animate__animated" id="addMention" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Add Mention of {{ $quote->no_quote }}
                        </h4>
                        <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select class="select2 form-select" id="selectAddress"
                                            aria-label="Default select example" name="admin" data-allow-clear="true">
                                            <option disabled selected>----- Choose Admin -------</option>
                                            @foreach ($admin as $admins)
                                                <option value="{{$admins->id}}">
                                                    {{ $admins->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="selectAddress">Choose Admin</label>
                                    </div>
                                </div>
                                <div class="col-12  mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="textarea" name="comment" id="comment">
                                        <label for="html5-date-input">Comment</label>
                                    </div>
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
