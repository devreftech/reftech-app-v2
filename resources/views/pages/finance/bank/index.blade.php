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
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-label-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#transferBankModal">
                <i class="mdi mdi-bank-transfer me-1"></i> Transfer Antar Bank
            </button>
            <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createBankModal">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Rekening Bank
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
                        <span class="text-info">REFTECH (AR Reftech)</span>
                        <span class="badge bg-label-info rounded-pill ms-1">{{ count($reftechBanks) }}</span>
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

            {{-- Tab 2: Reftech Banks --}}
            <div class="tab-pane fade" id="tab-reftech" role="tabpanel">
                <div class="alert alert-info d-flex align-items-center m-3 mb-0 py-2.5 px-3 rounded" role="alert">
                    <i class="mdi mdi-information-outline me-2 fs-5"></i>
                    <div style="font-size: 13px;">
                        <strong>Rekening Bank REFTECH:</strong> Digunakan untuk penampungan pembayaran faktur / invoice AR Reftech (baik transaksi PPN PT. Reftech Jaya Optima maupun Non-PPN) serta rekening operasional.
                    </div>
                </div>
                @include('pages.finance.bank._table_rows', ['bankList' => $reftechBanks, 'tabId' => 'reftech'])
            </div>

            {{-- Tab 3: Kojisha Banks --}}
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
                                <label class="form-label fw-semibold small">Kantor Cabang (KCP)</label>
                                <input type="text" name="branch" class="form-control" placeholder="Contoh: Bandung Asia Afrika">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Saldo Awal (Rp)</label>
                                <input type="number" step="any" name="initial_balance" class="form-control" placeholder="0" value="0">
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
                                                <input type="number" step="any" min="0" name="plafond" class="form-control" placeholder="Contoh: 5000000" value="0">
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
                                <input type="number" step="any" min="1" name="amount" class="form-control" placeholder="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Biaya Admin Bank (Rp)</label>
                                <input type="number" step="any" min="0" name="fee" class="form-control" placeholder="0" value="0">
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

    {{-- Modals Edit Bank (Diletakkan di root page agar tampil penuh dengan backdrop normal) --}}
    @foreach ($banks as $item)
        <div class="modal fade" id="editBankModal-{{ $item->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0">
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
                                    <label class="form-label fw-semibold small">Kantor Cabang (KCP)</label>
                                    <input type="text" name="branch" class="form-control" value="{{ $item->branch }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Saldo Awal (Rp)</label>
                                    <input type="number" step="any" name="initial_balance" class="form-control" value="{{ $item->initial_balance }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold small">Penyesuaian Saldo Terkini (Rp)</label>
                                    <input type="number" step="any" name="adjust_saldo" class="form-control" value="{{ $item->saldo }}">
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
                                                    <input type="number" step="any" min="0" name="plafond" class="form-control" value="{{ $item->plafond ?: 0 }}">
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
</script>
@endpush

