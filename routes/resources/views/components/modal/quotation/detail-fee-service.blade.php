<div class="modal-onboarding modal fade animate__animated" id="detailFee" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <h4 class="onboarding-title text-body"> Insert Fee of {{ $quote->no_quote }}</h4>
                    <div class="onboarding-info mb-3">
                        {{ $quote->pic->client->company }}
                    </div>
                    <form>
                        <div class="row mb-4">
                            @foreach ($subQuote as $subtitle)
                                @foreach ($subtitle->detail as $item)
                                    <div class="col-6">
                                        <p class="text-start"> {{ $item->product }}'s fee</p>
                                    </div>
                                    <div class="col-6">
                                        : Rp {{ number_format($item->fee, 0, ',', '.') }}
                                    </div>
                                @endforeach
                            @endforeach
                            <hr>
                            <div class="col-6">
                                <h5 class="text-start"> Total Fee</h5>
                            </div>
                            <div class="col-6">
                                <h5>: Rp {{ number_format($quote->fee, 0, ',', '.') }}</h5>
                            </div>
                            <div class="col-6">
                                <h5 class="text-start"> Nett Profit</h5>
                            </div>
                            <div class="col-6">
                                <h5>: Rp {{ number_format($quote->nett, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
