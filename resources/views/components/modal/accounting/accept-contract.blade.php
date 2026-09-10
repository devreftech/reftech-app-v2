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
                    @php
                        $isOrderModal = $contract->type == 'Order' || (bool) ($contract->quotation?->isKojisha());
                        $isPpn = ($contract->quotation?->tax == '11' || $contract->quotation?->tax == '1');
                        $docNoun = $isOrderModal ? 'Confirm Order' : 'Selling Contract';
                    @endphp
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body mb-1">Accept {{ $docNoun }}</h4>
                        <div class="text-muted small mb-2">No. Quotation: <strong class="text-dark">{{ $contract->no_contract }}</strong></div>
                        <div class="onboarding-info mb-3">
                            <span class="fw-semibold text-dark">{{ $contract->quotation?->pic?->client?->company ?? '-' }}</span>
                        </div>

                        <!-- Card Keterangan Entitas & PPN -->
                        <div class="p-3 mb-3 rounded-3 text-start bg-light border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Tipe Entitas & Dokumen:</span>
                                <div>
                                    @if ($isOrderModal)
                                        <span class="badge bg-label-warning me-1">Kojisha</span>
                                        <span class="badge bg-label-dark">Confirm Order</span>
                                    @else
                                        <span class="badge bg-label-info me-1">Reftech</span>
                                        <span class="badge bg-label-primary">Selling Contract</span>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Status Pajak:</span>
                                @if ($isPpn)
                                    <span class="badge bg-label-primary"><i class="mdi mdi-check-circle-outline me-1"></i>PPN (11%)</span>
                                @else
                                    <span class="badge bg-label-danger"><i class="mdi mdi-close-circle-outline me-1"></i>Non-PPN</span>
                                @endif
                            </div>
                            <div class="alert alert-secondary py-1 px-2 mb-0 mt-2" style="font-size: 11px;">
                                <i class="mdi mdi-information-outline me-1 text-primary"></i>
                                <span><strong>Reftech</strong> &rarr; Selling Contract (<code>SELLCTX/RJO</code>) &bull; <strong>Kojisha</strong> &rarr; Confirm Order (<code>CO/KII</code>)</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3 text-start">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="no_contract_{{ $contract->id }}" name="no_contract"
                                        placeholder="No Contract"
                                        value="{{ $result }}/{{ $isPpn ? 'P' : 'NP' }}/{{ $isOrderModal ? 'CO/KII' : 'SELLCTX/RJO' }}/{{ $thisYear }}" required>
                                    <label for="no_contract_{{ $contract->id }}">No {{ $docNoun }}</label>
                                </div>
                                <p class="text-danger text-start mt-2 mb-0 small">
                                    <i class="mdi mdi-history me-1"></i>Last No :
                                    <strong>
                                    @if (!$isOrderModal && $contract->quotation?->tax == '11')
                                        {{ @$numberLastSP->no_contract ?? '-' }}
                                    @elseif (!$isOrderModal && $contract->quotation?->tax == '0')
                                        {{ @$numberLastSNP->no_contract ?? '-' }}
                                    @elseif ($isOrderModal && $contract->quotation?->tax == '11')
                                        {{ @$numberLastCP->no_contract ?? '-' }}
                                    @elseif ($isOrderModal && $contract->quotation?->tax == '0')
                                        {{ @$numberLastCNP->no_contract ?? '-' }}
                                    @else
                                        -
                                    @endif
                                    </strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 d-flex justify-content-between">
                    <a href="{{ route('contract.show', $contract->id) }}" class="btn btn-outline-primary waves-effect">
                        <i class="mdi mdi-eye-outline me-1"></i>Detail
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
