<form action="{{ route('add-comment.quotation', $quote->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="addComment" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Add Comment {{ $quote->no_quote }}</h4>
                        <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <textarea class="form-control" name="comment" id="comment" cols="30" rows="10"></textarea>
                                        <label for="comment">Comment</label>
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
