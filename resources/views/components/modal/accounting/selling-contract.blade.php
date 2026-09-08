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
                    @php
                        $isPpn = ($quote->tax == '11' || $quote->tax == '1');
                    @endphp
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body mb-1"> Create Selling Contract</h4>
                        <div class="text-muted small mb-2">No. Quotation: <strong class="text-dark">{{ $quote->no_quote }}</strong></div>
                        <div class="onboarding-info mb-3">
                            <span class="fw-semibold text-dark">{{ $quote->pic?->client?->company ?? '-' }}</span>
                        </div>

                        <!-- Card Keterangan Entitas & PPN -->
                        <div class="p-3 mb-3 rounded-3 text-start bg-light border">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold">Tipe Entitas & Dokumen:</span>
                                <div>
                                    <span class="badge bg-label-info me-1">Reftech</span>
                                    <span class="badge bg-label-primary">Selling Contract</span>
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
                                    <input type="text" class="form-control" id="no_contract" name="no_contract"
                                        placeholder="No Selling Contract" value="{{$quote->tax == '11' ? $formattedNumberSP : $formattedNumberSNP}}/{{$quote->tax == '11' ? 'P' : 'NP'}}/SELLCTX/RJO/{{$thisYear}}" required>
                                    <label for="no_contract">No Selling Contract</label>
                                </div>
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
