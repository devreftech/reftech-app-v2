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
                <i class="mdi mdi-receipt-text-check-outline me-1"></i> Rincian bukti pembayaran faktur pembelian, cicilan vendor &amp; saldo bank
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('payable.index_receipt') }}" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
            <a href="{{ route('payable.show_invoice', $product->id) }}" class="btn btn-label-primary btn-sm">
                <i class="mdi mdi-file-document-outline me-1"></i> Lihat Invoice
            </a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                <i class="mdi mdi-cash-plus me-1"></i> Catat Pembayaran / Cicilan
            </button>
            @if ($product->accept == 0)
                <button type="button" class="btn btn-label-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDate">
                    <i class="mdi mdi-calendar-edit me-1"></i> Ubah Tanggal
                </button>
                <button type="button" class="btn btn-label-info btn-sm" data-bs-toggle="modal" data-bs-target="#addPPH">
                    <i class="mdi mdi-calculator-variant-outline me-1"></i> {{ $product->pph > 0 ? 'Edit' : 'Tambah' }} PPH
                </button>
                <button type="button" class="btn btn-success btn-sm accept-product" data-id="{{ $product->id }}">
                    <i class="mdi mdi-check-decagram me-1"></i> Set Lunas (PAID)
                </button>
            @else
                <button type="button" class="btn btn-label-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDate">
                    <i class="mdi mdi-calendar-edit me-1"></i> Ubah Tanggal
                </button>
                <button type="button" class="btn btn-label-danger btn-sm unconfirm-product" data-id="{{ $product->id }}">
                    <i class="mdi mdi-close-circle-outline me-1"></i> Batalkan Lunas (Set UNPAID)
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
                            @if ($product->accept == 1)
                                <span class="badge bg-label-success rounded-pill px-3 py-1 fw-semibold">
                                    <i class="mdi mdi-check-circle-outline me-1"></i>PAID
                                </span>
                            @elseif ($product->accept == 2)
                                <span class="badge bg-label-warning rounded-pill px-3 py-1 fw-semibold">
                                    <i class="mdi mdi-progress-clock me-1"></i>PARTIAL
                                </span>
                            @else
                                <span class="badge bg-label-danger rounded-pill px-3 py-1 fw-semibold">
                                    <i class="mdi mdi-clock-outline me-1"></i>UNPAID
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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                                    <i class="mdi mdi-domain text-primary me-2 fs-5"></i> Informasi Supplier / Vendor
                                </h6>
                                @if($product->id_supplier)
                                    <a href="{{ route('payable.statement', ['supplier_id' => $product->id_supplier]) }}" class="btn btn-xs btn-label-primary">
                                        <i class="mdi mdi-book-open-outline me-1"></i> Lihat Kartu Hutang
                                    </a>
                                @endif
                            </div>
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
                                    <div class="col-5 text-muted small">Jatuh Tempo (Due Date)</div>
                                    <div class="col-7 text-dark">
                                        {{ $product->due_date ? Carbon\Carbon::parse($product->due_date)->format('d-m-Y') : '-' }}
                                        @if($product->due_status == 'overdue')
                                            <span class="badge bg-label-danger rounded-pill ms-1" style="font-size: 10px;">Overdue</span>
                                        @elseif($product->due_status == 'due_soon')
                                            <span class="badge bg-label-warning rounded-pill ms-1" style="font-size: 10px;">Due Soon</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Progress & Outstanding Card (Point 1 & 3) --}}
            @php
                $totalPaid = $product->total_paid;
                $remaining = $product->remaining_payable;
                $percentPaid = $product->total > 0 ? min(100, round(($totalPaid / $product->total) * 100)) : 0;
            @endphp
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f8faff 0%, #eef3ff 100%);">
                <div class="card-body p-3">
                    <div class="row align-items-center g-3">
                        <div class="col-12 col-md-4 border-end">
                            <span class="text-muted small text-uppercase fw-semibold">Total Tagihan (Invoice)</span>
                            <h4 class="fw-bolder text-dark mb-0">Rp {{ number_format($product->total, 0, ',', '.') }}</h4>
                        </div>
                        <div class="col-12 col-md-4 border-end">
                            <span class="text-success small text-uppercase fw-semibold">Sudah Dibayar (Paid)</span>
                            <h4 class="fw-bolder text-success mb-0">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h4>
                            <small class="text-muted">{{ $percentPaid }}% Terbayar</small>
                        </div>
                        <div class="col-12 col-md-4">
                            <span class="text-danger small text-uppercase fw-semibold">Sisa Hutang (Outstanding)</span>
                            <h4 class="fw-bolder text-danger mb-0">Rp {{ number_format($remaining, 0, ',', '.') }}</h4>
                            @if($remaining > 0)
                                <button type="button" class="btn btn-xs btn-primary mt-1" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                                    <i class="mdi mdi-cash-fast me-1"></i> Bayar Sisa
                                </button>
                            @else
                                <span class="badge bg-label-success rounded-pill px-2 py-1 mt-1">Lunas Sepenuhnya</span>
                            @endif
                        </div>
                    </div>
                    {{-- Progress Bar --}}
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentPaid }}%;" aria-valuenow="{{ $percentPaid }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>

            {{-- Installment Payment History Table (Point 1, 2, 3) --}}
            <div class="card border mb-4">
                <div class="card-header bg-transparent py-3 d-flex align-items-center justify-content-between">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-history text-primary me-2 fs-5"></i> Riwayat Pembayaran &amp; Bukti Transfer Vendor
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
                                        Belum ada rincian transaksi pembayaran yang dicatat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

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

    {{-- Modal Record Payment (Point 1, 2, 3) --}}
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
                        <small class="text-muted">Masukkan nominal DP atau pelunasan bertahap.</small>
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
