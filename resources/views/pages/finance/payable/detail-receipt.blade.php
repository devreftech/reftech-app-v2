@extends('layouts.sales.app')
@section('title', 'Detail Purchase Payment #' . $receipt)
@section('content')
    {{-- Page Breadcrumb & Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Account Payable / <a href="{{ route('payable.index_receipt') }}" class="text-muted text-decoration-none">Purchase Payment</a> /</span>
                <span class="text-primary">{{ $receipt }}</span>
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-receipt-text-check-outline me-1"></i> Rincian bukti pembayaran faktur pembelian barang & status verifikasi supplier
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('payable.index_receipt') }}" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('payable.show_invoice', $product->id) }}" class="btn btn-label-primary btn-sm">
                <i class="mdi mdi-file-document-outline me-1"></i> Lihat Invoice
            </a>
            @if ($product->accept == 0)
                <button type="button" class="btn btn-label-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDate">
                    <i class="mdi mdi-calendar-edit me-1"></i> Ubah Tanggal
                </button>
                <button type="button" class="btn btn-label-info btn-sm" data-bs-toggle="modal" data-bs-target="#addPPH">
                    <i class="mdi mdi-calculator-variant-outline me-1"></i> {{ $product->pph > 0 ? 'Edit' : 'Tambah' }} PPH
                </button>
                <button type="button" class="btn btn-success btn-sm accept-product" data-id="{{ $product->id }}">
                    <i class="mdi mdi-check-decagram me-1"></i> Konfirmasi Pembayaran (Set PAID)
                </button>
            @else
                <button type="button" class="btn btn-label-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDate">
                    <i class="mdi mdi-calendar-edit me-1"></i> Ubah Tanggal
                </button>
                <button type="button" class="btn btn-label-danger btn-sm unconfirm-product" data-id="{{ $product->id }}">
                    <i class="mdi mdi-close-circle-outline me-1"></i> Batalkan Konfirmasi Lunas (Set UNPAID)
                </button>
            @endif
        </div>
    </div>

    {{-- Main Voucher Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            {{-- Voucher Top Banner --}}
            <div class="row align-items-center justify-content-between pb-3 mb-4 border-bottom">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-label-primary rounded p-2 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="mdi mdi-receipt-text-check fs-3"></i>
                        </div>
                        <div>
                            <span class="badge bg-label-primary text-uppercase px-2 py-1 mb-1 fw-bold" style="letter-spacing: .5px; font-size: 11px;">
                                Payment Voucher
                            </span>
                            <h4 class="fw-bolder mb-0 text-dark">PURCHASE PAYMENT RECEIPT</h4>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <div class="d-inline-flex flex-column align-items-md-end">
                        <span class="text-muted small mb-1">Payment Receipt No.</span>
                        <h3 class="fw-bolder text-primary mb-1">{{ $receipt }}</h3>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">
                                <i class="mdi mdi-calendar-outline me-1"></i>{{ Carbon\Carbon::parse($product->date_payment ?? $product->date)->format('d F Y') }}
                            </span>
                            <span>•</span>
                            @if ($product->accept == 0)
                                <span class="badge bg-label-danger rounded-pill px-3 py-1 fw-semibold">
                                    <i class="mdi mdi-clock-outline me-1"></i>UNPAID
                                </span>
                            @else
                                <span class="badge bg-label-success rounded-pill px-3 py-1 fw-semibold">
                                    <i class="mdi mdi-check-circle-outline me-1"></i>PAID
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Voucher Info 2-Column Grid --}}
            <div class="row g-3 mb-4">
                {{-- Supplier Info --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100 border bg-light-subtle shadow-none">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                <i class="mdi mdi-domain text-primary me-2 fs-5"></i> Informasi Supplier / Vendor
                            </h6>
                            <div class="d-flex flex-column gap-2">
                                <div class="row">
                                    <div class="col-4 text-muted small">Nama Supplier</div>
                                    <div class="col-8 fw-bold text-dark">
                                        {{ $product->supp->supplier ?? $product->supplier ?? '-' }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 text-muted small">NPWP</div>
                                    <div class="col-8 text-dark">
                                        {{ $product->supp->npwp ?? '-' }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-4 text-muted small">Kategori / Info</div>
                                    <div class="col-8">
                                        @if(strtolower($product->info ?? '') == 'import')
                                            <span class="badge bg-label-info rounded-pill px-2 py-1">
                                                <i class="mdi mdi-airplane me-1"></i>Import
                                            </span>
                                        @else
                                            <span class="badge bg-label-primary rounded-pill px-2 py-1">
                                                <i class="mdi mdi-map-marker-radius-outline me-1"></i>{{ $product->info ?? 'Lokal' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Invoice & Transaction Reference --}}
                <div class="col-12 col-md-6">
                    <div class="card h-100 border bg-light-subtle shadow-none">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-dark mb-3 d-flex align-items-center">
                                <i class="mdi mdi-file-document-outline text-primary me-2 fs-5"></i> Referensi Faktur Pembelian
                            </h6>
                            <div class="d-flex flex-column gap-2">
                                <div class="row">
                                    <div class="col-5 text-muted small">No. Invoice Supplier</div>
                                    <div class="col-7">
                                        <a href="{{ route('payable.show_invoice', $product->id) }}" class="fw-bold text-primary text-decoration-none">
                                            <i class="mdi mdi-link-variant me-1"></i>{{ $product->invoice ?? '-' }}
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 text-muted small">Tanggal Invoice</div>
                                    <div class="col-7 text-dark">
                                        {{ $product->date ? Carbon\Carbon::parse($product->date)->format('d-m-Y') : '-' }}
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-5 text-muted small">Tanggal Pelunasan</div>
                                    <div class="col-7 text-dark">
                                        {{ $product->date_payment ? Carbon\Carbon::parse($product->date_payment)->format('d-m-Y') : 'Belum ditentukan' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Unpaid / Paid Notice Bar with Inline Actions --}}
            @if ($product->accept == 0)
                <div class="alert alert-warning border-warning d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3 mb-4 rounded-3 gap-2" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-alert-circle-outline fs-4 text-warning"></i>
                        <div>
                            <span class="fw-bold text-dark d-block">Status: Menunggu Konfirmasi Pelunasan</span>
                            <small class="text-muted">Periksa kembali rincian barang, tanggal pembayaran, dan potongan PPH sebelum melakukan konfirmasi.</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDate">
                            <i class="mdi mdi-calendar-edit me-1"></i> Tanggal
                        </button>
                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#addPPH">
                            <i class="mdi mdi-calculator-variant-outline me-1"></i> PPH
                        </button>
                        <button type="button" class="btn btn-success btn-sm accept-product" data-id="{{ $product->id }}">
                            <i class="mdi mdi-check-circle me-1"></i> Konfirmasi Lunas
                        </button>
                    </div>
                </div>
            @else
                <div class="alert alert-success border-success d-flex flex-column flex-md-row align-items-md-center justify-content-between p-3 mb-4 rounded-3 gap-2" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-check-decagram fs-4 text-success"></i>
                        <div>
                            <span class="fw-bold text-dark d-block">Pembayaran Telah Dikonfirmasi Lunas (PAID)</span>
                            <small class="text-muted">Transaksi pembayaran pembelian ini telah selesai diverifikasi dan masuk dalam kas pengeluaran.</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" class="btn btn-label-danger btn-sm unconfirm-product" data-id="{{ $product->id }}">
                            <i class="mdi mdi-undo me-1"></i> Batalkan Konfirmasi Lunas
                        </button>
                    </div>
                </div>
            @endif

            {{-- Items Breakdown Table Header --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center">
                    <i class="mdi mdi-package-variant-closed text-primary me-2 fs-5"></i> Rincian Barang Pembelian
                </h5>
                <span class="badge bg-label-secondary">{{ count($detProduct) }} Items</span>
            </div>

            {{-- Items Table --}}
            <div class="table-responsive border rounded-3 mb-4">
                <table class="table table-hover mb-0" style="font-size: 13px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;" class="fw-semibold text-center text-dark">#</th>
                            <th class="fw-semibold text-dark">Nama Barang &amp; Deskripsi</th>
                            <th style="width: 120px;" class="fw-semibold text-center text-dark">Qty</th>
                            <th style="width: 180px;" class="fw-semibold text-end text-dark">Harga Satuan (Modal)</th>
                            @if ($product->pph > 0)
                                <th style="width: 150px;" class="fw-semibold text-end text-dark">PPH</th>
                            @endif
                            <th style="width: 180px;" class="fw-semibold text-end text-dark">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $no = 0; @endphp
                        @forelse ($detProduct as $item)
                            @php $no++; @endphp
                            <tr>
                                <td class="text-center align-top text-muted pt-3">{{ $no }}</td>
                                <td class="align-top pt-3">
                                    <div class="fw-bold text-dark mb-1" style="font-size: 13px;">
                                        {{ $item->detailProduct?->replacement ?? '-' }}
                                    </div>
                                    @if ($item->detailProduct?->product?->description)
                                        <div class="text-secondary" style="font-size: 13px; line-height: 1.5; white-space: pre-wrap;">{{ $item->detailProduct->product->description }}</div>
                                    @endif
                                </td>
                                <td class="text-center align-top fw-semibold text-dark pt-3" style="font-size: 13px;">
                                    {{ $item->qty }} {{ $item->detailProduct?->product?->unit ?? '' }}
                                </td>
                                <td class="text-end align-top text-dark pt-3" style="font-size: 13px;">
                                    Rp {{ number_format($item->modal, 0, ',', '.') }}
                                </td>
                                @if ($product->pph > 0)
                                    <td class="text-end align-top text-danger pt-3" style="font-size: 13px;">
                                        Rp {{ number_format($product->pph, 0, ',', '.') }}
                                    </td>
                                @endif
                                <td class="text-end align-top fw-bold text-dark pt-3" style="font-size: 13px;">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $product->pph > 0 ? 6 : 5 }}" class="text-center py-4 text-muted">
                                    Tidak ada item produk terlampir.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Calculation Summary Footer --}}
            <div class="row justify-content-end">
                <div class="col-12 col-md-5">
                    <div class="card border bg-light-subtle shadow-none">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal Barang:</span>
                                <span class="fw-semibold text-dark">Rp {{ number_format($detProduct->sum('amount'), 0, ',', '.') }}</span>
                            </div>
                            @if ($product->pph > 0)
                                <div class="d-flex justify-content-between mb-2 text-danger">
                                    <span>Potongan PPH:</span>
                                    <span class="fw-semibold">- Rp {{ number_format($product->pph, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="border-top pt-2 mt-2 d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark fs-6">Grand Total:</span>
                                <span class="fw-bolder text-primary fs-4">Rp {{ number_format($product->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.modal.payable.pph')
    @include('components.modal.payable.date')
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
@endpush

@push('script')
    <script>
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        $(".invoice-item-pph-label").on('keyup', function() {
            var input = $(this);
            var input_val = input.val();
            input_val = formatNumber(input_val);
            input.val(input_val);
            var nomorInt = parseFloat(input_val.replace(/[.,]/g, '')) || 0;
            $(`#pph`).val(nomorInt);
        });

        // Trigger Konfirmasi Lunas (PAID)
        $(document).on('click', '.accept-product', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Konfirmasi Pelunasan?",
                text: "Status pembayaran ini akan diubah menjadi PAID (Lunas).",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Konfirmasi Lunas!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-success me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '/payable/receipt/' + id + '/confirm',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Berhasil!",
                                text: response.message || "Pembayaran telah berhasil dikonfirmasi (PAID).",
                                customClass: {
                                    confirmButton: "btn btn-success waves-effect",
                                },
                            });
                            setTimeout(function() {
                                window.location.reload();
                            }, 1200);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat memproses data!'
                            });
                        }
                    });
                }
            });
        });

        // Trigger Batalkan Konfirmasi (UNPAID)
        $(document).on('click', '.unconfirm-product', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Batalkan Konfirmasi Lunas?",
                text: "Status pembayaran akan dikembalikan menjadi UNPAID (Belum Lunas).",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Batalkan Lunas!",
                cancelButtonText: "Kembali",
                customClass: {
                    confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '/payable/receipt/' + id + '/unconfirm',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Dibatalkan!",
                                text: response.message || "Status berhasil dikembalikan menjadi UNPAID.",
                                customClass: {
                                    confirmButton: "btn btn-success waves-effect",
                                },
                            });
                            setTimeout(function() {
                                window.location.reload();
                            }, 1200);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat memproses data!'
                            });
                        }
                    });
                }
            });
        });
    </script>
@endpush
