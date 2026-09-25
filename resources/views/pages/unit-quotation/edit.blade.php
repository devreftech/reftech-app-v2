@extends('layouts.sales.app')
@section('title', 'Edit Smart Quote')
@section('content')
    {{-- Hero Page Header & Top Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <span class="text-muted fw-light">Sales / <a href="{{ route('unit-quotation.show', $quote->id) }}" class="text-muted">Smart Quote</a> /</span> Edit
            </h4>
            <p class="text-muted mb-0 small"><i class="mdi mdi-file-document-edit-outline me-1"></i> Edit quotation document {{ $quote->no_quote }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('unit-quotation.show', $quote->id) }}" class="btn btn-label-secondary">
                <i class="mdi mdi-arrow-left me-1"></i> Back
            </a>
            @if (!in_array(Auth::user()->role, ['Admin', 'Sales Manager']))
                @if ($quote->is_draft)
                    <button type="button" class="btn btn-outline-primary shadow-sm btn-action-draft">
                        <i class="mdi mdi-file-document-edit-outline me-1"></i> Simpan Draft
                    </button>
                    <button type="submit" form="form-unit-quotation" class="btn btn-primary shadow-sm btn-action-save">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Terbitkan Quotation
                    </button>
                @else
                    <button type="submit" form="form-unit-quotation" class="btn btn-primary shadow-sm">
                        <i class="mdi mdi-content-save me-1"></i> Update Quotation
                    </button>
                @endif
            @else
                <button type="submit" form="form-unit-quotation" class="btn btn-primary shadow-sm">
                    <i class="mdi mdi-content-save me-1"></i> Update Quotation
                </button>
            @endif
        </div>
    </div>

    <form action="{{ route('unit-quotation.update', $quote->id) }}" method="POST" id="form-unit-quotation">
        @csrf
        @method('PUT')
        <input type="hidden" name="is_draft" id="input_is_draft" value="{{ $quote->is_draft ? 1 : 0 }}">

        {{-- Hero Quotation Header Card --}}
        <div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9ff 0%, #f0f2ff 100%); border-left: 5px solid #696cff !important;">
            <div class="card-body py-3">
                <div class="row align-items-center g-3">
                    <div class="col-md-8 col-12">
                        <label class="form-label text-uppercase fw-bold text-primary small mb-1" style="letter-spacing: .5px;">
                            <i class="mdi mdi-pound me-1"></i> Quotation Number
                        </label>
                        <input type="text" class="form-control form-control-lg fw-bold bg-white text-primary border-primary-subtle shadow-sm"
                            name="no_quote" placeholder="Quotation Number" value="{{ old('no_quote', $quote->no_quote) }}" style="font-size: 1.35rem;">
                    </div>
                    <div class="col-md-4 col-12 text-md-end">
                        <span class="badge bg-label-info px-3 py-2 fs-6 rounded-pill">
                            <i class="mdi mdi-file-document-edit-outline me-1"></i> STATUS: EDIT MODE
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- HEADER CLIENT & DETAILS --}}
        <div class="card mb-4 border shadow-xs" style="border-radius: 12px; border-color: #e6e8ec !important; background: #ffffff;">
            {{-- Section 1: Customer & Delivery Address --}}
            <div class="card-header bg-white border-bottom py-3 px-4" style="border-color: #f0f2f5 !important;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="avatar avatar-sm bg-label-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-domain fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 14.5px;">Customer & Delivery Information</h6>
                            <small class="text-muted" style="font-size: 11.5px;">Pilih perusahaan client, kontak penanggung jawab (PIC), dan alamat tujuan pengiriman</small>
                        </div>
                    </div>
                    <span class="badge bg-label-primary rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 11px;">
                        <i class="mdi mdi-account-group-outline me-1"></i> Customer Data
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                @if ($isManager ?? false)
                    <div class="p-3 mb-4 rounded-3 border" style="background: linear-gradient(135deg, #f8faff 0%, #f4f6fb 100%); border-color: #dbe4ff !important;">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <div class="fw-semibold text-dark small">
                                    <i class="mdi mdi-briefcase-check-outline text-primary me-1"></i> Atribusi Penjualan (Pemilik Penawaran & Omset PO)
                                </div>
                                <span class="text-muted" style="font-size: 11px;">Tentukan apakah omset penawaran ini diakui sebagai Sales Project atau didelegasikan ke Sales individu.</span>
                            </div>
                            <div style="min-width: 260px;">
                                <select class="select2 form-select form-select-sm" name="id_sales" id="edit-sales-select">
                                    <option value="{{ Auth::id() }}" {{ $quote->id_sales == Auth::id() ? 'selected' : '' }}>
                                        Sales Project ({{ Auth::user()->name }})
                                    </option>
                                    <optgroup label="Delegasikan ke Sales:">
                                        @foreach ($salesUsers as $s)
                                            <option value="{{ $s->id }}" {{ $quote->id_sales == $s->id ? 'selected' : '' }}>
                                                {{ $s->name }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-lg-4 col-md-5 col-12">
                        <label class="form-label fw-semibold text-dark small mb-1" for="client-select">
                            <i class="mdi mdi-domain text-primary me-1"></i> Perusahaan / Client <span class="text-danger">*</span>
                        </label>
                        <select class="select2 form-select" name="id_client" id="client-select">
                            <option value="">-- Select Client --</option>
                            @foreach ($clients as $c)
                                <option value="{{ $c->id }}"
                                    data-role="{{ $c->role }}"
                                    {{ $quote->id_client == $c->id ? 'selected' : '' }}>
                                    {{ $c->company }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-3 col-12">
                        <label class="form-label fw-semibold text-dark small mb-1" for="pic-select">
                            <i class="mdi mdi-account-tie-outline text-primary me-1"></i> PIC / Contact Person <span class="text-danger">*</span>
                        </label>
                        <select class="select2 form-select" name="id_pic" id="pic-select">
                            <option value="">-- Select PIC --</option>
                            @if ($quote->pic)
                                <option value="{{ $quote->pic->id }}"
                                    data-name="{{ $quote->pic->name_pic }}"
                                    data-position="{{ $quote->pic->position }}"
                                    data-phone="{{ $quote->pic->phone_pic }}"
                                    data-email="{{ $quote->pic->email_pic }}"
                                    selected>
                                    {{ $quote->pic->name_pic }}{{ $quote->pic->position ? ' (' . $quote->pic->position . ')' : '' }}
                                </option>
                            @endif
                        </select>
                    </div>

                    <div class="col-lg-5 col-md-4 col-12">
                        <label class="form-label fw-semibold text-dark small mb-1" for="address-select">
                            <i class="mdi mdi-map-marker-radius-outline text-primary me-1"></i> Address / Plant Destination
                        </label>
                        <select class="select2 form-select" id="address-select">
                            <option value="">-- Select Address --</option>
                        </select>
                        <input type="hidden" name="id_plant" id="input-id-plant" value="{{ $quote->id_plant }}">
                        <input type="hidden" name="address" id="input-address-hidden" value="{{ $quote->address }}">
                        
                        <div id="address-preview-card" class="mt-2 p-2 px-3 rounded border d-none" style="background-color: #f8f9fa; border-color: #e5e7eb !important;">
                            <div class="d-flex align-items-start gap-2">
                                <i class="mdi mdi-map-marker-radius text-primary fs-5 mt-0" style="line-height: 1.2;"></i>
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-label-primary" id="address-preview-badge">Office / Factory</span>
                                    </div>
                                    <div class="text-dark small fw-normal" id="address-preview-text" style="font-size: 0.8rem; line-height: 1.35; word-break: break-word;">-</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" id="manual-address-wrapper" style="display: none;">
                        <div class="p-3 rounded-3 border bg-light-subtle" style="border-style: dashed !important; border-color: #7367f0 !important;">
                            <label class="form-label fw-semibold text-primary small mb-1" for="input-address-manual">
                                <i class="mdi mdi-pencil-outline me-1"></i> Alamat Pengiriman Khusus (Custom Address)
                            </label>
                            <textarea class="form-control" id="input-address-manual" rows="2" style="height: 65px;" placeholder="Masukkan alamat lengkap pengiriman khusus...">{{ $quote->address }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Quotation Parameters & Specifications --}}
            <div class="card-header bg-light-subtle border-top border-bottom py-3 px-4" style="border-color: #e6e8ec !important;">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="avatar avatar-sm bg-label-info rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="mdi mdi-file-document-outline fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark" style="font-size: 14.5px;">Quotation Parameters & Specifications</h6>
                            <small class="text-muted" style="font-size: 11.5px;">Atur tanggal penawaran, masa berlaku, kategori spesifikasi, dan deskripsi proyek</small>
                        </div>
                    </div>
                    <span class="badge bg-label-info rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 11px;">
                        <i class="mdi mdi-cog-outline me-1"></i> Parameters
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label fw-semibold text-dark small mb-1" for="input-date">
                            <i class="mdi mdi-calendar-range text-primary me-1"></i> Tanggal Penawaran
                        </label>
                        <input type="date" class="form-control" id="input-date" name="date"
                            value="{{ old('date', $quote->date?->format('Y-m-d')) }}">
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label fw-semibold text-dark small mb-0" for="input-expired-date">
                                <i class="mdi mdi-calendar-clock text-warning me-1"></i> Expired Quotation
                            </label>
                            <span class="badge bg-label-warning px-1.5 py-0" style="font-size: 9.5px;">Auto +1 Bulan</span>
                        </div>
                        <input type="date" class="form-control bg-light-subtle" id="input-expired-date" name="expired_date"
                            value="{{ old('expired_date', $quote->expired_date ? \Carbon\Carbon::parse($quote->expired_date)->format('Y-m-d') : \Carbon\Carbon::parse($quote->date)->addMonth()->format('Y-m-d')) }}"
                            readonly title="Auto-calculated: 1 month from quotation date">
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label fw-semibold text-dark small mb-1" for="select-type">
                            <i class="mdi mdi-tag-outline text-primary me-1"></i> Tipe Penawaran
                        </label>
                        <select class="form-select" id="select-type" name="type">
                            <option value="" disabled>-- Type --</option>
                            @foreach (['Unit', 'Rental', 'Project', 'Parts', 'Service', 'Piping', 'Air Audit', 'General Check / Visit', 'HVAC', 'Fire System'] as $t)
                                <option value="{{ $t }}" {{ $quote->type === $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <label class="form-label fw-semibold text-dark small mb-1" for="select-week">
                            <i class="mdi mdi-calendar-week text-primary me-1"></i> Periode Week
                        </label>
                        <select class="form-select" id="select-week" name="week">
                            <option value="" disabled>-- Week --</option>
                            @foreach ([1,2,3,4,5] as $w)
                                <option value="{{ $w }}" {{ $quote->week == $w ? 'selected' : '' }}>Week {{ $w }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6 col-12" id="unit-condition-wrapper" style="display:none;">
                        <label class="form-label fw-semibold text-dark small mb-1" for="select-unit-condition">
                            <i class="mdi mdi-certificate-outline text-success me-1"></i> Kondisi Unit
                        </label>
                        <select class="form-select" id="select-unit-condition" name="unit_condition">
                            <option value="" disabled {{ $quote->unit_condition ? '' : 'selected' }}>-- Kondisi --</option>
                            <option value="Baru" {{ $quote->unit_condition === 'Baru' ? 'selected' : '' }}>Unit Baru</option>
                            <option value="Second" {{ $quote->unit_condition === 'Second' ? 'selected' : '' }}>Unit Second</option>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-5 col-12">
                        <label class="form-label fw-semibold text-dark small mb-1" for="input-no-pr">
                            <i class="mdi mdi-pound text-secondary me-1"></i> No PR <span class="text-muted small fw-normal">(Optional)</span>
                        </label>
                        <input type="text" class="form-control" id="input-no-pr" name="no_pr" placeholder="mis. PR-2026/09/001"
                            value="{{ old('no_pr', $quote->no_pr) }}">
                    </div>

                    <div class="col-lg-8 col-md-7 col-12">
                        <label class="form-label fw-semibold text-dark small mb-1" for="input-title">
                            <i class="mdi mdi-format-title text-primary me-1"></i> Judul / Deskripsi Proyek
                        </label>
                        <input type="text" class="form-control" id="input-title" name="title" placeholder="mis. Pengadaan & Instalasi Air Compressor Screw 55kW"
                            value="{{ old('title', $quote->title) }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- QUOTATION OPTIONS (Opsi 1, Opsi 2, dst — buat perbandingan harga) --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <ul class="nav nav-pills flex-wrap gap-2 mb-0" id="options-tab-nav" role="tablist"></ul>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-add-option">
                    <i class="mdi mdi-plus me-1"></i> Tambah Opsi
                </button>
            </div>
            <div class="tab-content" id="options-tab-content">
                {{-- option panes injected by JS --}}
            </div>
        </div>

        {{-- NOTE + TERMS & CONDITIONS (shared, gak per-opsi) --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-lg-9 mx-auto">
                        {{-- Note --}}
                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="fw-bold mb-2 text-dark">
                                <i class="mdi mdi-notebook-edit-outline me-1 text-primary"></i> Note / Quotation Remarks
                            </h6>
                            <textarea class="form-control" name="note" id="note"
                                rows="3" placeholder="• Write your quotation note/remarks here..."
                                style="overflow-y: hidden; resize: none;">{{ old('note', $quote->note) }}</textarea>
                            <div class="form-text text-muted mt-1"><i class="mdi mdi-information-outline me-1"></i>Tekan <kbd>Enter</kbd> untuk baris baru otomatis ber-bullet.</div>
                        </div>

                        {{-- KETENTUAN RENTAL UNIT KOMPRESOR (Card terpisah khusus tipe Rental) --}}
                        <div class="mb-4 pb-3 border-bottom" id="rental-terms-card-wrapper" style="{{ (old('type', $quote->type) === 'Rental') ? '' : 'display: none;' }}">
                            <div class="card border border-warning shadow-none" style="background: #fffdf9; border-radius: 8px;">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                                            <i class="mdi mdi-file-document-check-outline me-2 text-warning fs-5"></i> KETENTUAN RENTAL UNIT KOMPRESOR
                                        </h6>
                                        <button type="button" class="btn btn-xs btn-outline-warning py-0.5 px-2 rounded shadow-none" id="btnResetRentalTerms" title="Muat ulang template klausul ketentuan rental dari master setting">
                                            <i class="mdi mdi-sync me-1"></i> Muat Ulang Template
                                        </button>
                                    </div>
                                    <textarea class="form-control bg-white" name="rental_terms" id="rental_terms"
                                        rows="4" placeholder="• Masukkan klausul ketentuan rental unit kompresor di sini..."
                                        style="overflow-y: hidden; resize: none;">{{ old('rental_terms', $quote->rental_terms ?? $rentalNoteTemplate ?? '') }}</textarea>
                                    <div class="form-text text-muted mt-1 d-flex justify-content-between align-items-center">
                                        <span><i class="mdi mdi-information-outline me-1 text-warning"></i>Ketentuan khusus rental kompresor. Tekan <kbd>Enter</kbd> untuk baris baru otomatis ber-bullet.</span>
                                        <span class="badge bg-label-warning small">Khusus Type Rental</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Terms & Conditions --}}
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                                <h6 class="fw-bold mb-0 text-dark">
                                    <i class="mdi mdi-shield-check-outline me-1 text-primary"></i> Terms & Conditions
                                    <span id="terms-card-active-option-label" class="badge bg-label-primary ms-1" style="display:none; font-size:11px; vertical-align:middle;"></span>
                                </h6>
                                <div id="wrapper-toggle-merge-terms" style="display: none;">
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="toggle-merge-terms" name="merge_terms" value="1" {{ old('merge_terms', $quote->merge_terms) ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold small text-primary" for="toggle-merge-terms" style="font-size:11.5px; cursor:pointer;">
                                            <i class="mdi mdi-set-all me-1"></i>Gabungkan T&amp;C Semua Opsi
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted small mb-3 d-none" id="terms-card-hint" style="font-size:11px;">
                                <i class="mdi mdi-information-outline me-1"></i>Isian di bawah ini T&amp;C khusus untuk opsi yang lagi aktif di tab atas — tiap Opsi bisa beda, tinggal pindah tab lalu isi ulang.
                            </p>
                            <p class="text-primary small mb-3 d-none" id="terms-card-merged-hint" style="font-size:11px; background: #eef2ff; padding: 6px 10px; border-radius: 6px; border-left: 3px solid #696cff;">
                                <i class="mdi mdi-check-circle-outline me-1"></i><strong>Mode T&amp;C Tergabung Aktif:</strong> Ketentuan di bawah ini berlaku sama untuk semua opsi dan dicetak 1 kali di bagian bawah dokumen.
                            </p>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="validity">Validity of Quotation</label>
                                <div class="col-sm-8">
                                    <input type="text" id="validity" class="form-control form-control-sm" name="validity"
                                        value="{{ old('validity', $quote->validity) }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="pricing">Price</label>
                                <div class="col-sm-8">
                                    <input type="text" id="pricing" class="form-control form-control-sm" name="pricing"
                                        value="{{ old('pricing', $quote->pricing) }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="payment-select">Payment</label>
                                <div class="col-sm-8">
                                    <select class="form-select form-select-sm" id="payment-select">
                                        @if(isset($paymentTemplates) && count($paymentTemplates) > 0)
                                            <optgroup label="Template Sales">
                                                @foreach($paymentTemplates as $pt)
                                                    <option value="{{ $pt->payment_term }}">
                                                        {{ $pt->name }} {{ $pt->client ? '('.$pt->client->company.')' : '' }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                        <optgroup label="Standar System">
                                            <option value="Cash Before Delivery">Cash Before Delivery</option>
                                            <option value="DP 50% & BP 50%">DP 50% & BP 50%</option>
                                            <option value="DP 30% & BP 70%">DP 30% & BP 70%</option>
                                            <option value="14 Days after invoice release">14 Days after invoice release</option>
                                            <option value="30 Days after invoice release">30 Days after invoice release</option>
                                        </optgroup>
                                        <option value="manual">-- Custom (Isi Sendiri) --</option>
                                    </select>
                                    <input type="hidden" name="payment" id="input-payment-hidden" value="{{ old('payment', $quote->payment) }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center" id="manual-payment-wrapper" style="display: none;">
                                <div class="col-sm-8 offset-sm-4">
                                    <input type="text" class="form-control form-control-sm" id="input-payment-manual" placeholder="Ketik custom payment term...">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center" id="warranty-wrapper" style="{{ empty(old('warranty', $quote->warranty)) ? 'display: none;' : '' }}">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="warranty">Warranty</label>
                                <div class="col-sm-8">
                                    <input type="text" id="warranty" class="form-control form-control-sm" name="warranty"
                                        value="{{ old('warranty', $quote->warranty) }}">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-4 col-form-label text-muted small fw-semibold" for="delivery">Delivery Process</label>
                                <div class="col-sm-8">
                                    <textarea id="delivery" class="form-control form-control-sm" name="delivery_process" rows="1" style="resize: vertical;">{{ old('delivery_process', $quote->delivery_process) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('unit-quotation.show', $quote->id) }}" class="btn btn-label-secondary">Cancel</a>
            @if (!in_array(Auth::user()->role, ['Admin', 'Sales Manager']))
                @if ($quote->is_draft)
                    <button type="button" class="btn btn-outline-primary shadow-sm px-4 btn-action-draft">
                        <i class="mdi mdi-file-document-edit-outline me-1"></i> Simpan Draft
                    </button>
                    <button type="submit" class="btn btn-primary shadow-sm px-4 btn-action-save">
                        <i class="mdi mdi-check-circle-outline me-1"></i> Terbitkan Quotation
                    </button>
                @else
                    <button type="submit" class="btn btn-primary shadow-sm px-4">
                        <i class="mdi mdi-content-save me-1"></i> Save Changes
                    </button>
                @endif
            @else
                <button type="submit" class="btn btn-primary shadow-sm px-4">
                    <i class="mdi mdi-content-save me-1"></i> Save Changes
                </button>
            @endif
        </div>
    </form>

    <datalist id="common-spec-keys">
        <option value="Brand">
        <option value="Model">
        <option value="Power">
        <option value="Air Capacity">
        <option value="Max Pressure">
        <option value="Voltage">
        <option value="Cooling Method">
        <option value="Condition">
        <option value="Year">
        <option value="Warranty">
    </datalist>

    <template id="tmpl-unit-row">
        <div class="unit-row border-bottom p-3" data-type="unit">
            <input type="hidden" name="items[__IDX__][type]" value="unit">
            <input type="hidden" name="items[__IDX__][id_unit]" class="field-id-unit">
            <input type="hidden" name="items[__IDX__][id_fixed_asset]" class="field-id-fixed-asset">
            <input type="hidden" name="items[__IDX__][id_equivalent]" class="field-id-equivalent">
            <input type="hidden" name="items[__IDX__][spec_visible]" class="field-spec-visible">
            <textarea name="items[__IDX__][description]" class="field-description" style="display:none;"></textarea>

            <div class="d-flex align-items-center mb-2">
                <div class="btn-drag-handle text-muted me-2" title="Geser (drag & drop) untuk memindahkan posisi">
                    <i class="mdi mdi-drag-vertical fs-4"></i>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input unit-source-radio" type="radio" name="unit_source___IDX__"
                        value="sparepart" checked>
                    <label class="form-check-label small">Spare Part</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input unit-source-radio" type="radio" name="unit_source___IDX__"
                        value="catalog">
                    <label class="form-check-label small">Catalog Unit</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input unit-source-radio" type="radio" name="unit_source___IDX__"
                        value="fixed_asset">
                    <label class="form-check-label small">Unit Second</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input unit-source-radio" type="radio" name="unit_source___IDX__"
                        value="rental">
                    <label class="form-check-label small">Rental</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input unit-source-radio" type="radio" name="unit_source___IDX__"
                        value="other">
                    <label class="form-check-label small">Other</label>
                </div>
            </div>

            <div class="row g-2 align-items-start">
                <div class="col-md-4">
                    <div class="unit-source-catalog" style="display:none;">
                        <select class="select2-unit-search form-select form-select-sm" style="width:100%">
                            <option value="">Search unit (SKU / Brand / Model)...</option>
                        </select>
                        <div class="unit-inventory-stock-feedback mt-1" style="display:none;"></div>
                    </div>
                    <div class="unit-source-fixed-asset" style="display:none;">
                        <select class="select2-fixed-asset-search form-select form-select-sm" style="width:100%">
                            <option value="">Search Unit Second (SKU / Brand / Serial Number)...</option>
                        </select>
                    </div>
                    <div class="unit-source-equivalent">
                        <select class="select2-equivalent-search form-select form-select-sm" style="width:100%">
                            <option value="">Search Spare Part / Equivalent (PN / Brand / Name)...</option>
                        </select>
                    </div>
                    <div class="unit-source-other" style="display:none;">
                        <input type="text" class="form-control form-control-sm field-other-input"
                            placeholder="Ketik Nama / Tipe Unit (Vendor Luar)...">
                    </div>
                </div>
                <div class="col-md-1">
                    <input type="number" class="form-control form-control-sm text-center field-qty"
                        name="items[__IDX__][qty]" value="1" min="1" placeholder="Qty">
                </div>
                <div class="col-md-1">
                    <input type="text" class="form-control form-control-sm text-center field-info-qty"
                        name="items[__IDX__][info_qty]" value="Unit" readonly>
                    <select class="form-select form-select-sm text-center field-info-qty-select px-1" style="display: none;">
                        <option value="Days" selected>Days</option>
                        <option value="Month">Month</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control form-control-sm text-end field-price rupiah-input"
                        name="items[__IDX__][price]" placeholder="Price" autocomplete="off">
                </div>
                <div class="col-md-1">
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control text-center field-disc"
                            name="items[__IDX__][disc]" value="0" min="0" max="100" placeholder="Disc">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-2 text-end">
                    <span class="field-amount fw-semibold text-primary">Rp 0</span>
                </div>
                <div class="col-md-1 text-end">
                    <div class="d-inline-flex align-items-center gap-1">
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-move-up" title="Geser ke atas"><i class="mdi mdi-arrow-up"></i></button>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-move-down" title="Geser ke bawah"><i class="mdi mdi-arrow-down"></i></button>
                        </div>
                        <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row" title="Hapus Baris">
                            <i class="mdi mdi-delete-outline"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Custom Title --}}
            <div class="mt-2">
                <input type="text" class="form-control form-control-sm field-label"
                    name="items[__IDX__][label]"
                    placeholder="Item title (auto-filled, editable)">
            </div>

            {{-- Spec Preview (Catalog Unit & Unit Second) --}}
            <div class="spec-preview mt-2 ms-1 ps-3 border-start border-2" style="display:none;">
                <div class="spec-rows"></div>
                <p class="text-muted small mb-0 mt-1">
                    <i class="mdi mdi-information-outline me-1"></i>
                    Click <kbd>×</kbd> on a spec to hide it from the quotation.
                </p>
            </div>

            {{-- Manual Spec Section for Other --}}
            <div class="other-spec-section mt-2 ms-1 ps-3 border-start border-2 border-primary" style="display:none;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-semibold text-dark small" style="font-size: 11.5px;">
                        <i class="mdi mdi-tune-vertical text-primary me-1"></i>Spesifikasi Unit (Manual Input):
                    </span>
                    <button type="button" class="btn btn-xs btn-outline-primary btn-add-other-spec py-0 px-2" style="font-size: 10.5px;">
                        <i class="mdi mdi-plus me-1"></i>Tambah Spesifikasi
                    </button>
                </div>
                <div class="other-spec-rows"></div>
                <div class="text-muted small mt-1" style="font-size: 10.5px;">
                    <i class="mdi mdi-information-outline me-1 text-secondary"></i>Spesifikasi akan ditampilkan rapi 2-kolom sejajar pada cetakan penawaran &amp; detail.
                </div>
            </div>

            {{-- Spare Part Stock Preview --}}
            <div class="equivalent-stock-preview mt-2" style="display:none;">
                <span class="badge bg-label-info me-1">BDG Stock: <span class="stock-bdg">0</span></span>
                <span class="badge bg-label-info me-1">BKS Stock: <span class="stock-bks">0</span></span>
                <span class="badge bg-label-warning">Pending Stock: <span class="stock-pending">0</span></span>
            </div>
        </div>
    </template>

    <template id="tmpl-custom-row">
        <div class="unit-row border-bottom p-3" data-type="custom">
            <input type="hidden" name="items[__IDX__][type]" value="custom">

            <div class="row g-3 align-items-start">
                {{-- Left: Drag Handle + Item Title & Description (30%) --}}
                <div class="col-md-4 d-flex align-items-start" style="flex: 0 0 30%; max-width: 30%;">
                    <div class="btn-drag-handle text-muted me-2 mt-1" title="Geser (drag & drop) untuk memindahkan posisi">
                        <i class="mdi mdi-drag-vertical fs-4"></i>
                    </div>
                    <div class="w-100">
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm fw-bold field-label"
                                name="items[__IDX__][label]" placeholder="Item Title *" required>
                        </div>
                        <div>
                            <textarea class="form-control form-control-sm field-description"
                                name="items[__IDX__][description]" rows="2" placeholder="Description (optional)"></textarea>
                        </div>
                    </div>
                </div>

                {{-- Right: Qty, Price, Disc, Nominal, Move Up/Down, Remove (70%) --}}
                <div class="col-md-8" style="flex: 0 0 70%; max-width: 70%;">
                    <div class="row g-2 align-items-center h-100 pt-1">
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Qty</span>
                                <input type="number" class="form-control text-center field-qty"
                                    name="items[__IDX__][qty]" value="1" min="1">
                                <select class="form-select field-info-qty-select" style="max-width:72px;">
                                    <option value="Lot">Lot</option>
                                    <option value="Set">Set</option>
                                    <option value="Unit">Unit</option>
                                    <option value="Pcs">Pcs</option>
                                    <option value="Ls">Ls</option>
                                    <option value="Btg">Btg</option>
                                    <option value="Mtr">Mtr</option>
                                    <option value="Days">Days</option>
                                    <option value="Bln">Bln</option>
                                    <option value="Box">Box</option>
                                    <option value="Roll">Roll</option>
                                    <option value="Kg">Kg</option>
                                    <option value="Jam">Jam</option>
                                    <option value="Paket">Paket</option>
                                    <option value="Titik">Titik</option>
                                    <option value="__custom__">+ Custom</option>
                                </select>
                                <input type="text" class="form-control text-center field-info-qty-custom px-1" placeholder="Satuan" style="display:none; max-width:65px;" maxlength="25">
                                <button type="button" class="btn btn-outline-secondary btn-custom-qty-cancel px-1" title="Batal custom (kembali ke list satuan)" style="display:none;"><i class="mdi mdi-close"></i></button>
                                <input type="hidden" class="field-info-qty" name="items[__IDX__][info_qty]" value="Lot">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Price</span>
                                <input type="text" class="form-control form-control-sm text-end field-price rupiah-input"
                                    name="items[__IDX__][price]" placeholder="Price" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Disc</span>
                                <input type="number" class="form-control text-center field-disc"
                                    name="items[__IDX__][disc]" value="0" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <span class="text-muted small me-1">Nominal:</span>
                            <span class="field-amount fw-semibold text-primary">Rp 0</span>
                        </div>
                        <div class="col-md-1 text-end">
                            <div class="d-inline-flex align-items-center gap-1">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-move-up" title="Geser ke atas"><i class="mdi mdi-arrow-up"></i></button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary btn-move-down" title="Geser ke bawah"><i class="mdi mdi-arrow-down"></i></button>
                                </div>
                                <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row" title="Hapus Baris">
                                    <i class="mdi mdi-delete-outline"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="tmpl-header-row">
        <div class="unit-row border-bottom p-3 bg-light" data-type="header">
            <input type="hidden" name="items[__IDX__][type]" value="header">
            <div class="d-flex align-items-center gap-2">
                <div class="btn-drag-handle text-muted" title="Geser (drag & drop) untuk memindahkan posisi">
                    <i class="mdi mdi-drag-vertical fs-4"></i>
                </div>
                <span class="badge bg-primary text-uppercase" style="font-size:10px;">Head Title</span>
                <div class="flex-grow-1">
                    <input type="text" class="form-control form-control-sm fw-bold text-primary field-label"
                        name="items[__IDX__][label]" placeholder="Head Title (e.g. A. SCOPE OF WORK, B. PIPING SYSTEM) *" required>
                </div>
                <span class="badge bg-label-primary section-subtotal-badge text-nowrap" style="font-size:11px;"></span>
                <div class="d-flex align-items-center gap-1">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-move-up" title="Geser ke atas"><i class="mdi mdi-arrow-up"></i></button>
                        <button type="button" class="btn btn-xs btn-outline-secondary btn-move-down" title="Geser ke bawah"><i class="mdi mdi-arrow-down"></i></button>
                    </div>
                    <button type="button" class="btn btn-sm btn-icon btn-label-danger btn-remove-row" title="Hapus Baris">
                        <i class="mdi mdi-delete-outline"></i>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- Tab nav-item untuk 1 Opsi --}}
    <template id="tmpl-option-tab">
        <li class="nav-item" data-option-idx="__OPT__">
            <a class="nav-link" data-bs-toggle="pill" href="#option-pane-__OPT__" role="tab">
                <i class="mdi mdi-file-document-outline me-1"></i>
                <span class="tab-title-display">Opsi</span>
            </a>
        </li>
    </template>

    {{-- Pane untuk 1 Opsi: judul, line items sendiri, summary sendiri --}}
    <template id="tmpl-option-pane">
        <div class="option-pane tab-pane fade" id="option-pane-__OPT__" data-option-idx="__OPT__">
            <div class="p-3 border-bottom bg-light-subtle d-flex align-items-center gap-2 flex-wrap option-header-toolbar" style="display: none;">
                <div class="option-title-wrapper align-items-center gap-2" style="display: none;">
                    <label class="fw-semibold small text-muted mb-0">Judul Opsi:</label>
                    <input type="text" class="form-control form-control-sm fw-bold option-title-input" style="max-width:320px;"
                        name="options[__OPT__][title]" placeholder="Judul Opsi (mis. Unit Baru, Unit Second)">
                </div>
                <span class="badge bg-label-secondary items-count-badge">0 Items</span>
                <button type="button" class="btn btn-sm btn-outline-danger ms-auto btn-remove-option" style="display: none;">
                    <i class="mdi mdi-delete-outline me-1"></i> Hapus Opsi Ini
                </button>
            </div>

            <div class="line-items-container">
                {{-- rows injected by JS --}}
            </div>
            <div class="empty-state text-center text-muted py-5 my-2">
                <div class="avatar avatar-md bg-label-primary mx-auto mb-3" style="width: 54px; height: 54px;">
                    <i class="mdi mdi-package-variant-closed fs-3" style="line-height: 54px;"></i>
                </div>
                <h6 class="fw-bold mb-1">No Line Items Added Yet</h6>
                <p class="text-muted small mb-0">Click the buttons below to add Spare Parts/Units from catalog, Custom Items, or Head Titles.</p>
            </div>
            <div class="d-flex flex-wrap gap-2 p-3 border-top border-bottom bg-light-subtle">
                <button type="button" class="btn btn-sm btn-primary shadow-sm btn-add-unit">
                    <i class="mdi mdi-plus me-1"></i> Add Item
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-add-custom">
                    <i class="mdi mdi-format-list-bulleted me-1"></i> Add Custom Item
                </button>
                <button type="button" class="btn btn-sm btn-outline-info btn-add-header">
                    <i class="mdi mdi-format-header-1 me-1"></i> Add Head Title
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary btn-add-transport">
                    <i class="mdi mdi-truck-outline me-1"></i> Add Transport
                </button>
            </div>

            {{-- Summary (subtotal/diskon/trade-in/tax/shipping/total) khusus opsi ini --}}
            <div class="p-4 bg-light-subtle border-top">
                <div class="row g-4 align-items-start">
                    {{-- Left Column: Trade-In Unit Customer Card --}}
                    <div class="col-lg-7 col-12">
                        <div class="card border border-primary-subtle shadow-xs mb-0" style="border-radius: 8px; background: #ffffff;">
                            <div class="card-header py-2.5 px-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="mdi mdi-swap-horizontal-bold text-primary fs-5"></i>
                                    <span class="fw-bold text-heading" style="font-size: 13px;">Trade-In Unit Bekas Customer</span>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input toggle-trade-in cursor-pointer" type="checkbox"
                                        name="options[__OPT__][has_trade_in]" value="1" id="toggleTradeInEdit___OPT__">
                                    <label class="form-check-label small fw-semibold text-muted ms-1" for="toggleTradeInEdit___OPT__">Aktifkan Trade-In</label>
                                </div>
                            </div>
                            <div class="card-body p-3 trade-in-body" style="display: none;">
                                <p class="text-muted small mb-2.5" style="font-size: 11px;">
                                    <i class="mdi mdi-information-outline text-primary me-1"></i>Masukkan identitas unit bekas customer dan nilai kompensasi potongan harga.
                                </p>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold mb-1" style="font-size: 11px;">Brand Unit</label>
                                        <input type="text" class="form-control form-control-sm trade-in-brand"
                                            name="options[__OPT__][trade_in_brand]" placeholder="mis. Kaeser, Atlas Copco, Hitachi">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold mb-1" style="font-size: 11px;">Model / Tipe</label>
                                        <input type="text" class="form-control form-control-sm trade-in-model"
                                            name="options[__OPT__][trade_in_model]" placeholder="mis. CS91, GA 22, OSP-15">
                                    </div>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold mb-1" style="font-size: 11px;">Power / Kapasitas</label>
                                        <input type="text" class="form-control form-control-sm trade-in-power"
                                            name="options[__OPT__][trade_in_power]" placeholder="mis. 55 kW / 75 HP">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold mb-1" style="font-size: 11px;">Serial Number (SN)</label>
                                        <input type="text" class="form-control form-control-sm trade-in-sn"
                                            name="options[__OPT__][trade_in_sn]" placeholder="mis. SN-982143">
                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold mb-1 text-primary" style="font-size: 11.5px;">Nilai Kompensasi / Potongan Trade-In (Rp) *</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text fw-bold text-primary">Rp</span>
                                            <input type="text" class="form-control form-control-sm rupiah-input fw-bold text-end trade-in-price"
                                                name="options[__OPT__][trade_in_price]" value="0" placeholder="0" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold mb-1" style="font-size: 11px;">Catatan / Kondisi Unit</label>
                                        <input type="text" class="form-control form-control-sm trade-in-notes"
                                            name="options[__OPT__][trade_in_notes]" placeholder="mis. Siap rekondisi, unit running">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Financial Summary --}}
                    <div class="col-lg-5 col-12">
                        <div class="d-flex flex-column gap-3 py-2">
                            {{-- Subtotal --}}
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <span class="text-muted fw-semibold" style="font-size: 13.5px;">Subtotal</span>
                                <span class="fw-bold text-dark fs-6 display-subtotal">Rp 0</span>
                            </div>

                            {{-- Discount --}}
                            <div class="d-flex justify-content-between align-items-center gap-3 py-1">
                                <div class="flex-grow-1" style="max-width: 200px;">
                                    <select class="form-select form-select-sm fw-semibold select-diskon-type border-primary-subtle text-heading shadow-xs" name="options[__OPT__][diskon_type]" style="font-size: 12px; border-radius: 6px; cursor: pointer; padding-top: 6px; padding-bottom: 6px;">
                                        <option value="percent" selected>Diskon Persentase ( % )</option>
                                        <option value="amount">Diskon Nominal ( Rp )</option>
                                    </select>
                                </div>
                                <div style="width: 165px;">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text fw-bold text-primary diskon-prefix-addon" style="display: none; font-size: 11px;">Rp</span>
                                        <input type="text" class="form-control text-end fw-bold input-diskon" name="options[__OPT__][diskon]" value="0" placeholder="0" autocomplete="off" style="font-size: 13px; padding-top: 5px; padding-bottom: 5px;">
                                        <span class="input-group-text fw-bold text-primary diskon-suffix-addon" style="font-size: 11px;">%</span>
                                    </div>
                                    <div class="text-end mt-1">
                                        <span class="badge bg-label-info diskon-feedback-badge" style="font-size: 10px; font-weight: 500;">Potongan: Rp 0</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Potongan Trade-In --}}
                            <div class="d-none justify-content-between align-items-center py-1 display-trade-in-row">
                                 <div>
                                     <span class="text-danger fw-semibold" style="font-size: 13.5px;"><i class="mdi mdi-swap-horizontal-bold me-1"></i>Potongan Trade-In</span>
                                     <span class="badge bg-label-secondary trade-in-summary-badge d-block text-start mt-0.5" style="font-size: 10px; font-weight: 500; display: none;">-</span>
                                 </div>
                                 <span class="fw-bold text-danger fs-6 display-trade-in">- Rp 0</span>
                            </div>

                            {{-- Dasar Pengenaan Pajak (DPP) --}}
                            <div class="d-none justify-content-between align-items-center py-1 display-dpp-row">
                                <span class="text-muted fw-semibold" style="font-size: 13px;">Dasar Pengenaan Pajak (DPP)</span>
                                <span class="fw-bold text-dark display-dpp" style="font-size: 13px;">Rp 0</span>
                            </div>

                            {{-- PPN --}}
                            <div class="d-flex justify-content-between align-items-center py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-muted fw-semibold" style="font-size: 13.5px;">PPN 12%</span>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input toggle-tax" type="checkbox" name="options[__OPT__][tax]" value="1" checked>
                                    </div>
                                </div>
                                <span class="display-tax fw-semibold text-muted small">Rp 0</span>
                            </div>

                            {{-- Shipping Cost --}}
                            <div class="d-flex justify-content-between align-items-center pb-3 border-bottom">
                                <div>
                                    <span class="text-muted fw-semibold d-block" style="font-size: 13.5px;">Shipping Cost</span>
                                    <span class="text-muted" style="font-size: 10.5px;">( Non-taxable )</span>
                                </div>
                                <div style="width: 165px;">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text fw-semibold" style="font-size:11px;">Rp</span>
                                        <input type="text" class="form-control text-end fw-semibold rupiah-input input-shipping" name="options[__OPT__][shipping]" value="0" placeholder="0" autocomplete="off" style="padding-top: 5px; padding-bottom: 5px;">
                                    </div>
                                </div>
                            </div>

                            {{-- Total Amount --}}
                            <div class="p-3.5 rounded-3 d-flex justify-content-between align-items-center mt-2" style="background: linear-gradient(135deg, #f0f2ff 0%, #e8ebff 100%); border: 1px dashed #696cff;">
                                <div>
                                    <div class="text-uppercase fw-bold text-primary" style="font-size: 11px; letter-spacing: 0.8px;">Total Amount</div>
                                    <div class="text-muted" style="font-size: 10.5px;">( Inclusive of Tax, Discount &amp; Trade-In )</div>
                                </div>
                                <div class="fw-bolder text-primary fs-3 display-total" style="letter-spacing: -0.5px;">Rp 0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .bg-light-primary { background-color: #f0f0ff !important; border: 2px dashed #696cff !important; }
        .btn-drag-handle { cursor: grab; padding: 2px 4px; border-radius: 4px; transition: background 0.15s; }
        .btn-drag-handle:hover { background: #e8e8ff; color: #696cff !important; }
        .btn-drag-handle:active { cursor: grabbing; }
        /* Sembunyikan toolbar header opsi (badge items, judul opsi, tombol hapus) jika opsi cuma 1 */
        .tab-content:not(.has-multi-options) .option-header-toolbar {
            display: none !important;
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/sortablejs/sortable.js"></script>
@endpush

@push('page-script')
    <script>
        window.EDIT_OPTIONS = @json($editOptions);
        window.EDIT_CLIENT_ID = {{ $quote->id_client ?? 'null' }};
        window.EDIT_PIC_ID = {{ $quote->id_pic ?? 'null' }};
        window.EDIT_PLANT_ID = {{ $quote->id_plant ?? 'null' }};
        window.EDIT_ADDRESS = @json($quote->address ?? '');
        window.EDIT_PAYMENT = @json($quote->payment ?? '');
        window.TRANSPORT_PRICES = @json($transportationPrices);
        window.RENTAL_NOTE_TEMPLATE = @json($rentalNoteTemplate ?? '');
    </script>
    <script src="{{ asset('assets') }}/includes/form-unit-quotation.js?v={{ filemtime(public_path('assets/includes/form-unit-quotation.js')) }}"></script>

    <script>
        (function () {
            const inputDate   = document.getElementById('input-date');
            const expiredDate = document.getElementById('input-expired-date');

            function addOneMonth(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                const day = d.getDate();
                d.setMonth(d.getMonth() + 1);
                if (d.getDate() !== day) d.setDate(0);
                return d.toISOString().slice(0, 10);
            }

            inputDate.addEventListener('change', function () {
                expiredDate.value = addOneMonth(this.value);
            });
        })();

        // ── Auto-bullet on Note & Ketentuan Rental textarea ──
        (function () {
            const BULLET = '\u2022 ';

            function attachAutoBullet(ta) {
                if (!ta) return;

                // When user first focuses & textarea is empty, pre-fill bullet
                ta.addEventListener('focus', function () {
                    if (this.value.trim() === '') {
                        this.value = BULLET;
                        this.setSelectionRange(BULLET.length, BULLET.length);
                    }
                });

                ta.addEventListener('keydown', function (e) {
                    if (e.key !== 'Enter') return;
                    e.preventDefault();

                    const start = this.selectionStart;
                    const end   = this.selectionEnd;
                    const val   = this.value;

                    // Find the current line
                    const lineStart = val.lastIndexOf('\n', start - 1) + 1;
                    const currentLine = val.substring(lineStart, start);

                    // If current line is only a bullet (empty item), remove bullet & exit list
                    if (currentLine === BULLET || currentLine === '\u2022') {
                        this.value = val.substring(0, lineStart) + val.substring(end);
                        this.setSelectionRange(lineStart, lineStart);
                        return;
                    }

                    // Otherwise insert newline + bullet
                    const insert = '\n' + BULLET;
                    this.value = val.substring(0, start) + insert + val.substring(end);
                    const newPos = start + insert.length;
                    this.setSelectionRange(newPos, newPos);
                });

                // Ensure first line starts with bullet on blur if not empty
                ta.addEventListener('blur', function () {
                    if (this.value && !this.value.startsWith(BULLET)) {
                        this.value = BULLET + this.value;
                    }
                });

                // Auto-resize height to fit content (including existing content on load)
                function autoResize() {
                    ta.style.height = 'auto';
                    ta.style.height = Math.max(ta.scrollHeight, 80) + 'px';
                }
                ta.addEventListener('input', autoResize);
                ta.addEventListener('keydown', function () {
                    setTimeout(autoResize, 0);
                });
                autoResize(); // run immediately to fit existing content
            }

            attachAutoBullet(document.getElementById('note'));
            attachAutoBullet(document.getElementById('rental_terms'));
        })();

        $(document).on('click', '.btn-action-draft', function(e) {
            e.preventDefault();
            $('#input_is_draft').val('1');
            $('#form-unit-quotation').submit();
        });

        $(document).on('click', '.btn-action-save', function() {
            $('#input_is_draft').val('0');
        });
    </script>
@endpush
