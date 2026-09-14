@extends('layouts.sales.app')
@section('title', isset($fixed) ? 'Edit Fixed Asset - ' . ($fixed->code ?? $fixed->type) : 'Tambah Fixed Asset')

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <style>
        .form-section-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #566a7f;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px dashed #d9dee3;
        }
        .form-floating-outline .form-control:focus, 
        .form-floating-outline .form-select:focus {
            border-color: #696cff;
        }
        .asset-preview-badge {
            font-size: 0.8125rem;
            padding: 6px 12px;
            border-radius: 6px;
        }
        .field-card {
            background: #fafbfc;
            border: 1px solid #eef1f4;
            border-radius: 10px;
            padding: 1.25rem;
        }
    </style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header & Breadcrumb -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Finance / 
                    <a href="{{ request('redirect_to') ?: route('fixed.index', ['type' => $fixed->type ?? '']) }}" class="text-muted">
                        Fixed Asset
                    </a> /
                </span> 
                {{ isset($fixed) ? 'Edit Aset' : 'Tambah Aset Tetap Baru' }}
            </h4>
            <p class="text-muted mb-0">
                {{ isset($fixed) ? 'Perbarui informasi perolehan, spesifikasi unit, dan pemetaan akun akuntansi.' : 'Pencatatan aset tetap baru perusahaan untuk perhitungan nilai buku dan penyusutan otomatis.' }}
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ request('redirect_to') ?: (isset($fixed) ? route('fixed.show', $fixed->id) : route('fixed.index', ['type' => $fixed->type ?? ''])) }}" 
               class="btn btn-outline-secondary waves-effect">
                <i class="mdi mdi-arrow-left me-1"></i> Batal / Kembali
            </a>
        </div>
    </div>

    <!-- Main Form -->
    <form id="fixedAssetForm" action="{{ isset($fixed) ? route('fixed.update', $fixed->id) : route('fixed.store') }}" method="POST">
        @csrf
        @if (isset($fixed))
            @method('PATCH')
        @endif

        {{-- Hidden redirect_to parameter to return to tool-finance or specific page --}}
        @if (request('redirect_to') || old('redirect_to'))
            <input type="hidden" name="redirect_to" value="{{ request('redirect_to', old('redirect_to')) }}">
        @endif

        <div class="row">
            <!-- Left Column: Master & Identity -->
            <div class="col-xl-8 col-lg-7 col-12">
                <!-- Section 1: Data Identitas & Kategori -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-section-title">
                            <i class="mdi mdi-cube-outline text-primary fs-5"></i>
                            <span>1. Identitas & Klasifikasi Aset</span>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Kategori Asset -->
                            <div class="col-md-6 col-12">
                                <label for="type" class="form-label fw-semibold">Kategori Aset <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" {{ isset($fixed) ? 'disabled' : 'required' }}>
                                    <option value="">-- Pilih Kategori Aset --</option>
                                    <option value="Kendaraan" {{ ($fixed->type ?? old('type')) == 'Kendaraan' ? 'selected' : '' }}>Kendaraan (Mobil / Motor)</option>
                                    <option value="Mesin" {{ ($fixed->type ?? old('type')) == 'Mesin' ? 'selected' : '' }} {{ !isset($fixed) ? 'disabled' : '' }}>
                                        Mesin{{ !isset($fixed) ? ' (Diinput lewat Barang Masuk Unit)' : '' }}
                                    </option>
                                    <option value="Tools" {{ ($fixed->type ?? old('type')) == 'Tools' ? 'selected' : '' }}>Tools (Alat Kerja Lapangan)</option>
                                    <option value="Peralatan Kantor" {{ ($fixed->type ?? old('type')) == 'Peralatan Kantor' ? 'selected' : '' }}>Peralatan Kantor / Elektronik</option>
                                    <option value="Bangunan" {{ ($fixed->type ?? old('type')) == 'Bangunan' ? 'selected' : '' }}>Bangunan / Gedung</option>
                                    <option value="Tanah" {{ ($fixed->type ?? old('type')) == 'Tanah' ? 'selected' : '' }}>Tanah</option>
                                </select>
                                @if (isset($fixed))
                                    <input type="hidden" name="type" value="{{ $fixed->type }}">
                                    <small class="text-muted">Kategori aset tidak dapat diubah setelah terdaftar.</small>
                                @else
                                    <small class="text-muted">Kategori Mesin (unit reftech) diinput terintegrasi melalui modul Gudang / Barang Masuk Unit.</small>
                                @endif
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Kode Asset -->
                            <div class="col-md-6 col-12">
                                <label for="no-code-input" class="form-label fw-semibold">Kode Aset (Asset Code) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="mdi mdi-barcode text-muted"></i></span>
                                    <input class="form-control font-monospace fw-semibold @error('code') is-invalid @enderror" type="text" 
                                           id="no-code-input" name="code" 
                                           placeholder="Kode otomatis ter-generate..." 
                                           value="{{ old('code', $fixed->code ?? '') }}" required>
                                </div>
                                @if (!isset($fixed))
                                    <small class="text-muted">Kode terisi otomatis sesuai kategori, tetap bisa disesuaikan manual jika ada format khusus.</small>
                                @endif
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Special Context for Tools -->
                        @if (isset($fixed) && $fixed->type === 'Tools')
                            <div class="alert alert-primary d-flex align-items-center mb-3">
                                <i class="mdi mdi-tools mdi-24px me-3 text-primary"></i>
                                <div>
                                    <div class="fw-bold">Alat Milik Teknisi Terdaftar</div>
                                    <small class="text-body">
                                        Master: <strong>{{ $fixed->toolsMaster?->nama_tools ?? '-' }}</strong> | 
                                        Teknisi Penanggung Jawab (PIC): <strong>{{ $fixed->pic?->name ?? 'Belum Ditentukan' }}</strong> |
                                        Tgl Serah Terima: <strong>{{ $fixed->tanggal_serah_terima ? \Carbon\Carbon::parse($fixed->tanggal_serah_terima)->format('d M Y') : '-' }}</strong>
                                    </small>
                                </div>
                            </div>
                        @endif

                        <!-- Keterangan / Deskripsi -->
                        <div class="mb-3" id="desc-text-wrapper" style="{{ ($fixed->type ?? old('type')) === 'Mesin' ? 'display:none;' : '' }}">
                            <label for="desc-input" class="form-label fw-semibold">Keterangan / Nama Aset <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" 
                                   placeholder="Contoh: Mobil Operasional Toyota Avanza 2024 / Laptop MacBook Pro M3..." 
                                   id="desc-input" name="desc" 
                                   value="{{ old('desc', $fixed->desc ?? '') }}">
                            <small class="text-muted">Nama atau ringkasan barang yang muncul pada daftar laporan aset.</small>
                        </div>

                        <!-- Special Fields for Mesin (E-Stock) -->
                        <div id="mesin-fields-wrapper" style="{{ ($fixed->type ?? old('type')) === 'Mesin' ? '' : 'display:none;' }}">
                            <div class="field-card mb-3">
                                <h6 class="fw-bold text-primary mb-3"><i class="mdi mdi-cog-outline me-1"></i>Spesifikasi Mesin Unit</h6>
                                <div class="row g-3">
                                    <div class="col-md-6 col-12">
                                        <label for="unit-dropdown" class="form-label fw-semibold">Unit Global</label>
                                        <select id="unit-dropdown" class="select2 form-select" data-allow-clear="true" name="id_unit">
                                            <option value="">-- Pilih Unit Global --</option>
                                            @foreach ($units as $u)
                                                @php
                                                    $unitLabel = collect([$u->unit, $u->brand, $u->model, $u->sku])->filter()->join(' - ');
                                                @endphp
                                                <option value="{{ $u->id }}" data-label="{{ $unitLabel }}"
                                                    {{ ($fixed->id_unit ?? old('id_unit')) == $u->id ? 'selected' : '' }}>
                                                    {{ $unitLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label for="serial-number-input" class="form-label fw-semibold">Serial Number Mesin</label>
                                        <input class="form-control font-monospace" type="text" placeholder="Contoh: SN-2024-XXXX"
                                            id="serial-number-input" name="serial_number"
                                            value="{{ old('serial_number', $fixed->serial_number ?? '') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold d-block">Kondisi Unit</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kondisi" id="kondisi-second"
                                                value="Second"
                                                {{ (!isset($fixed) && old('kondisi', 'Second') == 'Second') || ($fixed->kondisi ?? '') == 'Second' ? 'checked' : '' }}
                                                {{ isset($fixed) ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="kondisi-second">Unit Second (Perlu QC/Pengecekan)</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kondisi" id="kondisi-baru"
                                                value="Baru" {{ ($fixed->kondisi ?? old('kondisi')) == 'Baru' ? 'checked' : '' }}
                                                {{ isset($fixed) ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="kondisi-baru">Unit Baru (Siap Operasional)</label>
                                        </div>
                                        @if (isset($fixed))
                                            <input type="hidden" name="kondisi" value="{{ $fixed->kondisi }}">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Special Fields for Kendaraan -->
                        <div id="kendaraan-fields-wrapper" style="{{ ($fixed->type ?? old('type')) === 'Kendaraan' ? '' : 'display:none;' }}">
                            <div class="field-card mb-3">
                                <h6 class="fw-bold text-primary mb-3"><i class="mdi mdi-car me-1"></i>Spesifikasi Armada Kendaraan</h6>
                                <div class="row g-3">
                                    <div class="col-md-6 col-12">
                                        <label for="plat-nomor-input" class="form-label fw-semibold">Plat Nomor Kendaraan</label>
                                        <input class="form-control font-monospace text-uppercase" type="text" 
                                               placeholder="Contoh: B 1234 ABC"
                                               id="plat-nomor-input" name="plat_nomor"
                                               value="{{ old('plat_nomor', $fixed->plat_nomor ?? '') }}">
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label for="jenis-kendaraan-input" class="form-label fw-semibold">Jenis Kendaraan</label>
                                        <select class="form-select" id="jenis-kendaraan-input" name="jenis_kendaraan">
                                            <option value="">Pilih Jenis</option>
                                            <option value="Mobil" {{ ($fixed->jenis_kendaraan ?? old('jenis_kendaraan')) == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                                            <option value="Motor" {{ ($fixed->jenis_kendaraan ?? old('jenis_kendaraan')) == 'Motor' ? 'selected' : '' }}>Motor</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label for="merk-model-input" class="form-label fw-semibold">Merk & Tipe Kendaraan</label>
                                        <input class="form-control" type="text" placeholder="Contoh: Toyota Hilux 2.4 G 4x4"
                                            id="merk-model-input" name="merk_model"
                                            value="{{ old('merk_model', $fixed->merk_model ?? '') }}">
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <label for="bahan-bakar-input" class="form-label fw-semibold">Jenis Bahan Bakar</label>
                                        <select class="form-select" id="bahan-bakar-input" name="bahan_bakar">
                                            <option value="">Pilih Bahan Bakar</option>
                                            <option value="Solar" {{ ($fixed->bahan_bakar ?? old('bahan_bakar')) == 'Solar' ? 'selected' : '' }}>Solar / Dexlite</option>
                                            <option value="Pertalite" {{ ($fixed->bahan_bakar ?? old('bahan_bakar')) == 'Pertalite' ? 'selected' : '' }}>Pertalite / Pertamax</option>
                                            <option value="Listrik" {{ ($fixed->bahan_bakar ?? old('bahan_bakar')) == 'Listrik' ? 'selected' : '' }}>Listrik (EV)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label for="atas-nama-input" class="form-label fw-semibold">Atas Nama (STNK / BPKB)</label>
                                        <input class="form-control" type="text" placeholder="Nama pemilik di STNK (kosongkan bila atas nama PT)..."
                                            id="atas-nama-input" name="atas_nama"
                                            value="{{ old('atas_nama', $fixed->atas_nama ?? '') }}">
                                        <small class="text-muted">Diisi jika kepemilikan STNK perorangan / kuasa operasional.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Supplier & Invoice -->
                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label for="supplier-dropdown" class="form-label fw-semibold">Supplier / Rekanan</label>
                                <select id="supplier-dropdown" class="select2 form-select" data-allow-clear="true" name="supplier">
                                    <option value="">Pilih Supplier...</option>
                                    @foreach ($suppliers as $supp)
                                        <option value="{{ $supp->id }}"
                                            {{ ($fixed->id_supplier ?? old('supplier')) == $supp->id ? 'selected' : '' }}>
                                            {{ $supp->supplier }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-12">
                                <label for="no-voucher-input" class="form-label fw-semibold">No. Invoice / Kwitansi Beli</label>
                                <input class="form-control" type="text" placeholder="Contoh: INV/2024/09/001"
                                    id="no-voucher-input" name="no_invoice" value="{{ old('no_invoice', $fixed->no_invoice ?? '') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Jadwal & Kronologi Tanggal -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-section-title">
                            <i class="mdi mdi-calendar-range text-primary fs-5"></i>
                            <span>2. Jadwal & Kronologi Tanggal Perolehan</span>
                        </div>

                        <div class="row g-3">
                            <!-- Tanggal Beli -->
                            <div class="col-md-4 col-12">
                                <label for="tgl_beli" class="form-label fw-semibold">Tanggal Beli / Invoice <span class="text-danger">*</span></label>
                                <input class="form-control" type="date" id="tgl_beli" name="beli"
                                    value="{{ old('beli', $fixed->beli ?? ($fixed->date ?? '')) }}" required>
                                <small class="text-muted">Tanggal faktur pembelian fisik.</small>
                            </div>

                            <!-- Tanggal Mulai Pakai -->
                            <div class="col-md-4 col-12">
                                <label for="pakai" class="form-label fw-semibold">Tanggal Mulai Pakai</label>
                                <input class="form-control" type="date" id="pakai" name="pakai"
                                    value="{{ old('pakai', $fixed->pakai ?? '') }}">
                                <small class="text-muted">Kapan aset mulai aktif digunakan.</small>
                            </div>

                            <!-- Tanggal Bayar -->
                            <div class="col-md-4 col-12">
                                <label for="bayar" class="form-label fw-semibold">Tanggal Bayar / Pelunasan</label>
                                <input class="form-control" type="date" id="bayar" name="bayar"
                                    value="{{ old('bayar', $fixed->bayar ?? '') }}">
                                <small class="text-muted">Tanggal pengeluaran kas / transfer.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Pemetaan Akun Akuntansi (COA) -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-section-title">
                            <i class="mdi mdi-book-open-page-variant-outline text-primary fs-5"></i>
                            <span>3. Struktur Akun Akuntansi (Chart of Accounts)</span>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Akun Aktiva -->
                            <div class="col-md-4 col-12">
                                <label for="aktiva" class="form-label fw-semibold">Akun Aktiva (Aset Tetap)</label>
                                <select id="aktiva" class="select2 form-select" data-allow-clear="true" name="aktiva">
                                    <option value="">-- Pilih Akun Aktiva --</option>
                                    @foreach ($account as $acc)
                                        <option value="{{ $acc->id }}"
                                            {{ ($fixed->id_aktiva ?? old('aktiva')) == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->code }} - {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Debit saat perolehan aset.</small>
                            </div>

                            <!-- Akun Akumulasi Penyusutan -->
                            <div class="col-md-4 col-12">
                                <label for="penyusutan" class="form-label fw-semibold">Akun Akumulasi Penyusutan</label>
                                <select id="penyusutan" class="select2 form-select" data-allow-clear="true" name="penyusutan">
                                    <option value="">-- Pilih Akun Akumulasi --</option>
                                    @foreach ($account as $acc)
                                        <option value="{{ $acc->id }}"
                                            {{ ($fixed->id_penyusutan ?? old('penyusutan')) == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->code }} - {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Kontra-aset pada neraca.</small>
                            </div>

                            <!-- Akun Beban Penyusutan -->
                            <div class="col-md-4 col-12">
                                <label for="beban" class="form-label fw-semibold">Akun Beban Penyusutan</label>
                                <select id="beban" class="select2 form-select" data-allow-clear="true" name="beban">
                                    <option value="">-- Pilih Akun Beban --</option>
                                    @foreach ($account as $acc)
                                        <option value="{{ $acc->id }}"
                                            {{ ($fixed->id_beban ?? old('beban')) == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->code }} - {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Biaya di Laporan Laba Rugi.</small>
                            </div>
                        </div>

                        <!-- Akun Kas / Bank Pengeluaran -->
                        <div class="row g-3">
                            <div class="col-md-6 col-12">
                                <label for="bank" class="form-label fw-semibold">Akun Sumber Dana (Kas / Bank)</label>
                                <select id="bank" class="select2 form-select" data-allow-clear="true" name="bank">
                                    <option value="">-- Pilih Akun Kas/Bank --</option>
                                    @foreach ($account as $acc)
                                        <option value="{{ $acc->id }}"
                                            {{ ($fixed->id_pengeluaran ?? old('bank')) == $acc->id ? 'selected' : '' }}>
                                            {{ $acc->code }} - {{ $acc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Kredit saat pembayaran aset dilakukan.</small>
                            </div>

                            <!-- Status Pembayaran -->
                            <div class="col-md-6 col-12">
                                <label for="status" class="form-label fw-semibold">Status Pembayaran</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1" {{ (string) ($fixed->status ?? old('status', '1')) === '1' ? 'selected' : '' }}>
                                        Lunas (Sudah Dibayar)
                                    </option>
                                    <option value="0" {{ (string) ($fixed->status ?? old('status')) === '0' ? 'selected' : '' }}>
                                        Hutang (Belum Dibayar)
                                    </option>
                                </select>
                                <small class="text-muted">Pilih status lunas atau hutang aset.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Financial & Summary Actions -->
            <div class="col-xl-4 col-lg-5 col-12">
                <!-- Nilai Finansial Aset Card -->
                <div class="card shadow-sm mb-4 border-start border-4 border-primary">
                    <div class="card-body">
                        <div class="form-section-title">
                            <i class="mdi mdi-cash-multiple text-primary fs-5"></i>
                            <span>4. Nilai Finansial Aset</span>
                        </div>

                        <!-- Qty -->
                        <div class="mb-3">
                            <label for="qty-1" class="form-label fw-semibold">Jumlah Kuantitas (Qty) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" placeholder="Min 1"
                                name="qty" id="qty-1" min="1" value="{{ old('qty', $fixed->qty ?? 1) }}" required>
                        </div>

                        <!-- Total Perolehan -->
                        <div class="mb-3">
                            <label for="totalLabel-1" class="form-label fw-semibold">Total Nilai Perolehan <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text fw-bold">Rp</span>
                                <input type="text" class="form-control form-control-lg fw-bold text-dark invoice-item-amount-label" 
                                       id="totalLabel-1" name="harga" placeholder="0" 
                                       value="{{ old('harga', isset($fixed) && $fixed->total ? number_format($fixed->total, 0, ',', '.') : '') }}" required>
                            </div>
                            <input type="hidden" class="invoice-item-amount" name="total" id="amount-1" 
                                   value="{{ old('total', $fixed->total ?? 0) }}">
                            <small class="text-muted d-block mt-1">Nilai kapitalisasi awal aset tetap sebelum disusutkan.</small>
                            <div class="mt-1 small text-primary fst-italic invoice-item-say-total"></div>
                        </div>

                        <hr class="my-3">

                        <!-- Masa Manfaat (Bulan) -->
                        <div class="mb-3">
                            <label for="umur" class="form-label fw-semibold">Masa Manfaat (Umur Ekonomis)</label>
                            <div class="input-group">
                                <input type="number" class="form-control" placeholder="Contoh: 48"
                                    name="umur" id="umur" min="1"
                                    value="{{ old('umur', $fixed->umur ?? 48) }}">
                                <span class="input-group-text">Bulan</span>
                            </div>
                            <small class="text-muted d-block mt-1">Default 48 bulan (4 tahun) untuk penyusutan 25% / tahun.</small>
                        </div>

                        <!-- Metode Penyusutan -->
                        <div class="mb-3">
                            <label for="metode" class="form-label fw-semibold">Metode Penyusutan</label>
                            <select class="form-select" id="metode" name="metode">
                                <option value="Metode Garis Lurus" {{ ($fixed->metode ?? old('metode', 'Metode Garis Lurus')) == 'Metode Garis Lurus' ? 'selected' : '' }}>
                                    Metode Garis Lurus (Straight Line)
                                </option>
                                <option value="Metode Saldo Menurun" {{ ($fixed->metode ?? old('metode')) == 'Metode Saldo Menurun' ? 'selected' : '' }}>
                                    Metode Saldo Menurun (Declining Balance)
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Submit Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-2 waves-effect waves-light">
                            <i class="mdi {{ isset($fixed) ? 'mdi-content-save-check' : 'mdi-plus-circle' }} me-1"></i>
                            {{ isset($fixed) ? 'Simpan Perubahan' : 'Tambah Aset Tetap' }}
                        </button>
                        <a href="{{ request('redirect_to') ?: (isset($fixed) ? route('fixed.show', $fixed->id) : route('fixed.index', ['type' => $fixed->type ?? ''])) }}" 
                           class="btn btn-outline-secondary w-100 waves-effect">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('after-script')
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                width: '100%',
                allowClear: true
            });

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function terbilang(n) {
                const angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan",
                    "sepuluh", "sebelas"
                ];
                n = parseInt(n);
                if (isNaN(n) || n === 0) return "";
                if (n < 12) return angka[n];
                if (n < 20) return terbilang(n - 10) + " belas";
                if (n < 100) return terbilang(Math.floor(n / 10)) + " puluh " + terbilang(n % 10);
                if (n < 200) return "seratus " + terbilang(n - 100);
                if (n < 1000) return terbilang(Math.floor(n / 100)) + " ratus " + terbilang(n % 100);
                if (n < 2000) return "seribu " + terbilang(n - 1000);
                if (n < 1000000) return terbilang(Math.floor(n / 1000)) + " ribu " + terbilang(n % 1000);
                if (n < 1000000000) return terbilang(Math.floor(n / 1000000)) + " juta " + terbilang(n % 1000000);
                if (n < 1000000000000) return terbilang(Math.floor(n / 1000000000)) + " miliar " + terbilang(n % 1000000000);
                return "";
            }

            // Sync formatted currency input and hidden numeric input
            function updateCurrencyDisplay() {
                var input = $('#totalLabel-1');
                var val = input.val();
                var cleaned = val.replace(/\D/g, "");
                var num = parseInt(cleaned, 10) || 0;
                
                $('#amount-1').val(num);
                if (cleaned.length > 0) {
                    input.val(formatNumber(cleaned));
                    var tb = terbilang(num);
                    if (tb) {
                        $('.invoice-item-say-total').text('Terbilang: ' + tb.charAt(0).toUpperCase() + tb.slice(1) + ' Rupiah');
                    } else {
                        $('.invoice-item-say-total').text('');
                    }
                } else {
                    $('.invoice-item-say-total').text('');
                }
            }

            $('#totalLabel-1').on('input keyup change', function() {
                updateCurrencyDisplay();
            });
            updateCurrencyDisplay();

            // Toggle category fields
            function toggleCategoryFields(cat) {
                if (cat === 'Mesin') {
                    $('#desc-text-wrapper').hide();
                    $('#mesin-fields-wrapper').slideDown();
                    $('#kendaraan-fields-wrapper').slideUp();
                } else if (cat === 'Kendaraan') {
                    $('#desc-text-wrapper').slideDown();
                    $('#mesin-fields-wrapper').slideUp();
                    $('#kendaraan-fields-wrapper').slideDown();
                } else {
                    $('#desc-text-wrapper').slideDown();
                    $('#mesin-fields-wrapper').slideUp();
                    $('#kendaraan-fields-wrapper').slideUp();
                }
            }

            $('#type').on('change', function() {
                var selectedType = $(this).val();
                toggleCategoryFields(selectedType);

                @if (!isset($fixed))
                    if (selectedType) {
                        $.get('{{ route('fixed.next-code') }}', { type: selectedType }, function(res) {
                            if (res && res.code) {
                                $('#no-code-input').val(res.code);
                            }
                        });
                    }
                @endif
            });

            // Update description automatically when unit is selected for Mesin
            $('#unit-dropdown').on('change', function() {
                var label = $(this).find(':selected').data('label') || '';
                if (label) {
                    $('#desc-input').val(label);
                }
            });
        });
    </script>
@endpush
