<form action="{{ route('accept.contract', $contract->id) }}" method="POST">
    @csrf
    <div class="modal-onboarding modal fade animate__animated" id="acceptContract{{ $contract->id }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    @php
                        $isOrderUnit = $contract->type == 'Order';
                        $docNoun     = $isOrderUnit ? 'Confirm Order' : 'Selling Contract';
                        $suffixDoc   = $isOrderUnit ? 'CO/KII' : 'SELLCTX/RJO';
                        $isPpnUnit   = (bool) ($contract->unitQuotation?->tax);
                        $lastNoUnit  = $isOrderUnit
                            ? ($isPpnUnit ? ($unitNumbers['lastCP'] ?? null) : ($unitNumbers['lastCNP'] ?? null))
                            : ($isPpnUnit ? ($unitNumbers['lastSP'] ?? null) : ($unitNumbers['lastSNP'] ?? null));
                    @endphp
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Accept {{ $docNoun }} of {{ $contract->no_contract }}</h4>
                        <div class="onboarding-info mb-3">
                            {{ $contract->unitQuotation?->client?->company ?? '-' }}
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="no_contract_{{ $contract->id }}"
                                        name="no_contract" placeholder="No Contract"
                                        value="{{ $result }}/{{ $isPpnUnit ? 'P' : 'NP' }}/{{ $suffixDoc }}/{{ $thisYear }}">
                                    <label for="no_contract_{{ $contract->id }}">No Contract</label>
                                </div>
                                <p class="text-danger text-start mt-2 mb-0">Last No : {{ $lastNoUnit ?? '-' }}</p>
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
