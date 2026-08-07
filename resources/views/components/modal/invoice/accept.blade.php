<form action="{{ route('invoice.update', $invoice->id) }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    <div class="modal-onboarding modal fade animate__animated" id="acceptInvoice-{{ $quote->id }}" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Accept Invoice {{ $quote->no_quote }}</h4>
                        <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    @php
                                        if ($quote->pic->client->info == 'Reftech') {
                                            $code = 'RJO';
                                        } else {
                                            $code = 'KII';
                                        }

                                        if ($quote->pic->client->info == 'Reftech') {
                                            $nextCode = 'RJO';
                                        } else {
                                            $code = 'KII';
                                        }

                                        if ($quote->tax == '11' && $invoice->flag == 'Reftech') {
                                            $nextCode = $nextCodePR;
                                        } elseif ($quote->tax == '0' && $invoice->flag == 'Reftech') {
                                            $nextCode = $nextCodeNPR;
                                        } elseif ($quote->tax == '11' && $invoice->flag == 'Kojisha') {
                                            $nextCode = $nextCodePK;
                                        } elseif ($quote->tax == '0' && $invoice->flag == 'Kojisha') {
                                            $nextCode = $nextCodeNPK;
                                        }

                                    @endphp
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            value="{{ $quote->tax != 0 ? $nextCode . '/SJ-P/' . $code . '/' . $monthCode . '/' . $year : $nextCode . '/SJ-NP/' . $code . '/' . $monthCode . '/' . $year }}"
                                            placeholder="Put No Invoice Here ...." id="invoice" name="invoice">
                                        <label for="invoice">No Invoice</label>
                                    </div>

                                    <p class="text-danger text-start">
                                        Last No :
                                        @if ($quote->tax == '11' && $invoice->flag == 'Reftech')
                                            {{ $displayLastPR ?? '-' }}
                                        @elseif ($quote->tax == '0' && $invoice->flag == 'Reftech')
                                            {{ $displayLastNPR ?? '-' }}
                                        @elseif($quote->tax == '11' && $invoice->flag == 'Kojisha')
                                            {{ $displayLastPK ?? '-' }}
                                        @elseif ($quote->tax == '0' && $invoice->flag == 'Kojisha')
                                            {{ $displayLastNPK ?? '-' }}
                                        @endif
                                    </p>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            placeholder="Put Terms & Payments Here ...." id="payment" name="payment"
                                            value="">
                                        <label for="payment">Terms & Payments</label>
                                    </div>
                                </div>
                                {{-- @if ($lastPayment->type == 'Tempo')
                                @endif --}}
                                {{-- @if (!is_null($lastInvoiceP->no_invoice) || !is_null($lastInvoiceNP->no_invoice))
                                    <div class="col-12 mb-3">
                                        <p class="text-muted fw-medium text-start">Last Invoice :
                                            {{ $quote->tax != 0 ? $lastInvoiceP->no_invoice : $lastInvoiceNP->no_invoice }}
                                        </p>
                                    </div>
                                @endif --}}
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
