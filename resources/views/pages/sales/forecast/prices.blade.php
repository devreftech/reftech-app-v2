@extends('layouts.sales.app')
@section('title', 'Master Harga Jasa PM')
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><span class="text-muted fw-light">Forecast /</span> Master Harga Jasa PM</h4>
            <p class="text-muted mb-0">Kelola tarif standar jasa PM (PM1 - PM4) per kapasitas power (kW) dan template scope penawaran.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-2 shadow-sm waves-effect" id="btnOpenTemplateModal">
                <i class="mdi mdi-text-box-edit-outline fs-5 text-primary"></i>
                <span>Template Scope & Remarks (Global)</span>
            </button>
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm waves-effect waves-light" id="btnOpenAddModal">
                <i class="mdi mdi-plus fs-5"></i>
                <span>Tambah Harga Master PM</span>
            </button>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('message'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="mdi mdi-check-circle-outline me-2 fs-4"></i>
        <div>{{ session('message') }}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Summary KPI Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #f6f8fd 0%, #e9effd 100%);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small d-block mb-1">TOTAL POWER TERDAFTAR</span>
                        <h3 class="mb-0 fw-bold text-primary">{{ $prices->count() }} <span class="fs-6 text-muted font-normal">Kapasitas</span></h3>
                    </div>
                    <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                        <i class="mdi mdi-engine fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #f3faf7 0%, #e1f5ed 100%);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small d-block mb-1">RATA-RATA TARIF PM 1</span>
                        <h3 class="mb-0 fw-bold text-success">
                            Rp {{ number_format($prices->count() > 0 ? $prices->avg('price_pm1') : 0, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="avatar avatar-md bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                        <i class="mdi mdi-wrench-outline fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm" style="background: linear-gradient(135deg, #fffcf4 0%, #fff3d6 100%);">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted fw-semibold small d-block mb-1">RATA-RATA TARIF PM 2</span>
                        <h3 class="mb-0 fw-bold text-warning">
                            Rp {{ number_format($prices->count() > 0 ? $prices->avg('price_pm2') : 0, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="avatar avatar-md bg-warning text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm">
                        <i class="mdi mdi-oil fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Full-Width Master Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 py-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-table text-primary fs-4"></i>
                <h5 class="card-title mb-0 fw-bold">Daftar Harga Jasa Standar PM</h5>
            </div>
            <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                <div class="input-group input-group-merge style-search">
                    <span class="input-group-text border-end-0 bg-light"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" id="searchTable" class="form-control form-control-sm border-start-0 bg-light" placeholder="Cari kapasitas power (mis: 15 kW)...">
                </div>
            </div>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0" id="pricesTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 40px;"></th>
                        <th class="fw-bold">Power (kW)</th>
                        <th class="fw-bold">PM 1 (Air & Oil Filter)</th>
                        <th class="fw-bold">PM 2 (+ Separator & Oli)</th>
                        <th class="fw-bold">PM 3 (+ Carbon Remover)</th>
                        <th class="fw-bold">PM 4 (+ Overhaul)</th>
                        <th class="fw-bold text-center" style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($prices as $price)
                    <tr class="main-row" data-power="{{ strtolower($price->power) }}">
                        <td class="text-center">
                            <button type="button" class="btn btn-icon btn-xs btn-label-secondary btn-toggle-detail" data-target="#detail-{{ $price->id }}" title="Lihat Scope Standar">
                                <i class="mdi mdi-chevron-down fs-5"></i>
                            </button>
                        </td>
                        <td>
                            <span class="badge bg-label-primary px-3 py-2 fs-6 fw-bold border border-primary-subtle">
                                <i class="mdi mdi-flash me-1"></i>{{ $price->power }}
                            </span>
                        </td>
                        <td><span class="fw-bold text-dark">Rp {{ number_format($price->price_pm1, 0, ',', '.') }}</span></td>
                        <td><span class="fw-bold text-dark">Rp {{ number_format($price->price_pm2, 0, ',', '.') }}</span></td>
                        <td><span class="fw-bold text-dark">Rp {{ number_format($price->price_pm3, 0, ',', '.') }}</span></td>
                        <td><span class="fw-bold text-dark">Rp {{ number_format($price->price_pm4, 0, ',', '.') }}</span></td>
                        <td class="text-center">
                            <div class="d-inline-flex gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary btn-edit waves-effect"
                                        data-id="{{ $price->id }}"
                                        data-power="{{ $price->power }}"
                                        data-pm1="{{ $price->price_pm1 }}"
                                        data-pm2="{{ $price->price_pm2 }}"
                                        data-pm3="{{ $price->price_pm3 }}"
                                        data-pm4="{{ $price->price_pm4 }}">
                                    <i class="mdi mdi-pencil me-1"></i>Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete waves-effect"
                                        data-id="{{ $price->id }}"
                                        data-power="{{ $price->power }}">
                                    <i class="mdi mdi-trash-can-outline"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <!-- Expandable Detail Row (Preview Scope & Remarks) -->
                    <tr class="detail-row d-none bg-light" id="detail-{{ $price->id }}">
                        <td colspan="7" class="p-3">
                            <div class="card border border-info border-opacity-25 shadow-none mb-0" style="background: #fafcff;">
                                <div class="card-header bg-transparent py-2 px-3 border-bottom d-flex align-items-center justify-content-between">
                                    <h6 class="mb-0 fw-bold text-primary fs-7"><i class="mdi mdi-information-outline me-1"></i>Template Scope Kerja & Remarks Quotation yang Berlaku di Penawaran</h6>
                                    <small class="text-muted">Klik panah pada baris untuk menutup</small>
                                </div>
                                <div class="card-body p-3">
                                    <div class="row g-3">
                                        <!-- PM1 -->
                                        <div class="col-md-3">
                                            <div class="p-2 border rounded bg-white h-100">
                                                <div class="fw-bold text-success border-bottom pb-1 mb-2 d-flex justify-content-between">
                                                    <span>PM 1</span>
                                                    <span>Rp {{ number_format($price->price_pm1, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="small mb-2">
                                                    <span class="fw-semibold text-muted d-block mb-1">Scope Kerja Penawaran:</span>
                                                    <div class="text-dark whitespace-pre-line bg-light p-2 rounded" style="white-space: pre-line; max-height: 120px; overflow-y: auto;">{{ $price->desc_pm1 ?: ($defaultTemplate->desc_pm1 ?? '-') }}</div>
                                                </div>
                                                <div class="small">
                                                    <span class="fw-semibold text-muted d-block mb-1">Remarks Quotation:</span>
                                                    <div class="text-muted fst-italic bg-light p-2 rounded" style="white-space: pre-line;">{{ $price->note_pm1 ?: ($defaultTemplate->note_pm1 ?? '-') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- PM2 -->
                                        <div class="col-md-3">
                                            <div class="p-2 border rounded bg-white h-100">
                                                <div class="fw-bold text-primary border-bottom pb-1 mb-2 d-flex justify-content-between">
                                                    <span>PM 2</span>
                                                    <span>Rp {{ number_format($price->price_pm2, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="small mb-2">
                                                    <span class="fw-semibold text-muted d-block mb-1">Scope Kerja Penawaran:</span>
                                                    <div class="text-dark whitespace-pre-line bg-light p-2 rounded" style="white-space: pre-line; max-height: 120px; overflow-y: auto;">{{ $price->desc_pm2 ?: ($defaultTemplate->desc_pm2 ?? '-') }}</div>
                                                </div>
                                                <div class="small">
                                                    <span class="fw-semibold text-muted d-block mb-1">Remarks Quotation:</span>
                                                    <div class="text-muted fst-italic bg-light p-2 rounded" style="white-space: pre-line;">{{ $price->note_pm2 ?: ($defaultTemplate->note_pm2 ?? '-') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- PM3 -->
                                        <div class="col-md-3">
                                            <div class="p-2 border rounded bg-white h-100">
                                                <div class="fw-bold text-warning border-bottom pb-1 mb-2 d-flex justify-content-between">
                                                    <span>PM 3</span>
                                                    <span>Rp {{ number_format($price->price_pm3, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="small mb-2">
                                                    <span class="fw-semibold text-muted d-block mb-1">Scope Kerja Penawaran:</span>
                                                    <div class="text-dark whitespace-pre-line bg-light p-2 rounded" style="white-space: pre-line; max-height: 120px; overflow-y: auto;">{{ $price->desc_pm3 ?: ($defaultTemplate->desc_pm3 ?? '-') }}</div>
                                                </div>
                                                <div class="small">
                                                    <span class="fw-semibold text-muted d-block mb-1">Remarks Quotation:</span>
                                                    <div class="text-muted fst-italic bg-light p-2 rounded" style="white-space: pre-line;">{{ $price->note_pm3 ?: ($defaultTemplate->note_pm3 ?? '-') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- PM4 -->
                                        <div class="col-md-3">
                                            <div class="p-2 border rounded bg-white h-100">
                                                <div class="fw-bold text-danger border-bottom pb-1 mb-2 d-flex justify-content-between">
                                                    <span>PM 4</span>
                                                    <span>Rp {{ number_format($price->price_pm4, 0, ',', '.') }}</span>
                                                </div>
                                                <div class="small mb-2">
                                                    <span class="fw-semibold text-muted d-block mb-1">Scope Kerja Penawaran:</span>
                                                    <div class="text-dark whitespace-pre-line bg-light p-2 rounded" style="white-space: pre-line; max-height: 120px; overflow-y: auto;">{{ $price->desc_pm4 ?: ($defaultTemplate->desc_pm4 ?? '-') }}</div>
                                                </div>
                                                <div class="small">
                                                    <span class="fw-semibold text-muted d-block mb-1">Remarks Quotation:</span>
                                                    <div class="text-muted fst-italic bg-light p-2 rounded" style="white-space: pre-line;">{{ $price->note_pm4 ?: ($defaultTemplate->note_pm4 ?? '-') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-3">
                                <i class="mdi mdi-alert-circle-outline text-muted fs-1 mb-2 d-block"></i>
                                <h6 class="text-muted fw-bold">Belum Ada Master Harga Jasa PM</h6>
                                <p class="text-muted small">Klik tombol "+ Tambah Harga Master PM" di atas untuk menambahkan data baru.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal 1: Manage Master Harga Jasa PM (Prices Only - Super Compact) -->
<div class="modal fade" id="modalManagePrice" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-label-primary rounded d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-currency-usd fs-4"></i>
                    </div>
                    <h5 class="modal-title fw-bold mb-0" id="modalManagePriceTitle">Form Master Harga Jasa PM</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('forecast.prices.update') }}" method="POST" id="formManagePrice">
                @csrf
                <div class="modal-body p-4">
                    <!-- Power Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-1" for="powerSelect">Kapasitas Power (kW) <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-12 mb-2">
                                <select class="form-select" id="powerSelect" name="power">
                                    <option value="" disabled selected>-- Pilih Kapasitas Terdaftar --</option>
                                    @foreach($availablePowers as $power)
                                        <option value="{{ $power }}">{{ $power }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <input type="text" class="form-control" id="customPower" name="custom_power" placeholder="Atau ketik kustom, misal: 45 kW">
                            </div>
                        </div>
                    </div>

                    <div class="divider text-start my-3">
                        <div class="divider-text fw-bold text-muted fs-7"><i class="mdi mdi-cash-multiple me-1"></i>Input Tarif Jasa Standar</div>
                    </div>

                    <!-- 4 Compact Price Inputs -->
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small mb-1" for="price_pm1"><span class="badge bg-success me-1">PM 1</span> Air & Oil Filter <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="text" class="form-control currency-mask" id="price_pm1" name="price_pm1" required placeholder="0">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small mb-1" for="price_pm2"><span class="badge bg-primary me-1">PM 2</span> + Separator & Oli <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="text" class="form-control currency-mask" id="price_pm2" name="price_pm2" required placeholder="0">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small mb-1" for="price_pm3"><span class="badge bg-warning me-1 text-dark">PM 3</span> + Carbon Remover <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="text" class="form-control currency-mask" id="price_pm3" name="price_pm3" required placeholder="0">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small mb-1" for="price_pm4"><span class="badge bg-danger me-1">PM 4</span> + Overhaul Service <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="text" class="form-control currency-mask" id="price_pm4" name="price_pm4" required placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-content-save me-1"></i>Simpan Harga Master</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal 2: Manage Global Standard Template for Scope & Remarks -->
<div class="modal fade" id="modalStandardTemplate" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-sm bg-label-info rounded d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-file-document-edit-outline fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Template Scope Kerja & Remarks Quotation (Global)</h5>
                        <small class="text-muted">Template ini otomatis dipanggil saat penawaran harga dibuat jika tidak ada kustomisasi khusus.</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('forecast.prices.template') }}" method="POST" id="formStandardTemplate">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <!-- PM 1 Template -->
                        <div class="col-md-6">
                            <div class="card h-100 border shadow-none" style="background: #fafcfe;">
                                <div class="card-header bg-transparent py-2 border-bottom d-flex align-items-center justify-content-between">
                                    <span class="badge bg-success px-3 py-1 fs-7 fw-bold"><i class="mdi mdi-wrench me-1"></i>Template Standar PM 1</span>
                                    <small class="text-muted fw-semibold">Level 1</small>
                                </div>
                                <div class="card-body p-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small" for="tpl_desc_pm1">Scope Kerja PM 1 (Enter utk auto-bullet)</label>
                                        <textarea class="form-control form-control-sm desc-pm-bullet" id="tpl_desc_pm1" name="desc_pm1" rows="3" placeholder="Scope kerja PM1">{{ $defaultTemplate->desc_pm1 ?? "• Pembersihan unit kompresor\n• Penggantian Air Filter & Oil Filter\n• Pengecekan temperatur & tekanan kerja" }}</textarea>
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold small" for="tpl_note_pm1">Remarks Quotation PM 1</label>
                                        <textarea class="form-control form-control-sm" id="tpl_note_pm1" name="note_pm1" rows="2" placeholder="Catatan quotation PM1">{{ $defaultTemplate->note_pm1 ?? "Scope pekerjaan jasa PM 1" }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PM 2 Template -->
                        <div class="col-md-6">
                            <div class="card h-100 border shadow-none" style="background: #fafcfe;">
                                <div class="card-header bg-transparent py-2 border-bottom d-flex align-items-center justify-content-between">
                                    <span class="badge bg-primary px-3 py-1 fs-7 fw-bold"><i class="mdi mdi-oil me-1"></i>Template Standar PM 2</span>
                                    <small class="text-muted fw-semibold">Level 2</small>
                                </div>
                                <div class="card-body p-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small" for="tpl_desc_pm2">Scope Kerja PM 2 (Enter utk auto-bullet)</label>
                                        <textarea class="form-control form-control-sm desc-pm-bullet" id="tpl_desc_pm2" name="desc_pm2" rows="3" placeholder="Scope kerja PM2">{{ $defaultTemplate->desc_pm2 ?? "• Pembersihan unit kompresor\n• Penggantian Separator Filter & Oli Kompresor\n• General Health Check & Pengecekan arus listrik" }}</textarea>
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold small" for="tpl_note_pm2">Remarks Quotation PM 2</label>
                                        <textarea class="form-control form-control-sm" id="tpl_note_pm2" name="note_pm2" rows="2" placeholder="Catatan quotation PM2">{{ $defaultTemplate->note_pm2 ?? "Scope pekerjaan jasa PM 2" }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PM 3 Template -->
                        <div class="col-md-6">
                            <div class="card h-100 border shadow-none" style="background: #fffdfa;">
                                <div class="card-header bg-transparent py-2 border-bottom d-flex align-items-center justify-content-between">
                                    <span class="badge bg-warning px-3 py-1 fs-7 fw-bold text-dark"><i class="mdi mdi-flask-outline me-1"></i>Template Standar PM 3</span>
                                    <small class="text-muted fw-semibold">Level 3</small>
                                </div>
                                <div class="card-body p-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small" for="tpl_desc_pm3">Scope Kerja PM 3 (Enter utk auto-bullet)</label>
                                        <textarea class="form-control form-control-sm desc-pm-bullet" id="tpl_desc_pm3" name="desc_pm3" rows="3" placeholder="Scope kerja PM3">{{ $defaultTemplate->desc_pm3 ?? "• Scope PM 2\n• Flushing system & Pembersihan Carbon Remover\n• Pengecekan elemen pendingin" }}</textarea>
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold small" for="tpl_note_pm3">Remarks Quotation PM 3</label>
                                        <textarea class="form-control form-control-sm" id="tpl_note_pm3" name="note_pm3" rows="2" placeholder="Catatan quotation PM3">{{ $defaultTemplate->note_pm3 ?? "Scope pekerjaan jasa PM 3" }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PM 4 Template -->
                        <div class="col-md-6">
                            <div class="card h-100 border shadow-none" style="background: #fffafa;">
                                <div class="card-header bg-transparent py-2 border-bottom d-flex align-items-center justify-content-between">
                                    <span class="badge bg-danger px-3 py-1 fs-7 fw-bold"><i class="mdi mdi-cog-sync-outline me-1"></i>Template Standar PM 4</span>
                                    <small class="text-muted fw-semibold">Level 4</small>
                                </div>
                                <div class="card-body p-3">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small" for="tpl_desc_pm4">Scope Kerja PM 4 (Enter utk auto-bullet)</label>
                                        <textarea class="form-control form-control-sm desc-pm-bullet" id="tpl_desc_pm4" name="desc_pm4" rows="3" placeholder="Scope kerja PM4">{{ $defaultTemplate->desc_pm4 ?? "• Overhaul Service & penggantian major kit\n• Kalibrasi elemen & bearing\n• Full testing & commissioning" }}</textarea>
                                    </div>
                                    <div>
                                        <label class="form-label fw-semibold small" for="tpl_note_pm4">Remarks Quotation PM 4</label>
                                        <textarea class="form-control form-control-sm" id="tpl_note_pm4" name="note_pm4" rows="2" placeholder="Catatan quotation PM4">{{ $defaultTemplate->note_pm4 ?? "Garansi service overhaul sesuai ketentuan berlaku" }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top py-3">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-content-save me-1"></i>Simpan Template Standar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="formDeletePrice" action="" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@push('script')
<script>
    $(document).ready(function() {
        const manageModal = new bootstrap.Modal(document.getElementById('modalManagePrice'));
        const templateModal = new bootstrap.Modal(document.getElementById('modalStandardTemplate'));

        // Helper currency formatter
        function formatRupiah(val) {
            if (val === undefined || val === null || val === '') return '';
            var strVal = val.toString().replace(/[^0-9]/g, '');
            return strVal.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Apply live currency mask
        $(document).on('input', '.currency-mask', function() {
            var formatted = formatRupiah($(this).val());
            $(this).val(formatted);
        });

        // Search Table filter
        $('#searchTable').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#pricesTable tbody tr.main-row').filter(function() {
                var match = $(this).text().toLowerCase().indexOf(value) > -1;
                $(this).toggle(match);
                var targetId = $(this).find('.btn-toggle-detail').data('target');
                if (!match && targetId) {
                    $(targetId).addClass('d-none');
                    $(this).find('.btn-toggle-detail i').removeClass('mdi-chevron-up').addClass('mdi-chevron-down');
                }
            });
        });

        // Toggle detail row
        $('.btn-toggle-detail').click(function() {
            var target = $($(this).data('target'));
            var icon = $(this).find('i');
            
            if (target.hasClass('d-none')) {
                target.removeClass('d-none');
                icon.removeClass('mdi-chevron-down').addClass('mdi-chevron-up');
            } else {
                target.addClass('d-none');
                icon.removeClass('mdi-chevron-up').addClass('mdi-chevron-down');
            }
        });

        // Sync powerSelect and customPower
        $('#powerSelect').change(function() {
            if ($(this).val() !== '') {
                $('#customPower').val('');
            }
        });
        $('#customPower').on('input', function() {
            if ($(this).val().trim() !== '') {
                $('#powerSelect').val('');
            }
        });

        // Open Add Price Modal
        $('#btnOpenAddModal').click(function() {
            $('#formManagePrice')[0].reset();
            $('#powerSelect').val('');
            $('#customPower').val('');
            $('#modalManagePriceTitle').text('Tambah Master Harga Jasa PM Baru');
            manageModal.show();
        });

        // Open Edit Price Modal
        $('.btn-edit').click(function() {
            var btn = $(this);
            var power = btn.data('power');

            $('#modalManagePriceTitle').text('Edit Master Harga Jasa PM - ' + power);
            
            var matchedSelect = false;
            $('#powerSelect option').each(function() {
                if ($(this).val().toLowerCase() === power.toLowerCase()) {
                    $('#powerSelect').val($(this).val());
                    $('#customPower').val('');
                    matchedSelect = true;
                    return false;
                }
            });

            if (!matchedSelect) {
                $('#powerSelect').val('');
                $('#customPower').val(power);
            }

            $('#price_pm1').val(formatRupiah(btn.data('pm1')));
            $('#price_pm2').val(formatRupiah(btn.data('pm2')));
            $('#price_pm3').val(formatRupiah(btn.data('pm3')));
            $('#price_pm4').val(formatRupiah(btn.data('pm4')));

            manageModal.show();
        });

        // Open Standard Template Modal
        $('#btnOpenTemplateModal').click(function() {
            $('.desc-pm-bullet').each(function() {
                autoResizeDescPm(this);
            });
            templateModal.show();
        });

        // Delete Confirm SweetAlert
        $('.btn-delete').click(function() {
            var id = $(this).data('id');
            var power = $(this).data('power');
            var deleteUrl = "{{ url('/forecast/prices') }}/" + id;

            Swal.fire({
                title: 'Hapus Master Harga?',
                text: "Apakah Anda yakin ingin menghapus tarif harga jasa PM untuk power (" + power + ")? Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Data',
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'btn btn-danger waves-effect waves-light me-2',
                    cancelButton: 'btn btn-label-secondary waves-effect'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    var deleteForm = $('#formDeletePrice');
                    deleteForm.attr('action', deleteUrl);
                    deleteForm.submit();
                }
            });
        });

        // Auto-bullet logic on Scope Kerja
        var BULLET = '• ';

        function autoResizeDescPm(ta) {
            ta.style.height = 'auto';
            ta.style.height = Math.max(ta.scrollHeight, 70) + 'px';
        }

        $('.desc-pm-bullet').each(function() {
            var ta = this;

            ta.addEventListener('focus', function() {
                if (this.value.trim() === '') {
                    this.value = BULLET;
                    this.setSelectionRange(BULLET.length, BULLET.length);
                }
            });

            ta.addEventListener('keydown', function(e) {
                if (e.key !== 'Enter') return;
                e.preventDefault();

                var start = this.selectionStart;
                var end   = this.selectionEnd;
                var val   = this.value;

                var lineStart = val.lastIndexOf('\n', start - 1) + 1;
                var currentLine = val.substring(lineStart, start);

                if (currentLine === BULLET || currentLine === '•') {
                    this.value = val.substring(0, lineStart) + val.substring(end);
                    this.setSelectionRange(lineStart, lineStart);
                    return;
                }

                var insert = '\n' + BULLET;
                this.value = val.substring(0, start) + insert + val.substring(end);
                var newPos = start + insert.length;
                this.setSelectionRange(newPos, newPos);
            });

            ta.addEventListener('blur', function() {
                if (this.value && !this.value.startsWith(BULLET) && this.value.trim() !== '') {
                    this.value = BULLET + this.value;
                }
            });

            ta.addEventListener('input', function() { autoResizeDescPm(ta); });
            ta.addEventListener('keydown', function() {
                setTimeout(function() { autoResizeDescPm(ta); }, 0);
            });
            autoResizeDescPm(ta);
        });
    });
</script>
@endpush
@endsection
