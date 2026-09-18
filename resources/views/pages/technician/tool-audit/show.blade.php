@extends('layouts.sales.app')
@section('title', 'Form Audit Tools - ' . $audit->no_audit)
@section('hide-chat', true)
@section('content')
    <style>
        /* Mobile-First Custom Styles */
        :root {
            --audit-primary: #696cff;
            --audit-success: #28c76f;
            --audit-warning: #ff9f43;
            --audit-danger: #ea5455;
        }

        .sticky-mobile-header {
            position: sticky;
            top: 60px;
            z-index: 1020;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
        }

        .filter-pill-btn {
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 14px;
            border: 1px solid #d9dee3;
            background: #ffffff;
            color: #566a7f;
            white-space: nowrap;
            transition: all 0.2s;
        }

        .filter-pill-btn.active {
            background-color: #696cff;
            color: #ffffff !important;
            border-color: #696cff;
            box-shadow: 0 2px 8px rgba(105, 108, 255, 0.35);
        }

        /* Redesigned Tool Card */
        .tool-mobile-card {
            border-radius: 16px;
            border: 1px solid #e7ebf0;
            background: #ffffff;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .tool-mobile-card:hover {
            box-shadow: 0 6px 20px rgba(67, 89, 113, 0.08);
        }

        .tool-mobile-card.is-complete {
            border-left: 5px solid #28c76f;
        }

        .tool-mobile-card.is-incomplete {
            border-left: 5px solid #ff9f43;
        }

        .tool-mobile-card.is-problem {
            border-left: 5px solid #ea5455;
        }

        .tool-index-badge {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(105, 108, 255, 0.12);
            color: #696cff;
        }

        /* Photo Bay Styling */
        .photo-box-card {
            border: 1px solid #e7ebf0;
            border-radius: 12px;
            background: #fbfcfe;
            padding: 8px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
        }

        .photo-bay-title {
            font-size: 11px;
            font-weight: 700;
            color: #697a8d;
            margin-bottom: 4px;
            letter-spacing: 0.2px;
        }

        .photo-overlay-zoom {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
            border-radius: 8px;
        }

        .photo-box-card:hover .photo-overlay-zoom {
            opacity: 1;
        }

        .camera-icon-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #eef2ff;
            color: #696cff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s ease;
        }

        .camera-upload-box:hover .camera-icon-circle {
            transform: scale(1.1);
        }

        .camera-upload-box {
            border: 2px dashed #b8c2cc;
            border-radius: 12px;
            background: #f8faff;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 110px;
            width: 100%;
        }

        .camera-upload-box:hover {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.05);
        }

        .camera-upload-box.has-photo {
            border-style: solid;
            border-color: #28c76f;
            background: #ffffff;
            padding: 6px;
        }

        .foto-square-preview {
            width: 90px !important;
            height: 90px !important;
            max-width: 90px !important;
            max-height: 90px !important;
            aspect-ratio: 1 / 1 !important;
            object-fit: cover !important;
            border-radius: 8px !important;
            display: block !important;
            margin: 0 auto !important;
        }

        /* Touch-friendly segmented radio condition buttons */
        .kondisi-btn-group {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .kondisi-btn-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 10px 4px 8px 4px;
            border-radius: 12px;
            border: 2px solid #eaedf1;
            background: #fbfbfc;
            color: #566a7f;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            text-align: center;
            min-height: 66px;
        }

        .kondisi-btn-option:hover {
            border-color: #c4c9d2;
            background: #ffffff;
            transform: translateY(-1px);
        }

        .kondisi-icon {
            font-size: 22px;
            line-height: 1;
            margin-bottom: 3px;
        }

        .kondisi-title {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }

        .kondisi-sub {
            font-size: 9.5px;
            opacity: 0.75;
            font-weight: 500;
            margin-top: 1px;
        }

        /* Option: ADA (Green) */
        .kondisi-radio-input[value="Ada"]:checked + .kondisi-btn-option {
            border-color: #28c76f;
            background-color: rgba(40, 199, 111, 0.1);
            color: #1e9d54;
            box-shadow: 0 3px 10px rgba(40, 199, 111, 0.2);
            font-weight: 700;
        }

        /* Option: RUSAK (Orange) */
        .kondisi-radio-input[value="Rusak"]:checked + .kondisi-btn-option {
            border-color: #ff9f43;
            background-color: rgba(255, 159, 67, 0.1);
            color: #dc7a18;
            box-shadow: 0 3px 10px rgba(255, 159, 67, 0.2);
            font-weight: 700;
        }

        /* Option: HILANG (Red) */
        .kondisi-radio-input[value="Hilang"]:checked + .kondisi-btn-option {
            border-color: #ea5455;
            background-color: rgba(234, 84, 85, 0.1);
            color: #cf3334;
            box-shadow: 0 3px 10px rgba(234, 84, 85, 0.2);
            font-weight: 700;
        }

        /* Locked state when photo has not been uploaded yet */
        .kondisi-btn-group.is-locked {
            opacity: 0.45;
            position: relative;
        }

        .kondisi-btn-group.is-locked .kondisi-btn-option {
            cursor: not-allowed;
            background: #f1f3f5;
            border-color: #e2e6ea;
            color: #a1acb8;
        }

        .kondisi-btn-group.is-locked .kondisi-btn-option:hover {
            border-color: #ff9f43;
            background: #fff8f0;
            color: #ff9f43;
            transform: none;
        }

        /* Qty Stepper Buttons */
        .qty-stepper-btn {
            border: 1px solid #d9dee3;
            background: #f8f9fa;
            color: #566a7f;
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            border-radius: 8px;
            transition: all 0.15s ease;
        }

        .qty-stepper-btn:hover {
            background: #e9ecef;
            color: #32475b;
        }

        .qty-stepper-btn:active {
            transform: scale(0.95);
        }

        .sticky-bottom-actions {
            position: sticky;
            bottom: 15px;
            z-index: 1010;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .pulse-submitting {
            animation: pulse-border 1.5s infinite;
        }

        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(105, 108, 255, 0.6); }
            70% { box-shadow: 0 0 0 10px rgba(105, 108, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(105, 108, 255, 0); }
        }
    </style>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm" role="alert">
            <div class="d-flex align-items-center">
                <i class="mdi mdi-check-circle-outline fs-4 me-2"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm" role="alert">
            <div class="fw-bold mb-1"><i class="mdi mdi-alert-circle me-1"></i>Periksa Kembali Data Form:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Top Navigation & Info Header --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <a href="{{ route('tool-audit.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="mdi mdi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalPanduanAudit">
                <i class="mdi mdi-book-open-page-variant-outline me-1"></i> Panduan &amp; Ketentuan
            </button>
            @php
                $statusBadge = [
                    'Draft' => 'bg-label-secondary',
                    'Submitted' => 'bg-label-warning',
                    'Verified' => 'bg-label-success',
                    'Rejected' => 'bg-label-danger',
                ][$audit->status_submit] ?? 'bg-label-secondary';
            @endphp
            <span class="badge {{ $statusBadge }} rounded-pill px-3 py-1 font-12 fw-semibold">
                {{ $audit->status_submit === 'Submitted' ? 'Menunggu Verifikasi Admin' : $audit->status_submit }}
            </span>
        </div>
    </div>

    {{-- Audit Header Card --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        Form Self-Audit Tools: <span class="text-primary">{{ $audit->no_audit }}</span>
                    </h5>
                    <p class="text-muted small mb-0">
                        Periode <strong>{{ $audit->period->period_title }}</strong> &bull;
                        Batas Akhir: <strong class="text-danger">{{ \Carbon\Carbon::parse($audit->period->tanggal_selesai)->translatedFormat('d M Y') }}</strong>
                    </p>
                </div>
                @if ($editable)
                    <div class="d-flex align-items-center gap-2">
                        <div id="globalAutoSaveStatus" class="badge bg-label-success px-3 py-2 rounded-pill font-11 shadow-xs" style="display: inline-flex; align-items: center; gap: 4px;">
                            <i class="mdi mdi-cloud-check fs-6 text-success"></i> <span>Otomatis Tersimpan ke Draft</span>
                        </div>
                    </div>
                @endif
            </div>

            @if ($audit->status_submit === 'Rejected' && $audit->catatan_admin)
                <div class="alert alert-danger mt-3 mb-0 py-2 px-3 rounded" role="alert">
                    <div class="d-flex align-items-start gap-2">
                        <i class="mdi mdi-alert-circle-outline fs-5 mt-1 text-danger"></i>
                        <div>
                            <strong class="font-13">Catatan Admin (Perlu Revisi):</strong>
                            <div class="font-12 mt-1">{{ $audit->catatan_admin }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- STICKY MOBILE CONTROLLER BAR (Filter, Live Counter & Search) --}}
    <div class="sticky-mobile-header p-2 p-md-3 mb-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            
            {{-- Horizontal Scrolling Pill Filters for Mobile --}}
            <div class="d-flex align-items-center gap-1 overflow-auto pb-1 pb-md-0" id="filterContainer" style="scrollbar-width: none;">
                <button type="button" class="filter-pill-btn active" data-filter="all">
                    Semua (<span id="countAll">{{ $audit->items->count() }}</span>)
                </button>
                <button type="button" class="filter-pill-btn" data-filter="incomplete">
                    ⏳ Belum Lengkap (<span id="countIncomplete">0</span>)
                </button>
                <button type="button" class="filter-pill-btn" data-filter="complete">
                    ✓ Lengkap (<span id="countComplete">0</span>)
                </button>
                <button type="button" class="filter-pill-btn" data-filter="problem">
                    ⚠️ Rusak / Hilang (<span id="countProblem">0</span>)
                </button>
            </div>

            {{-- Live Search Input --}}
            <div class="position-relative" style="min-width: 200px;">
                <i class="mdi mdi-magnify position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                <input type="text" id="toolSearchInput" class="form-control form-control-sm ps-5 rounded-pill" placeholder="Cari nama / kode alat...">
            </div>
        </div>

        {{-- Mobile Progress Bar --}}
        <div class="d-flex align-items-center gap-2 mt-2 pt-1 border-top">
            <div class="progress flex-grow-1" style="height: 6px; border-radius: 4px; background: rgba(0,0,0,0.06);">
                <div id="mobileProgressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
            </div>
            <span class="small fw-bold text-dark font-11 text-nowrap" id="mobileProgressText">
                0 / {{ $audit->items->count() }} Lengkap (0%)
            </span>
        </div>
    </div>

    {{-- Form Submit Container --}}
    <form id="formAuditSubmit" action="{{ route('tool-audit.submit', $audit->id) }}" method="post" enctype="multipart/form-data">
        @csrf

        {{-- Tool Cards List --}}
        <div class="row g-3 mb-4" id="toolCardsList">
            @foreach ($audit->items as $index => $item)
                @php
                    $tool = $item->fixedAsset;
                    $master = $tool->toolsMaster ?? null;
                    $kondisi = old("items.{$item->id}.kondisi", $item->kondisi);
                    $hasFoto = !empty($item->foto_audit);
                    $isComplete = !empty($kondisi) && $hasFoto;
                    $isProblem = in_array($kondisi, ['Rusak', 'Hilang']);
                    $fotoAwal = $tool && $tool->foto_awal ? asset($tool->foto_awal) : null;
                    $fotoAudit = $item->foto_audit ? asset($item->foto_audit) : null;
                    $toolName = $master->nama_tools ?? ($tool->desc ?? 'Alat Kerja');
                    $toolSearchData = strtolower($toolName . ' ' . ($tool->code ?? '') . ' ' . ($tool->serial_number ?? ''));
                @endphp

                <div class="col-12 tool-card-item"
                    id="tool-card-{{ $item->id }}"
                    data-item-id="{{ $item->id }}"
                    data-is-complete="{{ $isComplete ? '1' : '0' }}"
                    data-is-problem="{{ $isProblem ? '1' : '0' }}"
                    data-search="{{ $toolSearchData }}">
                    
                    <div class="card tool-mobile-card shadow-sm p-3 p-md-4 {{ $isComplete ? 'is-complete' : 'is-incomplete' }} {{ $isProblem ? 'is-problem' : '' }}">
                        
                        {{-- Card Header: Number, Name, Meta Tags, Status Badge --}}
                        <div class="d-flex align-items-start justify-content-between gap-2 mb-3 pb-2 border-bottom">
                            <div class="d-flex align-items-start gap-2 min-w-0">
                                <span class="tool-index-badge flex-shrink-0 mt-1">
                                    {{ $index + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <h6 class="fw-bold text-dark mb-1 font-15 text-truncate" title="{{ $toolName }}">
                                        {{ $toolName }}
                                    </h6>
                                    <div class="d-flex flex-wrap align-items-center gap-1">
                                        <span class="badge bg-label-secondary font-11 py-1 px-2">
                                            <i class="mdi mdi-barcode me-1"></i>Kode: <strong>{{ $tool->code ?? '-' }}</strong>
                                        </span>
                                        @if ($tool->serial_number)
                                            <span class="badge bg-label-info font-11 py-1 px-2">
                                                <i class="mdi mdi-pound me-1"></i>SN: <code>{{ $tool->serial_number }}</code>
                                            </span>
                                        @endif
                                        <span class="badge bg-label-primary font-11 py-1 px-2">
                                            <i class="mdi mdi-cube-outline me-1"></i>Qty Baseline: <strong>{{ $tool->qty }}</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <span class="card-status-badge badge {{ $isComplete ? 'bg-label-success' : 'bg-label-warning' }} rounded-pill font-11 py-1 px-2">
                                    <i class="mdi {{ $isComplete ? 'mdi-check-circle' : 'mdi-clock-outline' }} me-1"></i>
                                    <span class="badge-text">{{ $isComplete ? 'Lengkap' : 'Belum Lengkap' }}</span>
                                </span>
                            </div>
                        </div>

                        {{-- Card Body: Two-column grid (Photo Bay vs Condition & Fields) --}}
                        <div class="row g-3 align-items-start">
                            
                            {{-- Column 1: Photo Bay (Foto Baseline vs Foto Audit) --}}
                            <div class="col-12 col-md-5 col-lg-4">
                                <div class="row g-2">
                                    {{-- Foto Baseline (Serah Terima) --}}
                                    @if ($fotoAwal)
                                        <div class="col-6">
                                            <div class="photo-box-card">
                                                <div class="photo-bay-title">
                                                    <i class="mdi mdi-history me-1"></i>Foto Awal
                                                </div>
                                                <div class="position-relative overflow-hidden rounded my-1 w-100" style="cursor: pointer;" onclick="previewPhoto('{{ $fotoAwal }}', 'Foto Awal: {{ addslashes($toolName) }}')">
                                                    <img src="{{ $fotoAwal }}" alt="Foto Awal" class="foto-square-preview" loading="lazy" decoding="async">
                                                    <div class="photo-overlay-zoom">
                                                        <i class="mdi mdi-magnify-plus text-white fs-5"></i>
                                                    </div>
                                                </div>
                                                <div class="font-10 text-muted">Baseline</div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Foto Audit Upload / Snap --}}
                                    <div class="{{ $fotoAwal ? 'col-6' : 'col-12' }}">
                                        <div class="foto-upload-wrapper h-100" data-item-id="{{ $item->id }}">
                                            <div class="photo-box-card {{ $fotoAudit ? 'border-success' : '' }}">
                                                <div class="photo-bay-title {{ $fotoAudit ? 'text-success fw-bold' : 'text-primary' }}">
                                                    <i class="mdi {{ $fotoAudit ? 'mdi-check-decagram' : 'mdi-camera' }} me-1"></i>Foto Sekarang
                                                </div>

                                                <div class="camera-upload-box {{ $fotoAudit ? 'has-photo' : '' }} my-1" onclick="triggerCameraInput('{{ $item->id }}')">
                                                    <div class="foto-preview-container w-100">
                                                        @if ($fotoAudit)
                                                            <div class="position-relative d-inline-block">
                                                                <img src="{{ $fotoAudit }}" alt="Foto Audit" class="foto-preview-img foto-square-preview" loading="lazy" decoding="async">
                                                            </div>
                                                            <div class="small text-success font-10 mt-1 fw-semibold">
                                                                <i class="mdi mdi-camera-retake me-1"></i>Ganti Foto
                                                            </div>
                                                        @else
                                                            <div class="py-2 text-center">
                                                                <div class="camera-icon-circle mb-1">
                                                                    <i class="mdi mdi-camera-plus fs-3"></i>
                                                                </div>
                                                                <div class="font-11 fw-bold text-dark">Ambil / Upload</div>
                                                                <div class="text-danger font-10 fw-semibold">*Wajib Foto Fisik</div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="upload-status-text text-primary small font-10" style="display: none;"></div>

                                                @if ($fotoAudit)
                                                    <a href="javascript:void(0);" class="font-10 text-primary text-decoration-none mt-1" onclick="previewPhoto('{{ $fotoAudit }}', 'Foto Audit: {{ addslashes($toolName) }}')">
                                                        <i class="mdi mdi-eye-outline me-1"></i>Lihat Penuh
                                                    </a>
                                                @else
                                                    <div class="font-10 text-muted mt-1">Square 1:1</div>
                                                @endif

                                                @if ($editable)
                                                    <input type="file"
                                                        id="file-input-{{ $item->id }}"
                                                        class="d-none foto-audit-input"
                                                        accept="image/*"
                                                        capture="environment"
                                                        name="items[{{ $item->id }}][foto_audit]">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Column 2: Condition Radios & Quantitative Details --}}
                            <div class="col-12 col-md-7 col-lg-8">
                                @if ($editable)
                                    {{-- Lock hint if photo not uploaded --}}
                                    <div class="photo-lock-hint-{{ $item->id }} alert alert-warning py-1 px-2 font-11 mb-2 d-flex align-items-center rounded-3 border-0 bg-warning bg-opacity-10 text-warning" style="{{ $fotoAudit ? 'display: none !important;' : '' }}">
                                        <i class="mdi mdi-lock-outline fs-6 me-1"></i>
                                        <span>Ambil / upload <strong>Foto Fisik</strong> terlebih dahulu untuk memilih kondisi alat.</span>
                                    </div>

                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <label class="form-label font-12 fw-bold text-dark mb-0">
                                            Pilih Kondisi Fisik Alat:
                                        </label>
                                    </div>
                                    
                                    {{-- Modern Segmented Condition Grid --}}
                                    <div class="kondisi-btn-group mb-3 {{ !$fotoAudit ? 'is-locked' : '' }}" id="kondisi-group-{{ $item->id }}">
                                        {{-- ADA --}}
                                        <div>
                                            <input type="radio" class="d-none kondisi-radio-input"
                                                name="items[{{ $item->id }}][kondisi]"
                                                id="cond-ada-{{ $item->id }}"
                                                value="Ada"
                                                data-item-id="{{ $item->id }}"
                                                {{ $kondisi == 'Ada' ? 'checked' : '' }}
                                                {{ !$fotoAudit ? 'disabled' : '' }}>
                                            <label class="kondisi-btn-option" for="cond-ada-{{ $item->id }}" onclick="handleLockedConditionClick(event, '{{ $item->id }}')">
                                                <i class="mdi mdi-check-circle-outline kondisi-icon"></i>
                                                <span class="kondisi-title">ADA</span>
                                                <span class="kondisi-sub">Normal / Baik</span>
                                            </label>
                                        </div>

                                        {{-- RUSAK --}}
                                        <div>
                                            <input type="radio" class="d-none kondisi-radio-input"
                                                name="items[{{ $item->id }}][kondisi]"
                                                id="cond-rusak-{{ $item->id }}"
                                                value="Rusak"
                                                data-item-id="{{ $item->id }}"
                                                {{ $kondisi == 'Rusak' ? 'checked' : '' }}
                                                {{ !$fotoAudit ? 'disabled' : '' }}>
                                            <label class="kondisi-btn-option" for="cond-rusak-{{ $item->id }}" onclick="handleLockedConditionClick(event, '{{ $item->id }}')">
                                                <i class="mdi mdi-alert-circle-outline kondisi-icon"></i>
                                                <span class="kondisi-title">RUSAK</span>
                                                <span class="kondisi-sub">Kendala Fisik</span>
                                            </label>
                                        </div>

                                        {{-- HILANG --}}
                                        <div>
                                            <input type="radio" class="d-none kondisi-radio-input"
                                                name="items[{{ $item->id }}][kondisi]"
                                                id="cond-hilang-{{ $item->id }}"
                                                value="Hilang"
                                                data-item-id="{{ $item->id }}"
                                                {{ $kondisi == 'Hilang' ? 'checked' : '' }}
                                                {{ !$fotoAudit ? 'disabled' : '' }}>
                                            <label class="kondisi-btn-option" for="cond-hilang-{{ $item->id }}" onclick="handleLockedConditionClick(event, '{{ $item->id }}')">
                                                <i class="mdi mdi-close-circle-outline kondisi-icon"></i>
                                                <span class="kondisi-title">HILANG</span>
                                                <span class="kondisi-sub">Tidak Ada</span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Row: Qty Stepper & Info --}}
                                    <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border mb-2 flex-wrap gap-2">
                                        <div>
                                            <label class="form-label font-12 fw-semibold text-dark mb-0">
                                                <i class="mdi mdi-counter me-1 text-primary"></i>Jumlah Fisik Sekarang:
                                            </label>
                                            <div class="font-10 text-muted">Sesuaikan jika ada selisih dari sistem</div>
                                        </div>
                                        <div class="d-flex align-items-center gap-1">
                                            <button class="qty-stepper-btn" type="button" onclick="adjustQty('{{ $item->id }}', -1)" title="Kurang">
                                                <i class="mdi mdi-minus"></i>
                                            </button>
                                            <input type="number" class="form-control form-control-sm qty-actual-input text-center fw-bold bg-white"
                                                style="width: 55px; height: 34px; border-radius: 8px;"
                                                name="items[{{ $item->id }}][qty_actual]"
                                                data-item-id="{{ $item->id }}"
                                                value="{{ old("items.{$item->id}.qty_actual", $item->qty_actual ?? $tool->qty) }}"
                                                min="0" required>
                                            <button class="qty-stepper-btn" type="button" onclick="adjustQty('{{ $item->id }}', 1)" title="Tambah">
                                                <i class="mdi mdi-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Conditional: Keterangan Rusak --}}
                                    <div class="conditional-alasan-box p-3 rounded-3 border mb-2" id="alasan-box-{{ $item->id }}"
                                        style="display: {{ in_array($kondisi, ['Rusak', 'Hilang']) ? 'block' : 'none' }}; background: #fffbf6; border-color: rgba(255, 159, 67, 0.35) !important;">
                                        <label class="form-label font-11 fw-bold text-warning mb-1 d-flex align-items-center">
                                            <i class="mdi mdi-comment-alert-outline me-1"></i>
                                            <span class="alasan-label-text">{{ $kondisi == 'Hilang' ? 'Kronologi / Catatan Hilang (Opsional):' : 'Penjelasan Bagian / Kerusakan Alat (Wajib):' }}</span>
                                        </label>
                                        <textarea class="form-control form-control-sm alasan-input bg-white"
                                            rows="2"
                                            data-item-id="{{ $item->id }}"
                                            name="items[{{ $item->id }}][alasan]"
                                            placeholder="{{ $kondisi == 'Hilang' ? 'Tuliskan catatan kejadian...' : 'Contoh: Kepala obeng patah saat pemakaian...' }}">{{ old("items.{$item->id}.alasan", $item->alasan) }}</textarea>
                                    </div>

                                    {{-- Conditional: Metode Ganti Hilang --}}
                                    <div class="conditional-metode-box p-3 rounded-3 border mb-2" id="metode-box-{{ $item->id }}"
                                        style="display: {{ $kondisi == 'Hilang' ? 'block' : 'none' }}; background: #fff5f5; border-color: rgba(234, 84, 85, 0.35) !important;">
                                        <label class="form-label font-11 fw-bold text-danger mb-1 d-flex align-items-center">
                                            <i class="mdi mdi-cash-refund me-1"></i>Metode Pertanggungjawaban (Wajib):
                                        </label>
                                        <select class="form-select form-select-sm metode-ganti-select bg-white"
                                            data-item-id="{{ $item->id }}"
                                            name="items[{{ $item->id }}][metode_ganti]">
                                            <option value="">-- Pilih Metode Pertanggungjawaban --</option>
                                            <option value="Beli Sendiri" {{ old("items.{$item->id}.metode_ganti", $item->metode_ganti) == 'Beli Sendiri' ? 'selected' : '' }}>
                                                Beli Sendiri (Spesifikasi & Tipe Sama)
                                            </option>
                                            <option value="Potong Bonus" {{ old("items.{$item->id}.metode_ganti", $item->metode_ganti) == 'Potong Bonus' ? 'selected' : '' }}>
                                                Potong Bonus / Potong Gaji
                                            </option>
                                        </select>
                                    </div>
                                @else
                                    {{-- Readonly view for submitted/verified audit --}}
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <span class="font-12 text-muted fw-semibold">Kondisi Fisik:</span>
                                            <span class="badge {{ ['Ada' => 'bg-success', 'Rusak' => 'bg-warning', 'Hilang' => 'bg-danger'][$item->kondisi] ?? 'bg-secondary' }} px-2 py-1">
                                                {{ $item->kondisi ?? 'Belum ditentukan' }}
                                            </span>
                                            <span class="font-12 text-muted ms-auto">Qty Fisik: <strong>{{ $item->qty_actual ?? $tool->qty }}</strong></span>
                                        </div>
                                        @if ($item->alasan)
                                            <div class="font-11 text-dark mt-2 p-2 bg-white rounded border">
                                                <i class="mdi mdi-comment-outline me-1 text-muted"></i><strong>Catatan:</strong> {{ $item->alasan }}
                                            </div>
                                        @endif
                                        @if ($item->metode_ganti)
                                            <div class="font-11 text-danger fw-semibold mt-2 p-2 bg-white rounded border">
                                                <i class="mdi mdi-cash me-1"></i><strong>Metode Ganti:</strong> {{ $item->metode_ganti }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        {{-- STICKY BOTTOM ACTION BAR --}}
        @if ($editable)
            <div class="sticky-bottom-actions p-3 shadow-lg mb-4">
                <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2 w-100 w-sm-auto justify-content-between justify-content-sm-start">
                        <div>
                            <div class="fw-bold font-13 text-dark" id="bottomSummaryText">0 dari {{ $audit->items->count() }} Tools Lengkap</div>
                            <div class="small text-success font-11 fw-semibold">
                                <i class="mdi mdi-cloud-check me-1"></i>Setiap pilihan &amp; foto otomatis tersimpan ke draft
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 w-100 w-sm-auto">
                        <a href="{{ route('tool-audit.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 flex-grow-1 flex-sm-grow-0" title="Keluar dari form. Seluruh progres Anda sudah tersimpan otomatis ke server.">
                            <i class="mdi mdi-arrow-left me-1"></i> Keluar / Lanjut Nanti
                        </a>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 flex-grow-1 flex-sm-grow-0 shadow-sm" id="btnCheckAndSubmit">
                            <i class="mdi mdi-send-check-outline me-1"></i> Submit Audit Tools
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </form>

    {{-- Lightbox Photo Modal --}}
    <div class="modal fade" id="photoPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom py-2 px-3">
                    <h6 class="modal-title fw-bold" id="photoPreviewTitle">Pratinjau Foto</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-2 bg-light">
                    <img id="photoPreviewImg" src="" alt="Preview" class="img-fluid rounded border shadow-sm" style="max-height: 480px; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>

    {{-- Validation Incomplete Checklist Modal --}}
    <div class="modal fade" id="incompleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom py-2 px-3 bg-label-warning">
                    <h6 class="modal-title fw-bold text-warning mb-0">
                        <i class="mdi mdi-alert-circle me-1"></i> Masih Ada Tools yang Belum Lengkap
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <p class="small text-muted mb-2">
                        Sebelum melakukan submit audit tools, pastikan seluruh <strong>kondisi fisik</strong> dan <strong>foto terbaru</strong> dari masing-masing alat sudah terisi:
                    </p>
                    <div id="incompleteListContainer" class="list-group list-group-flush border rounded mb-3" style="max-height: 240px; overflow-y: auto;">
                        {{-- Incomplete items will be appended here dynamically --}}
                    </div>
                    <div class="alert alert-info py-2 px-3 small font-11 mb-0">
                        <i class="mdi mdi-lightbulb-on me-1"></i> Progres pengisian Anda sudah otomatis tersimpan sebagai <strong>Draft</strong> jika ingin melanjutkannya nanti.
                    </div>
                </div>
                <div class="modal-footer border-top py-2 px-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-sm btn-primary" id="btnFocusIncomplete" data-bs-dismiss="modal">
                        <i class="mdi mdi-filter-outline me-1"></i> Tampilkan Tools Belum Lengkap
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Submit Modal --}}
    <div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-bottom py-2 px-3 bg-label-primary">
                    <h6 class="modal-title fw-bold text-primary mb-0">
                        <i class="mdi mdi-send-check-outline me-1"></i> Konfirmasi Submit Audit Tools
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 text-center">
                    <div class="avatar avatar-lg mx-auto mb-2 bg-label-success rounded-circle d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                        <i class="mdi mdi-check-all fs-2 text-success"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Seluruh Tools Sudah Lengkap!</h6>
                    <p class="small text-muted mb-0">
                        Apakah Anda yakin ingin melakukan submit laporan self-audit tools ini untuk proses verifikasi oleh Admin Warehouse?
                    </p>
                </div>
                <div class="modal-footer border-top py-2 px-3 justify-content-center">
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">Periksa Lagi</button>
                    <button type="button" class="btn btn-sm btn-primary px-4 shadow-sm" id="btnFinalSubmitForm">
                        <i class="mdi mdi-check-decagram me-1"></i> Ya, Submit Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PANDUAN & KETENTUAN AUDIT TOOLS --}}
    @include('components.modal-panduan-tool-audit')
