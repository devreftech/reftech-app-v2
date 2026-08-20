@php
    $monthKeyModal = $getPOModal[$item]['monthKey'] ?? $item;
    $modalDataList = $getPOModal[$item]['data'] ?? [];
@endphp
@once
    @push('after-style')
        <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
        <style>
            .po-modal-datatable {
                table-layout: fixed;
                width: 100% !important;
            }
            .po-modal-datatable tbody tr:hover,
            .po-modal-datatable tbody tr:hover > * {
                background-color: transparent !important;
            }
            .po-modal-datatable th,
            .po-modal-datatable td {
                vertical-align: middle;
            }
            .po-modal-datatable td.po-modal-title {
                max-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        </style>
    @endpush
    @push('after-script')
        <script src="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    @endpush
    @push('modals')
        <script>
            // Lazy-init: DataTable dibuat saat modal pertama kali dibuka, bukan saat halaman
            // dimuat, supaya perhitungan lebar kolom benar (tabel tidak berada di elemen hidden).
            document.addEventListener('shown.bs.modal', function (e) {
                var $table = $(e.target).find('table.po-modal-datatable');
                if ($table.length && !$.fn.DataTable.isDataTable($table[0])) {
                    $table.DataTable({
                        order: [],
                        autoWidth: false,
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100],
                        columnDefs: [
                            { targets: 0, width: '14%' },
                            { targets: 1, width: '20%' },
                            { targets: 2, width: '30%' },
                            { targets: 3, width: '16%' },
                            { targets: 4, width: '20%' }
                        ],
                        language: {
                            search: 'Cari:',
                            lengthMenu: 'Tampilkan _MENU_ PO',
                            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ PO',
                            infoEmpty: 'Tidak ada PO',
                            infoFiltered: '(disaring dari _MAX_ total PO)',
                            paginate: { previous: 'Sebelumnya', next: 'Berikutnya' },
                            zeroRecords: 'Tidak ada data yang cocok'
                        }
                    });
                }
            });
        </script>
    @endpush
@endonce
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
                    <div class="table-responsive">
                        <table class="table table-bordered po-modal-datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>No. Quotation</th>
                                    <th>Company</th>
                                    <th>Title</th>
                                    <th>PO Date</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @php
                                    $totalP = 0;
                                @endphp
                                @forelse ($modalDataList as $quoteData)
                                    @php
                                        $totalP += $quoteData['nett'];
                                        $isUnit = ($quoteData['source'] ?? 'quotation') === 'unit_quotation';
                                        $quoteObj = $isUnit ? null : \App\Models\Quotation::where('id', $quoteData['id'])->first();
                                        $rowTitle = $isUnit ? $quoteData['title'] : $quoteObj->title;
                                        $fullNoQuote = $isUnit ? $quoteData['no_quote'] : $quoteObj->no_quote;
                                        $shortNoQuote = strlen($fullNoQuote) > 5 ? substr($fullNoQuote, 0, 5) . '…' : $fullNoQuote;
                                    @endphp
                                    <tr>
                                        <td class="fw-medium">
                                            @if ($isUnit)
                                                <a class="text-black" title="{{ $fullNoQuote }}"
                                                    href="{{ route('unit-quotation.show', $quoteData['id']) }}">{{ $shortNoQuote }}</a>
                                            @else
                                                <a class="text-black" title="{{ $fullNoQuote }}"
                                                    href="{{ route('quotation.show', $quoteObj->id) }}">{{ $shortNoQuote }}</a>
                                            @endif
                                        </td>
                                        <td>{{ $isUnit ? $quoteData['company'] : ($quoteObj->pic->client->company ?? 'Client Di Hapus') }}</td>
                                        <td class="po-modal-title" title="{{ $rowTitle }}">{{ $rowTitle }}</td>
                                        <td>{{ \Carbon\Carbon::parse($isUnit ? $quoteData['estimated_date'] : $quoteObj->estimated_date)->format('d-m-Y') }}</td>
                                        <td class="text-end">Rp
                                            {{ number_format($isUnit ? $quoteData['nett'] : $quoteObj->nett, 0, '', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Kamu belum punya quotation</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="bg-label-secondary">
                                    <td colspan="3"></td>
                                    <td><strong>Total</strong></td>
                                    <td class="text-end"><strong>Rp {{ number_format($totalP, 0, '', '.') }}</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
