@extends('layouts.sales.app')
@section('title', 'Detail Return')
@section('content')
    <div class="row invoice-preview">
        {{-- Invoice --}}
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md"
                                            src="{{ url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png') }}"
                                            alt="" srcset="" width="60%">
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold">Return Product</h3>
                            <div>
                                <span class="fw-bolder">#{{ $return->no_return }}</span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted">{{ Carbon\Carbon::parse($return->date)->format('d-m-Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="row g-3">
                        @if ($unitQuote)
                            <div class="col-4 col-lg-2 fw-medium">
                                <p class="mb-1">Customer / Client</p>
                                <p class="mb-1">PIC Contact</p>
                                <p class="mb-1">Sales Person</p>
                                <p class="mb-1">No. Penawaran</p>
                            </div>
                            <div class="col-8 col-lg-4">
                                <p class="mb-1 fw-bold">: {{ $unitQuote->client?->company ?? ($unitQuote->pic?->name ?? '-') }}</p>
                                <p class="mb-1">: {{ $unitQuote->pic?->name ?? '-' }}</p>
                                <p class="mb-1">: {{ $return->sales?->name ?? ($unitQuote->sales?->name ?? '-') }}</p>
                                <p class="mb-1">: <span class="badge bg-label-primary">{{ $unitQuote->no_quote ?? '-' }}</span></p>
                            </div>
                        @elseif ($quote)
                            <div class="col-4 col-lg-2 fw-medium">
                                <p class="mb-1">Customer / Client</p>
                                <p class="mb-1">PIC Contact</p>
                                <p class="mb-1">Sales Person</p>
                                <p class="mb-1">No. Penawaran</p>
                            </div>
                            <div class="col-8 col-lg-4">
                                <p class="mb-1 fw-bold">: {{ $quote->pic?->client?->company ?? '-' }}</p>
                                <p class="mb-1">: {{ $quote->pic?->name_pic ?? '-' }}</p>
                                <p class="mb-1">: {{ $return->sales?->name ?? ($quote->sales?->name ?? '-') }}</p>
                                <p class="mb-1">: <span class="badge bg-label-primary">{{ $quote->no_quote ?? '-' }}</span></p>
                            </div>
                        @elseif (isset($productIn) && $productIn)
                            <div class="col-4 col-lg-2 fw-medium">
                                <p class="mb-1">Vendor / Supplier</p>
                                <p class="mb-1">No. Invoice</p>
                                <p class="mb-1">No. Penerimaan</p>
                            </div>
                            <div class="col-8 col-lg-4">
                                <p class="mb-1 fw-bold">: {{ $productIn->supplier?->nama_supplier ?? '-' }}</p>
                                <p class="mb-1">: {{ $productIn->invoice ?? '-' }}</p>
                                <p class="mb-1">: <span class="badge bg-label-info">{{ $productIn->no_product_in ?? '-' }}</span></p>
                            </div>
                        @else
                            <div class="col-4 col-lg-2 fw-medium">
                                <p class="mb-1">Tipe Dokumen</p>
                            </div>
                            <div class="col-8 col-lg-4">
                                <p class="mb-1 text-muted">: Dokumen Retur Standar (#{{ $return->no_return }})</p>
                            </div>
                        @endif

                        {{-- Resolution & Reason Info --}}
                        @if ($return->resolution || $return->reason_category)
                            <div class="col-12 col-lg-6">
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold font-12 text-dark">Solusi yang Diajukan:</span>
                                        @if($return->resolution === 'replacement')
                                            <span class="badge bg-primary"><i class="mdi mdi-swap-horizontal me-1"></i>Ganti Barang (Replacement)</span>
                                        @elseif($return->resolution === 'refund')
                                            <span class="badge bg-success"><i class="mdi mdi-cash-refund me-1"></i>Refund Dana (Pengembalian Uang)</span>
                                        @elseif($return->resolution === 'deposit')
                                            <span class="badge bg-info"><i class="mdi mdi-credit-card-plus-outline me-1"></i>Potong Tagihan / Deposit</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $return->resolution ?? '-' }}</span>
                                        @endif
                                    </div>
                                    @if($return->reason_category)
                                        <div class="font-12 mb-1">
                                            <span class="text-muted">Alasan:</span>
                                            <strong class="text-dark">{{ ucfirst(str_replace('_', ' ', $return->reason_category)) }}</strong>
                                        </div>
                                    @endif
                                    @if($return->reason_note)
                                        <div class="font-11 text-muted fst-italic mb-2">
                                            "{{ $return->reason_note }}"
                                        </div>
                                    @endif
                                    @if($return->resolution === 'refund' && ($return->bank_name || $return->bank_account))
                                        <div class="pt-2 border-top font-11">
                                            <span class="fw-bold text-success"><i class="mdi mdi-bank-outline me-1"></i>Rekening Refund:</span>
                                            {{ $return->bank_name }} - {{ $return->bank_account }} a/n {{ $return->bank_holder }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table m-0 mb-4">
                        <thead class="table-light border-top">
                            <tr>
                                <th style="width: 40px;">No.</th>
                                <th>Item &amp; Deskripsi</th>
                                <th style="width: 80px;" class="text-center">Qty</th>
                                <th style="width: 130px;" class="text-end">Harga Satuan</th>
                                <th style="width: 140px;" class="text-end">Subtotal</th>
                                <th>Catatan Item</th>
                                <th style="width: 120px;" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 0;
                            @endphp
                            @foreach ($dReturn as $product)
                                @php
                                    $no++;
                                    $displayName = $product->item_name ?? ($product->replacement?->replacement ?? ('Item #' . $product->id));
                                    $subtotal = $product->amount > 0 ? $product->amount : ($product->qty * $product->price);
                                @endphp
                                <tr style="font-size: 13px">
                                    <td class="align-top">{{ $no }}</td>
                                    <td class="align-top">
                                        <p class="mb-0 fw-semibold" style="font-size: 12px">
                                            {{ $displayName }}
                                        </p>
                                        @if ($product->replacement?->product?->commodity && $product->replacement?->product?->commodity !== $displayName)
                                            <small class="text-muted d-block">{{ $product->replacement->product->commodity }}</small>
                                        @endif
                                    </td>
                                    <td class="align-top text-center fw-bold">{{ $product->qty }}</td>
                                    <td class="align-top text-end font-monospace">
                                        {{ $product->price > 0 ? 'Rp ' . number_format($product->price, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="align-top text-end font-monospace fw-bold text-danger">
                                        {{ $subtotal > 0 ? 'Rp ' . number_format($subtotal, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="align-top font-12 text-muted">{{ $product->note ?? '-' }}</td>
                                    <td class="align-top text-center">
                                        @if ($return->status == 0)
                                            @if ($product->status == 0)
                                                <a href="#"
                                                    class="btn btn-primary btn-sm d-grid w-100 waves-effect accept-return mb-1"
                                                    data-id="{{ $product->id }}" data-return="{{ $return->id }}">
                                                    Accept
                                                </a>
                                            @else
                                                <span class="badge bg-label-success">Accepted</span>
                                            @endif
                                        @else
                                            @if ($product->status == 0)
                                                <span class="badge bg-label-secondary">Not Accepted</span>
                                            @else
                                                <span class="badge bg-success">Done</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- End: Invoice --}}
        {{-- Button Invocie --}}
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body">
                    @if ($return->status == 0)
                        <a class="btn btn-primary d-grid w-100 mb-3 waves-effect"
                            href="{{ route('product-in.return', $return->id) }}">
                            Cetak Product In
                        </a>
                        <a href="#" class="btn btn-outline-danger d-grid w-100 mb-3 waves-effect delete-invoice"
                            data-id="{{ $return->id }}">Delete</a>
                    @endif
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
        </div>
        {{-- End : Button Invoice --}}
    </div>

@endsection
@push('after-style')
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush
@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush
@push('page-script')
    <script src="{{ asset('assets') }}/js/extended-ui-sweetalert2.js"></script>
@endpush
@push('script')
    <script>
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        $('#backButton').click(function() {
            window.history.back();
        });

        $(".invoice-item-modal-label").on('keyup', function() {
            var input = $(this)
            var id = input.data('id');
            var input_val = input.val();

            // original length
            var original_len = input_val.length;

            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // send updated string to input
            input.val(input_val);
            var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
            // console.log(id);
            $(`#modal-${id}`).val(nomorInt);
            var modal = $(`#modal-${id}`).val();
            console.log(modal);
        });

        $(document).on('click', '.accept-return', function() {
            var id = $(this).data('id');
            var idreturn = $(this).data('return');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Accept it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '{{ url('accept') }}/return/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Accepted!",
                                    text: "Your file has been accepted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/return/' + idreturn;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Accept!'
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
    </script>
@endpush
