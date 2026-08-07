<div class="modal-onboarding modal modal-lg fade animate__animated" id="detailPending-{{$pending->id}}" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <h3 class="onboarding-title"> Detail Pending Of {{ $pending->quote->pic->client->company }}
                    </h3>
                    {{-- <div class="onboarding-info mb-3">
                            {{ $quote->pic->client->company }}
                        </div> --}}
                    <form>
                        <div class="row">
                            <div class="col-12 col-md-6 mb-3">
                                <h5>Client Information</h5>
                                <div class="row">
                                    <div class="col-4">
                                        <p>Sales </p>
                                        <p>Client </p>
                                        <p>PIC </p>
                                        <p>R/K </p>
                                    </div>
                                    <div class="col-8">
                                        <p>: {{ $pending->quote->sales->name }}</p>
                                        <p>: {{ $pending->quote->pic->client->company }}</p>
                                        <p>: {{ $pending->quote->pic->name_pic }}</p>
                                        <p>: {{ $pending->quote->pic->client->info }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <h5>Invoice Information</h5>
                                <div class="row">
                                    <div class="col-4">
                                        <p>No Invoice </p>
                                        <p>No PO </p>
                                        <p>PO Date </p>
                                    </div>
                                    <div class="col-8">
                                        <p>: {{ $pending->quote->invoice->first()->no_invoice ?? 'Belum ada invoice' }}</p>
                                        <p>: {{ $pending->quote->invoice->first()->no_po ?? 'Belum ada invoice' }}</p>
                                        <p>: {{ $pending->quote->po_date }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">

                                <div class="table-responsive">
                                    <table class="table m-0">
                                        <thead class="table-light border-top">
                                            <tr>
                                                <th>No.</th>
                                                <th>Item</th>
                                                <th>Qty</th>
                                                <th>Note</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 0;
                                            @endphp
                                            @forelse ($pending->detail as $item)
                                                @php
                                                    $no++;
                                                @endphp
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>
                                                        <p>
                                                            {{ $item->replacement->replacement }}
                                                        </p>
                                                        <p>
                                                            {{ $item->desc }}
                                                        </p>
                                                    </td>
                                                    <td>
                                                        {{ $item->qty }}
                                                        {{ $item->replacement->product->info_qty }}
                                                    </td>
                                                    <td>{{ $item->note }}</td>
                                                </tr>
                                            @empty
                                            <tr>
                                                <td colspan="3">Barang belum di cek</td>
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
