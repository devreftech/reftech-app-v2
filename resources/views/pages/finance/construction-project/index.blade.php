@extends('layouts.sales.app')

@section('title', 'Proyek Konstruksi - Catatan Pengeluaran Pembangunan')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
    <style>
        .kpi-stat-card {
            border-radius: 10px;
            border: 1px solid rgba(67, 89, 113, 0.12);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .kpi-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }
        .table-custom th {
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        .table-custom td {
            vertical-align: middle;
            font-size: 0.88rem;
        }
        /* Custom modal scrollbar styling like Kanban */
        .modal-dialog-scrollable .modal-body {
            max-height: calc(85vh - 140px);
            overflow-y: auto;
        }
        .modal-dialog-scrollable .modal-body::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        .modal-dialog-scrollable .modal-body::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4 py-3 flex-grow-1">

    <!-- Header & Top Action Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-hammer-wrench text-primary fs-3"></i>
                <span>Proyek Konstruksi &amp; Biaya Pembangunan</span>
            </h4>
            <p class="text-muted mb-0 small">
                Pencatatan harian biaya material, upah jasa tukang, dan sewa alat lapangan untuk proyek pembangunan gedung/workshop.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            @if ($buildings->isNotEmpty())
                <!-- Dropdown Pilih Gedung / Proyek -->
                <form method="GET" action="{{ route('proyek-konstruksi.index') }}" class="d-inline-block">
                    <select name="building_id" class="form-select form-select-sm fw-semibold border-primary" onchange="this.form.submit()">
                        @foreach ($buildings as $bld)
                            <option value="{{ $bld->id }}" {{ ($selectedBuilding && $selectedBuilding->id == $bld->id) ? 'selected' : '' }}>
                                🏢 {{ $bld->desc }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif

            @if ($selectedBuilding)
                <a href="{{ route('fixed.show', $selectedBuilding->id) }}" class="btn btn-outline-secondary btn-sm waves-effect" title="Lihat di Modul Fixed Asset">
                    <i class="mdi mdi-domain me-1"></i> Fixed Asset
                </a>

                <button type="button" class="btn btn-outline-primary btn-sm waves-effect shadow-sm" data-bs-toggle="modal" data-bs-target="#modalLinkPo">
                    <i class="mdi mdi-link-variant me-1"></i> Hubungkan PO
                </button>

                <button type="button" class="btn btn-primary btn-sm waves-effect shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddCost">
                    <i class="mdi mdi-plus-circle-outline me-1"></i> Catat Pengeluaran
                </button>
            @endif
        </div>
    </div>

    @if (!$selectedBuilding)
        <!-- Empty State jika belum ada Aset Bangunan yang di-assign -->
        <div class="card shadow-sm text-center py-5">
            <div class="card-body">
                <i class="mdi mdi-office-building-cog text-muted" style="font-size: 64px;"></i>
                @if (in_array(Auth::user()?->role, ['Admin', 'Developer', 'Accounting', 'Finance Manager']))
                    <h5 class="fw-bold mt-3 mb-1">Belum Ada Aset Bangunan / Gedung</h5>
                    <p class="text-muted col-md-6 mx-auto small">
                        Untuk mulai mencatat biaya pembangunan, buat aset tetap kategori <strong>Bangunan</strong> dengan status <em>Dalam Proses Pembangunan (Proyek Konstruksi)</em>.
                    </p>
                    <a href="{{ route('fixed.create') }}?type=Bangunan" class="btn btn-primary btn-sm">
                        <i class="mdi mdi-plus me-1"></i> Buat Aset Bangunan Baru
                    </a>
                @else
                    <h5 class="fw-bold mt-3 mb-1">Belum Ada Proyek Bangunan yang Ditugaskan</h5>
                    <p class="text-muted col-md-6 mx-auto small">
                        Akun Anda belum didaftarkan sebagai <strong>PIC Petugas Penginput</strong> pada proyek pembangunan gedung manapun. Silakan hubungi Administrator atau Finance untuk meminta akses penugasan.
                    </p>
                @endif
            </div>
        </div>
    @else

        <!-- Banner Info Proyek Terpilih -->
        <div class="card mb-3 border-0 shadow-xs" style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);">
            <div class="card-body p-3">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-primary text-white rounded d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-office-building fs-3"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <h5 class="fw-bold text-dark mb-0">{{ $selectedBuilding->desc }}</h5>
                                <span class="badge bg-primary font-monospace">{{ $selectedBuilding->code ?: 'BGN-' . $selectedBuilding->id }}</span>
                                @if ($selectedBuilding->status_bangunan === 'construction')
                                    <span class="badge bg-warning"><i class="mdi mdi-hammer-wrench me-1"></i>Dalam Proses Pembangunan</span>
                                @else
                                    <span class="badge bg-success"><i class="mdi mdi-check-circle-outline me-1"></i>Siap Operasional</span>
                                @endif
                                <span class="badge bg-label-info">{{ ucfirst($selectedBuilding->tipe_pengadaan ?: 'swakelola') }}</span>
                            </div>
                            <small class="text-muted d-block mt-0.5">
                                <i class="mdi mdi-map-marker-outline me-1"></i>{{ $selectedBuilding->lokasi_bangunan ?: 'Lokasi belum diisi' }}
                                @if ($selectedBuilding->luas_bangunan)
                                    | Luas: {{ $selectedBuilding->luas_bangunan }} m²
                                @endif
                            </small>
                        </div>
                    </div>

                    @if ($unmappedCount > 0 && in_array(Auth::user()?->role, ['Admin', 'Developer', 'Accounting', 'Finance Manager']))
                        <div class="alert alert-warning py-1.5 px-3 mb-0 d-flex align-items-center gap-2 small">
                            <i class="mdi mdi-alert-circle-outline fs-5"></i>
                            <div>
                                Ada <strong>{{ $unmappedCount }}</strong> pengeluaran lapangan yang <strong>belum dipetakan Akun Kas / Beban COA</strong>.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- 4 KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <!-- Grand Total Proyek -->
            <div class="col-sm-6 col-xl-3">
                <div class="card kpi-stat-card bg-white h-100 p-3" style="border-left: 4px solid #10b981 !important;">
                    <div class="d-flex align-items-start justify-content-between mb-1">
                        <span class="detail-label text-muted small fw-semibold">Total Biaya Proyek</span>
                        <span class="badge bg-label-success" style="font-size: 10px;">Grand Total</span>
                    </div>
                    <h4 class="fw-bold mb-1 text-success" style="font-size: 1.25rem;">
                        Rp {{ number_format($totalProjectCost, 0, ',', '.') }}
                    </h4>
                    <small class="text-muted d-block" style="font-size: 0.74rem;">
                        Akumulasi material, upah, dan operasional
                    </small>
                </div>
            </div>

            <!-- Material (Masuk Aktiva) -->
            <div class="col-sm-6 col-xl-3">
                <div class="card kpi-stat-card bg-white h-100 p-3" style="border-left: 4px solid #696cff !important;">
                    <div class="d-flex align-items-start justify-content-between mb-1">
                        <span class="detail-label text-muted small fw-semibold">Belanja Material</span>
                        <span class="badge bg-label-primary" style="font-size: 10px;">Kapitalisasi Aktiva</span>
                    </div>
                    <h4 class="fw-bold mb-1 text-primary" style="font-size: 1.25rem;">
                        Rp {{ number_format($totalMaterialCost, 0, ',', '.') }}
                    </h4>
                    <small class="text-muted d-block" style="font-size: 0.74rem;">
                        Otomatis masuk Nilai Perolehan Aset Tetap
                    </small>
                </div>
            </div>

            <!-- Jasa & Upah Tukang -->
            <div class="col-sm-6 col-xl-3">
                <div class="card kpi-stat-card bg-white h-100 p-3" style="border-left: 4px solid #f59e0b !important;">
                    <div class="d-flex align-items-start justify-content-between mb-1">
                        <span class="detail-label text-muted small fw-semibold">Jasa &amp; Upah Tukang</span>
                        <span class="badge bg-label-warning" style="font-size: 10px;">Expense</span>
                    </div>
                    <h4 class="fw-bold mb-1 text-warning" style="font-size: 1.25rem;">
                        Rp {{ number_format($totalJasaCost, 0, ',', '.') }}
                    </h4>
                    <small class="text-muted d-block" style="font-size: 0.74rem;">
                        Upah mandor, tukang, borongan
                    </small>
                </div>
            </div>

            <!-- Sewa & Operasional -->
            <div class="col-sm-6 col-xl-3">
                <div class="card kpi-stat-card bg-white h-100 p-3" style="border-left: 4px solid #03c3ec !important;">
                    <div class="d-flex align-items-start justify-content-between mb-1">
                        <span class="detail-label text-muted small fw-semibold">Sewa Alat &amp; Operasional</span>
                        <span class="badge bg-label-info" style="font-size: 10px;">Expense</span>
                    </div>
                    <h4 class="fw-bold mb-1 text-info" style="font-size: 1.25rem;">
                        Rp {{ number_format($totalSewaCost, 0, ',', '.') }}
                    </h4>
                    <small class="text-muted d-block" style="font-size: 0.74rem;">
                        Sewa molen, scaffolding, konsumsi dll
                    </small>
                </div>
            </div>
        </div>

        <!-- Filter & Search Card -->
        <div class="card shadow-sm mb-3">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('proyek-konstruksi.index') }}">
                    <input type="hidden" name="building_id" value="{{ $selectedBuilding->id }}">
                    <div class="row g-2 align-items-center">
                        <div class="col-md-4 col-12">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                <input type="text" name="q" class="form-control" placeholder="Cari deskripsi / pembayaran / bukti..." value="{{ request('q') }}">
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <select name="kategori" class="form-select form-select-sm">
                                <option value="">-- Semua Kategori --</option>
                                <option value="material" {{ request('kategori') == 'material' ? 'selected' : '' }}>🏗️ Material Fisik</option>
                                <option value="jasa" {{ request('kategori') == 'jasa' ? 'selected' : '' }}>👷 Jasa / Upah</option>
                                <option value="sewa" {{ request('kategori') == 'sewa' ? 'selected' : '' }}>⚙️ Sewa Alat</option>
                                <option value="operasional" {{ request('kategori') == 'operasional' ? 'selected' : '' }}>📦 Operasional</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="date" name="date_from" class="form-control form-control-sm" placeholder="Dari Tgl" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2 col-6">
                            <input type="date" name="date_to" class="form-control form-control-sm" placeholder="Sampai Tgl" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-1 col-6 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-primary w-100" title="Filter Data">
                                <i class="mdi mdi-filter-outline"></i>
                            </button>
                            <a href="{{ route('proyek-konstruksi.index', ['building_id' => $selectedBuilding->id]) }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                                <i class="mdi mdi-refresh"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Catatan Pengeluaran -->
        <div class="card shadow-sm mb-4">
            <div class="card-header border-bottom py-3 px-3 px-md-4 d-flex justify-content-between align-items-center bg-transparent">
                <h5 class="card-title mb-0 fw-bold fs-6">
                    <i class="mdi mdi-format-list-bulleted text-primary me-2"></i>Rincian Pengeluaran Lapangan
                </h5>
                <span class="text-muted small">Total <strong>{{ $costs->total() }}</strong> transaksi tercatat</span>
            </div>

            <div class="table-responsive">
                <table class="table table-custom table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Deskripsi / Item</th>
                            <th class="text-center">Qty &amp; Satuan</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Total Biaya</th>
                            <th>Payment / Penerima</th>
                            <th class="text-center">Nota / Bukti</th>
                            @if (in_array(Auth::user()?->role, ['Admin', 'Developer', 'Accounting', 'Finance Manager']))
                                <th>Mapping COA</th>
                            @endif
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($costs as $cost)
                            <tr>
                                <!-- Tanggal -->
                                <td class="small text-nowrap fw-semibold">
                                    {{ $cost->tanggal ? $cost->tanggal->format('d M Y') : '-' }}
                                    @if ($cost->creator)
                                        <div class="text-muted" style="font-size: 11px;">Oleh: {{ $cost->creator->name }}</div>
                                    @endif
                                </td>

                                <!-- Kategori -->
                                <td>
                                    @if ($cost->kategori_biaya === 'material')
                                        <span class="badge bg-label-primary d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-cube-outline"></i> Material (Aktiva)
                                        </span>
                                    @elseif ($cost->kategori_biaya === 'jasa')
                                        <span class="badge bg-label-warning d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-account-hard-hat"></i> Jasa/Upah
                                        </span>
                                    @elseif ($cost->kategori_biaya === 'sewa')
                                        <span class="badge bg-label-info d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-cog-outline"></i> Sewa Alat
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary d-inline-flex align-items-center gap-1">
                                            <i class="mdi mdi-cash-multiple"></i> Operasional
                                        </span>
                                    @endif
                                </td>

                                <!-- Deskripsi / Item -->
                                <td>
                                    <div class="fw-bold text-dark">{{ $cost->nama_item }}</div>
                                    <div class="d-flex flex-wrap gap-1 align-items-center mt-1">
                                        @if ($cost->purchase_order_id)
                                            <a href="{{ route('purchase.show', $cost->purchase_order_id) }}" target="_blank" class="badge bg-label-info text-decoration-none d-inline-flex align-items-center gap-1" title="Buka Detail Purchase Order">
                                                <i class="mdi mdi-link-variant"></i> PO: {{ $cost->purchaseOrder?->no_po ?: $cost->no_bukti }}
                                            </a>
                                        @endif
                                        @if ($cost->supplier)
                                            <small class="text-primary"><i class="mdi mdi-store-outline me-1"></i>{{ $cost->supplier->supplier }}</small>
                                        @endif
                                    </div>
                                    @if ($cost->catatan)
                                        <small class="text-muted fst-italic d-block mt-0.5">{{ $cost->catatan }}</small>
                                    @endif
                                </td>

                                <!-- Qty & Satuan -->
                                <td class="text-center small">
                                    <span class="fw-semibold">{{ $cost->qty }}</span> {{ $cost->satuan ?: '' }}
                                </td>

                                <!-- Harga Satuan -->
                                <td class="text-end small text-muted">
                                    Rp {{ number_format($cost->harga_satuan, 0, ',', '.') }}
                                </td>

                                <!-- Total Biaya -->
                                <td class="text-end fw-bold {{ $cost->kategori_biaya === 'material' ? 'text-primary' : 'text-warning' }} text-nowrap">
                                    Rp {{ number_format($cost->total_biaya, 0, ',', '.') }}
                                </td>

                                <!-- Payment / Penerima -->
                                <td class="small">
                                    {{ $cost->payee ?: '-' }}
                                    @if ($cost->no_bukti)
                                        <div class="text-muted font-monospace" style="font-size: 11px;">Ref: {{ $cost->no_bukti }}</div>
                                    @endif
                                </td>

                                <!-- Nota / Bukti -->
                                <td class="text-center">
                                    @if ($cost->foto_bukti)
                                        <a href="{{ asset($cost->foto_bukti) }}" target="_blank" class="btn btn-xs btn-label-secondary">
                                            <i class="mdi mdi-file-document-outline me-1"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <!-- Mapping COA (Khusus Finance/Admin/Accounting) -->
                                @if (in_array(Auth::user()?->role, ['Admin', 'Developer', 'Accounting', 'Finance Manager']))
                                    <td class="small">
                                        @if ($cost->pengeluaran)
                                            <div class="d-flex flex-column gap-0.5">
                                                <span class="badge bg-label-success" style="font-size: 10px;" title="Akun Kas / Bank">
                                                    <i class="mdi mdi-bank-outline me-1"></i>{{ $cost->pengeluaran->code }}
                                                </span>
                                                @if ($cost->beban)
                                                    <span class="badge bg-label-info" style="font-size: 10px;" title="Akun Beban">
                                                        <i class="mdi mdi-book-outline me-1"></i>{{ $cost->beban->code }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <button type="button" class="btn btn-xs btn-label-warning btn-map-coa" 
                                                data-id="{{ $cost->id }}"
                                                data-item="{{ $cost->nama_item }}"
                                                data-total="Rp {{ number_format($cost->total_biaya, 0, ',', '.') }}"
                                                data-cat="{{ $cost->kategori_biaya }}"
                                                data-pengeluaran="{{ $cost->id_pengeluaran }}"
                                                data-beban="{{ $cost->id_beban }}"
                                                data-nobukti="{{ $cost->no_bukti }}"
                                                data-bs-toggle="modal" data-bs-target="#modalMapCoa">
                                                <i class="mdi mdi-plus-box-outline me-1"></i> Petakan COA
                                            </button>
                                        @endif
                                    </td>
                                @endif

                                <!-- Aksi -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <!-- Edit Lapangan -->
                                        <button type="button" class="btn btn-xs btn-outline-primary btn-edit-cost"
                                            data-id="{{ $cost->id }}"
                                            data-poid="{{ $cost->purchase_order_id }}"
                                            data-tanggal="{{ $cost->tanggal ? $cost->tanggal->format('Y-m-d') : '' }}"
                                            data-kategori="{{ $cost->kategori_biaya }}"
                                            data-item="{{ $cost->nama_item }}"
                                            data-supplier="{{ $cost->supplier_id }}"
                                            data-payee="{{ $cost->payee }}"
                                            data-qty="{{ $cost->qty }}"
                                            data-satuan="{{ $cost->satuan }}"
                                            data-hargasatuan="{{ number_format($cost->harga_satuan, 0, ',', '.') }}"
                                            data-totalbiaya="{{ number_format($cost->total_biaya, 0, ',', '.') }}"
                                            data-nobukti="{{ $cost->no_bukti }}"
                                            data-catatan="{{ $cost->catatan }}"
                                            data-bs-toggle="modal" data-bs-target="#modalEditCost"
                                            title="Edit Catatan">
                                            <i class="mdi mdi-pencil-outline"></i>
                                        </button>

                                        <!-- Hapus -->
                                        <form action="{{ route('proyek-konstruksi.destroy', $cost->id) }}" method="POST" class="d-inline form-delete-cost">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-xs btn-outline-danger btn-delete-cost" title="Hapus Biaya">
                                                <i class="mdi mdi-trash-can-outline"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="mdi mdi-clipboard-text-outline fs-1 d-block mb-2 text-secondary"></i>
                                    <h5>Belum Ada Catatan Biaya</h5>
                                    <p class="small mb-3">Belum ada pengeluaran yang dicatat untuk proyek gedung ini.</p>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddCost">
                                        <i class="mdi mdi-plus-circle-outline me-1"></i> Catat Pengeluaran Pertama
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($costs->hasPages())
                <div class="card-footer border-top py-2 px-3">
                    {{ $costs->links() }}
                </div>
            @endif
        </div>
    @endif
</div>

<!-- ==========================================
     MODAL 1: FORM CATAT PENGELUARAN LAPANGAN (MULTI-ITEM NOTA)
     ========================================== -->
@if ($selectedBuilding)
<div class="modal fade" id="modalAddCost" tabindex="-1" aria-labelledby="modalAddCostLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1200px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('proyek-konstruksi.store') }}" method="POST" enctype="multipart/form-data" id="formAddMultiCost">
                @csrf
                <input type="hidden" name="fixed_asset_id" value="{{ $selectedBuilding->id }}">

                <div class="modal-header border-bottom py-3 px-4" style="background-color: #f8fafc;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-label-primary rounded d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-clipboard-plus-outline fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="modalAddCostLabel">
                                Catat Pengeluaran Proyek Pembangunan
                            </h5>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-label-primary font-monospace">{{ $selectedBuilding->code ?: 'FA-' . $selectedBuilding->id }}</span>
                                <span class="text-muted small">Proyek: <strong class="text-dark">{{ $selectedBuilding->desc }}</strong></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- SECTION 1: INFORMASI TRANSAKSI & BUKTI NOTA -->
                    <div class="p-3 mb-4 rounded-3 border" style="background-color: #f8fafc;">
                        <h6 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                            <i class="mdi mdi-receipt-text-outline fs-5"></i>
                            <span>1. Informasi Nota / Transaksi Lapangan</span>
                        </h6>

                        <div class="row g-3">
                            <!-- Tanggal Pengeluaran -->
                            <div class="col-md-3 col-sm-6 col-12">
                                <label class="form-label fw-semibold text-dark small mb-1">
                                    Tanggal Transaksi <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i class="mdi mdi-calendar-blank"></i></span>
                                    <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <!-- Payment / Keterangan Pembayaran -->
                            <div class="col-md-4 col-sm-6 col-12">
                                <label class="form-label fw-semibold text-dark small mb-1">
                                    Payment / Penerima / Kasbon
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i class="mdi mdi-cash-fast"></i></span>
                                    <input type="text" class="form-control" name="payee" placeholder="Contoh: Kasbon Mandor Joko / Transfer / Cash">
                                </div>
                            </div>

                            <!-- Toko / Supplier Rekanan -->
                            <div class="col-md-5 col-12">
                                <label class="form-label fw-semibold text-dark small mb-1">
                                    Toko Bangunan / Rekanan (Opsional)
                                </label>
                                <select name="supplier_id" class="form-select form-select-sm select2-add-supplier">
                                    <option value="">-- Pilih Rekanan / Toko (Opsional) --</option>
                                    @foreach ($suppliers as $supp)
                                        <option value="{{ $supp->id }}">{{ $supp->supplier }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- No. Nota / Kwitansi / Invoice -->
                            <div class="col-md-3 col-sm-6 col-12">
                                <label class="form-label fw-semibold text-dark small mb-1">No. Nota / Invoice / Kwitansi</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i class="mdi mdi-receipt-text-check-outline"></i></span>
                                    <input type="text" class="form-control font-monospace" name="no_bukti" placeholder="Contoh: 108/TB/IX">
                                </div>
                            </div>

                            <!-- Upload Foto Nota / Invoice / Kwitansi -->
                            <div class="col-md-4 col-sm-6 col-12">
                                <label class="form-label fw-semibold text-dark small mb-1 d-flex align-items-center justify-content-between">
                                    <span>Upload Nota / Invoice / Struk</span>
                                    <span class="text-muted" style="font-size: 11px;">(JPG/PNG/PDF)</span>
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white text-primary"><i class="mdi mdi-paperclip"></i></span>
                                    <input type="file" class="form-control form-control-sm bg-white" name="foto_bukti" accept="image/*,application/pdf">
                                </div>
                            </div>

                            <!-- Catatan Tambahan Transaksi -->
                            <div class="col-md-5 col-12">
                                <label class="form-label fw-semibold text-dark small mb-1">Catatan Tambahan (Opsional)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-white"><i class="mdi mdi-comment-text-outline"></i></span>
                                    <input type="text" class="form-control form-control-sm bg-white" name="catatan" placeholder="Catatan tahap pekerjaan / lokasi pasang...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: DAFTAR RINCIAN ITEM (MULTI-ITEM ROWS) -->
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                            <i class="mdi mdi-format-list-bulleted-type fs-5"></i>
                            <span>2. Rincian Item Belanja / Pekerjaan</span>
                        </h6>
                        <button type="button" class="btn btn-xs btn-outline-primary shadow-xs btn-add-row-item">
                            <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Baris Item
                        </button>
                    </div>

                    <div class="table-responsive border rounded-3 mb-3 bg-white">
                        <table class="table table-bordered align-middle mb-0" id="tableAddItems">
                            <thead class="table-light text-nowrap">
                                <tr style="font-size: 0.82rem;">
                                    <th style="width: 200px;">Kategori <span class="text-danger">*</span></th>
                                    <th>Deskripsi / Nama Barang / Pekerjaan <span class="text-danger">*</span></th>
                                    <th style="width: 90px;" class="text-center">Qty</th>
                                    <th style="width: 90px;" class="text-center">Satuan</th>
                                    <th style="width: 170px;" class="text-end">Harga Satuan (Rp)</th>
                                    <th style="width: 180px;" class="text-end">Subtotal (Rp) <span class="text-danger">*</span></th>
                                    <th style="width: 50px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyAddItems">
                                <!-- Baris Item 1 (Default) -->
                                <tr class="item-row" data-index="0">
                                    <td>
                                        <select class="form-select form-select-sm fw-semibold row-kategori" name="items[0][kategori_biaya]" required>
                                            <option value="material" selected>🏗️ Material Fisik</option>
                                            <option value="jasa">👷 Jasa / Upah</option>
                                            <option value="sewa">⚙️ Sewa Alat</option>
                                            <option value="operasional">📦 Operasional</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm row-name" name="items[0][nama_item]" placeholder="Contoh: Semen Gresik 50 Sak" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm text-center fw-bold row-qty" name="items[0][qty]" value="1">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm text-center row-satuan" name="items[0][satuan]" placeholder="sak">
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light">Rp</span>
                                            <input type="text" class="form-control form-control-sm text-end currency-input row-price" name="items[0][harga_satuan]" placeholder="0">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light fw-bold text-primary">Rp</span>
                                            <input type="text" class="form-control form-control-sm text-end fw-bold text-primary currency-input row-total" name="items[0][total_biaya]" placeholder="0" required>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-outline-danger btn-remove-row" title="Hapus Baris">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER RINGKASAN GRAND TOTAL NOTA -->
                    <div class="p-3 rounded-3 border d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3" style="background: linear-gradient(135deg, #f8fafc 0%, #edf2f7 100%);">
                        <div class="small text-muted">
                            <div class="fw-semibold text-dark mb-0.5">
                                <i class="mdi mdi-information-outline text-primary me-1"></i>Informasi Akuntansi Otomatis:
                            </div>
                            <div>Setiap item berkategori <strong>Material Fisik</strong> otomatis diakumulasikan ke <em>Nilai Perolehan Aset Bangunan</em>.</div>
                        </div>

                        <div class="d-flex align-items-center gap-3 bg-white p-2.5 px-3.5 rounded-3 border shadow-xs">
                            <div class="text-end">
                                <span class="small text-muted d-block text-uppercase fw-semibold" style="letter-spacing: 0.5px;">Grand Total Nota</span>
                                <h4 class="fw-bold text-primary mb-0" id="add_grand_total_display" style="font-size: 1.4rem;">Rp 0</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-3 px-4" style="background-color: #f8fafc;">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="mdi mdi-content-save-check-outline me-1"></i> Simpan Semua Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL 1B: HUBUNGKAN PURCHASE ORDER (PO)
     ========================================== -->
<div class="modal fade" id="modalLinkPo" tabindex="-1" aria-labelledby="modalLinkPoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1140px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form action="{{ route('proyek-konstruksi.link-po') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="fixed_asset_id" value="{{ $selectedBuilding->id }}">

                <div class="modal-header border-bottom py-3 px-4" style="background-color: #f8fafc;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-label-info rounded d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-link-variant-plus fs-3 text-info"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="modalLinkPoLabel">
                                Hubungkan Pengeluaran dari Purchase Order (PO)
                            </h5>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <span class="badge bg-label-primary font-monospace">{{ $selectedBuilding->code ?: 'FA-' . $selectedBuilding->id }}</span>
                                <span class="text-muted small">Proyek: <strong class="text-dark">{{ $selectedBuilding->desc }}</strong></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- SELECTION BOX: NO PO - SUPPLIER - NOMINAL TOTAL -->
                    <div class="card border border-info border-opacity-25 bg-label-info bg-opacity-10 mb-4 shadow-none">
                        <div class="card-body p-3.5">
                            <label class="form-label fw-bold text-dark fs-6 mb-2 d-flex align-items-center gap-2">
                                <i class="mdi mdi-file-document-check-outline text-info fs-5"></i>
                                <span>Pilih Purchase Order (No PO — Supplier — Total Nominal) <span class="text-danger">*</span></span>
                            </label>
                            
                            <select name="purchase_order_id" id="link_po_select" class="form-select select2-link-po" required>
                                <option value="">-- Cari No PO / Nama Supplier / Nominal Total --</option>
                                @foreach ($purchaseOrders as $po)
                                    <option value="{{ $po->id }}"
                                        data-nopo="{{ $po->no_po }}"
                                        data-supplier="{{ $po->supplier?->supplier ?: 'Tanpa Supplier' }}"
                                        data-supplierid="{{ $po->id_supplier }}"
                                        data-date="{{ $po->date ? $po->date->format('Y-m-d') : date('Y-m-d') }}"
                                        data-total="{{ (float)$po->total }}"
                                        data-totalfmt="Rp {{ number_format($po->total, 0, ',', '.') }}"
                                        data-note="{{ $po->note }}">
                                        {{ $po->no_po }} — {{ $po->supplier?->supplier ?: 'No Supplier' }} — Rp {{ number_format($po->total, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- PO Live Summary Info Box (Appears when PO is selected) -->
                            <div id="link_po_preview_box" class="mt-3 p-3 bg-white rounded-3 border border-info border-opacity-25 d-none">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 pb-2 border-bottom mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-label-primary font-monospace fs-6" id="link_po_prev_nopo">-</span>
                                        <span class="fw-bold text-dark" id="link_po_prev_supplier">-</span>
                                    </div>
                                    <div>
                                        <span class="text-muted small me-1">Total PO:</span>
                                        <span class="fw-bold text-success fs-6" id="link_po_prev_total">Rp 0</span>
                                    </div>
                                </div>
                                <div class="row g-2 small text-muted">
                                    <div class="col-sm-6 col-12">
                                        <i class="mdi mdi-calendar-outline me-1"></i>Tanggal PO: <strong class="text-dark" id="link_po_prev_date">-</strong>
                                    </div>
                                    <div class="col-sm-6 col-12">
                                        <i class="mdi mdi-note-text-outline me-1"></i>Catatan PO: <span class="fst-italic text-dark" id="link_po_prev_note">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FORM DETAIL PENGELUARAN YANG AKAN DICATAT -->
                    <div class="row g-4">
                        <!-- KOLOM KIRI: RINCIAN ITEM & PERHITUNGAN BIAYA -->
                        <div class="col-lg-6 col-12 border-end-lg pe-lg-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                                    <i class="mdi mdi-cube-send fs-5"></i>
                                    <span>1. Rincian Item &amp; Kalkulasi Biaya Proyek</span>
                                </h6>
                                <span class="badge bg-label-secondary small">Data Proyek</span>
                            </div>

                            <div class="row g-3">
                                <!-- Tanggal & Kategori -->
                                <div class="col-sm-6 col-12">
                                    <label class="form-label fw-semibold text-dark">
                                        Tanggal Pengeluaran <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-calendar-blank"></i></span>
                                        <input type="date" class="form-control" name="tanggal" id="link_po_tanggal" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-12">
                                    <label class="form-label fw-semibold text-dark">
                                        Kategori Pengeluaran <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select fw-semibold" name="kategori_biaya" id="link_po_kategori" required>
                                        <option value="material" selected>🏗️ Material Fisik (Kapitalisasi Aktiva)</option>
                                        <option value="jasa">👷 Jasa / Upah Kontraktor</option>
                                        <option value="sewa">⚙️ Sewa Alat Berat / Mesin</option>
                                        <option value="operasional">📦 Operasional / Pengadaan Lainnya</option>
                                    </select>
                                </div>

                                <!-- Deskripsi / Item -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">
                                        Deskripsi / Nama Barang / Pekerjaan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-lg fs-6" name="nama_item" id="link_po_nama_item" placeholder="Contoh: Pembelian Besi Beton / Seng Gelombang" required>
                                </div>

                                <!-- Kalkulasi Qty x Harga Satuan = Total Box -->
                                <div class="col-12">
                                    <div class="p-3.5 rounded-3 border" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                            <span class="fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.5px;">
                                                <i class="mdi mdi-calculator-variant-outline me-1"></i>Kalkulasi Biaya
                                            </span>
                                            <small class="text-muted">Ditarik dari total PO</small>
                                        </div>

                                        <div class="row g-2 align-items-center">
                                            <div class="col-sm-3 col-6">
                                                <label class="form-label fw-semibold text-dark small mb-1">Jumlah (Qty)</label>
                                                <input type="number" step="0.01" class="form-control bg-white text-center fw-bold" name="qty" id="link_po_qty" value="1">
                                            </div>
                                            <div class="col-sm-3 col-6">
                                                <label class="form-label fw-semibold text-dark small mb-1">Satuan</label>
                                                <input type="text" class="form-control bg-white text-center" name="satuan" id="link_po_satuan" value="ls" placeholder="ls, unit, set">
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <label class="form-label fw-semibold text-dark small mb-1">Harga Satuan (Rp)</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-white">Rp</span>
                                                    <input type="text" class="form-control bg-white currency-input text-end fw-semibold" name="harga_satuan" id="link_po_hargasatuan" placeholder="0">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-3 pt-2 border-top">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="form-label fw-bold text-primary mb-0 fs-6">
                                                        Total Biaya Masuk Proyek <span class="text-danger">*</span>
                                                    </label>
                                                    <span class="badge bg-primary">Nominal PO</span>
                                                </div>
                                                <div class="input-group">
                                                    <span class="input-group-text fw-bold text-primary bg-white fs-5">Rp</span>
                                                    <input type="text" class="form-control form-control-lg fw-bold text-primary bg-white currency-input text-end" name="total_biaya" id="link_po_totalbiaya" placeholder="0" required style="font-size: 1.35rem; letter-spacing: 0.5px;">
                                                </div>
                                                <div class="alert alert-primary bg-label-primary py-2 px-3 mt-2 mb-0 d-flex align-items-center gap-2 border-0 rounded small">
                                                    <i class="mdi mdi-information-outline fs-5"></i>
                                                    <div>Data ini akan otomatis terhubung ke riwayat transaksi Purchase Order bersangkutan.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: REKANAN, PAYMENT & DOKUMEN -->
                        <div class="col-lg-6 col-12 ps-lg-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                                    <i class="mdi mdi-receipt-text-outline fs-5"></i>
                                    <span>2. Rekanan, Payment &amp; Dokumen Bukti</span>
                                </h6>
                                <span class="badge bg-label-info small">Dokumentasi</span>
                            </div>

                            <div class="row g-3">
                                <!-- Payment / Keterangan Pembayaran -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">
                                        Payment / Keterangan Pembayaran
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-cash-fast"></i></span>
                                        <input type="text" class="form-control" name="payee" id="link_po_payee" placeholder="Contoh: PO 133-P/RJO/IX/2026 - PT Daikin">
                                    </div>
                                </div>

                                <!-- Toko / Supplier Rekanan -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">
                                        Toko Bangunan / Supplier Rekanan
                                    </label>
                                    <select name="supplier_id" id="link_po_supplier_id" class="form-select select2-link-po-supplier">
                                        <option value="">-- Pilih Toko / Supplier --</option>
                                        @foreach ($suppliers as $supp)
                                            <option value="{{ $supp->id }}">{{ $supp->supplier }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- No. Nota / PO & Upload Bukti Tambahan -->
                                <div class="col-sm-5 col-12">
                                    <label class="form-label fw-semibold text-dark">No. Bukti / PO</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-pound"></i></span>
                                        <input type="text" class="form-control font-monospace" name="no_bukti" id="link_po_nobukti" placeholder="No. PO">
                                    </div>
                                </div>

                                <div class="col-sm-7 col-12">
                                    <label class="form-label fw-semibold text-dark">Upload Nota / Dokumen Fisik (Opsional)</label>
                                    <input type="file" class="form-control" name="foto_bukti" accept="image/*,application/pdf">
                                    <small class="text-muted">Format: JPG, PNG, atau PDF (Maks. 5 MB)</small>
                                </div>

                                <!-- Catatan Lapangan -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">Catatan Proyek Tambahan</label>
                                    <textarea class="form-control" rows="3" name="catatan" id="link_po_catatan" placeholder="Catatan peruntukan material, lokasi pasang, dll..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-3 px-4" style="background-color: #f8fafc;">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-info px-4 shadow-sm text-white">
                        <i class="mdi mdi-link-variant me-1"></i> Hubungkan PO ke Proyek
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL 2: EDIT PENGELUARAN LAPANGAN
     ========================================== -->
<div class="modal fade" id="modalEditCost" tabindex="-1" aria-labelledby="modalEditCostLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 1140px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <form id="formEditCost" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-header border-bottom py-3 px-4" style="background-color: #f8fafc;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md bg-label-primary rounded d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-square-edit-outline fs-3 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="modalEditCostLabel">
                                Edit Catatan Pengeluaran Proyek
                            </h5>
                            <small class="text-muted">Perbarui rincian item, kalkulasi biaya, atau bukti nota fisik</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- KOLOM KIRI: RINCIAN ITEM & BIAYA -->
                        <div class="col-lg-6 col-12 border-end-lg pe-lg-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                                    <i class="mdi mdi-cube-send fs-5"></i>
                                    <span>1. Rincian Item &amp; Kalkulasi Biaya</span>
                                </h6>
                                <span class="badge bg-label-secondary small">Wajib Diisi</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-6 col-12">
                                    <label class="form-label fw-semibold text-dark">
                                        Tanggal Pengeluaran <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-calendar-blank"></i></span>
                                        <input type="date" class="form-control" name="tanggal" id="edit_tanggal" required>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-12">
                                    <label class="form-label fw-semibold text-dark">
                                        Kategori Pengeluaran <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select fw-semibold" name="kategori_biaya" id="edit_kategori_biaya" required>
                                        <option value="material">🏗️ Material Fisik (Besi, Semen, dll)</option>
                                        <option value="jasa">👷 Jasa / Upah Tukang / Mandor</option>
                                        <option value="sewa">⚙️ Sewa Alat (Molen, Scaffolding)</option>
                                        <option value="operasional">📦 Operasional / Konsumsi Lapangan</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">
                                        Deskripsi / Nama Barang / Pekerjaan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form-control-lg fs-6" name="nama_item" id="edit_nama_item" required>
                                </div>

                                <div class="col-12">
                                    <div class="p-3.5 rounded-3 border" style="background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);">
                                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                            <span class="fw-bold small text-secondary text-uppercase" style="letter-spacing: 0.5px;">
                                                <i class="mdi mdi-calculator-variant-outline me-1"></i>Kalkulasi Biaya
                                            </span>
                                            <small class="text-muted">Total otomatis dihitung</small>
                                        </div>

                                        <div class="row g-2 align-items-center">
                                            <div class="col-sm-3 col-6">
                                                <label class="form-label fw-semibold text-dark small mb-1">Jumlah (Qty)</label>
                                                <input type="number" step="0.01" class="form-control bg-white text-center fw-bold calc-qty" name="qty" id="edit_qty">
                                            </div>
                                            <div class="col-sm-3 col-6">
                                                <label class="form-label fw-semibold text-dark small mb-1">Satuan</label>
                                                <input type="text" class="form-control bg-white text-center" name="satuan" id="edit_satuan">
                                            </div>
                                            <div class="col-sm-6 col-12">
                                                <label class="form-label fw-semibold text-dark small mb-1">Harga Satuan (Rp)</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-white">Rp</span>
                                                    <input type="text" class="form-control bg-white currency-input calc-price text-end fw-semibold" name="harga_satuan" id="edit_harga_satuan">
                                                </div>
                                            </div>

                                            <div class="col-12 mt-3 pt-2 border-top">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="form-label fw-bold text-primary mb-0 fs-6">
                                                        Total Pengeluaran <span class="text-danger">*</span>
                                                    </label>
                                                    <span class="badge bg-primary">Nominal Akhir</span>
                                                </div>
                                                <div class="input-group">
                                                    <span class="input-group-text fw-bold text-primary bg-white fs-5">Rp</span>
                                                    <input type="text" class="form-control form-control-lg fw-bold text-primary bg-white currency-input calc-total text-end" name="total_biaya" id="edit_total_biaya" required style="font-size: 1.35rem; letter-spacing: 0.5px;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: PEMBAYARAN, TOKO & BUKTI -->
                        <div class="col-lg-6 col-12 ps-lg-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                                <h6 class="fw-bold text-primary mb-0 d-flex align-items-center gap-2">
                                    <i class="mdi mdi-receipt-text-outline fs-5"></i>
                                    <span>2. Pembayaran, Toko &amp; Bukti Nota</span>
                                </h6>
                                <span class="badge bg-label-info small">Dokumentasi</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">Payment / Keterangan Pembayaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-cash-fast"></i></span>
                                        <input type="text" class="form-control" name="payee" id="edit_payee">
                                    </div>
                                    <small class="text-muted">Metode pembayaran atau nama orang/pihak yang menerima uang.</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">Toko Bangunan / Rekanan (Opsional)</label>
                                    <select name="supplier_id" id="edit_supplier_id" class="form-select select2-edit-supplier">
                                        <option value="">-- Pilih Toko / Supplier (Opsional) --</option>
                                        @foreach ($suppliers as $supp)
                                            <option value="{{ $supp->id }}">{{ $supp->supplier }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-5 col-12">
                                    <label class="form-label fw-semibold text-dark">No. Nota / Kwitansi</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="mdi mdi-pound"></i></span>
                                        <input type="text" class="form-control font-monospace" name="no_bukti" id="edit_no_bukti">
                                    </div>
                                </div>

                                <div class="col-sm-7 col-12">
                                    <label class="form-label fw-semibold text-dark">Ganti Foto Nota Fisik</label>
                                    <input type="file" class="form-control" name="foto_bukti" accept="image/*,application/pdf">
                                    <small class="text-muted">Format: JPG, PNG, atau PDF (Maks. 5 MB)</small>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold text-dark">Catatan / Keterangan Tambahan</label>
                                    <textarea class="form-control" rows="3" name="catatan" id="edit_catatan"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-3 px-4" style="background-color: #f8fafc;">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                        <i class="mdi mdi-close me-1"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm">
                        <i class="mdi mdi-content-save-check-outline me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL 3: PEMETAAN AKUN AKUNTANSI (COA)
     (Khusus Finance / Accounting / Admin)
     ========================================== -->
<div class="modal fade" id="modalMapCoa" tabindex="-1" aria-labelledby="modalMapCoaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formMapCoa" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3">
                    <h5 class="modal-title fw-bold" id="modalMapCoaLabel">
                        <i class="mdi mdi-book-open-page-variant-outline text-primary me-2"></i>Pemetaan Akun Akuntansi (COA)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body py-3">
                    <div class="p-2.5 rounded bg-light mb-3">
                        <div class="small text-muted">Item Pengeluaran:</div>
                        <div class="fw-bold text-dark fs-6" id="map_item_title">-</div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <span class="badge bg-label-primary" id="map_item_cat">-</span>
                            <span class="fw-bold text-primary" id="map_item_total">Rp 0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sumber Dana (Akun Kas / Bank) <span class="text-danger">*</span></label>
                        <select name="id_pengeluaran" id="map_id_pengeluaran" class="form-select select2-map-coa" required>
                            <option value="">-- Pilih Akun Kas/Bank --</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ $acc->category }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Akun kas/bank yang berkurang untuk pembayaran ini.</small>
                    </div>

                    <div class="mb-3" id="map_beban_wrapper">
                        <label class="form-label fw-semibold">Akun Beban (Laba Rugi)</label>
                        <select name="id_beban" id="map_id_beban" class="form-select select2-map-coa">
                            <option value="">-- Pilih Akun Beban --</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }} ({{ $acc->category }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Untuk biaya Jasa / Sewa / Operasional (dicatat ke laporan beban laba rugi).</small>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold">No. Bukti / Ref Voucher Finance</label>
                        <input type="text" class="form-control font-monospace" name="no_bukti" id="map_no_bukti" placeholder="Contoh: BKK-2026/09/012">
                    </div>
                </div>

                <div class="modal-footer border-top py-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="mdi mdi-check-bold me-1"></i> Simpan Pemetaan COA
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('after-script')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Select2 Init
            $('#modalAddCost').on('shown.bs.modal', function () {
                $('.select2-add-supplier').select2({ dropdownParent: $('#modalAddCost'), width: '100%' });
            });
            $('#modalLinkPo').on('shown.bs.modal', function () {
                $('.select2-link-po').select2({ dropdownParent: $('#modalLinkPo'), width: '100%' });
                $('.select2-link-po-supplier').select2({ dropdownParent: $('#modalLinkPo'), width: '100%' });
            });
            $('#modalEditCost').on('shown.bs.modal', function () {
                $('.select2-edit-supplier').select2({ dropdownParent: $('#modalEditCost'), width: '100%' });
            });
            $('#modalMapCoa').on('shown.bs.modal', function () {
                $('.select2-map-coa').select2({ dropdownParent: $('#modalMapCoa'), width: '100%' });
            });

            // PO Select Change on Link PO Modal
            $('#link_po_select').on('change', function() {
                var selected = $(this).find('option:selected');
                var poId = selected.val();
                if (!poId) {
                    $('#link_po_preview_box').addClass('d-none');
                    return;
                }

                var nopo = selected.data('nopo') || '';
                var supplier = selected.data('supplier') || '';
                var supplierId = selected.data('supplierid') || '';
                var date = selected.data('date') || '{{ date("Y-m-d") }}';
                var total = selected.data('total') || 0;
                var totalfmt = selected.data('totalfmt') || 'Rp 0';
                var note = selected.data('note') || '';

                // Populate preview box
                $('#link_po_prev_nopo').text(nopo);
                $('#link_po_prev_supplier').text(supplier);
                $('#link_po_prev_total').text(totalfmt);
                $('#link_po_prev_date').text(date);
                $('#link_po_prev_note').text(note ? note : '-');
                $('#link_po_preview_box').removeClass('d-none');

                // Auto-fill form fields
                $('#link_po_tanggal').val(date);
                $('#link_po_nama_item').val('Pembelian ' + nopo + (supplier ? ' (' + supplier + ')' : ''));
                $('#link_po_payee').val('PO ' + nopo + (supplier ? ' - ' + supplier : ''));
                $('#link_po_nobukti').val(nopo);
                $('#link_po_qty').val('1');
                $('#link_po_satuan').val('ls');
                $('#link_po_hargasatuan').val(formatNumber(Math.round(total).toString()));
                $('#link_po_totalbiaya').val(formatNumber(Math.round(total).toString()));
                $('#link_po_catatan').val(note);
                if (supplierId) {
                    $('#link_po_supplier_id').val(supplierId).trigger('change');
                }
            });

            // Auto calculate Total on Link PO Modal
            $('#link_po_qty, #link_po_hargasatuan').on('input keyup change', function() {
                var qty = parseFloat($('#link_po_qty').val()) || 0;
                var price = parseClean($('#link_po_hargasatuan').val());
                if (qty > 0 && price > 0) {
                    var total = Math.round(qty * price);
                    $('#link_po_totalbiaya').val(formatNumber(total.toString()));
                }
            });

            // Currency formatting
            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
            function parseClean(val) {
                return parseFloat((val || '0').replace(/\./g, '').replace(/,/g, '.')) || 0;
            }

            $(document).on('input keyup change', '.currency-input', function() {
                var val = $(this).val();
                var cleaned = val.replace(/\D/g, "");
                $(this).val(formatNumber(cleaned));
            });

            // Multi-Item Dynamic Rows on modalAddCost
            var itemRowIndex = 1;

            function recalculateMultiItemTotals() {
                var grandTotal = 0;
                $('#tbodyAddItems .item-row').each(function() {
                    var qty = parseFloat($(this).find('.row-qty').val()) || 0;
                    var price = parseClean($(this).find('.row-price').val());
                    var rowTotal = 0;
                    
                    if (qty > 0 && price > 0) {
                        rowTotal = Math.round(qty * price);
                        $(this).find('.row-total').val(formatNumber(rowTotal.toString()));
                    } else {
                        rowTotal = parseClean($(this).find('.row-total').val());
                    }
                    grandTotal += rowTotal;
                });
                $('#add_grand_total_display').text('Rp ' + formatNumber(grandTotal.toString()));
            }

            // Recalculate on input in multi-item row
            $(document).on('input keyup change', '#tbodyAddItems .row-qty, #tbodyAddItems .row-price', function() {
                var row = $(this).closest('.item-row');
                var qty = parseFloat(row.find('.row-qty').val()) || 0;
                var price = parseClean(row.find('.row-price').val());
                if (qty > 0 && price > 0) {
                    var subtotal = Math.round(qty * price);
                    row.find('.row-total').val(formatNumber(subtotal.toString()));
                }
                recalculateMultiItemTotals();
            });

            $(document).on('input keyup change', '#tbodyAddItems .row-total', function() {
                recalculateMultiItemTotals();
            });

            // Add Row
            $('.btn-add-row-item').on('click', function() {
                var newRow = `
                    <tr class="item-row" data-index="${itemRowIndex}">
                        <td>
                            <select class="form-select form-select-sm fw-semibold row-kategori" name="items[${itemRowIndex}][kategori_biaya]" required>
                                <option value="material" selected>🏗️ Material Fisik</option>
                                <option value="jasa">👷 Jasa / Upah</option>
                                <option value="sewa">⚙️ Sewa Alat</option>
                                <option value="operasional">📦 Operasional</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm row-name" name="items[${itemRowIndex}][nama_item]" placeholder="Nama barang / pekerjaan..." required>
                        </td>
                        <td>
                            <input type="number" step="0.01" class="form-control form-control-sm text-center fw-bold row-qty" name="items[${itemRowIndex}][qty]" value="1">
                        </td>
                        <td>
                            <input type="text" class="form-control form-control-sm text-center row-satuan" name="items[${itemRowIndex}][satuan]" placeholder="satuan">
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="text" class="form-control form-control-sm text-end currency-input row-price" name="items[${itemRowIndex}][harga_satuan]" placeholder="0">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light fw-bold text-primary">Rp</span>
                                <input type="text" class="form-control form-control-sm text-end fw-bold text-primary currency-input row-total" name="items[${itemRowIndex}][total_biaya]" placeholder="0" required>
                            </div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-xs btn-outline-danger btn-remove-row" title="Hapus Baris">
                                <i class="mdi mdi-trash-can-outline"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#tbodyAddItems').append(newRow);
                itemRowIndex++;
                recalculateMultiItemTotals();
            });

            // Remove Row
            $(document).on('click', '.btn-remove-row', function() {
                var totalRows = $('#tbodyAddItems .item-row').length;
                if (totalRows <= 1) {
                    Swal.fire({
                        title: "Minimal 1 Item",
                        text: "Form harus memiliki minimal 1 baris item pengeluaran.",
                        icon: "info",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }
                $(this).closest('.item-row').remove();
                recalculateMultiItemTotals();
            });

            // Auto calculate Total on Edit Modal
            $('#edit_qty, #edit_harga_satuan').on('input keyup change', function() {
                var qty = parseFloat($('#edit_qty').val()) || 0;
                var price = parseClean($('#edit_harga_satuan').val());
                if (qty > 0 && price > 0) {
                    var total = Math.round(qty * price);
                    $('#edit_total_biaya').val(formatNumber(total.toString()));
                }
            });

            // Edit Cost Modal Population
            $('.btn-edit-cost').on('click', function() {
                var id = $(this).data('id');
                var url = '{{ url("proyek-konstruksi") }}/' + id;
                $('#formEditCost').attr('action', url);

                $('#edit_tanggal').val($(this).data('tanggal'));
                $('#edit_kategori_biaya').val($(this).data('kategori'));
                $('#edit_nama_item').val($(this).data('item'));
                $('#edit_payee').val($(this).data('payee'));
                $('#edit_qty').val($(this).data('qty'));
                $('#edit_satuan').val($(this).data('satuan'));
                $('#edit_harga_satuan').val($(this).data('hargasatuan'));
                $('#edit_total_biaya').val($(this).data('totalbiaya'));
                $('#edit_no_bukti').val($(this).data('nobukti'));
                $('#edit_catatan').val($(this).data('catatan'));
                $('#edit_supplier_id').val($(this).data('supplier')).trigger('change');
            });

            // Map COA Modal Population
            $('.btn-map-coa').on('click', function() {
                var id = $(this).data('id');
                var url = '{{ url("proyek-konstruksi") }}/' + id + '/map-coa';
                $('#formMapCoa').attr('action', url);

                $('#map_item_title').text($(this).data('item'));
                $('#map_item_total').text($(this).data('total'));
                $('#map_item_cat').text($(this).data('cat').toUpperCase());
                $('#map_id_pengeluaran').val($(this).data('pengeluaran')).trigger('change');
                $('#map_id_beban').val($(this).data('beban')).trigger('change');
                $('#map_no_bukti').val($(this).data('nobukti'));
            });

            // Delete Cost Confirmation
            $('.btn-delete-cost').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: "Hapus Pengeluaran Ini?",
                    text: "Catatan biaya akan dihapus dari riwayat proyek. Jika kategori Material, akumulasi nilai aktiva akan dikurangi.",
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
                        form.submit();
                    }
                });
            });
        });
    </script>
@endpush
