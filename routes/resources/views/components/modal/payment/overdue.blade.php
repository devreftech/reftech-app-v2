<div class="modal-onboarding modal modal-xl fade animate__animated" id="detailOverdue" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <h3 class="onboarding-title"> Detail Of Overdue
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
                                                <th>Overdue</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 0;
                                            @endphp
                                            @forelse ($overdue as $item)
                                                @php
                                                    $no++;
                                                    $days = \Carbon\Carbon::parse($item->due_date)->diffInDays(
                                                        \Carbon\Carbon::today(),
                                                        false,
                                                    );
                                                @endphp
                                                <tr>
                                                    <td>{{ $no }}</td>
                                                    <td>
                                                        <a href="{{ route('invoice.show', $item->quotation->invoice[0]->id) }}"
                                                            class="text-dark text-decoration-none">
                                                            {{ $item->quotation->invoice[0]->no_invoice }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <p>
                                                            {{ $item->quotation->po_date }}
                                                        </p>
                                                    </td>
                                                    <td>
                                                        {{ $item->quotation->pic->client->company }}
                                                    </td>
                                                    <td>
                                                        {{ $days }} Days Overdue
                                                    </td>
                                                    <td>{{ number_format($item->quotation->harga_total, 0, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3">tidak ada Overdue</td>
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
