<div class="modal fade" id="detailOverdue" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 92vw; width: 92vw;" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header border-bottom py-3 px-4 bg-light d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-xs bg-label-danger rounded p-1 d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                        <i class="mdi mdi-alert-octagon-outline fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Detail of Overdue Invoices</h5>
                        <small class="text-muted" style="font-size: 11.5px;">Daftar invoice yang telah melewati tanggal jatuh tempo kredit tempo</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-top modal-datatable-overdue w-100" style="font-size: 13px;">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 50px;">#</th>
                                <th>No. Invoice</th>
                                <th>Tanggal</th>
                                <th>Customer</th>
                                <th class="text-center">Keterlambatan</th>
                                <th class="text-end">Total Invoice</th>
                                <th class="text-center" style="width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 0;
                            @endphp
                            @foreach ($overdue as $item)
                                @php
                                    $no++;
                                    $days = \Carbon\Carbon::parse($item->due_date)->diffInDays(\Carbon\Carbon::today(), false);
                                    if ($item->id_unit_quotation) {
                                        $uq           = $item->unitQuotation;
                                        $inv          = \App\Models\Invoice::where('id_unit_quotation', $item->id_unit_quotation)->whereNotNull('no_invoice')->first();
                                        $invoiceRoute = $inv ? route('invoice.show_unit', $inv->id) : '#';
                                        $invoiceNo    = $inv?->no_invoice ?? '-';
                                        $itemDate     = $uq?->created_at?->format('d-m-Y') ?? '-';
                                        $company      = $uq?->client?->company ?? '-';
                                        $total        = $item->harga_total;
                                    } else {
                                        $inv0         = $item->quotation?->invoice?->first();
                                        $invoiceRoute = $inv0 ? route('invoice.show', $inv0->id) : '#';
                                        $invoiceNo    = $inv0?->no_invoice ?? '-';
                                        $itemDate     = $item->quotation?->po_date ? \Carbon\Carbon::parse($item->quotation->po_date)->format('d-m-Y') : '-';
                                        $company      = $item->quotation?->pic?->client?->company ?? ($item->client?->company ?? '-');
                                        $total        = $item->quotation?->harga_total ?? $item->harga_total;
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center fw-semibold text-muted">{{ $no }}</td>
                                    <td>
                                        <a href="{{ $invoiceRoute }}" class="fw-bold text-danger text-decoration-none" target="_blank">
                                            <i class="mdi mdi-file-document-outline me-1"></i>{{ $invoiceNo }}
                                        </a>
                                    </td>
                                    <td><span class="text-muted">{{ $itemDate }}</span></td>
                                    <td><span class="fw-semibold text-dark">{{ $company }}</span></td>
                                    <td class="text-center">
                                        <span class="badge bg-label-danger px-2 py-1">
                                            <i class="mdi mdi-clock-alert-outline me-1"></i>{{ max(0, $days) }} Hari Overdue
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-dark">Rp {{ number_format($total, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        @if($invoiceRoute !== '#')
                                            <a href="{{ $invoiceRoute }}" class="btn btn-xs btn-label-danger px-2" target="_blank" title="Buka Invoice">
                                                <i class="mdi mdi-eye-outline me-1"></i>Detail
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top py-2 px-4 bg-light d-flex justify-content-between align-items-center">
                <span class="text-muted small">Total: <strong class="text-danger">{{ count($overdue) }}</strong> Invoice Overdue</span>
                <button type="button" class="btn btn-label-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
