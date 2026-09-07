@extends('layouts.sales.app')
@section('title', 'Rekening Koran / Buku Bank - ' . $bank->bank)
@section('hide-chat', true)

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <style>
        .rf-chat-widget-wrapper, #rf-chat-widget-wrapper, .chat-bubble-container, #chat-bubble-container {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / <a href="{{ route('bank.index') }}" class="text-muted">Bank Account</a> /</span> Buku Bank &amp; Rekening Koran
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-book-open-outline me-1"></i> Rincian mutasi penerimaan (AR) &amp; pengeluaran (AP, Expense, Proyek) untuk <strong>{{ $bank->bank }} - {{ $bank->no_rek }}</strong>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('bank.statement_print', ['id' => $bank->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn btn-primary btn-sm">
                <i class="mdi mdi-printer me-1"></i> Cetak Rekening Koran
            </a>
            <a href="{{ route('bank.index') }}" class="btn btn-label-secondary btn-sm">
                <i class="mdi mdi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('bank.statement', $bank->id) }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold small">Rekening Bank</label>
                    <input type="text" class="form-control bg-light" value="{{ $bank->bank }} ({{ $bank->no_rek }} - {{ $bank->atas_nama }})" readonly>
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

    {{-- Bank Profile & Summary Cards --}}
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #f8faff 0%, #edf2ff 100%);">
        <div class="card-body p-4">
            <div class="row align-items-center mb-3">
                <div class="col-12 col-md-8">
                    <span class="badge bg-primary text-white text-uppercase px-2 py-1 mb-2 fw-bold" style="font-size: 11px;">
                        Buku Bank / Bank Statement Ledger
                    </span>
                    <h4 class="fw-bolder text-dark mb-1">{{ $bank->bank }} - {{ $bank->no_rek }}</h4>
                    <div class="text-muted small d-flex flex-wrap gap-3">
                        <span><i class="mdi mdi-account-circle-outline me-1"></i>A/N: <strong>{{ $bank->atas_nama ?: 'PT. Refrigerasi Teknik Indonesia' }}</strong></span>
                        @if($bank->branch)
                            <span><i class="mdi mdi-map-marker-outline me-1"></i>KCP: {{ $bank->branch }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="text-muted small d-block">Periode Mutasi</span>
                    <span class="fw-bold text-dark fs-6">
                        {{ Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    </span>
                </div>
            </div>

            {{-- Metric Row --}}
            <div class="row g-3">
                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm bg-white p-3">
                        <span class="text-muted small text-uppercase fw-semibold" style="font-size: 11px;">Saldo Awal Periode</span>
                        <h5 class="fw-bolder text-dark mb-0 mt-1">Rp {{ number_format($openingBalance, 0, ',', '.') }}</h5>
                        <small class="text-muted" style="font-size: 10px;">Sebelum {{ Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</small>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm bg-white p-3">
                        <span class="text-success small text-uppercase fw-semibold" style="font-size: 11px;">Total Kredit (+)</span>
                        <h5 class="fw-bolder text-success mb-0 mt-1">Rp {{ number_format($totalIn, 0, ',', '.') }}</h5>
                        <small class="text-muted" style="font-size: 10px;">Pelunasan AR, Transfer Masuk</small>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm bg-white p-3">
                        <span class="text-danger small text-uppercase fw-semibold" style="font-size: 11px;">Total Debet (-)</span>
                        <h5 class="fw-bolder text-danger mb-0 mt-1">Rp {{ number_format($totalOut, 0, ',', '.') }}</h5>
                        <small class="text-muted" style="font-size: 10px;">Expense, AP, Fee &amp; Transfer Keluar</small>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm p-3" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                        <span class="text-white-50 small text-uppercase fw-semibold" style="font-size: 11px;">Saldo Akhir / Berjalan</span>
                        <h5 class="fw-bolder text-white mb-0 mt-1">Rp {{ number_format($closingBalance, 0, ',', '.') }}</h5>
                        <small class="text-white-50" style="font-size: 10px;">Per {{ Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statement Ledger Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
            <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="mdi mdi-format-list-numbered me-2 text-primary fs-5"></i> Mutasi Rekening Koran Terintegrasi
            </h6>
            <span class="badge bg-label-secondary">{{ count($ledger) }} Transaksi</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 13px;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 110px;" class="fw-semibold text-dark text-nowrap">Tanggal</th>
                        <th style="width: 140px;" class="fw-semibold text-dark text-center">Modul Sumber</th>
                        <th style="width: 170px;" class="fw-semibold text-dark">No. Referensi</th>
                        <th class="fw-semibold text-dark">Deskripsi / Keterangan</th>
                        <th style="width: 140px;" class="fw-semibold text-dark text-end">Kredit (+)</th>
                        <th style="width: 140px;" class="fw-semibold text-dark text-end">Debet (-)</th>
                        <th style="width: 160px;" class="fw-semibold text-dark text-end">Saldo Berjalan</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Row Saldo Awal --}}
                    <tr class="table-light">
                        <td class="fw-bold text-nowrap">{{ Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</td>
                        <td class="text-center"><span class="badge bg-label-secondary rounded-pill px-2 py-1">SALDO AWAL</span></td>
                        <td class="text-muted">-</td>
                        <td class="fw-semibold text-dark">Saldo Awal Kas &amp; Bank per {{ Carbon\Carbon::parse($startDate)->format('d F Y') }}</td>
                        <td class="text-end text-muted">-</td>
                        <td class="text-end text-muted">-</td>
                        <td class="text-end fw-bolder text-dark">Rp {{ number_format($openingBalance, 0, ',', '.') }}</td>
                    </tr>

                    @forelse ($ledger as $item)
                        <tr>
                            <td class="text-nowrap">{{ Carbon\Carbon::parse($item->date)->format('d-m-Y') }}</td>
                            <td class="text-center">
                                <span class="badge {{ $item->badge_class }} rounded-pill px-2 py-1" style="font-size: 10px;">
                                    {{ $item->module }}
                                </span>
                            </td>
                            <td class="fw-semibold text-dark">
                                {{ $item->ref_no }}
                            </td>
                            <td>
                                {{ $item->description }}
                            </td>
                            <td class="text-end fw-bold text-success text-nowrap">
                                @if ($item->in > 0)
                                    <span>Rp {{ number_format($item->in, 0, ',', '.') }}</span>
                                    <span class="badge bg-primary text-white ms-1 px-1.5 py-0.5 rounded" style="font-size: 9px; font-weight: 700;" title="Kredit (Penerimaan)">KR</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-danger text-nowrap">
                                @if ($item->out > 0)
                                    <span>Rp {{ number_format($item->out, 0, ',', '.') }}</span>
                                    <span class="badge bg-danger text-white ms-1 px-1.5 py-0.5 rounded" style="font-size: 9px; font-weight: 700;" title="Debet (Pengeluaran)">DB</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-bolder text-dark">
                                Rp {{ number_format($item->running_balance, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <em>Tidak ada mutasi penerimaan atau pengeluaran pada rekening ini dalam rentang tanggal yang dipilih.</em>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold" style="border-top: 2px solid #dee2e6;">
                    <tr class="align-middle">
                        <td colspan="4" class="text-end text-uppercase fw-bolder text-dark py-3 text-nowrap" style="font-size: 13.5px;">Total Mutasi Periode Ini:</td>
                        <td class="text-end text-success py-3 text-nowrap" style="font-size: 15.5px; font-weight: 800;">
                            <span>Rp {{ number_format($totalIn, 0, ',', '.') }}</span>
                            <span class="badge bg-primary text-white ms-1 px-1.5 py-0.5 rounded" style="font-size: 10px; font-weight: 700; vertical-align: middle;">KR</span>
                        </td>
                        <td class="text-end text-danger py-3 text-nowrap" style="font-size: 15.5px; font-weight: 800;">
                            <span>Rp {{ number_format($totalOut, 0, ',', '.') }}</span>
                            <span class="badge bg-danger text-white ms-1 px-1.5 py-0.5 rounded" style="font-size: 10px; font-weight: 700; vertical-align: middle;">DB</span>
                        </td>
                        <td class="text-end text-primary py-3 text-nowrap" style="font-size: 16px; font-weight: 800;">Rp {{ number_format($closingBalance, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
