<form action="{{ route('selling.contract', $quote->id)}}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="sellingContract" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Create Selling Contract of {{ $quote->no_quote }}</h4>
                        <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div>
                        <form>
                            <div class="row">
                                <div class="col-12  mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" id="no_contract" name="no_contract"
                                            placeholder="John Doe" value="{{$quote->tax == '11' ? $formattedNumberSP : $formattedNumberSNP}}/{{$quote->tax == '11' ? 'P' : 'NP'}}/SELLCTX/RJO/{{$thisYear}}">
                                        <label for="no_contract">No Selling Contract</label>
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
