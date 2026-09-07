@extends('layouts.sales.app')
@section('title', 'Petty Cash (Kas Kecil) Management')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .petty-card {
            border-radius: 12px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .petty-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.09) !important;
        }
        .account-chip {
            cursor: pointer;
            transition: all 0.2s ease;
            border: 2px solid transparent;
        }
        .account-chip.active {
            border-color: #696cff !important;
            background-color: #f5f5ff !important;
        }
        .account-chip:hover:not(.active) {
            border-color: #e0e0e0;
            background-color: #fbfbfb;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(105, 108, 255, 0.03);
        }
        .total-summary-row {
            background-color: #f8f9fa !important;
            border-top: 2px solid #696cff !important;
            border-bottom: 2px solid #696cff !important;
        }
        .total-summary-num {
            font-size: 1.1rem !important;
            font-weight: 800 !important;
        }
    </style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance /</span> Petty Cash (Kas Kecil) Management
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-cash-register me-1"></i> Pencatatan kasir operasional harian, voucher kas keluar (BKK), dan pengisian kembali (reimbursement) kas kecil
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if($selectedBank)
                <a href="{{ route('petty_cash.print_statement', ['id_bank' => $selectedBank->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn btn-label-secondary btn-sm px-3 shadow-sm">
                    <i class="mdi mdi-printer me-1"></i> Cetak Buku Kas
                </a>
            @endif
            <button type="button" class="btn btn-label-success btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#topupModal">
                <i class="mdi mdi-arrow-down-bold-box me-1"></i> Isi Saldo / Top-Up (BKM)
            </button>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#disbursementModal">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Catat Pengeluaran (BKK)
            </button>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle-outline me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle-outline me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Petty Cash Accounts Selector Strip --}}
    @if($pettyCashBanks->isEmpty())
        <div class="card border-0 shadow-sm mb-4 border-start border-warning border-4">
            <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-md bg-label-warning rounded p-2">
                        <i class="mdi mdi-alert-circle-outline fs-3"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Belum Ada Rekening Kas Kecil (Petty Cash) Terdaftar</h6>
                        <p class="text-muted small mb-0">Untuk mulai menggunakan modul Petty Cash, tandai atau buat rekening di menu <strong>Kas &amp; Bank</strong> sebagai Akun Kas Kecil dan tentukan PIC pemegangnya.</p>
                    </div>
                </div>
                <a href="{{ route('bank.index') }}" class="btn btn-warning btn-sm text-dark px-3 fw-semibold">
                    <i class="mdi mdi-bank-plus me-1"></i> Buka Master Kas &amp; Bank
                </a>
            </div>
        </div>
    @else
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Pilih Akun Kasir / Kas Kecil:</span>
                <a href="{{ route('bank.index') }}" class="small text-primary text-decoration-none">
                    <i class="mdi mdi-cog-outline me-1"></i> Kelola di Master Bank
                </a>
            </div>
            <div class="row g-2">
                @foreach($pettyCashBanks as $pb)
                    <div class="col-12 col-sm-6 col-lg-3">
                        <a href="{{ route('petty_cash.index', ['id_bank' => $pb->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="text-decoration-none">
                            <div class="card shadow-sm account-chip h-100 p-3 {{ $selectedBankId == $pb->id ? 'active' : 'bg-white' }}">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-xs flex-shrink-0">
                                            <span class="avatar-initial rounded {{ $selectedBankId == $pb->id ? 'bg-primary text-white' : 'bg-label-primary text-primary' }} fw-bold" style="font-size: 10px;">
                                                <i class="mdi mdi-cash-register"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 13px;">{{ $pb->bank }}</h6>
                                            <small class="text-muted font-monospace" style="font-size: 11px;">{{ $pb->no_rek }}</small>
                                        </div>
                                    </div>
                                    @if($selectedBankId == $pb->id)
                                        <span class="badge bg-primary rounded-pill px-2 py-0.5" style="font-size: 10px;">Aktif</span>
                                    @endif
                                </div>
                                <div class="d-flex justify-content-between align-items-end mt-2 pt-2 border-top">
                                    <div>
                                        <small class="text-muted d-block" style="font-size: 10.5px;"><i class="mdi mdi-account-tie me-1"></i>{{ $pb->pic?->name ?? 'Kasir Operasional' }}</small>
                                        <span class="fw-bolder {{ $pb->saldo >= 0 ? 'text-primary' : 'text-danger' }}" style="font-size: 13.5px;">
                                            Rp {{ number_format($pb->saldo, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    @if($pb->plafond > 0)
                                        <small class="text-muted text-end" style="font-size: 10px;">
                                            Plafon:<br>Rp {{ number_format($pb->plafond, 0, ',', '.') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Executive Summary KPI Cards --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Saldo Kas Kecil --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm petty-card h-100" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                <div class="card-body p-3 text-white">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white-50 small fw-semibold text-uppercase" style="font-size: 11px;">Saldo Kas Kecil Saat Ini</span>
                        <span class="avatar-initial rounded bg-white bg-opacity-25 p-2 text-white">
                            <i class="mdi mdi-cash-multiple fs-4"></i>
                        </span>
                    </div>
                    <h4 class="fw-bolder text-white mb-1">Rp {{ number_format($currentBalance, 0, ',', '.') }}</h4>
                    <div class="d-flex justify-content-between align-items-center mt-2 pt-1 border-top border-white border-opacity-25">
                        <small class="text-white-50" style="font-size: 11px;">
                            {{ $selectedBank ? ($selectedBank->pic?->name ?? $selectedBank->bank) : 'Seluruh Kas Kecil' }}
                        </small>
                        @if($plafond > 0)
                            <small class="text-white fw-bold" style="font-size: 11px;">
                                Sisa: Rp {{ number_format($remainingPlafond, 0, ',', '.') }}
                            </small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Batas Plafon & Utilisasi --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm petty-card h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Batas Plafon Kas</span>
                        <span class="avatar-initial rounded bg-label-info p-2">
                            <i class="mdi mdi-shield-check fs-4"></i>
                        </span>
                    </div>
                    <h4 class="fw-bolder text-dark mb-1">
                        {{ $plafond > 0 ? 'Rp ' . number_format($plafond, 0, ',', '.') : 'Tidak Dibatasi' }}
                    </h4>
                    @if($plafond > 0)
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <div class="progress flex-grow-1" style="height: 6px;">
                                <div class="progress-bar {{ $usedPercentage > 80 ? 'bg-danger' : ($usedPercentage > 50 ? 'bg-warning' : 'bg-success') }}" 
                                     role="progressbar" style="width: {{ $usedPercentage }}%;" aria-valuenow="{{ $usedPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted fw-bold" style="font-size: 11px;">{{ $usedPercentage }}% Terpakai</small>
                        </div>
                    @else
                        <small class="text-muted" style="font-size: 11px;">Atur batas plafon di edit rekening</small>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 3: Total Pengeluaran Periode Ini --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm petty-card h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-danger small fw-semibold text-uppercase" style="font-size: 11px;">Pengeluaran Kas (Debet)</span>
                        <span class="avatar-initial rounded bg-label-danger p-2">
                            <i class="mdi mdi-cash-minus fs-4"></i>
                        </span>
                    </div>
                    <h4 class="fw-bolder text-danger mb-1">Rp {{ number_format($totalDisbursement, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11px;">{{ $disbursementCount }} Bukti Kas Keluar (BKK) periode ini</small>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Reimbursement / Top-up --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm petty-card h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-success small fw-semibold text-uppercase" style="font-size: 11px;">Reimbursement Masuk (Kredit)</span>
                        <span class="avatar-initial rounded bg-label-success p-2">
                            <i class="mdi mdi-cash-plus fs-4"></i>
                        </span>
                    </div>
                    <h4 class="fw-bolder text-success mb-1">Rp {{ number_format($totalTopup, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Pengisian dana dari Rekening Bank Kantor</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('petty_cash.index') }}" method="GET" class="row g-2 align-items-center">
                <input type="hidden" name="id_bank" value="{{ $selectedBankId }}">
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Kategori</label>
                    <select name="category" class="form-select form-select-sm">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $categoryFilter == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Tipe Mutasi</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">-- Semua Tipe --</option>
                        <option value="disbursement" {{ $typeFilter == 'disbursement' ? 'selected' : '' }}>Pengeluaran (BKK)</option>
                        <option value="topup" {{ $typeFilter == 'topup' ? 'selected' : '' }}>Top-Up (BKM)</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-1 align-self-end">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="mdi mdi-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('petty_cash.index', ['id_bank' => $selectedBankId]) }}" class="btn btn-label-secondary btn-sm" title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Petty Cash Ledger Table Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-book-open-page-variant text-primary fs-4"></i> Buku Kas Kecil (Transaction Ledger)
                </h5>
                <p class="text-muted small mb-0">
                    Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</strong>
                    @if($selectedBank)
                        &bull; Akun: <strong>{{ $selectedBank->bank }}</strong> (PIC: {{ $selectedBank->pic?->name ?? '-' }})
                    @endif
                </p>
            </div>
            <span class="badge bg-label-primary px-3 py-1 rounded-pill">{{ $transactions->total() }} Transaksi Ditemukan</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr style="font-size: 12px;">
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-nowrap">Tanggal</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-nowrap">No. Voucher</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3">Kategori &amp; Tipe</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3">Uraian / Keperluan</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3">Penerima / Sumber</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-end text-nowrap">Debet (-)</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-end text-nowrap">Kredit (+)</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-center">Bukti / Struk</th>
                        <th class="text-uppercase fw-bold text-muted py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13px;">
                    @forelse ($transactions as $tx)
                        <tr>
                            <td class="px-3 text-nowrap">
                                <span class="fw-semibold text-dark">{{ $tx->date->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-3 text-nowrap">
                                @if($tx->type === 'disbursement')
                                    <span class="badge bg-label-danger font-monospace px-2 py-1">
                                        <i class="mdi mdi-receipt me-1"></i>{{ $tx->voucher_number }}
                                    </span>
                                @else
                                    <span class="badge bg-label-success font-monospace px-2 py-1">
                                        <i class="mdi mdi-arrow-down-bold-circle me-1"></i>{{ $tx->voucher_number }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-3">
                                @if($tx->type === 'disbursement')
                                    <span class="badge bg-label-warning rounded-pill px-2 py-0.5 mb-1" style="font-size: 10.5px;">
                                        {{ $tx->category }}
                                    </span>
                                @else
                                    <span class="badge bg-label-info rounded-pill px-2 py-0.5 mb-1" style="font-size: 10.5px;">
                                        Top-Up Reimbursement
                                    </span>
                                @endif
                                <div class="text-muted small" style="font-size: 11px;">
                                    Kasir: {{ $tx->creator?->name ?? 'Kasir' }}
                                </div>
                            </td>
                            <td class="px-3">
                                <div class="text-dark fw-semibold" style="max-width: 260px; line-height: 1.35;">{{ $tx->description }}</div>
                            </td>
                            <td class="px-3">
                                @if($tx->type === 'disbursement')
                                    <span class="text-dark fw-semibold"><i class="mdi mdi-account-arrow-right me-1 text-muted"></i>{{ $tx->recipient ?: '-' }}</span>
                                @else
                                    <span class="text-info fw-semibold">
                                        <i class="mdi mdi-bank me-1"></i>{{ $tx->sourceBank ? $tx->sourceBank->bank . ' (' . $tx->sourceBank->no_rek . ')' : 'Bank Utama' }}
                                    </span>
                                @endif
                            </td>
                            {{-- Kolom Debet (-) Pengeluaran --}}
                            <td class="px-3 text-end text-nowrap">
                                @if($tx->type === 'disbursement')
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <span class="fw-bold text-danger">Rp {{ number_format($tx->amount, 0, ',', '.') }}</span>
                                        <span class="badge bg-danger text-white rounded-pill px-1.5 py-0.5" style="font-size: 9px; font-weight: 700;">DB</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            {{-- Kolom Kredit (+) Pemasukan / Topup --}}
                            <td class="px-3 text-end text-nowrap">
                                @if($tx->type === 'topup')
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <span class="fw-bold text-primary">Rp {{ number_format($tx->amount, 0, ',', '.') }}</span>
                                        <span class="badge bg-primary text-white rounded-pill px-1.5 py-0.5" style="font-size: 9px; font-weight: 700;">KR</span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            {{-- Bukti / Struk --}}
                            <td class="px-3 text-center">
                                @if($tx->proof_attachment)
                                    <a href="{{ asset('storage/' . $tx->proof_attachment) }}" target="_blank" class="btn btn-xs btn-label-info rounded-pill px-2.5" title="Lihat Bukti Nota / Lampiran">
                                        <i class="mdi mdi-paperclip me-1"></i> Bukti
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            {{-- Aksi --}}
                            <td class="px-3 text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('petty_cash.print_voucher', $tx->id) }}" target="_blank" class="btn btn-xs btn-label-secondary rounded-pill px-2" title="Cetak Voucher Resmi">
                                        <i class="mdi mdi-printer"></i>
                                    </a>
                                    <form action="{{ route('petty_cash.destroy', $tx->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus transaksi [{{ $tx->voucher_number }}]? Saldo kas akan dipulihkan secara otomatis.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-label-danger rounded-pill px-2" title="Hapus & Pulihkan Saldo">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="mdi mdi-receipt-text-outline fs-1 d-block mb-2 text-secondary"></i>
                                <em>Belum ada transaksi kas kecil pada rentang tanggal &amp; filter yang dipilih.</em>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($transactions->isNotEmpty())
                    <tfoot>
                        <tr class="total-summary-row">
                            <td colspan="5" class="px-3 py-3 text-end fw-bold text-dark fs-6">
                                <i class="mdi mdi-calculator me-1 text-primary"></i> TOTAL MUTASI PERIODE INI:
                            </td>
                            <td class="px-3 py-3 text-end text-nowrap">
                                <span class="text-danger total-summary-num">Rp {{ number_format($totalDisbursement, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-3 py-3 text-end text-nowrap">
                                <span class="text-primary total-summary-num">Rp {{ number_format($totalTopup, 0, ',', '.') }}</span>
                            </td>
                            <td colspan="2" class="px-3 py-3 text-center">
                                <span class="badge bg-label-dark rounded-pill px-3 py-1">
                                    Net: Rp {{ number_format($totalTopup - $totalDisbursement, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="card-footer bg-white border-top py-2 px-4">
                {{ $transactions->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    {{-- Modal 1: Catat Pengeluaran Kas Kecil (BKK) --}}
    <div class="modal fade animate__animated fadeIn" id="disbursementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('petty_cash.disbursement') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-primary text-white py-3 px-4">
                        <h5 class="modal-title text-white d-flex align-items-center gap-2">
                            <i class="mdi mdi-cash-minus"></i> Catat Pengeluaran Kas Kecil (BKK)
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Pilih Akun Kasir / Kas Kecil <span class="text-danger">*</span></label>
                                <select name="id_bank" class="form-select" required id="bkkBankSelect">
                                    @foreach($pettyCashBanks as $pb)
                                        <option value="{{ $pb->id }}" {{ $selectedBankId == $pb->id ? 'selected' : '' }}>
                                            {{ $pb->bank }} ({{ $pb->no_rek }}) - PIC: {{ $pb->pic?->name ?? 'Kasir' }} [Saldo: Rp {{ number_format($pb->saldo, 0, ',', '.') }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tanggal Transaksi <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ \Carbon\Carbon::now()->toDateString() }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nominal Pengeluaran (Rp) <span class="text-danger">*</span></label>
                                <input type="number" step="any" min="1" name="amount" class="form-control form-control-lg fw-bold text-danger" placeholder="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Kategori Pengeluaran <span class="text-danger">*</span></label>
                                <select name="category" class="form-select" required>
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Diberikan Kepada / Penerima</label>
                                <input type="text" name="recipient" class="form-control" placeholder="Contoh: Pak Budi (Driver) / Nama Toko">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Uraian / Keperluan Rinci <span class="text-danger">*</span></label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Contoh: Pembelian bensin & tol operasional dinas, snack meeting tamu klien..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Upload Bukti Struk / Nota / Kwitansi</label>
                                <input type="file" name="proof_attachment" class="form-control" accept="image/*,application/pdf">
                                <small class="text-muted" style="font-size: 11px;">Format: JPG, PNG, atau PDF (Maks. 5 MB)</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2 px-4">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-check me-1"></i> Simpan Voucher BKK
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal 2: Isi Ulang / Top-Up Kasir (BKM) --}}
    <div class="modal fade animate__animated fadeIn" id="topupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('petty_cash.topup') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-success text-white py-3 px-4">
                        <h5 class="modal-title text-white d-flex align-items-center gap-2">
                            <i class="mdi mdi-cash-plus"></i> Pengisian Kembali / Top-Up Kasir (BKM)
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Rekening Kas Kecil Tujuan (Penerima Dana) <span class="text-danger">*</span></label>
                                <select name="id_bank" class="form-select" required id="topupTargetBank">
                                    @foreach($pettyCashBanks as $pb)
                                        <option value="{{ $pb->id }}" {{ $selectedBankId == $pb->id ? 'selected' : '' }} data-saldo="{{ $pb->saldo }}" data-plafond="{{ $pb->plafond }}">
                                            {{ $pb->bank }} ({{ $pb->no_rek }}) - PIC: {{ $pb->pic?->name ?? 'Kasir' }} [Saldo: Rp {{ number_format($pb->saldo, 0, ',', '.') }} | Plafon: Rp {{ number_format($pb->plafond, 0, ',', '.') }}]
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Rekening Bank Kantor Sumber (Asal Dana) <span class="text-danger">*</span></label>
                                <select name="id_source_bank" class="form-select" required>
                                    <option value="" disabled selected>-- Pilih Rekening Sumber Kantor --</option>
                                    @foreach($sourceBanks as $sb)
                                        <option value="{{ $sb->id }}">
                                            [{{ $sb->entity ?: 'Reftech' }}] {{ $sb->bank }} - {{ $sb->no_rek }} (Saldo: Rp {{ number_format($sb->saldo, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tanggal Pengisian <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ \Carbon\Carbon::now()->toDateString() }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nominal Pengisian (Rp) <span class="text-danger">*</span></label>
                                <input type="number" step="any" min="1" name="amount" id="topupAmount" class="form-control form-control-lg fw-bold text-success" placeholder="0" required>
                            </div>
                            @if($selectedBank && $selectedBank->plafond > 0 && ($selectedBank->plafond - $selectedBank->saldo) > 0)
                                <div class="col-12">
                                    <button type="button" class="btn btn-xs btn-label-primary rounded-pill" onclick="document.getElementById('topupAmount').value = '{{ $selectedBank->plafond - $selectedBank->saldo }}'">
                                        <i class="mdi mdi-auto-fix me-1"></i> Isi Penuh Sesuai Plafon (Rp {{ number_format($selectedBank->plafond - $selectedBank->saldo, 0, ',', '.') }})
                                    </button>
                                </div>
                            @endif
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Catatan / Keterangan Pengisian</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Contoh: Reimbursement kas kecil minggu ke-1 September 2026"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Upload Bukti Transfer Bank (Opsional)</label>
                                <input type="file" name="proof_attachment" class="form-control" accept="image/*,application/pdf">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2 px-4">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success text-white">
                            <i class="mdi mdi-check me-1"></i> Proses Pengisian Kas (BKM)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
