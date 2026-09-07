@extends('layouts.sales.app')
@section('title', 'Kartu Hutang Supplier (Statement of Account)')
@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / Account Payable /</span> Kartu Hutang Supplier (SOA)
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-book-open-outline me-1"></i> Rekapitulasi mutasi hutang, faktur pembelian, pelunasan &amp; saldo berjalan per vendor
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($selectedSupplier)
                <a href="{{ route('payable.statement_export', ['id' => $selectedSupplier->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-label-success btn-sm">
                    <i class="mdi mdi-file-excel-outline me-1"></i> Ekspor Excel
                </a>
                <a href="{{ route('payable.statement_print', ['id' => $selectedSupplier->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn btn-primary btn-sm">
                    <i class="mdi mdi-printer me-1"></i> Cetak Kartu Hutang
                </a>
            @endif
            <a href="{{ route('payable.index_invoice') }}" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Invoice
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('payable.statement') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">Pilih Supplier / Vendor <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select select2" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($suppliers as $s)
                            <option value="{{ $s->id }}" {{ $selectedSupplierId == $s->id ? 'selected' : '' }}>
                                {{ $s->supplier }} {{ $s->code ? '(' . $s->code . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold small">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-12 col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-filter-outline me-1"></i> Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if ($selectedSupplier)
        {{-- Supplier Info & Metric Summary Cards --}}
        <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f8faff 0%, #eef2ff 100%);">
            <div class="card-body p-4">
                <div class="row align-items-center mb-3">
                    <div class="col-12 col-md-8">
                        <span class="badge bg-primary text-white text-uppercase px-2 py-1 mb-2 fw-bold" style="font-size: 11px;">
                            Statement of Account
                        </span>
                        <h4 class="fw-bolder text-dark mb-1">{{ $selectedSupplier->supplier }}</h4>
                        <div class="text-muted small d-flex flex-wrap gap-3">
                            @if($selectedSupplier->npwp)
                                <span><i class="mdi mdi-card-account-details-outline me-1"></i>NPWP: {{ $selectedSupplier->npwp }}</span>
                            @endif
                            @if($selectedSupplier->phone)
                                <span><i class="mdi mdi-phone-outline me-1"></i>{{ $selectedSupplier->phone }}</span>
                            @endif
                            @if($selectedSupplier->address)
                                <span><i class="mdi mdi-map-marker-outline me-1"></i>{{ $selectedSupplier->address }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                        <span class="text-muted small d-block">Periode Laporan</span>
                        <span class="fw-bold text-dark fs-6">
                            {{ Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ Carbon\Carbon::parse($endDate)->format('d M Y') }}
                        </span>
                    </div>
                </div>

                {{-- Metric Row --}}
                <div class="row g-3">
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm bg-white p-3">
                            <span class="text-muted small text-uppercase fw-semibold" style="font-size: 11px;">Saldo Awal</span>
                            <h5 class="fw-bolder text-dark mb-0 mt-1">Rp {{ number_format($openingBalance, 0, ',', '.') }}</h5>
                            <small class="text-muted" style="font-size: 10px;">Sebelum {{ Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm bg-white p-3">
                            <span class="text-primary small text-uppercase fw-semibold" style="font-size: 11px;">Total Pembelian (+)</span>
                            <h5 class="fw-bolder text-primary mb-0 mt-1">Rp {{ number_format($totalDebit, 0, ',', '.') }}</h5>
                            <small class="text-muted" style="font-size: 10px;">Penambahan hutang periode ini</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm bg-white p-3">
                            <span class="text-success small text-uppercase fw-semibold" style="font-size: 11px;">Total Pembayaran (-)</span>
                            <h5 class="fw-bolder text-success mb-0 mt-1">Rp {{ number_format($totalCredit, 0, ',', '.') }}</h5>
                            <small class="text-muted" style="font-size: 10px;">Pelunasan periode ini</small>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm p-3" style="background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%); border-left: 4px solid #ef4444 !important;">
                            <span class="text-danger small text-uppercase fw-bold" style="font-size: 11px;">Saldo Akhir Hutang</span>
                            <h5 class="fw-bolder text-danger mb-0 mt-1">Rp {{ number_format($endingBalance, 0, ',', '.') }}</h5>
                            <small class="text-danger" style="font-size: 10px;">Sisa kewajiban saat ini</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statement Ledger Table --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                    <i class="mdi mdi-format-list-numbered me-2 text-primary fs-5"></i> Mutasi Buku Pembantu Hutang
                </h6>
                <span class="badge bg-label-secondary">{{ count($transactions) }} Transaksi</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 13px;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;" class="fw-semibold text-dark">Tanggal</th>
                            <th style="width: 110px;" class="fw-semibold text-dark text-center">Tipe</th>
                            <th style="width: 180px;" class="fw-semibold text-dark">No. Referensi</th>
                            <th class="fw-semibold text-dark">Deskripsi / Keterangan</th>
                            <th style="width: 150px;" class="fw-semibold text-dark text-end">Pembelian (Debit)</th>
                            <th style="width: 150px;" class="fw-semibold text-dark text-end">Pembayaran (Kredit)</th>
                            <th style="width: 160px;" class="fw-semibold text-dark text-end">Saldo Hutang</th>
                            <th style="width: 80px;" class="fw-semibold text-dark text-center">Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Row Saldo Awal --}}
                        <tr class="table-light">
                            <td class="fw-bold">{{ Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</td>
                            <td class="text-center"><span class="badge bg-label-secondary rounded-pill px-2 py-1">SALDO AWAL</span></td>
                            <td class="text-muted">-</td>
                            <td class="fw-semibold text-dark">Saldo Awal per {{ Carbon\Carbon::parse($startDate)->format('d F Y') }}</td>
                            <td class="text-end text-muted">-</td>
                            <td class="text-end text-muted">-</td>
                            <td class="text-end fw-bolder text-dark">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
                            <td class="text-center text-muted">-</td>
                        </tr>

                        @forelse ($transactions as $t)
                            <tr>
                                <td>{{ Carbon\Carbon::parse($t->date)->format('d-m-Y') }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $t->badge_class }} rounded-pill px-2 py-1" style="font-size: 10px;">
                                        {{ $t->type }}
                                    </span>
                                </td>
                                <td>
                                    @if($t->link)
                                        <a href="{{ $t->link }}" class="fw-bold text-primary text-decoration-none">
                                            {{ $t->ref }}
                                        </a>
                                    @else
                                        <span class="fw-semibold text-dark">{{ $t->ref }}</span>
                                    @endif
                                </td>
                                <td>{{ $t->description }}</td>
                                <td class="text-end fw-semibold text-primary">
                                    {{ $t->debit > 0 ? 'Rp ' . number_format($t->debit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end fw-semibold text-success">
                                    {{ $t->credit > 0 ? 'Rp ' . number_format($t->credit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end fw-bold {{ $t->balance > 0 ? 'text-dark' : 'text-success' }}">
                                    Rp {{ number_format($t->balance, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    @if(!empty($t->proof_file))
                                        <a href="{{ asset('storage/' . $t->proof_file) }}" target="_blank" class="btn btn-sm btn-icon btn-label-info" data-bs-toggle="tooltip" title="Lihat Bukti Transfer">
                                            <i class="mdi mdi-receipt-text-outline"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    Tidak ada transaksi mutasi hutang pada rentang tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="4" class="text-end text-dark">TOTAL MUTASI &amp; SALDO AKHIR:</td>
                            <td class="text-end text-primary">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                            <td class="text-end text-success">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                            <td class="text-end text-danger fs-6">Rp {{ number_format($endingBalance, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="card-body">
                <div class="avatar avatar-xl bg-label-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="mdi mdi-account-search-outline fs-2"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Silakan Pilih Supplier Terlebih Dahulu</h5>
                <p class="text-muted mb-0 small">Pilih supplier dari dropdown di atas dan tentukan rentang tanggal untuk melihat kartu hutang (Statement of Account).</p>
            </div>
        </div>
    @endif
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "-- Pilih Supplier --",
                allowClear: true,
                width: '100%'
            });
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
