@php
    $monthKeyModal = $getPOModal[$item]['monthKey'] ?? $item;
    $modalDataList = $getPOModal[$item]['data'] ?? [];
@endphp
<div class="modal animate__animated animate__fadeIn" id="overviewPO{{ $monthKeyModal }}" tabindex="-1"
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
                                @forelse ($modalDataList as $key => $quoteData)
                                    @php
                                        $totalQ = $quoteData['nett'];
                                        $totalP += $totalQ;
                                        $isUnit = ($quoteData['source'] ?? 'quotation') === 'unit_quotation';
                                        $quoteObj = $isUnit ? null : \App\Models\Quotation::where('id', $quoteData['id'])->first();
                                    @endphp
                                    <tr>
                                        <td class="fw-medium">
                                            @if ($isUnit)
                                                <a class="text-black"
                                                    href="{{ route('unit-quotation.show', $quoteData['id']) }}">{{ $quoteData['no_quote'] }}</a>
                                            @else
                                                <a class="text-black"
                                                    href="{{ route('quotation.show', $quoteObj->id) }}">{{ $quoteObj->no_quote }}</a>
                                            @endif
                                        </td>
                                        <td>{{ $isUnit ? $quoteData['company'] : ($quoteObj->pic->client->company ?? 'Client Di Hapus') }}</td>
                                        <td>{{ $isUnit ? $quoteData['title'] : $quoteObj->title }}</td>
                                        <td>{{ \Carbon\Carbon::parse($isUnit ? $quoteData['estimated_date'] : $quoteObj->estimated_date)->format('d-m-Y') }}</td>
                                        <td class="text-end">Rp
                                            {{ number_format($isUnit ? $quoteData['nett'] : $quoteObj->nett, 0, '', '.') }}</td>
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
