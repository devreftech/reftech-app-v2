<div class="modal-onboarding modal modal-xl fade animate__animated" id="detailOutstanding" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <h3 class="onboarding-title"> Detail Of Outstanding
                    </h3>
                    <form>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table m-0">
                                        <thead class="table-light border-top">
                                            <tr>
                                                <th>No.</th>
                                                <th>Invoice.</th>
                                                <th>Date</th>
                                                <th>Customer</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 0;
                                            @endphp
                                            @forelse ($invoice as $item)
                                                @php
                                                    $no++;
                                                    if ($item->id_unit_quotation) {
                                                        $uq           = $item->unitQuotation;
                                                        $inv          = \App\Models\Invoice::where('id_unit_quotation', $item->id_unit_quotation)->whereNotNull('no_invoice')->first();
                                                        $invoiceRoute = $inv ? route('invoice.show_unit', $inv->id) : '#';
                                                        $invoiceNo    = $inv?->no_invoice ?? '-';
                                                        $itemDate     = $uq?->created_at?->format('d-m-Y') ?? '-';
                                                        $company      = $uq?->client?->company ?? '-';
                                                        $total        = $item->harga_total;
                                                    } else {
                                                        $inv0         = $item->quotation->invoice->first();
                                                        $invoiceRoute = $inv0 ? route('invoice.show', $inv0->id) : '#';
                                                        $invoiceNo    = $inv0?->no_invoice ?? '-';
                                                        $itemDate     = $item->quotation->po_date ?? '-';
                                                        $company      = $item->quotation->pic->client->company ?? '-';
                                                        $total        = $item->quotation->harga_total;
                                                    }
                                                @endphp
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>
                                                        <a href="{{ $invoiceRoute }}" class="text-dark text-decoration-none">
                                                            {{ $invoiceNo }}
                                                        </a>
                                                    </td>
                                                    <td><p>{{ $itemDate }}</p></td>
                                                    <td>{{ $company }}</td>
                                                    <td>{{ number_format($total, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3">tidak ada Outstanding</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
