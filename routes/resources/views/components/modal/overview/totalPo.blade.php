<div class="modal animate__animated animate__fadeIn" id="overviewPO{{ $getPOModal[$item]['monthKey'] }}" tabindex="-1"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="exampleModalLabel5"> Total PO
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card mb-4">
                    <div class="table-responsive text-nowrap">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>PO No.</th>
                                    <th>Company</th>
                                    <th>Title</th>
                                    <th>PO Date</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @php
                                    $totalP = 0;
                                    $key = 0;
                                @endphp
                                @forelse ($getPOModal[$item]['data'] as $key => $quoteData)
                                    @php
                                        $totalQ = $quoteData['nett'];
                                        $totalP += $totalQ;
                                        $quoteObj = \App\Models\Quotation::where('id', $quoteData['id'])->first();
                                    @endphp
                                    <tr>
                                        <td class="fw-medium">
                                            <a class="text-black"
                                                href="{{ route('quotation.show', $quoteObj->id) }}">{{ $quoteObj->no_quote }}</a>
                                        </td>
                                        <td>{{ $quoteObj->pic->client->company ?? 'Client Di Hapus' }}</td>
                                        <td>{{ $quoteObj->title }}</td>
                                        <td>{{ \Carbon\Carbon::parse($quoteObj->estimated_date)->format('d-m-Y') }}</td>
                                        <td class="text-end">Rp
                                            {{ number_format($quoteObj->nett, 0, '', '.') }}</td>
                                    </tr>
                                    @php
                                        $key++;
                                    @endphp
                                @empty
                                    <td colspan="5" class="text-center">Kamu belum punya quotation</td>
                                @endforelse
                                <tr class="bg-label-secondary">
                                    <td colspan="3">
                                    </td>
                                    <td><strong>Total</strong></td>
                                    <td class="text-end"><strong>Rp {{ number_format($totalP, 0, '', '.') }}</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
