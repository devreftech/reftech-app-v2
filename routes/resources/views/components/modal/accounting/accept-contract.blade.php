<!-- Pastikan ini berada di dalam bagian modal -->
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
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Accept Contract of {{ $contract->no_contract }}</h4>
                        <div class="onboarding-info mb-3">
                            {{ $contract->quotation->pic->client->company }}
                        </div>
                        <div class="row">
                            <div class="col-12  mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="no_contract" name="no_contract"
                                        placeholder="John Doe"
                                        value="{{ $result }}/{{ $contract->quotation->tax == '11' ? 'P' : 'NP' }}/{{ $contract->type == 'Selling' ? 'SELLCTX/RJO' : 'CO/KII' }}/{{ $thisYear }}">
                                    <label for="no_contract">No Contract</label>
                                </div>
                            </div>
                            <p class="text-danger text-start">
                                Last No :
                                @if ($contract->type == 'Selling' && $contract->quotation->tax == '11')
                                    {{ @$numberLastSP->no_contract }}
                                @elseif ($contract->type == 'Selling' && $contract->quotation->tax == '0')
                                    {{ @$numberLastSNP->no_contract }}
                                @elseif ($contract->type == 'Order' && $contract->quotation->tax == '11')
                                    {{ @$numberLastCP->no_contract }}
                                @elseif ($contract->type == 'Order' && $contract->quotation->tax == '0')
                                    {{ @$numberLastCNP->no_contract }}
                                @endif
                            </p>
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