@endsection

@push('page-script')
    <script>
        const csrfToken = '{{ csrf_token() }}';
        const globalStatus = document.getElementById('globalAutoSaveStatus');

        function setAutoSaveStatus(state, text) {
            if (!globalStatus) return;
            if (state === 'saving') {
                globalStatus.className = 'badge bg-label-warning px-3 py-2 rounded-pill font-11 shadow-xs';
                globalStatus.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan ke draft...';
            } else if (state === 'saved') {
                const now = new Date();
                const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replace(/\./g, ':');
                globalStatus.className = 'badge bg-label-success px-3 py-2 rounded-pill font-11 shadow-xs';
                globalStatus.innerHTML = '<i class="mdi mdi-cloud-check fs-6 text-success me-1"></i> ' + (text || `Otomatis Tersimpan (${timeStr})`);
            } else if (state === 'error') {
                globalStatus.className = 'badge bg-label-danger px-3 py-2 rounded-pill font-11 shadow-xs';
                globalStatus.innerHTML = '<i class="mdi mdi-alert-circle text-danger me-1"></i> Gagal simpan otomatis';
            }
        }

        // Trigger native file/camera input when camera upload box is tapped
        function triggerCameraInput(itemId) {
            const input = document.getElementById('file-input-' + itemId);
            if (input) {
                input.click();
            }
        }

        // Handle tap/click on locked condition options when photo is not yet uploaded
        function handleLockedConditionClick(event, itemId) {
            const kGroup = document.getElementById('kondisi-group-' + itemId);
            if (kGroup && kGroup.classList.contains('is-locked')) {
                event.preventDefault();
                event.stopPropagation();

                const card = document.getElementById('tool-card-' + itemId);
                const camBox = card ? card.querySelector('.camera-upload-box') : null;
                if (camBox) {
                    camBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    camBox.style.borderColor = '#ff9f43';
                    camBox.style.backgroundColor = 'rgba(255, 159, 67, 0.15)';
                    setTimeout(() => {
                        camBox.style.borderColor = '';
                        camBox.style.backgroundColor = '';
                    }, 1400);

                    triggerCameraInput(itemId);
                }
            }
        }

        // Real-time calculation of complete / incomplete cards
        function updateProgressStats() {
            const allCards = document.querySelectorAll('.tool-card-item');
            const total = allCards.length;
            let completeCount = 0;
            let problemCount = 0;

            allCards.forEach(card => {
                const itemId = card.getAttribute('data-item-id');
                const kondisiChecked = card.querySelector('.kondisi-radio-input:checked');
                const hasPhoto = card.querySelector('.camera-upload-box.has-photo') !== null;
                const isComplete = (kondisiChecked !== null && hasPhoto);
                const isProblem = (kondisiChecked && (kondisiChecked.value === 'Rusak' || kondisiChecked.value === 'Hilang'));

                card.setAttribute('data-is-complete', isComplete ? '1' : '0');
                card.setAttribute('data-is-problem', isProblem ? '1' : '0');

                // Update card visual borders & badge
                const cardInner = card.querySelector('.tool-mobile-card');
                const statusBadge = card.querySelector('.card-status-badge');
                const badgeText = card.querySelector('.badge-text');
                const badgeIcon = statusBadge ? statusBadge.querySelector('i') : null;

                if (isComplete) {
                    completeCount++;
                    if (cardInner) {
                        cardInner.classList.remove('is-incomplete');
                        cardInner.classList.add('is-complete');
                        if (isProblem) cardInner.classList.add('is-problem'); else cardInner.classList.remove('is-problem');
                    }
                    if (statusBadge) {
                        statusBadge.className = 'card-status-badge badge bg-label-success rounded-pill font-11';
                        if (badgeText) badgeText.innerText = 'Lengkap';
                        if (badgeIcon) badgeIcon.className = 'mdi mdi-check-circle me-1';
                    }
                } else {
                    if (cardInner) {
                        cardInner.classList.remove('is-complete');
                        cardInner.classList.add('is-incomplete');
                    }
                    if (statusBadge) {
                        statusBadge.className = 'card-status-badge badge bg-label-warning rounded-pill font-11';
                        if (badgeText) badgeText.innerText = 'Belum Lengkap';
                        if (badgeIcon) badgeIcon.className = 'mdi mdi-clock-outline me-1';
                    }
                }

                if (isProblem) problemCount++;
            });

            const incompleteCount = total - completeCount;
            const percent = total > 0 ? Math.round((completeCount / total) * 100) : 0;

            // Update pill filter counters
            document.getElementById('countAll').innerText = total;
            document.getElementById('countComplete').innerText = completeCount;
            document.getElementById('countIncomplete').innerText = incompleteCount;
            document.getElementById('countProblem').innerText = problemCount;

            // Update progress bar
            const pBar = document.getElementById('mobileProgressBar');
            if (pBar) pBar.style.width = percent + '%';

            const pText = document.getElementById('mobileProgressText');
            if (pText) pText.innerText = `${completeCount} / ${total} Lengkap (${percent}%)`;

            const bSummary = document.getElementById('bottomSummaryText');
            if (bSummary) bSummary.innerText = `${completeCount} dari ${total} Tools Lengkap (${percent}%)`;
        }

        // Apply tab filtering and live search
        function applyCardFilters() {
            const activeFilter = document.querySelector('.filter-pill-btn.active')?.getAttribute('data-filter') || 'all';
            const searchQuery = (document.getElementById('toolSearchInput')?.value || '').trim().toLowerCase();
            const allCards = document.querySelectorAll('.tool-card-item');

            allCards.forEach(card => {
                const isComplete = card.getAttribute('data-is-complete') === '1';
                const isProblem = card.getAttribute('data-is-problem') === '1';
                const searchData = card.getAttribute('data-search') || '';

                let matchesFilter = true;
                if (activeFilter === 'complete') matchesFilter = isComplete;
                else if (activeFilter === 'incomplete') matchesFilter = !isComplete;
                else if (activeFilter === 'problem') matchesFilter = isProblem;

                const matchesSearch = !searchQuery || searchData.includes(searchQuery);

                card.style.display = (matchesFilter && matchesSearch) ? '' : 'none';
            });
        }

        // Auto Save function
        let autoSaveTimeouts = {};
        function triggerItemAutoSave(itemId) {
            setAutoSaveStatus('saving');
            clearTimeout(autoSaveTimeouts[itemId]);

            autoSaveTimeouts[itemId] = setTimeout(function () {
                const card = document.getElementById('tool-card-' + itemId);
                if (!card) return;

                const qtyInput = card.querySelector('.qty-actual-input');
                const kondisiRadio = card.querySelector('.kondisi-radio-input:checked');
                const alasanInput = card.querySelector('.alasan-input');
                const metodeSelect = card.querySelector('.metode-ganti-select');

                const payload = {
                    _token: csrfToken,
                    qty_actual: qtyInput ? qtyInput.value : 0,
                    kondisi: kondisiRadio ? kondisiRadio.value : null,
                    alasan: alasanInput ? alasanInput.value : null,
                    metode_ganti: metodeSelect ? metodeSelect.value : null,
                };

                fetch(`/tool-audit/item/${itemId}/auto-save`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        setAutoSaveStatus('saved');
                        updateProgressStats();
                    } else {
                        setAutoSaveStatus('error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    setAutoSaveStatus('error');
                });
            }, 500);
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateProgressStats();

            // Filter button listeners
            document.querySelectorAll('.filter-pill-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    applyCardFilters();
                });
            });

            // Search input listener
            const searchInput = document.getElementById('toolSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', applyCardFilters);
            }

            // Radio condition listeners
            document.querySelectorAll('.kondisi-radio-input').forEach(radio => {
                radio.addEventListener('change', function () {
                    const itemId = this.getAttribute('data-item-id');
                    const val = this.value;
                    const alasanBox = document.getElementById('alasan-box-' + itemId);
                    const metodeBox = document.getElementById('metode-box-' + itemId);

                    if (alasanBox) {
                        alasanBox.style.display = (val === 'Rusak' || val === 'Hilang') ? 'block' : 'none';
                        const label = alasanBox.querySelector('.alasan-label-text');
                        const textarea = alasanBox.querySelector('textarea');
                        if (val === 'Hilang') {
                            if (label) label.innerText = 'Kronologi / Catatan Hilang (Opsional):';
                            if (textarea) textarea.placeholder = 'Tuliskan catatan kejadian...';
                        } else {
                            if (label) label.innerText = 'Penjelasan Bagian / Kerusakan Alat (Wajib):';
                            if (textarea) textarea.placeholder = 'Contoh: Kepala obeng patah saat pemakaian...';
                        }
                    }

                    if (metodeBox) {
                        metodeBox.style.display = (val === 'Hilang') ? 'block' : 'none';
                    }

                    updateProgressStats();
                    triggerItemAutoSave(itemId);
                });
            });

            // Inputs change listeners
            document.querySelectorAll('.qty-actual-input, .alasan-input').forEach(input => {
                input.addEventListener('input', function () {
                    triggerItemAutoSave(this.getAttribute('data-item-id'));
                });
            });

            document.querySelectorAll('.metode-ganti-select').forEach(select => {
                select.addEventListener('change', function () {
                    triggerItemAutoSave(this.getAttribute('data-item-id'));
                });
            });

            // AJAX Photo Upload
            document.querySelectorAll('.foto-audit-input').forEach(input => {
                input.addEventListener('change', function () {
                    const file = this.files[0];
                    if (!file) return;

                    const wrapper = this.closest('.foto-upload-wrapper');
                    const itemId = wrapper.getAttribute('data-item-id');
                    const uploadBox = wrapper.querySelector('.camera-upload-box');
                    const previewContainer = wrapper.querySelector('.foto-preview-container');
                    const statusText = wrapper.querySelector('.upload-status-text');
                    const fileInput = this;

                    // Disable input during upload
                    fileInput.disabled = true;
                    statusText.style.display = 'block';
                    statusText.innerHTML = '<span class="spinner-border spinner-border-sm text-primary"></span> Mengunggah foto...';
                    setAutoSaveStatus('saving');

                    const formData = new FormData();
                    formData.append('foto_audit', file);
                    formData.append('_token', csrfToken);

                    fetch(`/tool-audit/item/${itemId}/upload-photo`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => { throw err; });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            previewContainer.innerHTML = `
                                <img src="${data.foto_url}" alt="Foto Audit" class="foto-preview-img foto-square-preview">
                                <div class="small text-success font-10 mt-1 fw-semibold">
                                    <i class="mdi mdi-camera-retake me-1"></i>Ganti Foto
                                </div>
                            `;
                            uploadBox.classList.add('has-photo');
                            statusText.innerHTML = '<span class="text-success"><i class="mdi mdi-check-circle"></i> Foto tersimpan</span>';
                            fileInput.value = '';

                            // Unlock condition options for this tool
                            const kGroup = document.getElementById('kondisi-group-' + itemId);
                            if (kGroup) {
                                kGroup.classList.remove('is-locked');
                                kGroup.querySelectorAll('.kondisi-radio-input').forEach(r => {
                                    r.disabled = false;
                                });
                            }
                            const lockHint = document.querySelector('.photo-lock-hint-' + itemId);
                            if (lockHint) {
                                lockHint.style.setProperty('display', 'none', 'important');
                            }

                            setAutoSaveStatus('saved');
                            updateProgressStats();
                        } else {
                            throw new Error('Gagal mengunggah foto.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        let msg = 'Gagal mengunggah foto.';
                        if (err.errors && err.errors.foto_audit) {
                            msg = err.errors.foto_audit.join(', ');
                        } else if (err.message) {
                            msg = err.message;
                        }
                        alert(msg);
                        statusText.innerHTML = `<span class="text-danger"><i class="mdi mdi-alert-circle"></i> ${msg}</span>`;
                        setAutoSaveStatus('error');
                    })
                    .finally(() => {
                        fileInput.disabled = false;
                    });
                });
            });

            // Manual Save Draft button
            const btnDraft = document.getElementById('btnManualDraft');
            if (btnDraft) {
                btnDraft.addEventListener('click', function () {
                    const form = document.getElementById('formAuditSubmit');
                    if (!form) return;
                    form.action = '{{ route("tool-audit.save-draft", $audit->id) }}';
                    form.submit();
                });
            }

            // Check and Submit Flow
            const btnCheckSubmit = document.getElementById('btnCheckAndSubmit');
            if (btnCheckSubmit) {
                btnCheckSubmit.addEventListener('click', function () {
                    const allCards = document.querySelectorAll('.tool-card-item');
                    const incompleteItems = [];

                    allCards.forEach((card, idx) => {
                        const itemId = card.getAttribute('data-item-id');
                        const toolName = card.querySelector('h6')?.innerText || `Tools #${idx+1}`;
                        const kondisiChecked = card.querySelector('.kondisi-radio-input:checked');
                        const hasPhoto = card.querySelector('.camera-upload-box.has-photo') !== null;

                        const missing = [];
                        if (!kondisiChecked) missing.push('Kondisi belum dipilih');
                        if (!hasPhoto) missing.push('Foto fisik belum diupload');
                        if (kondisiChecked && kondisiChecked.value === 'Rusak') {
                            const alasan = card.querySelector('.alasan-input')?.value.trim();
                            if (!alasan) missing.push('Penjelasan kerusakan belum diisi');
                        }
                        if (kondisiChecked && kondisiChecked.value === 'Hilang') {
                            const metode = card.querySelector('.metode-ganti-select')?.value;
                            if (!metode) missing.push('Metode ganti belum dipilih');
                        }

                        if (missing.length > 0) {
                            incompleteItems.push({
                                id: itemId,
                                name: toolName,
                                reasons: missing
                            });
                        }
                    });

                    if (incompleteItems.length > 0) {
                        const container = document.getElementById('incompleteListContainer');
                        container.innerHTML = '';
                        incompleteItems.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'list-group-item list-group-item-action py-2 px-3';
                            div.innerHTML = `
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="font-12 text-dark">${item.name}</strong>
                                    <span class="badge bg-label-danger font-10">${item.reasons.join(', ')}</span>
                                </div>
                            `;
                            div.onclick = function () {
                                const modalEl = document.getElementById('incompleteModal');
                                const modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();
                                
                                const targetCard = document.getElementById('tool-card-' + item.id);
                                if (targetCard) {
                                    targetCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                    targetCard.classList.add('pulse-submitting');
                                    setTimeout(() => targetCard.classList.remove('pulse-submitting'), 2000);
                                }
                            };
                            container.appendChild(div);
                        });

                        const modal = new bootstrap.Modal(document.getElementById('incompleteModal'));
                        modal.show();
                    } else {
                        // All complete! Show confirmation modal
                        const modal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
                        modal.show();
                    }
                });
            }

            // Focus Incomplete Button in modal
            const btnFocusInc = document.getElementById('btnFocusIncomplete');
            if (btnFocusInc) {
                btnFocusInc.addEventListener('click', function () {
                    const incPill = document.querySelector('.filter-pill-btn[data-filter="incomplete"]');
                    if (incPill) incPill.click();
                    window.scrollTo({ top: 120, behavior: 'smooth' });
                });
            }

            // Final Submit Button inside Confirmation Modal
            const btnFinalSubmit = document.getElementById('btnFinalSubmitForm');
            if (btnFinalSubmit) {
                btnFinalSubmit.addEventListener('click', function () {
                    const form = document.getElementById('formAuditSubmit');
                    form.action = '{{ route("tool-audit.submit", $audit->id) }}';
                    form.submit();
                });
            }
        });

        function adjustQty(itemId, delta) {
            const card = document.getElementById('tool-card-' + itemId);
            if (!card) return;
            const input = card.querySelector('.qty-actual-input');
            if (!input || input.disabled) return;
            let val = parseInt(input.value) || 0;
            val = Math.max(0, val + delta);
            input.value = val;
            input.dispatchEvent(new Event('input', { bubbles: true }));
        }

        function previewPhoto(url, title) {
            document.getElementById('photoPreviewImg').src = url;
            document.getElementById('photoPreviewTitle').innerText = title || 'Pratinjau Foto';
            const modal = new bootstrap.Modal(document.getElementById('photoPreviewModal'));
            modal.show();
        }
    </script>
@endpush
