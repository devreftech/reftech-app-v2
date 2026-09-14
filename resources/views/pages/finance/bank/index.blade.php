@extends('layouts.sales.app')
@section('title', 'Master Bank Account (Kas & Bank)')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <style>
        .bank-card {
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .bank-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
        }
    </style>
@endpush

@section('content')
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance /</span> Master Bank Account &amp; Kas
            </h4>
            <p class="text-muted mb-0 small">
                <i class="mdi mdi-bank me-1"></i> Pengelolaan rekening bank, saldo awal/akhir, dan pemantauan arus kas terintegrasi
            </p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-label-warning btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#adjustmentBankModal">
                <i class="mdi mdi-scale-balance me-1"></i> Penyesuaian Saldo (Opname / Cut-off)
            </button>
            <a href="{{ route('finance.reconciliation.index') }}" class="btn btn-label-info btn-sm px-3 shadow-sm">
                <i class="mdi mdi-checkbox-marked-circle-auto-outline me-1"></i> Rekonsiliasi Bank
            </a>
            <button type="button" class="btn btn-label-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#transferBankModal">
                <i class="mdi mdi-bank-transfer me-1"></i> Transfer Antar Bank
            </button>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createBankModal">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Rekening Bank
            </button>
            @if(in_array(auth::user()?->role, ['Finance Manager', 'Finance', 'Developer']) || auth::user()?->isDeveloper())
                <a href="{{ route('finance.security.manage') }}" class="btn btn-label-secondary btn-sm px-2 shadow-sm" title="Pengaturan PIN Finance">
                    <i class="mdi mdi-shield-key-outline"></i>
                </a>
            @endif
            <form action="{{ route('finance.security.lock') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-secondary btn-sm px-2 shadow-sm" title="Kunci Sesi Vault">
                    <i class="mdi mdi-lock-outline"></i>
                </button>
            </form>
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

    {{-- Executive Summary KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bank-card h-100" style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);">
                <div class="card-body p-3 text-white">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white-50 small fw-semibold text-uppercase" style="font-size: 11px;">Total Likuiditas Bank</span>
                        <span class="avatar-initial rounded bg-white bg-opacity-25 p-2 text-white">
                            <i class="mdi mdi-wallet fs-4"></i>
                        </span>
                    </div>
                    <h4 class="fw-bolder text-white mb-1">Rp {{ number_format($totalLiquidBalance, 0, ',', '.') }}</h4>
                    <small class="text-white-50" style="font-size: 11px;">{{ $activeBankCount }} Rekening Bank Aktif</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bank-card h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 11px;">Total Saldo Awal</span>
                        <span class="avatar-initial rounded bg-label-secondary p-2">
                            <i class="mdi mdi-cash-register fs-4"></i>
                        </span>
                    </div>
                    <h4 class="fw-bolder text-dark mb-1">Rp {{ number_format($totalInitialBalance, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Modal / Saldo Awal Cut-Off</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bank-card h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-success small fw-semibold text-uppercase" style="font-size: 11px;">Total Penerimaan (AR)</span>
                        <span class="avatar-initial rounded bg-label-success p-2">
                            <i class="mdi mdi-cash-plus fs-4"></i>
                        </span>
                    </div>
                    <h4 class="fw-bolder text-success mb-1">Rp {{ number_format($totalIn, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Uang masuk dari pelunasan klien</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm bank-card h-100 bg-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-danger small fw-semibold text-uppercase" style="font-size: 11px;">Total Pengeluaran</span>
                        <span class="avatar-initial rounded bg-label-danger p-2">
                            <i class="mdi mdi-cash-minus fs-4"></i>
                        </span>
                    </div>
                    <h4 class="fw-bolder text-danger mb-1">Rp {{ number_format($totalOut, 0, ',', '.') }}</h4>
                    <small class="text-muted" style="font-size: 11px;">Expense + Hutang AP + Proyek</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Bank Accounts Table Card with Nav Tabs --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom p-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center px-4 py-3 border-bottom gap-2">
                <div>
                    <h5 class="card-title mb-1 fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="mdi mdi-bank-check text-primary fs-4"></i> Daftar Rekening Bank Operasional
                    </h5>
                    <p class="text-muted small mb-0">Kelompok rekening kas &amp; bank untuk penampungan piutang (AR) dan pengeluaran per entitas</p>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-label-primary px-3 py-1 rounded-pill">Total {{ count($banks) }} Rekening Terdaftar</span>
                </div>
            </div>
            {{-- Nav Tabs --}}
            <ul class="nav nav-tabs nav-fill px-3 pt-2" role="tablist" style="border-bottom: 0;">
                <li class="nav-item">
                    <button type="button" class="nav-link active fw-bold py-3 d-flex align-items-center justify-content-center gap-2" role="tab" data-bs-toggle="tab" data-bs-target="#tab-all" aria-controls="tab-all" aria-selected="true">
                        <i class="mdi mdi-bank-outline fs-5 text-primary"></i>
                        <span>Semua Rekening</span>
                        <span class="badge bg-label-secondary rounded-pill ms-1">{{ count($banks) }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link fw-bold py-3 d-flex align-items-center justify-content-center gap-2" role="tab" data-bs-toggle="tab" data-bs-target="#tab-reftech" aria-controls="tab-reftech" aria-selected="false">
                        <i class="mdi mdi-domain fs-5 text-info"></i>
                        <span class="text-info">Reftech Pusat (Bandung)</span>
                        <span class="badge bg-label-info rounded-pill ms-1">{{ count($reftechPusatBanks) }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link fw-bold py-3 d-flex align-items-center justify-content-center gap-2" role="tab" data-bs-toggle="tab" data-bs-target="#tab-palembang" aria-controls="tab-palembang" aria-selected="false">
                        <i class="mdi mdi-map-marker-radius fs-5 text-success"></i>
                        <span class="text-success">Cabang Palembang</span>
                        <span class="badge bg-label-success rounded-pill ms-1">{{ count($palembangBanks) }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link fw-bold py-3 d-flex align-items-center justify-content-center gap-2" role="tab" data-bs-toggle="tab" data-bs-target="#tab-kojisha" aria-controls="tab-kojisha" aria-selected="false">
                        <i class="mdi mdi-office-building fs-5 text-warning"></i>
                        <span class="text-warning">KOJISHA (AR Kojisha)</span>
                        <span class="badge bg-label-warning rounded-pill ms-1">{{ count($kojishaBanks) }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content p-0">
            {{-- Tab 1: All Banks --}}
            <div class="tab-pane fade show active" id="tab-all" role="tabpanel">
                @include('pages.finance.bank._table_rows', ['bankList' => $banks, 'tabId' => 'all'])
            </div>

            {{-- Tab 2: Reftech Pusat Banks --}}
            <div class="tab-pane fade" id="tab-reftech" role="tabpanel">
                <div class="alert alert-info d-flex align-items-center m-3 mb-0 py-2.5 px-3 rounded" role="alert">
                    <i class="mdi mdi-information-outline me-2 fs-5"></i>
                    <div style="font-size: 13px;">
                        <strong>Rekening Reftech Pusat (Bandung):</strong> Digunakan untuk penampungan pembayaran faktur / invoice AR Reftech serta operasional kantor pusat.
                    </div>
                </div>
                @include('pages.finance.bank._table_rows', ['bankList' => $reftechPusatBanks, 'tabId' => 'reftech'])
            </div>

            {{-- Tab 3: Cabang Palembang Banks --}}
            <div class="tab-pane fade" id="tab-palembang" role="tabpanel">
                <div class="alert alert-success d-flex align-items-center m-3 mb-0 py-2.5 px-3 rounded" role="alert">
                    <i class="mdi mdi-map-marker-check me-2 fs-5"></i>
                    <div style="font-size: 13px;">
                        <strong>Rekening Cabang Palembang:</strong> Digunakan untuk penampungan saldo capital cabang, modal kerja, dan operasional khusus Cabang Palembang.
                    </div>
                </div>
                @include('pages.finance.bank._table_rows', ['bankList' => $palembangBanks, 'tabId' => 'palembang'])
            </div>

            {{-- Tab 4: Kojisha Banks --}}
            <div class="tab-pane fade" id="tab-kojisha" role="tabpanel">
                <div class="alert alert-warning d-flex align-items-center m-3 mb-0 py-2.5 px-3 rounded" role="alert">
                    <i class="mdi mdi-information-outline me-2 fs-5"></i>
                    <div style="font-size: 13px;">
                        <strong>Rekening Bank KOJISHA:</strong> Digunakan untuk penampungan pembayaran faktur / invoice AR Kojisha (baik transaksi PPN PT. Kojisha Innotiv Indonesia maupun Non-PPN).
                    </div>
                </div>
                @include('pages.finance.bank._table_rows', ['bankList' => $kojishaBanks, 'tabId' => 'kojisha'])
            </div>
        </div>
    </div>

    {{-- Modal Create Bank --}}
    <div class="modal fade" id="createBankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('bank.store') }}" method="POST">
                    @csrf
                    <div class="modal-header bg-primary text-white py-3 px-4">
                        <h5 class="modal-title text-white d-flex align-items-center gap-2">
                            <i class="mdi mdi-bank-plus"></i> Tambah Rekening Bank Baru
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Entitas / Tujuan AR <span class="text-danger">*</span></label>
                                <select name="entity" class="form-select" required>
                                    <option value="Reftech" selected>REFTECH (Reftech Jaya Optima / Reftech)</option>
                                    <option value="Kojisha">KOJISHA (Kojisha Innotiv Indonesia)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nama Bank <span class="text-danger">*</span></label>
                                <input type="text" name="bank" class="form-control" placeholder="Contoh: BCA, Mandiri, BRI" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">No. Rekening <span class="text-danger">*</span></label>
                                <input type="text" name="no_rek" class="form-control" placeholder="Contoh: 1234567890" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Atas Nama <span class="text-danger">*</span></label>
                                <input type="text" name="atas_nama" class="form-control" placeholder="Contoh: PT. REFTECH JAYA OPTIMA" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Kantor Cabang (KCP) / Wilayah</label>
                                <input type="text" name="branch" list="branchListSuggestions" class="form-control" placeholder="Pilih cabang: Bandung / Palembang">
                                <datalist id="branchListSuggestions">
                                    <option value="Bandung">Pusat (Bandung)</option>
                                    <option value="Palembang">Cabang Palembang</option>
                                </datalist>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Saldo Awal (Rp)</label>
                                <input type="text" inputmode="numeric" name="initial_balance" class="form-control rupiah-mask" placeholder="0" value="0">
                            </div>
                            <div class="col-12">
                                <div class="card bg-light border p-3 rounded">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" name="is_petty_cash" value="1" id="isPettyCashCreate" onchange="togglePettyCashFields('Create')">
                                        <label class="form-check-label fw-bold text-dark" for="isPettyCashCreate">
                                            <i class="mdi mdi-cash-register me-1 text-primary"></i> Jadikan Sebagai Akun Kas Kecil (Petty Cash)
                                        </label>
                                    </div>
                                    <p class="text-muted small mb-0" style="font-size: 11px;">
                                        Centang opsi ini jika rekening/kas ini dipegang oleh kasir/PIC untuk operasional kas kecil harian.
                                    </p>
                                    <div id="pettyCashFieldsCreate" style="display: none;" class="mt-3 pt-3 border-top">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">PIC Pemegang Kasir / Petty Cash</label>
                                                <select name="pic_id" class="form-select">
                                                    <option value="">-- Pilih PIC Pemegang Kasir --</option>
                                                    @foreach($users as $u)
                                                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->nip ?? 'PIC' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small">Batas Plafon Kas Kecil (Rp)</label>
                                                <input type="text" inputmode="numeric" name="plafond" class="form-control rupiah-mask" placeholder="Contoh: 5.000.000" value="0">
                                                <small class="text-muted" style="font-size: 10px;">Batas maksimal saldo kas kecil</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Catatan / Keterangan</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Contoh: Rekening PPN Reftech (Swift: CENAIDJA) / Rekening Non-PPN"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2 px-4">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-check me-1"></i> Tambahkan Rekening
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Transfer Antar Rekening Kas & Bank --}}
    <div class="modal fade" id="transferBankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form action="{{ route('bank.transfer') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-info text-white py-3 px-4">
                        <h5 class="modal-title text-white d-flex align-items-center gap-2">
                            <i class="mdi mdi-bank-transfer"></i> Transfer Dana Antar Bank / Kas
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Rekening Sumber (Asal Pemindahan) <span class="text-danger">*</span></label>
                                <select name="id_from_bank" class="form-select" required id="transferFromBank">
                                    <option value="" disabled selected>-- Pilih Rekening Pengirim --</option>
                                    @foreach($banks->where('is_active', 1) as $b)
                                        <option value="{{ $b->id }}" data-saldo="{{ $b->saldo }}">
                                            [{{ $b->entity ?: 'Reftech' }}] {{ $b->bank }} - {{ $b->no_rek }} (Saldo: Rp {{ number_format($b->saldo, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Rekening Tujuan (Penerima) <span class="text-danger">*</span></label>
                                <select name="id_to_bank" class="form-select" required id="transferToBank">
                                    <option value="" disabled selected>-- Pilih Rekening Penerima --</option>
                                    @foreach($banks->where('is_active', 1) as $b)
                                        <option value="{{ $b->id }}">
                                            [{{ $b->entity ?: 'Reftech' }}] {{ $b->bank }} - {{ $b->no_rek }} ({{ $b->atas_nama }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Nominal Transfer (Rp) <span class="text-danger">*</span></label>
                                <input type="text" inputmode="numeric" name="amount" class="form-control rupiah-mask" placeholder="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Biaya Admin Bank (Rp)</label>
                                <input type="text" inputmode="numeric" name="fee" class="form-control rupiah-mask" placeholder="0" value="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tanggal Transfer <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ \Carbon\Carbon::now()->toDateString() }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Bukti Transfer (Opsional)</label>
                                <input type="file" name="proof_file" class="form-control" accept="image/*,application/pdf">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Catatan / Keperluan Pemindahan Dana</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="Contoh: Pemindahan kas operasional / Reklasifikasi dana Reftech ke Kojisha"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2 px-4">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info text-white">
                            <i class="mdi mdi-check me-1"></i> Proses Transfer Dana
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Penyesuaian Saldo (Opname Kas & Bank / Rekonsiliasi) --}}
    <div class="modal fade" id="adjustmentBankModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow-lg border-0">
                <form action="{{ route('bank.adjustment') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header bg-warning text-dark py-3 px-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="mdi mdi-scale-balance"></i> Penyesuaian Saldo (Opname Kas &amp; Bank)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <div class="alert alert-info border-0 d-flex align-items-start gap-2 mb-3 p-3 rounded" style="background-color: #e8f4fd; border-left: 4px solid #03c3ec !important;">
                            <i class="mdi mdi-information-outline fs-4 text-info flex-shrink-0 mt-0.5"></i>
                            <div style="font-size: 13px;">
                                <strong class="text-dark d-block mb-1">Rekonsiliasi / Cut-off Saldo Kas &amp; Bank</strong>
                                <span class="text-muted">
                                    Gunakan fitur ini untuk menyelaraskan saldo sistem dengan rekening koran riil (misal saat tutup buku akhir tahun atau penyesuaian pengeluaran yang belum sempat terinput). Mutasi penyesuaian akan tercatat transparan di buku bank.
                                </span>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Pilih Rekening Bank <span class="text-danger">*</span></label>
                                <select name="id_bank" class="form-select" required id="adjBankSelect" onchange="updateAdjCalculation()">
                                    <option value="" disabled selected>-- Pilih Rekening Bank --</option>
                                    @foreach($banks as $b)
                                        <option value="{{ $b->id }}" data-saldo="{{ (float)$b->saldo }}">
                                            [{{ $b->entity ?: 'Reftech' }}] {{ $b->bank }} - {{ $b->no_rek }} (Saldo Sistem: Rp {{ number_format($b->saldo, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-muted">Saldo Saat Ini di Sistem</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="text" class="form-control bg-light fw-bold" id="adjCurrentSaldoDisplay" value="0" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-dark">Saldo Riil Rekening Koran <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" inputmode="numeric" name="adjusted_balance" class="form-control rupiah-mask fw-bold text-primary" id="adjNewSaldoInput" placeholder="0" required oninput="updateAdjCalculation()">
                                </div>
                            </div>

                            {{-- Live Difference Card --}}
                            <div class="col-12">
                                <div class="card border p-3 rounded bg-light" id="adjDiffCard">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-semibold text-muted">Selisih Penyesuaian Saldo:</span>
                                        <span class="badge bg-secondary rounded-pill px-2.5 py-1" id="adjDiffBadge">Belum dihitung</span>
                                    </div>
                                    <h5 class="mb-1 fw-bold text-dark" id="adjDiffNominal">Rp 0</h5>
                                    <small class="text-muted" id="adjDiffExplanation" style="font-size: 11px;">
                                        Pilih rekening dan masukkan saldo riil rekening koran untuk melihat selisih mutasi.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Tanggal Penyesuaian / Cut-off <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ \Carbon\Carbon::now()->toDateString() }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Bukti Rekening Koran (Opsional)</label>
                                <input type="file" name="proof_file" class="form-control" accept="image/*,application/pdf">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Alasan / Keterangan Penyesuaian <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="2" placeholder="Contoh: Penyesuaian saldo cut-off akhir tahun 2026 mengikuti rekening koran riil bank." required></textarea>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch p-2 bg-light rounded border">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" name="is_cutoff_initial" value="1" id="isCutoffInitialCheck">
                                    <label class="form-check-label fw-bold small text-dark" for="isCutoffInitialCheck">
                                        <i class="mdi mdi-calendar-check me-1 text-primary"></i> Jadikan saldo riil ini sebagai Saldo Awal Cut-Off baru
                                    </label>
                                    <div class="text-muted ms-4" style="font-size: 11px;">
                                        Centang jika penyesuaian ini merupakan penetapan saldo awal buku tahun baru / cut-off akuntansi.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top py-2 px-4">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning fw-semibold">
                            <i class="mdi mdi-check me-1"></i> Terapkan Penyesuaian Saldo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modals Edit Bank (Diletakkan di root page agar tampil penuh dengan backdrop normal) --}}
    @foreach ($banks as $item)
        <div class="modal fade" id="editBankModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
                    @if($item->total_tx_count > 0)
                        {{-- Mode Read-only / Terkunci karena sudah ada transaksi --}}
                        <div class="modal-header bg-secondary text-white py-3 px-4">
                            <h5 class="modal-title fw-bold text-white d-flex align-items-center gap-2">
                                <i class="mdi mdi-lock-outline"></i> Detail Rekening (Terkunci) - {{ $item->bank }} ({{ $item->no_rek }})
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="alert alert-warning border-0 d-flex align-items-start gap-2 mb-3 p-3 rounded" style="background-color: #fff8e6; border-left: 4px solid #ffab00 !important;">
                                <i class="mdi mdi-shield-lock-outline fs-4 text-warning flex-shrink-0 mt-0.5"></i>
                                <div style="font-size: 13px;">
                                    <strong class="text-dark d-block mb-1">Rekening Terkunci (Memiliki {{ $item->total_tx_count }} Riwayat Transaksi)</strong>
                                    <span class="text-muted">
                                        Rekening ini telah memiliki <strong>{{ $item->total_tx_count }} riwayat mutasi / transaksi</strong> aktif (penerimaan AR, pembayaran AP, kas kecil, beban operasional, dsb).
                                        Demi menjaga validitas pembukuan, jurnal akuntansi, dan rekonsiliasi kas/bank, data identitas dan saldo rekening ini <strong>tidak dapat diubah atau dihapus</strong>.
                                        <br><br>
                                        <em>Catatan: Jika rekening ini sudah tidak dipakai oleh perusahaan, silakan gunakan opsi <strong>Nonaktifkan</strong> pada tabel.</em>
                                    </span>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Entitas / Tujuan AR</label>
                                    <input type="text" class="form-control bg-light" value="{{ ($item->entity ?? 'Reftech') == 'Kojisha' ? 'KOJISHA (Kojisha Innotiv Indonesia)' : 'REFTECH (Reftech Jaya Optima / Reftech)' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Nama Bank</label>
                                    <input type="text" class="form-control bg-light" value="{{ $item->bank }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">No. Rekening</label>
                                    <input type="text" class="form-control bg-light" value="{{ $item->no_rek }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Atas Nama</label>
                                    <input type="text" class="form-control bg-light" value="{{ $item->atas_nama }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Kantor Cabang (KCP)</label>
                                    <input type="text" class="form-control bg-light" value="{{ $item->branch ?: '-' }}" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small text-muted">Saldo Awal (Rp)</label>
                                    <input type="text" class="form-control bg-light" value="{{ number_format($item->initial_balance ?: 0, 0, ',', '.') }}" disabled>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small text-muted">Saldo Terkini / Berjalan (Rp)</label>
                                    <input type="text" class="form-control bg-light fw-bold text-primary" value="Rp {{ number_format($item->saldo, 0, ',', '.') }}" disabled>
                                    <small class="text-muted" style="font-size: 10px;">Saldo berjalan terupdate otomatis mengikuti mutasi transaksi</small>
                                </div>
                                @if($item->is_petty_cash)
                                    <div class="col-12">
                                        <div class="card bg-light border p-3 rounded">
                                            <div class="fw-bold text-dark mb-2">
                                                <i class="mdi mdi-cash-register me-1 text-primary"></i> Akun Kas Kecil (Petty Cash)
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small text-muted">PIC Pemegang Kasir</label>
                                                    <input type="text" class="form-control bg-white" value="{{ $item->pic ? $item->pic->name : '-' }}" disabled>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small text-muted">Batas Plafon Kas (Rp)</label>
                                                    <input type="text" class="form-control bg-white" value="Rp {{ number_format($item->plafond ?: 0, 0, ',', '.') }}" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12">
                                    <label class="form-label fw-semibold small text-muted">Catatan / Keterangan</label>
                                    <textarea class="form-control bg-light" rows="2" disabled>{{ $item->description ?: '-' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top py-2 px-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-secondary" disabled>
                                <i class="mdi mdi-lock-outline me-1"></i> Data Terkunci (Ada {{ $item->total_tx_count }} Mutasi)
                            </button>
                        </div>
                    @else
                        {{-- Mode Edit Aktif untuk Rekening Baru / Belum Ada Transaksi --}}
                        <form action="{{ route('bank.update', $item->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header bg-warning text-dark py-3 px-4">
                                <h5 class="modal-title fw-bold d-flex align-items-center gap-2">
                                    <i class="mdi mdi-pencil-box-outline"></i> Edit Rekening - {{ $item->bank }} ({{ $item->no_rek }})
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 text-start">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Entitas / Tujuan AR <span class="text-danger">*</span></label>
                                        <select name="entity" class="form-select" required>
                                            <option value="Reftech" {{ ($item->entity ?? 'Reftech') == 'Reftech' ? 'selected' : '' }}>REFTECH (Reftech Jaya Optima / Reftech)</option>
                                            <option value="Kojisha" {{ ($item->entity ?? 'Reftech') == 'Kojisha' ? 'selected' : '' }}>KOJISHA (Kojisha Innotiv Indonesia)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Nama Bank <span class="text-danger">*</span></label>
                                        <input type="text" name="bank" class="form-control" value="{{ $item->bank }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">No. Rekening <span class="text-danger">*</span></label>
                                        <input type="text" name="no_rek" class="form-control" value="{{ $item->no_rek }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Atas Nama <span class="text-danger">*</span></label>
                                        <input type="text" name="atas_nama" class="form-control" value="{{ $item->atas_nama }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Kantor Cabang (KCP) / Wilayah</label>
                                        <input type="text" name="branch" list="branchListSuggestions" class="form-control" value="{{ $item->branch }}" placeholder="Pilih cabang: Bandung / Palembang">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small">Saldo Awal (Rp)</label>
                                        <input type="text" inputmode="numeric" name="initial_balance" class="form-control rupiah-mask" value="{{ $item->initial_balance ? number_format($item->initial_balance, 0, ',', '.') : 0 }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold small">Penyesuaian Saldo Terkini (Rp)</label>
                                        <input type="text" inputmode="numeric" name="adjust_saldo" class="form-control rupiah-mask" value="{{ number_format($item->saldo, 0, ',', '.') }}">
                                        <small class="text-muted" style="font-size: 10px;">Ubah jika perlu rekonsiliasi kas riil</small>
                                    </div>
                                    <div class="col-12">
                                        <div class="card bg-light border p-3 rounded">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" name="is_petty_cash" value="1" id="isPettyCashEdit-{{ $item->id }}" {{ $item->is_petty_cash ? 'checked' : '' }} onchange="togglePettyCashFields('Edit-{{ $item->id }}')">
                                                <label class="form-check-label fw-bold text-dark" for="isPettyCashEdit-{{ $item->id }}">
                                                    <i class="mdi mdi-cash-register me-1 text-primary"></i> Akun Kas Kecil (Petty Cash)
                                                </label>
                                            </div>
                                            <div id="pettyCashFieldsEdit-{{ $item->id }}" style="display: {{ $item->is_petty_cash ? 'block' : 'none' }};" class="mt-3 pt-3 border-top">
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small">PIC Pemegang Kasir</label>
                                                        <select name="pic_id" class="form-select">
                                                            <option value="">-- Pilih PIC --</option>
                                                            @foreach($users as $u)
                                                                <option value="{{ $u->id }}" {{ $item->pic_id == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold small">Batas Plafon Kas (Rp)</label>
                                                        <input type="text" inputmode="numeric" name="plafond" class="form-control rupiah-mask" value="{{ $item->plafond ? number_format($item->plafond, 0, ',', '.') : 0 }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold small">Catatan / Keterangan</label>
                                        <textarea name="description" class="form-control" rows="2">{{ $item->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-top py-2 px-4">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning">
                                    <i class="mdi mdi-check me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('after-script')
<script>
    function togglePettyCashFields(suffix) {
        var chk = document.getElementById('isPettyCash' + suffix);
        var container = document.getElementById('pettyCashFields' + suffix);
        if (chk && container) {
            container.style.display = chk.checked ? 'block' : 'none';
        }
    }

    function updateAdjCalculation() {
        var select = document.getElementById('adjBankSelect');
        var newSaldoInput = document.getElementById('adjNewSaldoInput');
        var curSaldoDisplay = document.getElementById('adjCurrentSaldoDisplay');
        var diffNominal = document.getElementById('adjDiffNominal');
        var diffBadge = document.getElementById('adjDiffBadge');
        var diffExplanation = document.getElementById('adjDiffExplanation');

        if (!select || !newSaldoInput) return;

        var selectedOption = select.options[select.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            curSaldoDisplay.value = '0';
            diffNominal.innerText = 'Rp 0';
            diffBadge.className = 'badge bg-secondary rounded-pill px-2.5 py-1';
            diffBadge.innerText = 'Pilih Rekening';
            return;
        }

        var currentSaldo = parseFloat(selectedOption.getAttribute('data-saldo')) || 0;
        curSaldoDisplay.value = new Intl.NumberFormat('id-ID').format(currentSaldo);

        var rawNewVal = newSaldoInput.value.replace(/\./g, '').replace(/,/g, '.').replace(/[^0-9.-]/g, '');
        var newSaldo = parseFloat(rawNewVal) || 0;

        var diff = newSaldo - currentSaldo;
        var diffAbsFormatted = new Intl.NumberFormat('id-ID').format(Math.abs(diff));

        if (diff > 0) {
            diffNominal.className = 'mb-1 fw-bold text-success';
            diffNominal.innerText = '+ Rp ' + diffAbsFormatted;
            diffBadge.className = 'badge bg-label-success rounded-pill px-2.5 py-1';
            diffBadge.innerText = 'Penerimaan (+)';
            diffExplanation.innerText = 'Saldo riil lebih besar dari sistem. Selisih ini akan dicatat sebagai penyesuaian penerimaan masuk (kredit) di buku bank.';
        } else if (diff < 0) {
            diffNominal.className = 'mb-1 fw-bold text-danger';
            diffNominal.innerText = '- Rp ' + diffAbsFormatted;
            diffBadge.className = 'badge bg-label-danger rounded-pill px-2.5 py-1';
            diffBadge.innerText = 'Pengeluaran / Koreksi (-)';
            diffExplanation.innerText = 'Saldo riil lebih kecil dari sistem. Selisih ini akan dicatat sebagai penyesuaian pengeluaran (debet) untuk merefleksikan biaya/pengeluaran yang belum tercatat.';
        } else {
            diffNominal.className = 'mb-1 fw-bold text-dark';
            diffNominal.innerText = 'Rp 0';
            diffBadge.className = 'badge bg-label-info rounded-pill px-2.5 py-1';
            diffBadge.innerText = 'Saldo Sesuai';
            diffExplanation.innerText = 'Tidak ada perbedaan saldo antara sistem dan rekening koran.';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    });
</script>
@endpush

