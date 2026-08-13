@extends('layouts.sales.app')
@section('title', 'Detail Purchase Order')
@section('content')
    @php
        $totalPph = $totalPph ?? 0;
        $hasDisc = $dPurchase->contains(fn($item) => $item->disc > 0);
    @endphp
    <div class="row invoice-preview">
        {{-- Main PO Document Card --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card mb-3 shadow-sm border-0">
                <div class="card-body p-4">
                    {{-- Header --}}
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-0">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img src="{{ asset('/asset') }}/logo/Reftech-Log.png" alt="PT Reftech Jaya Optima" width="60%">
                                    </span>
                                </span>
                            </div>
                            <p class="mb-1 fw-bolder" style="font-size: 15px">PT Reftech Jaya Optima</p>
                            <div style="font-size: 12px; color: #555;">
                                <p class="mb-0">Taman Kopo Indah V, Soho Sommerville No. 31</p>
                                <p class="mb-0">Bandung – Jawa Barat 40218</p>
                                <p class="mb-0"><i class="mdi mdi-phone-outline me-1" style="font-size:11px;"></i>022 54417653 &nbsp;|&nbsp; <i class="mdi mdi-email-outline me-1" style="font-size:11px;"></i>info@reftech.id &nbsp;|&nbsp; <i class="mdi mdi-web me-1" style="font-size:11px;"></i>www.reftech.id</p>
                                <p class="mb-0 mt-1" style="font-size:10.5px; color:#444; font-weight:500;">
                                    <i class="mdi mdi-certificate-outline me-1 text-primary"></i><span class="fw-bold" style="color:#696cff;">ISO Certified:</span> ISO 9001:2015 &nbsp;|&nbsp; ISO 14001:2015 &nbsp;|&nbsp; ISO 45001:2018
                                </p>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold mb-1" style="letter-spacing:2px; color:#696cff;">PURCHASE ORDER</h3>
                            <p class="mb-1 fw-bold text-dark" style="font-size:16px;">#{{ $purchase->no_po }}</p>
                            <p class="mb-1 fw-bold" style="font-size:13px; color:#0f172a !important;">
                                <i class="mdi mdi-calendar-blank-outline me-1 text-primary"></i>{{ Carbon\Carbon::parse($purchase->date)->format('d-m-Y') }}
                            </p>
                            <div class="mb-1 mt-1">
                                <span class="badge bg-primary px-3 py-1 fs-6">PURCHASE ORDER</span>
                            </div>
                            @if ($purchase->category)
                                <p class="mb-0 text-muted" style="font-size:11px;">Category: {{ $purchase->category }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Accent Divider --}}
                    <div style="height:3px; background:linear-gradient(90deg,#696cff 0%,#9c9eff 60%,#e0e0e0 100%); border-radius:2px; margin:14px 0 16px;"></div>

                    {{-- Vendor / Supplier + Ship To / Prepared By Boxes --}}
                    <div style="display:flex !important; align-items:stretch !important; gap:12px; margin-bottom:16px; font-size:12px;">
                        {{-- Vendor Box --}}
                        <div style="flex:1; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                            <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Vendor / Supplier</p>
                            <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">{{ $purchase->company ?: '-' }}</p>
                            @php
                                $vendorParts = [];
                                if ($purchase->attn) {
                                    $vendorParts[] = '<i class="mdi mdi-account-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">ATTN: ' . e($purchase->attn) . '</span>';
                                }
                                if ($purchase->phone || $purchase->mobile) {
                                    $vendorParts[] = '<i class="mdi mdi-phone-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($purchase->phone ?: $purchase->mobile) . '</span>';
                                }
                                if ($purchase->email) {
                                    $vendorParts[] = '<i class="mdi mdi-email-outline me-1" style="font-size:11px; color:#444;"></i><span style="color:#222; font-weight:500;">' . e($purchase->email) . '</span>';
                                }
                            @endphp
                            @if (count($vendorParts) > 0)
                                <p class="mb-1" style="font-size:11.5px; color:#333;">
                                    {!! implode(' &nbsp;|&nbsp; ', $vendorParts) !!}
                                </p>
                            @endif
                            @if ($purchase->address)
                                <div class="mb-0" style="display:flex; align-items:flex-start; font-size:11.5px; color:#222;">
                                    <i class="mdi mdi-map-marker-outline me-1" style="font-size:11px; color:#444; line-height:1.4; flex-shrink:0;"></i><span style="font-weight:500; line-height:1.4;">{{ $purchase->address }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Ship To / Terms Box --}}
                        <div style="min-width:260px; display:flex; flex-direction:column; align-self:stretch; border:1px solid #dcdcdc; border-radius:6px; padding:10px 14px; background:#fafafa;">
                            <p class="mb-1 fw-bold text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">Ship To & Commercial Terms</p>
                            <p class="mb-1 fw-bold" style="font-size:13.5px; color:#111;">PT Reftech Jaya Optima</p>
                            <p class="mb-1" style="font-size:11.5px; color:#444;">
                                <i class="mdi mdi-truck-delivery-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">Delivery: {{ $purchase->delivery ?: '-' }}</span>
                            </p>
                            <p class="mb-0" style="font-size:11.5px; color:#222;">
                                <i class="mdi mdi-credit-card-outline me-1" style="font-size:11px; color:#444;"></i><span style="font-weight:500;">Payment: {{ $purchase->payment ?: '-' }}</span>
                            </p>
                        </div>
                    </div>

                    <p class="mb-3" style="font-size:12px; color:#777; font-style:italic;">
                        Dear Sir/Madam, Please find below our official Purchase Order for the following items :
                    </p>

                    {{-- Items Table --}}
                    <div class="table-responsive rounded border mb-3">
                        <table class="table table-bordered m-0" style="width:100%; font-size:12px;">
                            <thead style="font-size:11px; background:#eeeeff; color:#3d3d8f;">
                                <tr>
                                    <th class="text-center py-2" style="width:5%; font-weight:700; border-color:#d0d0ff;">No.</th>
                                    <th class="text-center py-2" style="width:45%; font-weight:700; border-color:#d0d0ff;">Item Description</th>
                                    <th class="text-center py-2" style="width:12%; font-weight:700; border-color:#d0d0ff;">Qty</th>
                                    <th class="text-center py-2" style="width:18%; font-weight:700; border-color:#d0d0ff;">Price (IDR)</th>
                                    @if ($hasDisc)
                                        <th class="text-center py-2" style="width:7%; font-weight:700; border-color:#d0d0ff;">Disc</th>
                                    @endif
                                    <th class="text-center py-2" style="width:13%; font-weight:700; border-color:#d0d0ff;">Amount (IDR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 0; @endphp
                                @foreach ($dPurchase as $product)
                                    @php $no++; @endphp
                                    <tr style="font-size: 12px">
                                        <td class="text-center align-top py-2">{{ $no }}</td>
                                        <td class="align-top py-2">
                                            <p class="mb-0 fw-semibold" style="font-size: 12px; color:#111;">
                                                {{ $product->product }}
                                            </p>
                                        </td>
                                        <td class="text-center align-top py-2">
                                            <span class="fw-bold" style="color:#222;">{{ $product->qty }}</span> {{ $product->info_qty }}
                                        </td>
                                        <td class="text-end align-top py-2">
                                            Rp {{ number_format($product->price, 0, '', '.') }}
                                        </td>
                                        @if ($hasDisc)
                                            <td class="text-center align-top py-2">
                                                {{ $product->disc ? $product->disc . '%' : '-' }}
                                            </td>
                                        @endif
                                        <td class="text-end align-top py-2 fw-semibold" style="color:#111;">
                                            Rp {{ number_format($product->amount, 0, '', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Calculations & Notes --}}
                    @php
                        $tax = ($purchase->total * 11) / 100;
                        $noTax = $purchase->total - ($purchase->total * 11) / 100;
                        $dpp = ($noTax * 11) / 12;
                    @endphp

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="p-3 rounded border h-100" style="background:#fafafa; font-size:12px;">
                                <p class="fw-bold mb-1 text-uppercase" style="font-size:10px; letter-spacing:.5px; color:#555;">
                                    <i class="mdi mdi-note-text-outline me-1 text-primary"></i> Note / Catatan
                                </p>
                                <p class="mb-0" style="color:#333; font-style:italic;">
                                    {{ $purchase->note ?: 'Tidak ada catatan khusus.' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-bordered m-0" style="font-size: 12px; border-color: #c5c5c5;">
                                    <tbody>
                                        <tr>
                                            <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">SUBTOTAL</td>
                                            <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle; width: 55%;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span>Rp</span>
                                                    <span class="fw-semibold">{{ number_format($purchase->subtotal, 0, '', '.') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @if ($purchase->diskon > 0)
                                            <tr>
                                                <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">DISCOUNT</td>
                                                <td class="py-1_5 px-3 text-danger" style="border-color: #c5c5c5; background: #ffffff; vertical-align: middle;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>- Rp</span>
                                                        <span class="fw-semibold">{{ number_format($purchase->diskon, 0, '', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($purchase->vat > 0)
                                            <tr>
                                                <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">DPP NILAI LAIN</td>
                                                <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>Rp</span>
                                                        <span class="fw-semibold">{{ $dpp == '0' ? '0' : number_format($dpp, 0, '', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">VAT 12%</td>
                                                <td class="py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #111; vertical-align: middle;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>Rp</span>
                                                        <span class="fw-semibold">{{ $tax == '0' ? '0' : number_format($tax, 0, '', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        @if ($totalPph > 0)
                                            <tr>
                                                <td class="text-end fw-semibold text-uppercase py-1_5 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #333; vertical-align: middle;">TOTAL PPH</td>
                                                <td class="py-1_5 px-3 text-danger" style="border-color: #c5c5c5; background: #ffffff; vertical-align: middle;">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>- Rp</span>
                                                        <span class="fw-semibold">{{ number_format($totalPph, 0, '', '.') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <td class="text-end fw-bolder text-uppercase py-2 px-3" style="border-color: #c5c5c5; background: #ffffff; color: #000; font-size: 13px; vertical-align: middle;">TOTAL PRICE</td>
                                            <td class="py-2 px-3 fw-bold" style="border-color: #c5c5c5; background: #ffffff; color: #000; font-size: 13px; vertical-align: middle;">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold">Rp</span>
                                                    <span class="fw-bold fs-6">{{ $purchase->total == '0' ? '0' : number_format($purchase->total, 0, '', '.') }}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Signatures --}}
                    <div class="row pt-3 text-center" style="font-size:12px;">
                        <div class="col-6">
                            <p class="fw-bold mb-1" style="color:#333;">Authorized By.</p>
                            <div class="my-1">
                                <img src="{{ url('') . '/asset/sign/ttdAngel.jpg' }}" alt="TTD Angel" height="70">
                            </div>
                            <p class="fw-bold mb-0" style="color:#111;">PT Reftech Jaya Optima</p>
                        </div>
                        <div class="col-6">
                            <div class="pt-2">
                                <p class="fw-bold mb-1" style="color:#333;">Accepted By Vendor.</p>
                                <div style="height:70px;"></div>
                                <p class="fw-bold mb-0" style="color:#111;">{{ $purchase->attn ?: '-' }}</p>
                                <p class="text-muted mb-0" style="font-size:11px;">{{ $purchase->company }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3 shadow-sm border-0">
                <div class="card-body">
                    @if ($purchase->category == 'Unit' && $purchase->receipt_status == 'Pending')
                        <a class="btn btn-success d-grid w-100 mb-3 waves-effect"
                            href="{{ route('unit-product-in.goods-receipt-form', $purchase->id) }}">
                            Terima Barang (Unit)
                        </a>
                    @endif
                    <a class="btn btn-primary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="{{ route('purchase.show_print', $purchase->id) }}">
                        Download / Print
                    </a>
                    <a class="btn btn-label-info d-grid w-100 mb-3 waves-effect"
                        href="{{ route('purchase.edit', $purchase->id) }}">
                        Edit PO
                    </a>
                    <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-purchase mb-3"
                        data-id="{{ $purchase->id }}">Delete PO</a>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    @if ($totalPph > 0)
                        <a href="#" class="btn btn-danger d-grid w-100 waves-effect delete-pph mb-3"
                            data-id="{{ $purchase->id }}">Delete PPH</a>
                    @else
                        <a type="button" data-bs-toggle="modal" data-bs-target="#addPph"
                            class="d-grid w-100 waves-effect mb-3">
                            <button type="button" class="btn btn-twitter">
                                Input PPH 23
                            </button>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('components.modal.purchase.pph')
@endsection
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/dropzone/dropzone.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <style>
        .clearfix::after {
            content: "";
            display: block;
            clear: both;
        }
    </style>
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/dropzone/dropzone.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/forms-file-upload.js"></script>
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('script')
    <script>
        $('#backButton').click(function() {
            window.history.back();
        });

        $(document).on('click', '.delete-purchase', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('purchase') }}/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '{{ route('purchase.index') }}';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });

        $(document).on('click', '.delete-pph', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('purchase') }}/delete-pph/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'PATCH',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/purchase/' + id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
