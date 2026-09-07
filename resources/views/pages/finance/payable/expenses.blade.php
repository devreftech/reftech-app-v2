@extends('layouts.sales.app')
@section('title', 'Biaya Operasional & Proyek AP')
@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Account Payable /</span> Biaya Operasional Proyek
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-cash-multiple me-1"></i> Kelola &amp; bayar pengeluaran non-inventory terkait proyek (akomodasi, transport, jasa teknisi, konsumsi)
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('payable.index_receipt') }}" class="btn btn-label-primary btn-sm">
                <i class="mdi mdi-receipt-text-check-outline me-1"></i> Purchase Payment
            </a>
            <a href="{{ route('payable.index_invoice') }}" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-file-document-outline me-1"></i> Purchase Invoice
            </a>
        </div>
    </div>

    {{-- Metric Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f8f9ff 0%, #edf0ff 100%); border-left: 5px solid #696cff !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-primary small" style="letter-spacing: .5px;">
                            <i class="mdi mdi-cash-multiple me-1"></i> Total Biaya Proyek
                        </span>
                        <span class="badge bg-label-primary rounded-pill px-2 py-1">{{ count($expenses) }} Items</span>
                    </div>
                    <div class="fw-bolder text-primary fs-4 mb-0">
                        Rp {{ number_format($totalAmount ?? 0, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 11px;">Akumulasi seluruh pengeluaran operasional</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f3fdf6 0%, #e8f9ee 100%); border-left: 5px solid #28a745 !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-success small" style="letter-spacing: .5px;">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Terbayar (Paid)
                        </span>
                    </div>
                    <div class="fw-bolder text-success fs-4 mb-0">
                        Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 11px;">Biaya yang sudah dibayarkan via kas/bank</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fff8f8 0%, #ffeded 100%); border-left: 5px solid #ff3e1d !important;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-uppercase fw-bold text-danger small" style="letter-spacing: .5px;">
                            <i class="mdi mdi-clock-alert-outline me-1"></i> Belum Dibayar (Pending AP)
                        </span>
                    </div>
                    <div class="fw-bolder text-danger fs-4 mb-0">
                        Rp {{ number_format($totalUnpaid ?? 0, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 11px;">Kewajiban biaya proyek yang menunggu pembayaran</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Expense List Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="mdi mdi-format-list-bulleted me-2 text-primary fs-5"></i> Daftar Pengeluaran Biaya Proyek
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 100px;" class="fw-semibold text-dark">Tanggal</th>
                        <th class="fw-semibold text-dark">Nama Biaya &amp; Proyek</th>
                        <th style="width: 130px;" class="fw-semibold text-dark text-center">Kategori</th>
                        <th style="width: 130px;" class="fw-semibold text-dark">Pengaju / User</th>
                        <th style="width: 150px;" class="fw-semibold text-dark text-end">Nominal Biaya</th>
                        <th style="width: 150px;" class="fw-semibold text-dark text-end">Terbayar</th>
                        <th style="width: 120px;" class="fw-semibold text-dark text-center">Status</th>
                        <th style="width: 130px;" class="fw-semibold text-dark text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $item)
                        @php
                            $paidAmount = $item->payments->sum('amount');
                            $isPaid = $paidAmount >= $item->amount && $item->amount > 0;
                            $isPartial = $paidAmount > 0 && $paidAmount < $item->amount;
                        @endphp
                        <tr>
                            <td>{{ $item->date ? Carbon\Carbon::parse($item->date)->format('d-m-Y') : '-' }}</td>
                            <td>
                                <div class="fw-bold text-dark mb-1">{{ $item->name }}</div>
                                @if($item->pending)
                                    <small class="text-muted d-block">
                                        <i class="mdi mdi-briefcase-outline me-1"></i>Project: {{ $item->pending->project ?? ('PO #' . $item->pending->id) }}
                                    </small>
                                @endif
                                @if($item->payment_info)
                                    <small class="text-info d-block">
                                        <i class="mdi mdi-bank me-1"></i>Rek. Tujuan: {{ $item->payment_info }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-label-info rounded-pill px-2 py-1">{{ $item->category ?? 'Operasional' }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $item->user->name ?? '-' }}</span>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                Rp {{ number_format($item->amount, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-semibold text-success">
                                Rp {{ number_format($paidAmount, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if ($isPaid)
                                    <span class="badge bg-label-success rounded-pill px-2 py-1"><i class="mdi mdi-check-circle me-1"></i>Paid</span>
                                @elseif ($isPartial)
                                    <span class="badge bg-label-warning rounded-pill px-2 py-1"><i class="mdi mdi-progress-clock me-1"></i>Partial</span>
                                @else
                                    <span class="badge bg-label-danger rounded-pill px-2 py-1"><i class="mdi mdi-clock-outline me-1"></i>Unpaid</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if (!$isPaid)
                                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#payExpenseModal{{ $item->id }}">
                                        <i class="mdi mdi-cash-check me-1"></i> Bayar
                                    </button>
                                @else
                                    <span class="badge bg-label-success rounded-pill px-2.5 py-1">
                                        <i class="mdi mdi-check-circle-outline me-1"></i> Lunas
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Tidak ada data pengeluaran proyek yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modals Pay Expense (Rendered outside table-responsive) --}}
    @foreach ($expenses as $item)
        @php
            $paidAmount = $item->payments->sum('amount');
            $remaining = max(0, $item->amount - $paidAmount);
        @endphp
        @if ($remaining > 0)
            <div class="modal fade animate__animated fadeIn" id="payExpenseModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('payable.pay_expense', $item->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header bg-primary text-white py-3 px-4">
                                <h5 class="modal-title text-white d-flex align-items-center gap-2">
                                    <i class="mdi mdi-cash-fast fs-4"></i> Pembayaran Biaya Proyek AP
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 text-start">
                                <div class="alert alert-primary p-3 mb-3 border-0">
                                    <div class="fw-bold text-dark fs-6 mb-1">{{ $item->name }}</div>
                                    @if($item->pending)
                                        <small class="text-muted d-block mb-1">
                                            <i class="mdi mdi-briefcase-outline me-1"></i> Proyek: <strong>{{ $item->pending->project ?? ('PO #' . $item->pending->id) }}</strong>
                                        </small>
                                    @endif
                                    @if($item->payment_info)
                                        <small class="text-primary d-block mb-1">
                                            <i class="mdi mdi-bank me-1"></i> Rekening Tujuan: <strong>{{ $item->payment_info }}</strong>
                                        </small>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                        <span class="small text-muted">Sisa Tagihan:</span>
                                        <span class="fw-bolder text-danger fs-6">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Potong dari Akun Kas &amp; Bank <span class="text-danger">*</span></label>
                                        <select name="id_bank" class="form-select" required>
                                            <option value="">-- Pilih Rekening Kas / Bank --</option>
                                            @foreach ($banks as $b)
                                                <option value="{{ $b->id }}">
                                                    [{{ strtoupper($b->entity ?? 'REFTECH') }}] {{ $b->bank }} - {{ $b->no_rek }} (Saldo: Rp {{ number_format($b->saldo, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Nominal Bayar (Rp) <span class="text-danger">*</span></label>
                                        <input type="number" name="amount" class="form-control" value="{{ $remaining }}" max="{{ $remaining }}" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Upload Bukti Transfer</label>
                                        <input type="file" name="proof_file" class="form-control" accept="image/*,application/pdf">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Catatan / Keterangan</label>
                                        <textarea name="note" class="form-control" rows="2" placeholder="Contoh: Transfer via m-Banking BCA ke teknisi / toko"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top py-2 px-4">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-check me-1"></i> Simpan &amp; Potong Saldo Bank
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection
