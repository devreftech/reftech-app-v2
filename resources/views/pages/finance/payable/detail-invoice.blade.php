@extends('layouts.sales.app')
@section('title', 'Detail Purchase Invoice #' . ($product->invoice ?? $product->no_product_in))
@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-2 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Account Payable / <a href="{{ route('payable.index_invoice') }}" class="text-muted text-decoration-none">Purchase Invoice</a> /</span>
                <span class="text-primary">{{ $product->invoice ?: $product->no_product_in }}</span>
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-receipt-text-outline me-1"></i> Rincian faktur pembelian barang, jatuh tempo &amp; riwayat pelunasan supplier
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('payable.index_invoice') }}" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('payable.show_receipt', $product->id) }}" class="btn btn-label-primary btn-sm">
                <i class="mdi mdi-receipt-text-check-outline me-1"></i> Payment Receipt
            </a>
            @if($product->id_supplier)
                <a href="{{ route('payable.statement', ['supplier_id' => $product->id_supplier]) }}" class="btn btn-label-info btn-sm">
                    <i class="mdi mdi-book-open-outline me-1"></i> Kartu Hutang
                </a>
            @endif
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="mdi mdi-cash-plus me-1"></i> Catat Pembayaran
            </button>
        </div>
    </div>

    @php
        $totalPaid = $product->total_paid;
        $remaining = $product->remaining_payable;
        $percentPaid = $product->total > 0 ? min(100, round(($totalPaid / $product->total) * 100)) : 0;
    @endphp

    {{-- Info Cards Grid --}}
    <div class="row g-3 mb-4">
        {{-- Left Info Card --}}
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-information-outline text-primary me-2 fs-5"></i> Informasi Faktur &amp; Vendor
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <span class="text-muted small text-uppercase fw-semibold">No. Invoice Supplier</span>
                                <h5 class="fw-bolder text-dark mb-0 mt-1">{{ $product->invoice ?: '-' }}</h5>
                                <small class="text-muted">DO: {{ $product->no_do ?: '-' }}</small>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded-3">
                                <span class="text-muted small text-uppercase fw-semibold">Tanggal Invoice</span>
                                <h5 class="fw-bolder text-dark mb-0 mt-1">
                                    {{ $product->date ? Carbon\Carbon::parse($product->date)->format('d F Y') : '-' }}
                                </h5>
                                <small class="text-muted">
                                    Jatuh Tempo: {{ $product->due_date ? Carbon\Carbon::parse($product->due_date)->format('d-m-Y') : '-' }}
                                    @if($product->due_status == 'overdue')
                                        <span class="badge bg-label-danger rounded-pill ms-1" style="font-size: 10px;">Overdue</span>
                                    @elseif($product->due_status == 'due_soon')
                                        <span class="badge bg-label-warning rounded-pill ms-1" style="font-size: 10px;">Due Soon</span>
                                    @endif
                                </small>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="p-3 border rounded-3 bg-light-subtle">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-dark"><i class="mdi mdi-domain text-primary me-1"></i> Supplier / Vendor</span>
                                    @if(strtolower($product->info ?? '') == 'import')
                                        <span class="badge bg-label-info rounded-pill"><i class="mdi mdi-airplane me-1"></i>Import</span>
                                    @else
                                        <span class="badge bg-label-primary rounded-pill"><i class="mdi mdi-map-marker-radius-outline me-1"></i>{{ $product->info ?: 'Lokal' }}</span>
                                    @endif
                                </div>
                                <h5 class="fw-bolder text-primary mb-1">{{ $product->supp->supplier ?? $product->supplier ?? '-' }}</h5>
                                <div class="text-muted small">
                                    <span>NPWP: {{ $product->supp->npwp ?? '-' }}</span> • 
                                    <span>Alamat: {{ $product->supp->address ?? '-' }}</span> • 
                                    <span>Telp: {{ $product->supp->phone ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Summary Card --}}
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8faff 0%, #edf2ff 100%);">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-calculator-variant-outline text-primary me-2 fs-5"></i> Ringkasan Pembayaran
                    </h6>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex flex-column gap-2 mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Total Faktur:</span>
                            <span class="fw-bold text-dark">Rp {{ number_format($product->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-success">Sudah Dibayar:</span>
                            <span class="fw-bold text-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</span>
                        </div>
                        <div class="border-top pt-2 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-danger">Sisa Hutang:</span>
                            <span class="fw-bolder text-danger fs-5">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>Progress Pelunasan</span>
                            <span class="fw-bold">{{ $percentPaid }}%</span>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentPaid }}%;" aria-valuenow="{{ $percentPaid }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="text-center">
                            @if ($product->accept == 1 || $remaining <= 0)
                                <span class="badge bg-label-success rounded-pill px-3 py-2 w-100 fw-semibold">
                                    <i class="mdi mdi-check-circle-outline me-1"></i> LUNAS (PAID)
                                </span>
                            @elseif ($product->accept == 2)
                                <span class="badge bg-label-warning rounded-pill px-3 py-2 w-100 fw-semibold">
                                    <i class="mdi mdi-progress-clock me-1"></i> DIBAYAR SEBAGIAN (PARTIAL)
                                </span>
                            @else
                                <span class="badge bg-label-danger rounded-pill px-3 py-2 w-100 fw-semibold">
                                    <i class="mdi mdi-clock-outline me-1"></i> BELUM DIBAYAR (UNPAID)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Pembayaran & Bukti Transfer Table --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="mdi mdi-history text-primary me-2 fs-5"></i> Riwayat Pembayaran / Cicilan &amp; Bukti Transfer
            </h6>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="mdi mdi-plus me-1"></i> Catat Pembayaran
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 140px;" class="fw-semibold text-dark">No. Bayar</th>
                        <th style="width: 110px;" class="fw-semibold text-dark">Tanggal</th>
                        <th class="fw-semibold text-dark">Akun Bank / Kas</th>
                        <th style="width: 120px;" class="fw-semibold text-dark">Metode</th>
                        <th style="width: 150px;" class="fw-semibold text-dark text-end">Nominal Bayar</th>
                        <th class="fw-semibold text-dark">Catatan</th>
                        <th style="width: 100px;" class="fw-semibold text-dark text-center">Bukti Transfer</th>
                        <th style="width: 80px;" class="fw-semibold text-dark text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $pay)
                        <tr>
                            <td class="fw-bold text-primary">{{ $pay->payment_number }}</td>
                            <td>{{ Carbon\Carbon::parse($pay->date)->format('d-m-Y') }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $pay->bank->bank ?? 'Kas/Bank' }}</div>
                                <small class="text-muted">{{ $pay->bank->no_rek ?? '-' }}</small>
                            </td>
                            <td><span class="badge bg-label-info rounded-pill px-2 py-1">{{ $pay->payment_method }}</span></td>
                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($pay->amount, 0, ',', '.') }}
                            </td>
                            <td>{{ $pay->note ?: '-' }}</td>
                            <td class="text-center">
                                @if($pay->proof_file)
                                    <a href="{{ asset('storage/' . $pay->proof_file) }}" target="_blank" class="btn btn-xs btn-label-info" data-bs-toggle="tooltip" title="Lihat Bukti Transfer">
                                        <i class="mdi mdi-file-image-outline me-1"></i> Bukti
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-xs btn-label-danger btn-delete-payment" data-id="{{ $pay->id }}" data-amount="{{ number_format($pay->amount, 0, ',', '.') }}" data-bs-toggle="tooltip" title="Hapus Pembayaran">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-3 text-muted">
                                Belum ada transaksi cicilan pembayaran yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail Items Table --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="mdi mdi-package-variant-closed text-primary me-2 fs-5"></i> Rincian Barang Masuk (Product In)
            </h6>
            <span class="badge bg-label-secondary">{{ count($detProduct) }} Items</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center fw-semibold text-dark">#</th>
                        <th class="fw-semibold text-dark">Nama Barang &amp; Deskripsi</th>
                        <th style="width: 120px;" class="fw-semibold text-center text-dark">Qty</th>
                        <th style="width: 160px;" class="fw-semibold text-end text-dark">Harga Modal</th>
                        <th style="width: 160px;" class="fw-semibold text-end text-dark">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp
                    @foreach ($detProduct as $products)
                        @php $no++; @endphp
                        <tr>
                            <td class="text-center align-top text-muted pt-3">{{ $no }}</td>
                            <td class="align-top pt-3">
                                <div class="fw-bold text-dark mb-1">{{ $products->detailProduct?->replacement }}</div>
                                @if($products->detailProduct?->product?->description)
                                    <div class="text-secondary" style="font-size: 12px; line-height: 1.4; white-space: pre-wrap;">{{ $products->detailProduct->product->description }}</div>
                                @endif
                            </td>
                            <td class="text-center align-top fw-semibold text-dark pt-3">
                                {{ $products->qty }} {{ $products->detailProduct?->product?->unit }}
                            </td>
                            <td class="text-end align-top text-dark pt-3">
                                Rp {{ number_format($products->modal, 0, ',', '.') }}
                            </td>
                            <td class="text-end align-top fw-bold text-dark pt-3">
                                Rp {{ number_format($products->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="table-light fw-bold">
                        <td colspan="4" class="text-end text-dark">Total Pembelian:</td>
                        <td class="text-end text-primary fs-6">Rp {{ number_format($product->total, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Retur Barang Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="mdi mdi-keyboard-return text-primary me-2 fs-5"></i> Retur Barang Pembelian
            </h6>
            @if ($product->accept == 0)
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#productReturn">
                    <i class="mdi mdi-plus me-1"></i> Retur Barang
                </button>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center fw-semibold text-dark">#</th>
                        <th class="fw-semibold text-dark">Item Barang</th>
                        <th style="width: 100px;" class="text-center fw-semibold text-dark">Qty</th>
                        <th class="fw-semibold text-dark">Alasan / Catatan</th>
                        <th style="width: 150px;" class="text-center fw-semibold text-dark">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp
                    @forelse ($return as $retur)
                        @php $no++; @endphp
                        <tr>
                            <td class="text-center align-top text-muted pt-3">{{ $no }}</td>
                            <td class="align-top pt-3">
                                <div class="fw-bold text-dark">{{ $retur->replacement?->replacement }}</div>
                            </td>
                            <td class="text-center align-top fw-semibold text-dark pt-3">
                                {{ $retur->qty }} {{ $retur->replacement?->product?->unit }}
                            </td>
                            <td class="align-top pt-3">{{ $retur->note ?: '-' }}</td>
                            <td class="text-center align-top pt-3">
                                @if ($retur->status == 0)
                                    <button type="button" class="btn btn-xs btn-primary clear-return" data-id="{{ $retur->id }}">
                                        Clear Return
                                    </button>
                                @else
                                    <span class="badge bg-label-success rounded-pill px-2 py-1">Sudah Clear</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-3 text-muted">Tidak ada retur pada invoice ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Record Payment --}}
    <div class="modal fade" id="recordPaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('payable.store_payment', $product->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="mdi mdi-cash-plus text-primary me-2"></i> Catat Pembayaran Vendor
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-primary p-3 mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="small">Total Faktur: <strong>Rp {{ number_format($product->total, 0, ',', '.') }}</strong></span>
                            <span class="small">Sisa Hutang: <strong class="text-danger">Rp {{ number_format($remaining, 0, ',', '.') }}</strong></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Pilih Akun Bank / Kas (Auto Potong Saldo) <span class="text-danger">*</span></label>
                        <select name="id_bank" class="form-select" required>
                            <option value="">-- Pilih Rekening Bank / Kas --</option>
                            @foreach ($banks as $b)
                                <option value="{{ $b->id }}">
                                    {{ $b->bank }} ({{ $b->no_rek }}) - Saldo: Rp {{ number_format($b->saldo, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Tanggal Bayar <span class="text-danger">*</span></label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small">Metode Pembayaran</label>
                            <select name="payment_method" class="form-select">
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Giro / Cek">Giro / Cek</option>
                                <option value="Tunai / Kas">Tunai / Kas</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nominal Bayar (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control fw-bold text-primary" value="{{ $remaining > 0 ? $remaining : $product->total }}" min="1" max="{{ $remaining > 0 ? $remaining : $product->total }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Upload Bukti Transfer Vendor (Image / PDF)</label>
                        <input type="file" name="proof_file" class="form-control" accept="image/*,application/pdf">
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small">Catatan / Memo</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Contoh: DP 50%, Pelunasan tahap 2, dll."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check me-1"></i> Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('components.modal.payable.return')
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
        // Trigger Delete Payment (Restore Bank Balance)
        $(document).on('click', '.btn-delete-payment', function() {
            var id = $(this).data('id');
            var amount = $(this).data('amount');
            Swal.fire({
                title: "Hapus Pembayaran?",
                text: "Saldo bank sebesar Rp " + amount + " akan dikembalikan secara otomatis.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '/payable/payment/' + id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: "success",
                                title: "Dihapus!",
                                text: response.message || "Data pembayaran berhasil dihapus & saldo bank dikembalikan.",
                                customClass: {
                                    confirmButton: "btn btn-success waves-effect",
                                },
                            });
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan saat menghapus data!'
                            });
                        }
                    });
                }
            });
        });

        $(document).on('click', '.clear-return', function() {
            var id = $(this).data('id');
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
                        url: '{{ url('product-in') }}/clear-return/' + id,
                        type: 'POST',
                        data: {
                            _method: 'POST',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Accepted!",
                                    text: "Your file has been Accepted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                });
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Accept!'
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
