@extends('layouts.sales.app')
@section('title', 'Surat Jalan ' . ($invoice->no_invoice ?? $unitQuote->no_quote))
@section('content')
    @php
        $isEkspedisi = strtolower($delivery->type ?? '') === 'ekspedisi';
        $client = $unitQuote->client ?? null;
        $address = $client ? ($delivery->destination == '1' ? $client->address : $client->subAddress) : '-';
        $doNumber = $invoice->no_invoice ?? $unitQuote->no_quote;
    @endphp

    <div class="row invoice-preview">
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                @if ($isEkspedisi)
                    <div class="card-body pb-0">
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column gap-3">
                            <div class="mb-xl-0 pb-1">
                                <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                    <span class="app-brand-logo demo">
                                        <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="Reftech Logo" width="180">
                                    </span>
                                </div>
                                <p class="mb-1 fw-bold text-dark" style="font-size:14px;">PT Reftech Jaya Optima</p>
                                <div style="font-size: 11px;" class="text-muted">
                                    <p class="mb-1" style="line-height:1.4;">Taman Kopo Indah V, Soho Sommerville No. 31, Bandung – Jawa Barat 40218</p>
                                    <p class="mb-0">
                                        <i class="mdi mdi-phone-outline me-1 text-primary"></i>022 54417653
                                        &nbsp;|&nbsp;
                                        <i class="mdi mdi-email-outline me-1 text-primary"></i>accounting@reftech.id
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h1 class="fw-bold" style="color:#181cffff; letter-spacing:2px;">DELIVERY ORDER</h1>
                                <p class="mb-1 fw-bold text-dark" style="font-size:14px;">#{{ $doNumber }}</p>
                                <p class="mb-0 text-muted small">{{ \Carbon\Carbon::parse($delivery->date)->format('d F Y') }}</p>
                            </div>
                        </div>

                        <div style="height:2px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:16px 0 18px;"></div>

                        {{-- Deliver To + Document Info --}}
                        <div style="display:flex; align-items:stretch; gap:12px; margin-bottom:20px; font-size:12px;">
                            <div style="flex:1; display:flex; flex-direction:column; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                                <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Deliver To</p>
                                <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">{{ $client->company ?? '-' }}</p>
                                <p class="mb-0" style="font-size:11.5px; color:#222; line-height:1.4;">
                                    <i class="mdi mdi-map-marker-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">{{ $address }}</span>
                                </p>
                            </div>
                            <div style="min-width:240px; display:flex; flex-direction:column; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                                <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Shipment Info</p>
                                <p class="mb-1 fw-semibold" style="font-size:12px; color:#222;">
                                    <i class="mdi mdi-clipboard-text-outline me-1 text-primary"></i>PO / Quote No: <span class="fw-bold">{{ $unitQuote->po_number ?: $unitQuote->no_quote }}</span>
                                </p>
                                <p class="mb-0 fw-semibold" style="font-size:12px; color:#222;">
                                    <i class="mdi mdi-truck-outline me-1 text-primary"></i>Jenis: <span class="fw-bold">{{ ucfirst($delivery->type) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered m-0" style="border:1px solid rgb(60,60,60);">
                                <thead>
                                    <tr style="background:#f0f2ff;">
                                        <th class="text-center" style="width:6%; font-size:12px;">No.</th>
                                        <th style="font-size:12px;">Description</th>
                                        <th class="text-center" style="width:16%; font-size:12px;">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $itemNo = 1; @endphp
                                    @foreach ($dDelivery as $item)
                                        @if (($item->type ?? 'item') === 'header')
                                            <tr style="background:#f0f0ff; border-top:1.5px solid #d0d0ff; border-bottom:1.5px solid #d0d0ff;">
                                                <td colspan="3" class="fw-bold text-uppercase py-2 px-3 text-primary" style="font-size:12px; letter-spacing:0.5px;">
                                                    <i class="mdi mdi-bookmark-outline me-1"></i> {{ $item->desc }}
                                                </td>
                                            </tr>
                                        @else
                                            <tr style="font-size: 13px" class="item-view-row" data-item-id="{{ $item->id }}">
                                                <td class="align-top text-center">{{ $itemNo++ }}</td>
                                                <td class="align-top">
                                                    <p class="mb-0 fw-medium item-desc" style="font-size:12px;">{{ $item->desc }}</p>
                                                </td>
                                                <td class="align-top text-center" style="white-space:nowrap;">{{ (float) $item->qty }} {{ $item->info_qty }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-4 mt-4 text-center">
                                <div style="height:56px;"></div>
                                <p class="fw-bold mx-3 mb-0" style="border-top:1px solid #999; padding-top:4px;">PT. Reftech Jaya Optima</p>
                                <p class="text-muted small mb-0">Shipper</p>
                            </div>
                            <div class="col-4"></div>
                            <div class="col-4 mt-4 text-center">
                                <div style="height:56px;"></div>
                                <p class="fw-bold mx-3 mb-0" style="border-top:1px solid #999; padding-top:4px;">{{ $client->company ?? '-' }}</p>
                                <p class="text-muted small mb-0">Received</p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card-body">
                        <div class="table-responsive mb-5">
                            <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="py-1">
                                            <div class="row">
                                                <div class="col-8">
                                                    <h5 class="fw-bold mb-0">Delivery Order</h5>
                                                </div>
                                                <div class="col-4">
                                                    <p class="mb-0"><span class="fw-bold">D.O. No :</span>
                                                        {{ $doNumber }}</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="py-0">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                                        <div class="mb-xl-0 pb-1">
                                                            <div class="d-flex svg-illustration align-items-center gap-2">
                                                                <span class="app-brand-logo demo">
                                                                    <span style="color: var(--bs-primary)">
                                                                        <img class="text-md"
                                                                            src="{{ asset('/asset') }}/logo/Reftech-Log.png"
                                                                            alt="" srcset="" width="60%">
                                                                    </span>
                                                                </span>
                                                            </div>
                                                            <p class="mb-1 mx-2 fw-bolder">PT Reftech Jaya Optima</p>
                                                            <div class="mx-2" style="font-size: 10px">
                                                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                                                <p class="mb-1">
                                                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                                                    {{ '   ' }}<i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>accounting@reftech.id
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="row mt-3" style="font-size: 13px">
                                                        <div class="col-4 text-end">
                                                            <p class="mb-1">Date</p>
                                                            <p class="mb-1">Order No</p>
                                                            <p class="mb-1">Customer</p>
                                                            <p class="mb-1">Delivery To</p>
                                                        </div>
                                                        <div class="col-8">
                                                            <p class="mb-1">: {{ \Carbon\Carbon::parse($delivery->date)->format('d-m-Y') }}</p>
                                                            <p class="mb-1">: {{ $unitQuote->po_number ?: $unitQuote->no_quote }}</p>
                                                            <p class="mb-1">: {{ $client->company ?? '-' }}</p>
                                                            <p class="mb-1">: {{ $address }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-center" style="width: 70%">Description</th>
                                    </tr>
                                    @php $itemNo = 1; @endphp
                                    @foreach ($dDelivery as $item)
                                        @if (($item->type ?? 'item') === 'header')
                                            <tr style="background:#f0f0ff;">
                                                <td colspan="3" class="fw-bold text-uppercase py-1" style="font-size:12px;">{{ $item->desc }}</td>
                                            </tr>
                                        @else
                                            <tr style="font-size: 13px;" class="item-view-row" data-item-id="{{ $item->id }}">
                                                <td class="align-top py-1">{{ $itemNo++ }}</td>
                                                <td class="align-top py-1">{{ $item->qty }} {{ $item->info_qty }}</td>
                                                <td class="align-top py-1 item-desc">{{ $item->desc }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr>
                                        <td colspan="3">
                                            <div class="row mb-3">
                                                <div class="col-4 mt-5 text-center">
                                                    <div class="pb-5"></div>
                                                    <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">Shipper</p>
                                                </div>
                                                <div class="col-4"></div>
                                                <div class="col-4 mt-5 text-center">
                                                    <div class="pb-5"></div>
                                                    <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">Recieved</p>
                                                </div>
                                            </div>
                                            <p class="mb-0">Distribusi : Putih dan Pink → Pelanggan, <span class="fw-bold">Kuning → Accounting PT. Reftech</span></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('print.delivery', $delivery->id) }}"
                        class="btn btn-primary waves-effect" target="_blank">
                        <i class="mdi mdi-printer-outline me-1"></i> Cetak
                    </a>
                    <a href="{{ route('unit-quotation.show', $unitQuote->id) }}" class="btn btn-outline-secondary waves-effect">
                        <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Quotation
                    </a>
                    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting')
                        <a href="#" class="btn btn-outline-danger waves-effect delete-delivery"
                            data-id="{{ $delivery->id }}"
                            data-redirect="{{ $invoice ? route('invoice.show_unit', $invoice->id) : route('unit-quotation.show', $unitQuote->id) }}">
                            <i class="mdi mdi-delete-outline me-1"></i> Hapus Surat Jalan
                        </a>
                    @endif
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <p class="mb-1 fw-semibold" style="font-size:11px;color:#888;">NO. INVOICE</p>
                    <p class="mb-2 fw-bold">{{ $invoice->no_invoice ?? '-' }}</p>
                    <p class="mb-1 fw-semibold" style="font-size:11px;color:#888;">NO. QUOTATION</p>
                    <p class="mb-2 fw-bold">{{ $unitQuote->no_quote }}</p>
                    <p class="mb-1 fw-semibold" style="font-size:11px;color:#888;">JENIS PENGIRIMAN</p>
                    <p class="mb-2">{{ ucfirst($delivery->type) }}</p>
                    <p class="mb-1 fw-semibold" style="font-size:11px;color:#888;">STATUS</p>
                    <span class="badge bg-success">Barang Keluar</span>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).on('change', '.item-view-toggle', function() {
            const $toggle = $(this);
            const id = $toggle.data('id');
            const $row = $toggle.closest('.item-view-row');
            const $desc = $row.find('.item-desc');

            $.ajax({
                url: '{{ url('/delivery/item') }}/' + id + '/toggle-view',
                type: 'PATCH',
                data: { _token: '{{ csrf_token() }}' },
                success: function(res) {
                    if (res.view == '1') {
                        $desc.css({ opacity: .45, 'text-decoration': 'line-through' });
                    } else {
                        $desc.css({ opacity: 1, 'text-decoration': 'none' });
                    }
                },
                error: function() {
                    $toggle.prop('checked', !$toggle.prop('checked'));
                    alert('Gagal menyimpan perubahan, coba lagi.');
                }
            });
        });

        $(document).on('click', '.delete-delivery', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const redirect = $(this).data('redirect');

            Swal.fire({
                title: "Hapus Surat Jalan ini?",
                text: "Item yang sudah dikirim di sini akan kembali dianggap belum terkirim (sisa qty di quotation akan pulih).",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('/delivery') }}/' + id,
                        type: 'POST',
                        data: { _method: 'DELETE', _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response == 1) {
                                window.location.href = redirect;
                            } else {
                                Swal.fire('Oops...', 'Gagal menghapus Surat Jalan.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Oops...', 'Gagal menghapus Surat Jalan.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endpush
