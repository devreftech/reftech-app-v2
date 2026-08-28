@extends('layouts.sales.app')

@section('title', 'Sales Webmail & Signature Studio')

@push('before-style')
<style>
    /* Full Webmail Hub Container */
    .mailbox-wrapper {
        min-height: calc(100vh - 170px);
        background: var(--bs-card-bg, #ffffff);
        border-radius: 12px;
        box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.08);
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }
    .dark-style .mailbox-wrapper {
        border-color: rgba(255, 255, 255, 0.08);
        box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.35);
    }

    /* Left Sidebar Navigation */
    .mailbox-sidebar {
        width: 250px;
        min-width: 250px;
        border-right: 1px solid rgba(0, 0, 0, 0.08);
        padding: 1.25rem 0.85rem;
        background: rgba(var(--bs-primary-rgb, 105, 108, 255), 0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .dark-style .mailbox-sidebar {
        border-right-color: rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.02);
    }
    .mailbox-nav-item {
        display: flex;
        align-items: center;
        padding: 0.6rem 0.85rem;
        border-radius: 8px;
        color: inherit;
        font-weight: 500;
        text-decoration: none;
        margin-bottom: 0.25rem;
        transition: all 0.18s ease-in-out;
        cursor: pointer;
        font-size: 0.9rem;
    }
    .mailbox-nav-item:hover {
        background: rgba(105, 108, 255, 0.1);
        color: #696cff;
    }
    .mailbox-nav-item.active {
        background: #696cff;
        color: #ffffff !important;
        font-weight: 600;
    }
    .mailbox-nav-item.active .badge {
        background: #ffffff !important;
        color: #696cff !important;
    }

    /* Middle List Panel */
    .mailbox-list-pane {
        width: 390px;
        min-width: 340px;
        border-right: 1px solid rgba(0, 0, 0, 0.08);
        display: flex;
        flex-direction: column;
        height: calc(100vh - 170px);
    }
    .dark-style .mailbox-list-pane {
        border-right-color: rgba(255, 255, 255, 0.08);
    }
    .mail-items-container {
        flex: 1;
        overflow-y: auto;
    }
    .mail-item {
        padding: 0.9rem 1.15rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        cursor: pointer;
        transition: background 0.15s ease;
        position: relative;
    }
    .dark-style .mail-item {
        border-bottom-color: rgba(255, 255, 255, 0.06);
    }
    .mail-item:hover {
        background: rgba(105, 108, 255, 0.05);
    }
    .mail-item.selected {
        background: rgba(105, 108, 255, 0.12);
        border-left: 4px solid #696cff;
    }
    .mail-item.unread {
        background: rgba(var(--bs-primary-rgb, 105, 108, 255), 0.04);
        font-weight: 600;
    }
    .mail-item.unread .mail-title {
        color: #696cff;
        font-weight: 700;
    }

    /* Right Detail / Reading Pane */
    .mailbox-detail-pane {
        flex: 1;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 170px);
        overflow-y: auto;
        background: var(--bs-card-bg, #ffffff);
    }
    .email-body-text {
        white-space: pre-wrap;
        font-family: inherit;
        line-height: 1.7;
        font-size: 0.95rem;
    }

    /* Star icon toggle */
    .star-toggle {
        cursor: pointer;
        color: #b4b7bd;
        transition: color 0.15s ease;
    }
    .star-toggle.starred, .star-toggle:hover {
        color: #ffab00 !important;
    }

    /* Template Cards */
    .template-card {
        border: 1px solid rgba(0, 0, 0, 0.08);
        border-radius: 10px;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .template-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }

    .mail-tag-pill {
        font-size: 0.72rem;
        padding: 0.2rem 0.55rem;
        border-radius: 20px;
        font-weight: 600;
    }

    /* Signature Studio Styling */
    .signature-builder-container {
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }
    .dark-style .signature-builder-container {
        background: #1e1e2d;
        border-color: #2b2c40;
    }
    .sig-template-radio-card {
        border: 2px solid transparent;
        border-radius: 10px;
        padding: 0.65rem 0.75rem;
        cursor: pointer;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        transition: all 0.2s;
    }
    .dark-style .sig-template-radio-card {
        background: #2b2c40;
    }
    .sig-template-radio-card:hover {
        border-color: #a5b4fc;
    }
    .sig-template-radio-card.active {
        border-color: #696cff;
        background: rgba(105, 108, 255, 0.05);
    }

    /* Color Swatch Picker */
    .color-swatch {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        cursor: pointer;
        display: inline-block;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 1px #cbd5e1;
        transition: transform 0.15s;
    }
    .color-swatch:hover, .color-swatch.active {
        transform: scale(1.15);
        box-shadow: 0 0 0 2px #696cff;
    }

    /* Code Editor Textarea */
    .code-editor-textarea {
        font-family: 'Fira Code', 'Consolas', 'Courier New', monospace;
        font-size: 0.82rem;
        line-height: 1.5;
        background-color: #0f172a;
        color: #38bdf8;
        border-radius: 8px;
        border: 1px solid #334155;
        padding: 0.75rem;
    }
    .code-editor-textarea:focus {
        background-color: #0b1120;
        color: #7dd3fc;
        border-color: #38bdf8;
        box-shadow: 0 0 0 0.2rem rgba(56, 189, 248, 0.25);
    }

    /* Split Compose Box Styling */
    .compose-sidebar-panel {
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        padding: 1.25rem;
    }
    .dark-style .compose-sidebar-panel {
        background: #1e1e2d;
        border-color: #2b2c40;
    }
    .compose-body-editor {
        min-height: 320px;
        font-size: 0.95rem;
        line-height: 1.65;
        border-radius: 8px;
    }

    /* Floating Minimized Composer Widget (Bottom Right Dock) */
    .floating-compose-dock {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1060;
        width: 320px;
        background: #ffffff;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        border: 1px solid #696cff;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .dark-style .floating-compose-dock {
        background: #2b2c40;
        border-color: #696cff;
    }
    .floating-compose-dock:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 35px rgba(105, 108, 255, 0.35);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Header Page & Sync Status -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center">
                <i class="mdi mdi-email-fast-outline text-primary me-2 fs-3"></i>
                Sales Webmail & Signature Studio
            </h4>
            <div class="d-flex align-items-center gap-2 text-muted small">
                <span>Kirim & terima email langsung dengan SMTP & IMAP akun pribadi, proteksi auto-save, dan HTML Signature profesional.</span>
                @if (!empty($mailSetting->smtp_username) && !empty($mailSetting->imap_username))
                    <span class="badge bg-label-success rounded-pill d-inline-flex align-items-center" id="smtpStatusBadge" title="Terkoneksi ke server {{ $mailSetting->smtp_host }} ({{ $mailSetting->smtp_username }})">
                        <i class="mdi mdi-check-circle-outline text-success me-1"></i> SMTP & IMAP Aktif ({{ $mailSetting->smtp_username }})
                    </span>
                @else
                    <span class="badge bg-label-warning rounded-pill d-inline-flex align-items-center" id="smtpStatusBadge" title="Server mail belum dikonfigurasi">
                        <i class="mdi mdi-alert-circle-outline text-warning me-1"></i> Belum Dikonfigurasi
                    </span>
                @endif
            </div>
        </div>
        <div class="dropdown mt-2 mt-sm-0">
            <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle shadow-sm d-flex align-items-center" data-bs-toggle="dropdown" aria-expanded="false" id="btnMailboxSettingsDropdown">
                <i class="mdi mdi-cog-outline me-1 fs-6"></i> Pengaturan & Aksi
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2" style="min-width: 240px;">
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2" href="javascript:void(0)" onclick="syncMailbox()" id="btnSyncMailbox">
                        <i class="mdi mdi-sync text-primary me-2 fs-5"></i>
                        <div>
                            <div class="fw-semibold small">Sync Email Sekarang</div>
                            <span class="text-muted" style="font-size: 0.72rem;">Tarik kotak masuk & terkirim server</span>
                        </div>
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalSmtpConfig">
                        <i class="mdi mdi-server-network text-info me-2 fs-5"></i>
                        <div>
                            <div class="fw-semibold small">Setting SMTP & IMAP</div>
                            <span class="text-muted" style="font-size: 0.72rem;">Konfigurasi akun server mail</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2" href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#modalSignatureStudio">
                        <i class="mdi mdi-palette-swatch-outline text-warning me-2 fs-5"></i>
                        <div>
                            <div class="fw-semibold small">Studio Signature HTML</div>
                            <span class="text-muted" style="font-size: 0.72rem;">Kelola template signature email</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Quick Stats Bar -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-none bg-label-primary p-2">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded bg-primary text-white"><i class="mdi mdi-inbox-arrow-down"></i></span>
                    </div>
                    <div class="text-truncate">
                        <span class="d-block text-muted small" style="font-size: 0.75rem;">Kotak Masuk</span>
                        <strong class="small" id="statInboxLabel">{{ $stats['total_inbox'] }} Pesan ({{ $stats['unread_inbox'] }} Baru)</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-none bg-label-success p-2">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded bg-success text-white"><i class="mdi mdi-send-check"></i></span>
                    </div>
                    <div class="text-truncate">
                        <span class="d-block text-muted small" style="font-size: 0.75rem;">Email Terkirim</span>
                        <strong class="small" id="statSentLabel">{{ $stats['total_sent'] }} Email</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-none bg-label-warning p-2">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded bg-warning text-white"><i class="mdi mdi-star"></i></span>
                    </div>
                    <div class="text-truncate">
                        <span class="d-block text-muted small" style="font-size: 0.75rem;">Penting & Bintang</span>
                        <strong class="small" id="statStarredLabel">{{ $stats['total_starred'] }} Pesan</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-none bg-label-info p-2">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2">
                        <span class="avatar-initial rounded bg-info text-white"><i class="mdi mdi-server-network"></i></span>
                    </div>
                    <div class="text-truncate">
                        <span class="d-block text-muted small" style="font-size: 0.75rem;">Server Mail</span>
                        <strong class="small text-truncate d-block" id="activeMailHostLabel">{{ $mailSetting ? $mailSetting->smtp_host : 'smtp.gmail.com' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Webmail Main Container -->
    <div class="mailbox-wrapper d-flex flex-column flex-md-row">
        <!-- 1. Left Sidebar Navigation -->
        <div class="mailbox-sidebar">
            <div>
                <!-- Big Compose Button -->
                <button class="btn btn-primary w-100 mb-3 shadow-sm d-flex align-items-center justify-content-center" onclick="openComposeModal()">
                    <i class="mdi mdi-plus-circle-outline me-2 fs-5"></i> Tulis Pesan Baru
                </button>

                <!-- Navigation Folders -->
                <div class="text-uppercase text-muted fw-semibold small px-2 mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Folder</div>
                <nav class="nav flex-column mb-3">
                    <div class="mailbox-nav-item active" data-folder="inbox" onclick="switchFolder('inbox', this)">
                        <i class="mdi mdi-inbox-outline me-2 fs-5"></i>
                        <span>Kotak Masuk</span>
                        <span class="badge bg-danger rounded-pill ms-auto" id="badgeInboxCount">{{ $stats['unread_inbox'] > 0 ? $stats['unread_inbox'] : $stats['total_inbox'] }}</span>
                    </div>
                    <div class="mailbox-nav-item" data-folder="sent" onclick="switchFolder('sent', this)">
                        <i class="mdi mdi-send-outline me-2 fs-5"></i>
                        <span>Terkirim</span>
                        <span class="badge bg-label-primary rounded-pill ms-auto" id="badgeSentCount">{{ $stats['total_sent'] }}</span>
                    </div>
                    <div class="mailbox-nav-item" data-folder="starred" onclick="switchFolder('starred', this)">
                        <i class="mdi mdi-star-outline me-2 fs-5 text-warning"></i>
                        <span>Berbintang</span>
                        <span class="badge bg-label-warning rounded-pill ms-auto" id="badgeStarredCount">{{ $stats['total_starred'] }}</span>
                    </div>
                    <div class="mailbox-nav-item" data-folder="draft" onclick="switchFolder('draft', this)">
                        <i class="mdi mdi-file-outline me-2 fs-5"></i>
                        <span>Draft</span>
                        <span class="badge bg-label-secondary rounded-pill ms-auto" id="sidebarDraftBadge">{{ $stats['total_draft'] }}</span>
                    </div>
                    <div class="mailbox-nav-item" data-folder="trash" onclick="switchFolder('trash', this)">
                        <i class="mdi mdi-delete-outline me-2 fs-5"></i>
                        <span>Sampah</span>
                        <span class="badge bg-label-danger rounded-pill ms-auto" id="badgeTrashCount">{{ $stats['total_trash'] }}</span>
                    </div>
                    <div class="mailbox-nav-item" data-folder="templates" onclick="switchFolder('templates', this)">
                        <i class="mdi mdi-file-document-multiple-outline me-2 fs-5"></i>
                        <span>Katalog Template</span>
                        <span class="badge bg-label-info rounded-pill ms-auto">{{ count($templates) }}</span>
                    </div>
                </nav>

                <!-- Labels / Tags Filter -->
                <div class="text-uppercase text-muted fw-semibold small px-2 mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Filter Label</div>
                <div class="d-flex flex-column gap-1">
                    <a href="javascript:void(0);" onclick="filterByTag('all')" class="d-flex align-items-center text-muted px-2 py-1 small rounded text-decoration-none hover-bg">
                        <span class="badge badge-dot bg-secondary me-2"></span> Semua Pesan
                    </a>
                    <a href="javascript:void(0);" onclick="filterByTag('Inquiry Baru')" class="d-flex align-items-center text-muted px-2 py-1 small rounded text-decoration-none hover-bg">
                        <span class="badge badge-dot bg-success me-2"></span> Inquiry Baru / Customer
                    </a>
                    <a href="javascript:void(0);" onclick="filterByTag('Leads Intro')" class="d-flex align-items-center text-muted px-2 py-1 small rounded text-decoration-none hover-bg">
                        <span class="badge badge-dot bg-primary me-2"></span> Leads Introduction
                    </a>
                    <a href="javascript:void(0);" onclick="filterByTag('Penawaran')" class="d-flex align-items-center text-muted px-2 py-1 small rounded text-decoration-none hover-bg">
                        <span class="badge badge-dot bg-info me-2"></span> Penawaran (Quotation)
                    </a>
                    <a href="javascript:void(0);" onclick="filterByTag('Tagihan')" class="d-flex align-items-center text-muted px-2 py-1 small rounded text-decoration-none hover-bg">
                        <span class="badge badge-dot bg-danger me-2"></span> Tagihan & Invoice
                    </a>
                </div>
            </div>

            <!-- Current Sender / Signature Badge Card -->
            <div class="p-2 rounded bg-label-secondary small border mt-4">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="badge bg-primary" style="font-size: 0.65rem;">Akun SMTP/IMAP</span>
                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalSmtpConfig" title="Edit Akun SMTP/IMAP" class="text-primary fw-bold">
                        <i class="mdi mdi-cog-outline" style="font-size: 0.9rem;"></i> Setting
                    </a>
                </div>
                <div class="fw-semibold text-truncate text-dark" id="sidebarUserName">{{ $mailSetting && $mailSetting->from_name ? $mailSetting->from_name : $userName }}</div>
                <div class="text-muted text-truncate" style="font-size: 0.75rem;" id="sidebarUserTitle">{{ $userTitle }}</div>
                <div class="text-muted text-truncate mt-1" style="font-size: 0.78rem;" title="{{ $mailSetting && $mailSetting->from_address ? $mailSetting->from_address : $userEmail }}">
                    <i class="mdi mdi-email-outline me-1"></i> {{ $mailSetting && $mailSetting->from_address ? $mailSetting->from_address : $userEmail }}
                </div>
            </div>
        </div>

        <!-- 2. Middle Mail List Pane -->
        <div class="mailbox-list-pane" id="mailboxListSection">
            <!-- Search & Actions Bar -->
            <div class="p-3 border-bottom d-flex align-items-center gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-transparent border-end-0"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" id="mailSearchInput" class="form-control border-start-0" placeholder="Cari email, pengirim, subjek..." onkeyup="searchEmails()">
                </div>
                <button class="btn btn-sm btn-outline-secondary" title="Refresh & Sync Email Baru dari Server" onclick="syncMailbox()">
                    <i class="mdi mdi-refresh"></i>
                </button>
            </div>

            <!-- Email Items List -->
            <div class="mail-items-container" id="mailListContainer">
                <!-- Dynamically Rendered by JS -->
            </div>
        </div>

        <!-- 3. Right Email Detail Pane -->
        <div class="mailbox-detail-pane" id="mailDetailSection">
            <!-- Empty Placeholder State when no email is selected -->
            <div id="detailEmptyState" class="d-flex flex-column align-items-center justify-content-center h-100 p-5 text-center" style="min-height: 520px;">
                <div class="avatar avatar-xl bg-label-primary mb-3 shadow-sm" style="width: 76px; height: 76px; border-radius: 50%;">
                    <i class="mdi mdi-email-open-outline text-primary" style="font-size: 38px; line-height: 76px;"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">Pilih Pesan untuk Membaca</h5>
                <p class="text-muted small mb-0" style="max-width: 340px;">
                    Klik salah satu email di daftar sebelah kiri untuk membaca detail isi surat, melihat dokumen lampiran, dan mengirim balasan.
                </p>
            </div>

            <!-- Mail Content Container (Hidden until an email is selected) -->
            <div id="detailActiveContent" class="d-none">
                <!-- Mail Action Header -->
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="replyEmail()" title="Balas Pesan Ini">
                            <i class="mdi mdi-reply me-1"></i> Balas
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="forwardEmail()" title="Teruskan ke Email Lain">
                            <i class="mdi mdi-share-outline me-1"></i> Teruskan
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()" title="Cetak Pesan">
                            <i class="mdi mdi-printer-outline"></i>
                        </button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success" id="detailStatusBadge">Received</span>
                        <button class="btn btn-sm btn-icon btn-outline-secondary star-toggle" id="detailStarBtn" onclick="toggleDetailStar()" title="Tandai Bintang">
                            <i class="mdi mdi-star"></i>
                        </button>
                        <button class="btn btn-sm btn-icon btn-outline-danger" title="Hapus pesan" onclick="deleteEmail()">
                            <i class="mdi mdi-delete-outline"></i>
                        </button>
                    </div>
                </div>

                <!-- Mail Content Container -->
                <div class="p-4" id="detailContentContainer">
                    <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3">
                                <span class="avatar-initial rounded-circle bg-label-primary fw-bold" id="detailAvatar">--</span>
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold text-dark" id="detailSubject">-</h5>
                                <div class="small text-muted">
                                    Dari: <span class="fw-semibold text-dark" id="detailSender">-</span>
                                </div>
                                <div class="small text-muted">
                                    Kepada: <span class="fw-semibold text-dark" id="detailRecipient">-</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small d-block" id="detailDate">-</span>
                            <span class="badge bg-label-success mt-1" id="detailTag">-</span>
                        </div>
                    </div>

                    <!-- View Mode Selector (HTML Visual vs Plain Text) -->
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom" id="viewModeBar">
                        <span class="small text-muted" style="font-size: 0.75rem;">
                            <i class="mdi mdi-format-paint text-primary me-1"></i> Mode Tampilan Pesan:
                        </span>
                        <div class="btn-group btn-group-xs" role="group">
                            <button type="button" class="btn btn-xs btn-primary active" id="btnViewHtml" onclick="switchDetailViewMode('html')">
                                <i class="mdi mdi-code-tags me-1"></i> Visual HTML
                            </button>
                            <button type="button" class="btn btn-xs btn-outline-secondary" id="btnViewPlain" onclick="switchDetailViewMode('plain')">
                                <i class="mdi mdi-format-align-left me-1"></i> Teks Polos
                            </button>
                        </div>
                    </div>

                    <!-- HTML Email Visual Viewer (Auto-resizing iframe) -->
                    <div id="detailHtmlContainer" class="mb-3">
                        <iframe id="detailHtmlFrame" style="width: 100%; border: 1px solid #e2e8f0; min-height: 480px; border-radius: 8px; background: #ffffff;" onload="autoResizeIframe(this)"></iframe>
                    </div>

                    <!-- Plain Text Email View -->
                    <div class="email-body-text mb-3 text-dark d-none" id="detailPlainText">
                    </div>

                    <!-- Attachment Area -->
                    <div class="p-3 bg-label-secondary rounded border mb-3 d-none" id="detailAttachmentArea">
                        <div class="fw-semibold small mb-2 d-flex align-items-center">
                            <i class="mdi mdi-paperclip me-1 text-primary"></i> Lampiran Dokumen
                        </div>
                        <div id="attachmentItemsList" class="d-flex flex-wrap gap-2">
                        </div>
                    </div>

                    <!-- Quick Reply Box -->
                    <div class="card border mt-4">
                        <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                            <span class="fw-semibold small"><i class="mdi mdi-reply me-1 text-primary"></i> Balas Cepat</span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-label-info small" style="font-size: 0.7rem;"><i class="mdi mdi-check me-1"></i>HTML Signature Auto-Attached</span>
                                <button class="btn btn-xs btn-outline-secondary" onclick="replyEmail()">Buka Editor Penuh</button>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <textarea class="form-control form-control-sm mb-2" rows="3" id="quickReplyText" placeholder="Ketik balasan singkat Anda di sini (signature HTML akan otomatis terlampir di email)..."></textarea>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small" style="font-size: 0.78rem;">Pengirim: {{ $userName }} ({{ $userEmail }})</span>
                                <button class="btn btn-sm btn-primary" onclick="sendQuickReply()"><i class="mdi mdi-send me-1"></i> Kirim Balasan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Template Manager View -->
        <div class="mailbox-detail-pane d-none" id="templatesSection">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold"><i class="mdi mdi-file-document-multiple-outline me-2 text-warning"></i> Master Template Email Sales & Bisnis</h5>
                    <span class="text-muted small">Template siap pakai untuk mempercepat penulisan Introduction, Penawaran, Tagihan, maupun Follow-Up.</span>
                </div>
                <button class="btn btn-sm btn-primary" onclick="openCreateTemplateModal()">
                    <i class="mdi mdi-plus me-1"></i> Buat Template Baru
                </button>
            </div>
            <div class="p-4">
                <div class="row g-3">
                    @foreach ($templates as $tpl)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 template-card">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <span class="badge bg-label-{{ $tpl['badge_color'] }}">{{ $tpl['badge'] }}</span>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon text-muted" data-bs-toggle="dropdown"><i class="mdi mdi-dots-vertical"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="javascript:void(0);" onclick="useTemplate('{{ $tpl['id'] }}')"><i class="mdi mdi-pencil me-1"></i> Gunakan di Compose</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0);"><i class="mdi mdi-file-edit-outline me-1"></i> Edit Teks</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <h6 class="fw-bold mb-2">{{ $tpl['name'] }}</h6>
                                <p class="text-muted small mb-2 text-truncate fw-semibold">Subjek: {{ $tpl['subject'] ?: '(Sesuai Kebutuhan)' }}</p>
                                <p class="text-muted small mb-3 flex-grow-1" style="max-height: 80px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                                    {{ $tpl['body'] }}
                                </p>
                                <button class="btn btn-sm btn-outline-primary w-100 mt-auto" onclick="useTemplate('{{ $tpl['id'] }}')">
                                    <i class="mdi mdi-send me-1"></i> Gunakan Template Ini
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1: COMPOSE MODAL LEBAR (DENGAN PROTEKSI DATA LOSS & AUTO-SAVE DRAFT) -->
<div class="modal fade" id="modalComposeEmail" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2 bg-white rounded text-primary d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-email-edit-outline fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white mb-0 fw-bold">Tulis & Kirim Pesan Email</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-white text-opacity-75">Anti-Hilang Data &bull; Auto-Save Draft Aktif</span>
                            <span class="badge bg-white text-primary rounded-pill px-2 py-0" id="autoSaveStatusBadge" style="font-size: 0.68rem;">
                                <i class="mdi mdi-cloud-check-outline me-1"></i> Draft Tersimpan
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-icon btn-outline-light text-white" title="Minimize / Perkecil ke Pojok Bawah" onclick="minimizeComposer()">
                        <i class="mdi mdi-window-minimize"></i>
                    </button>
                    <button type="button" class="btn-close btn-close-white" aria-label="Close" onclick="confirmCloseComposer()"></button>
                </div>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- LEFT COLUMN: INFORMASI PENGIRIMAN, PENERIMA, CC/BCC, SUBJEK, & LAMPIRAN -->
                    <div class="col-lg-4 col-md-5">
                        <div class="compose-sidebar-panel h-100 d-flex flex-column justify-content-between">
                            <div>
                                <!-- Header Step 1 -->
                                <h6 class="fw-bold mb-3 text-dark d-flex align-items-center">
                                    <i class="mdi mdi-account-arrow-right-outline text-primary me-2 fs-5"></i> Informasi Pengiriman
                                </h6>

                                <!-- Template Cepat Picker -->
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold text-muted mb-1">Pilih Template (Opsional):</label>
                                    <select class="form-select form-select-sm" id="composeTemplateSelect" onchange="onComposeTemplateChanged()">
                                        @foreach ($templates as $tpl)
                                            <option value="{{ $tpl['id'] }}">{{ $tpl['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Penerima (To) -->
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label small fw-semibold mb-0">
                                            Kepada (To): <span class="text-danger">*</span>
                                        </label>
                                        <div class="dropdown">
                                            <button class="btn btn-xs btn-outline-primary dropdown-toggle py-0" type="button" data-bs-toggle="dropdown" style="font-size: 0.72rem;">
                                                <i class="mdi mdi-account-box me-1"></i> Kontak
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow" style="max-height: 230px; overflow-y: auto;">
                                                <li class="dropdown-header text-uppercase small" style="font-size: 0.7rem;">Kontak Leads & Customer</li>
                                                @foreach ($quickContacts as $qc)
                                                <li>
                                                    <a class="dropdown-item d-flex justify-content-between align-items-center py-2" href="javascript:void(0);" onclick="pickContact('{{ $qc['email'] }}', '{{ $qc['name'] }}', '{{ $qc['company'] }}')">
                                                        <div>
                                                            <strong class="d-block small text-dark">{{ $qc['name'] }}</strong>
                                                            <span class="text-muted" style="font-size: 0.72rem;">{{ $qc['company'] }}</span>
                                                        </div>
                                                        <span class="badge bg-label-primary ms-2" style="font-size: 0.68rem;">{{ $qc['email'] }}</span>
                                                    </a>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" id="composeRecipientEmail" placeholder="contoh: klien@perusahaan.com" oninput="triggerAutoSave()">
                                    <span class="text-muted d-block mt-1" style="font-size: 0.72rem;">Bisa kirim ke alamat email mana pun (bebas).</span>
                                </div>

                                <!-- Toggle CC & BCC Link -->
                                <div class="mb-2">
                                    <a href="javascript:void(0);" class="small text-primary text-decoration-none d-inline-flex align-items-center" onclick="toggleCcBcc()">
                                        <i class="mdi mdi-plus-circle-outline me-1"></i> <span id="ccBccToggleText">Tampilkan CC & BCC</span>
                                    </a>
                                </div>

                                <!-- CC & BCC Container -->
                                <div id="ccBccContainer" class="d-none mb-3 p-2 bg-white rounded border">
                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold mb-0" style="font-size: 0.75rem;">CC (Tembusan):</label>
                                        <input type="text" class="form-control form-control-sm" id="composeCc" placeholder="email1@domain.com, email2@domain.com" oninput="triggerAutoSave()">
                                    </div>
                                    <div>
                                        <label class="form-label small fw-semibold mb-0" style="font-size: 0.75rem;">BCC (Tembusan Tersembunyi):</label>
                                        <input type="text" class="form-control form-control-sm" id="composeBcc" placeholder="supervisor@reftech.id" oninput="triggerAutoSave()">
                                    </div>
                                </div>

                                <!-- Subjek / Title -->
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold mb-1">Subjek / Title Email: <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control form-control-sm" id="composeSubject" placeholder="Masukkan subjek email..." oninput="triggerAutoSave()">
                                </div>

                                <!-- Lampiran File -->
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold mb-1">
                                        <i class="mdi mdi-paperclip me-1 text-primary"></i> Lampirkan File:
                                    </label>
                                    <input type="file" class="form-control form-control-sm" id="composeFile" multiple>
                                    <span class="text-muted d-block mt-1" style="font-size: 0.72rem;">Mendukung PDF, DOCX, XLSX, JPG, PNG (Maks 15MB).</span>
                                </div>
                            </div>

                            <!-- Signature Options in Left Column -->
                            <div class="pt-3 border-top">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-check-label small fw-semibold text-dark" for="toggleSignatureCheckbox">
                                        <i class="mdi mdi-signature-freehand text-primary me-1"></i> Sisipkan Signature
                                    </label>
                                    <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" id="toggleSignatureCheckbox" checked onchange="toggleComposeSignaturePreview()">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small" style="font-size: 0.72rem;">Model: <span class="fw-semibold text-primary" id="composeSigModelName">{{ $mailSetting ? $mailSetting->signature_layout : 'Corporate Modern' }}</span></span>
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalSignatureStudio" class="small text-primary text-decoration-none" style="font-size: 0.72rem;">
                                        <i class="mdi mdi-pencil"></i> Ganti Desain
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: BADAN EMAIL LUAS & LIVE SIGNATURE PREVIEW -->
                    <div class="col-lg-8 col-md-7 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                                <i class="mdi mdi-text-box-edit-outline text-primary me-2 fs-5"></i> Badan Pesan Email
                            </h6>
                            <div class="d-flex align-items-center gap-1">
                                <span class="text-muted small me-2" style="font-size: 0.75rem;">Tag Dinamis:</span>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0" onclick="insertTagIntoBody('{client_name}')">{client_name}</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0" onclick="insertTagIntoBody('{company_name}')">{company_name}</button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-0" onclick="insertTagIntoBody('{sales_name}')">{sales_name}</button>
                            </div>
                        </div>

                        <!-- Big Textarea for Body with oninput auto-save -->
                        <div class="flex-grow-1 d-flex flex-column mb-3">
                            <textarea class="form-control compose-body-editor flex-grow-1" id="composeBody" rows="12" placeholder="Tulis isi pesan lengkap Anda di sini..." oninput="triggerAutoSave()"></textarea>
                        </div>

                        <!-- Signature Visual Attachment at Bottom of Body -->
                        <div class="p-3 bg-white rounded border shadow-sm" id="composeSignaturePreviewBox">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-semibold" style="font-size: 0.75rem;">
                                    <i class="mdi mdi-shield-check text-success me-1"></i> HTML Signature Terlampir Otomatis:
                                </span>
                            </div>
                            <div id="composeSigHtmlContainer" style="overflow-x: auto;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-3 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="discardDraftAndReset()">
                        <i class="mdi mdi-trash-can-outline me-1"></i> Buang Draft / Reset
                    </button>
                    <span class="text-muted small d-none d-sm-inline">
                        Draft otomatis tersimpan di memori browser Anda.
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="minimizeComposer()">
                        <i class="mdi mdi-window-minimize me-1"></i> Perkecil
                    </button>
                    <button type="button" class="btn btn-outline-primary" onclick="saveDraftManual()">
                        <i class="mdi mdi-content-save-outline me-1"></i> Simpan Draft
                    </button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm" id="btnSendEmailSubmit" onclick="sendUniversalEmail()">
                        <i class="mdi mdi-send me-1"></i> Kirim Pesan Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: MODAL SETTING SMTP & IMAP AKUN EMAIL PENGGUNA -->
<div class="modal fade" id="modalSmtpConfig" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2 bg-white rounded text-primary d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-server-network fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white mb-0 fw-bold">Konfigurasi SMTP (Kirim) & IMAP (Terima Email)</h5>
                        <span class="small text-white text-opacity-75">Kirim dan terima email masuk secara real-time dari server email Anda</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Webmail cPanel / Hosting Guide Banner -->
                <div class="mb-3 p-3 bg-label-primary rounded border border-primary border-opacity-25 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2 bg-primary text-white rounded d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-web fs-5"></i>
                        </div>
                        <div>
                            <strong class="d-block text-primary small">Konfigurasi Webmail cPanel / Hosting Perusahaan</strong>
                            <span class="text-muted" style="font-size: 0.75rem;">Standar Port SSL Aman: SMTP Port 465 (SSL) &bull; IMAP Port 993 (SSL)</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-xs btn-primary shadow-sm" onclick="applySmtpPreset('cpanel')">
                        <i class="mdi mdi-auto-fix me-1"></i> Auto-Fill Host Webmail
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Pengirim (From Name): <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="smtpFromName" value="{{ $mailSetting && $mailSetting->from_name ? $mailSetting->from_name : $userName }}" placeholder="Reftech Sales">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Alamat Email Webmail: <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-sm" id="smtpFromAddress" value="{{ $mailSetting && $mailSetting->from_address ? $mailSetting->from_address : $userEmail }}" placeholder="support@reftech.id">
                    </div>

                    <!-- SMTP (Kirim) Section -->
                    <div class="col-12"><div class="border-top my-1"></div><strong class="text-primary small d-flex align-items-center"><i class="mdi mdi-send me-1"></i> Pengaturan SMTP (Server Kirim Email Keluar)</strong></div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">SMTP Host: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="smtpHostInput" value="{{ $mailSetting && $mailSetting->smtp_host ? $mailSetting->smtp_host : 'srv162.niagahoster.com' }}" placeholder="srv162.niagahoster.com atau mail.reftech.id">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">SMTP Port: <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" id="smtpPortInput" value="{{ $mailSetting && $mailSetting->smtp_port ? $mailSetting->smtp_port : 465 }}" placeholder="465">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Enkripsi SMTP:</label>
                        <select class="form-select form-select-sm" id="smtpEncryptionInput">
                            <option value="ssl" {{ ($mailSetting && $mailSetting->smtp_encryption === 'ssl') || !$mailSetting ? 'selected' : '' }}>SSL (Port 465 Rekomendasi)</option>
                            <option value="tls" {{ $mailSetting && $mailSetting->smtp_encryption === 'tls' ? 'selected' : '' }}>TLS (Port 587)</option>
                        </select>
                    </div>

                    <!-- IMAP (Terima) Section -->
                    <div class="col-12"><div class="border-top my-1"></div><strong class="text-success small d-flex align-items-center"><i class="mdi mdi-inbox-arrow-down me-1"></i> Pengaturan IMAP (Server Terima / Tarik Email Masuk)</strong></div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">IMAP Host: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="imapHostInput" value="{{ $mailSetting && $mailSetting->imap_host ? $mailSetting->imap_host : 'srv162.niagahoster.com' }}" placeholder="srv162.niagahoster.com atau mail.reftech.id">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">IMAP Port: <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" id="imapPortInput" value="{{ $mailSetting && $mailSetting->imap_port ? $mailSetting->imap_port : 993 }}" placeholder="993">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Enkripsi IMAP:</label>
                        <select class="form-select form-select-sm" id="imapEncryptionInput">
                            <option value="ssl" {{ ($mailSetting && $mailSetting->imap_encryption === 'ssl') || !$mailSetting ? 'selected' : '' }}>SSL (Port 993 Rekomendasi)</option>
                            <option value="tls" {{ $mailSetting && $mailSetting->imap_encryption === 'tls' ? 'selected' : '' }}>TLS</option>
                        </select>
                    </div>

                    <!-- Credentials -->
                    <div class="col-12"><div class="border-top my-1"></div><strong class="text-dark small d-flex align-items-center"><i class="mdi mdi-key-outline me-1"></i> Kredensial Login Akun Webmail</strong></div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Username / Email Login: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="smtpUsernameInput" value="{{ $mailSetting && $mailSetting->smtp_username ? $mailSetting->smtp_username : $userEmail }}" placeholder="support@reftech.id">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Password Akun Webmail: <span class="text-danger">*</span></label>
                        <input type="password" class="form-control form-control-sm" id="smtpPasswordInput" placeholder="Masukkan password akun email cPanel Anda">
                        <span class="text-muted small" style="font-size: 0.72rem;">Kredensial tersimpan secara terenkripsi aman (AES-256).</span>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="mt-3 p-3 bg-light rounded border small">
                    <strong class="d-block text-dark mb-1"><i class="mdi mdi-information text-primary me-1"></i> Panduan Webmail cPanel / Hosting:</strong>
                    <span>Gunakan host server mail cPanel hosting Anda (contoh: <code>srv162.niagahoster.com</code> atau <code>mail.reftech.id</code>) dengan port aman SSL (SMTP: <strong>465</strong>, IMAP: <strong>993</strong>). Masukkan alamat email lengkap dan password akun webmail Anda.</span>
                </div>
            </div>
            <div class="modal-footer bg-light py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-info btn-sm" id="btnTestSmtpConnection" onclick="testSmtpConnection()">
                    <i class="mdi mdi-lan-connect me-1"></i> Uji Koneksi (Test Handshake)
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary btn-sm shadow-sm" id="btnSaveSmtpSetting" onclick="saveSmtpConfiguration()">
                        <i class="mdi mdi-content-save-check me-1"></i> Simpan Konfigurasi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Minimized Dock Bar (Appears when user minimizes the composer) -->
<div id="floatingComposeDock" class="floating-compose-dock d-none p-3" onclick="restoreComposerFromDock()">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center text-truncate me-2">
            <span class="badge bg-primary me-2"><i class="mdi mdi-pencil"></i></span>
            <div class="text-truncate">
                <strong class="d-block small text-dark text-truncate" id="dockSubjectText">Draft: (Tanpa Subjek)</strong>
                <span class="text-muted" style="font-size: 0.72rem;" id="dockRecipientText">Kepada: -</span>
            </div>
        </div>
        <button type="button" class="btn btn-xs btn-icon btn-primary" title="Perbesar Kembali">
            <i class="mdi mdi-window-maximize"></i>
        </button>
    </div>
</div>

<!-- Modal 3: SIGNATURE STUDIO & CUSTOM HTML CODE BUILDER -->
<div class="modal fade" id="modalSignatureStudio" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2 bg-white rounded text-primary d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-palette-swatch-outline fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white mb-0 fw-bold">Signature Studio & Custom HTML Builder</h5>
                        <span class="small text-white text-opacity-75">Pilih template layout siap pakai atau tulis custom kode HTML signature Anda sendiri.</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Left Column -->
                    <div class="col-lg-6">
                        <div class="signature-builder-container p-3 h-100 d-flex flex-column">
                            <h6 class="fw-bold mb-2 text-dark d-flex align-items-center">
                                <span class="badge bg-primary rounded-pill me-2">1</span> Pilih Mode / Layout Template:
                            </h6>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="sig-template-radio-card {{ ($mailSetting && $mailSetting->signature_layout === 'sig_corporate') || !$mailSetting ? 'active' : '' }} h-100" data-template="sig_corporate" onclick="selectSigLayout('sig_corporate', this)">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-dark small" style="font-size: 0.82rem;">🏢 Corporate Modern</strong>
                                            <span class="badge bg-label-primary" style="font-size: 0.65rem;">Populer</span>
                                        </div>
                                        <p class="text-muted small mb-0" style="font-size: 0.72rem;">Left accent bar & logo Reftech.</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="sig-template-radio-card {{ $mailSetting && $mailSetting->signature_layout === 'sig_minimal' ? 'active' : '' }} h-100" data-template="sig_minimal" onclick="selectSigLayout('sig_minimal', this)">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-dark small" style="font-size: 0.82rem;">⚡ Clean Minimal</strong>
                                            <span class="badge bg-label-success" style="font-size: 0.65rem;">Simpel</span>
                                        </div>
                                        <p class="text-muted small mb-0" style="font-size: 0.72rem;">Layout horizontal 2-kolom ringkas.</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="sig-template-radio-card {{ $mailSetting && $mailSetting->signature_layout === 'sig_executive' ? 'active' : '' }} h-100" data-template="sig_executive" onclick="selectSigLayout('sig_executive', this)">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-dark small" style="font-size: 0.82rem;">💼 Executive Card</strong>
                                            <span class="badge bg-label-info" style="font-size: 0.65rem;">Badge</span>
                                        </div>
                                        <p class="text-muted small mb-0" style="font-size: 0.72rem;">Format kartu border dengan seal SLA.</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="sig-template-radio-card {{ $mailSetting && $mailSetting->signature_layout === 'sig_custom_html' ? 'active' : '' }} h-100 border-warning border-opacity-50" data-template="sig_custom_html" onclick="selectSigLayout('sig_custom_html', this)">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong class="text-warning small fw-bold" style="font-size: 0.82rem;">🛠️ Custom Raw HTML</strong>
                                            <span class="badge bg-warning" style="font-size: 0.65rem;">Code</span>
                                        </div>
                                        <p class="text-muted small mb-0" style="font-size: 0.72rem;">Paste / Tulis kode HTML bebas Anda.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- 2A. STANDARD TEMPLATE CONTROLS -->
                            <div id="standardTemplateControls">
                                <h6 class="fw-bold mb-2 text-dark d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill me-2">2</span> Warna Aksen Tema:
                                </h6>
                                <div class="d-flex align-items-center gap-2 mb-3">
                                    <span class="color-swatch active" style="background-color: #696cff;" title="Reftech Purple" onclick="selectSigColor('#696cff', this)"></span>
                                    <span class="color-swatch" style="background-color: #0284c7;" title="Corporate Sky" onclick="selectSigColor('#0284c7', this)"></span>
                                    <span class="color-swatch" style="background-color: #0d9488;" title="Emerald Tech" onclick="selectSigColor('#0d9488', this)"></span>
                                    <span class="color-swatch" style="background-color: #1e293b;" title="Executive Navy" onclick="selectSigColor('#1e293b', this)"></span>
                                    <span class="color-swatch" style="background-color: #e11d48;" title="Crimson Bold" onclick="selectSigColor('#e11d48', this)"></span>
                                </div>

                                <h6 class="fw-bold mb-2 text-dark d-flex align-items-center">
                                    <span class="badge bg-primary rounded-pill me-2">3</span> Data Profil Sales:
                                </h6>
                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold mb-0">Nama Lengkap:</label>
                                        <input type="text" class="form-control form-control-sm" id="studioNameInput" value="{{ $mailSetting && $mailSetting->from_name ? $mailSetting->from_name : $userName }}" onkeyup="refreshStudioLivePreview()">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold mb-0">Jabatan / Posisi:</label>
                                        <input type="text" class="form-control form-control-sm" id="studioTitleInput" value="{{ $userTitle }}" onkeyup="refreshStudioLivePreview()">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold mb-0">No. WhatsApp / Telp:</label>
                                        <input type="text" class="form-control form-control-sm" id="studioPhoneInput" value="{{ $userPhone }}" onkeyup="refreshStudioLivePreview()">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold mb-0">Email Kantor:</label>
                                        <input type="email" class="form-control form-control-sm" id="studioEmailInput" value="{{ $mailSetting && $mailSetting->from_address ? $mailSetting->from_address : $userEmail }}" readonly>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold mb-0">Tagline / Motto Tambahan:</label>
                                        <input type="text" class="form-control form-control-sm" id="studioTaglineInput" value="Solusi IT Hardware, Server & Refurbished Bergaransi Resmi 1 Tahun" onkeyup="refreshStudioLivePreview()">
                                    </div>
                                </div>
                            </div>

                            <!-- 2B. CUSTOM RAW HTML CODE CONTROLS -->
                            <div id="customHtmlControls" class="d-none flex-grow-1 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                                        <span class="badge bg-warning text-dark rounded-pill me-2">2</span> Custom HTML Code Editor:
                                    </h6>
                                    <button type="button" class="btn btn-xs btn-outline-warning" onclick="loadSampleHtmlBoilerplate()">
                                        <i class="mdi mdi-restore me-1"></i> Muat Template Dasar
                                    </button>
                                </div>
                                <p class="text-muted small mb-2" style="font-size: 0.75rem;">
                                    Tulis atau paste kode HTML signature kustom di sini. Live Preview akan ter-render seketika.
                                </p>

                                <textarea class="form-control code-editor-textarea flex-grow-1 mb-2" id="customHtmlCodeInput" rows="11" onkeyup="onCustomHtmlCodeChanged()" placeholder="<table cellpadding='0' cellspacing='0'>...</table>">{{ $mailSetting && $mailSetting->signature_html ? $mailSetting->signature_html : '' }}</textarea>

                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <span class="small text-muted" style="font-size: 0.72rem;">Sisipkan Tag Cepat:</span>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0" onclick="insertTagIntoCustomHtml('{name}')">{name}</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0" onclick="insertTagIntoCustomHtml('{title}')">{title}</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0" onclick="insertTagIntoCustomHtml('{phone}')">{phone}</button>
                                    <button type="button" class="btn btn-xs btn-outline-secondary py-0" onclick="insertTagIntoCustomHtml('{email}')">{email}</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Live Visual Preview & Code Export -->
                    <div class="col-lg-6 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-dark">
                                <i class="mdi mdi-eye-outline me-1 text-primary"></i> Live Visual Preview (HTML Render)
                            </h6>
                            <span class="badge bg-label-success small"><i class="mdi mdi-check-decagram me-1"></i>Instant Rendering</span>
                        </div>

                        <div class="p-4 bg-white rounded border shadow-sm flex-grow-1 d-flex flex-column justify-content-center" style="min-height: 320px;" id="studioLivePreviewContainer">
                        </div>

                        <div class="mt-3 p-3 bg-label-primary rounded border border-primary border-opacity-25 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <strong class="d-block small text-primary">Signature HTML ini tersimpan permanen di akun Anda</strong>
                                <span class="text-muted small" style="font-size: 0.75rem;">Setiap email baru & balasan akan menyertakan desain signature ini.</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="copySignatureHtml()">
                                <i class="mdi mdi-content-copy me-1"></i> Salin Kode HTML
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary shadow-sm" id="btnApplySigSubmit" onclick="applySignatureToMyAccount()">
                    <i class="mdi mdi-check-bold me-1"></i> Terapkan & Simpan Signature
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-script')
<script>
    // State & Data
    let allEmails = @json($emails);
    const allTemplates = @json($templates);
    let currentUserName = @json($mailSetting && $mailSetting->from_name ? $mailSetting->from_name : $userName);
    let currentUserEmail = @json($mailSetting && $mailSetting->from_address ? $mailSetting->from_address : $userEmail);
    let currentUserPhone = @json($userPhone);
    let currentUserTitle = @json($userTitle);
    let currentTagline = "Solusi IT Hardware, Server & Refurbished Bergaransi Resmi 1 Tahun";

    let selectedSigLayout = @json($mailSetting && $mailSetting->signature_layout ? $mailSetting->signature_layout : 'sig_corporate');
    let selectedSigColor = @json($mailSetting && $mailSetting->signature_color ? $mailSetting->signature_color : '#696cff');

    // Auto-Save Draft Storage Key
    const DRAFT_STORAGE_KEY = 'sales_mailbox_active_draft_v1';
    let autoSaveTimer = null;

    // Default Custom HTML Code Boilerplate
    let customHtmlCode = @json($mailSetting && $mailSetting->signature_html ? $mailSetting->signature_html : '') || `
<table cellpadding="0" cellspacing="0" border="0" style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #1e293b; max-width: 520px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; background: #ffffff;">
    <tr>
        <td style="vertical-align: top; padding-right: 14px; width: 64px;">
            <div style="width: 58px; height: 58px; border-radius: 50%; background: linear-gradient(135deg, #696cff 0%, #4338ca 100%); color: #ffffff; font-weight: bold; font-size: 20px; text-align: center; line-height: 58px; box-shadow: 0 4px 10px rgba(105, 108, 255, 0.3);">
                RT
            </div>
        </td>
        <td style="vertical-align: top;">
            <div style="font-size: 17px; font-weight: 800; color: #0f172a; letter-spacing: -0.3px;">{name}</div>
            <div style="font-size: 12.5px; font-weight: 600; color: #696cff; margin-bottom: 8px;">{title} &bull; PT Reftech Indonesia</div>
            
            <div style="font-size: 12px; color: #475569; line-height: 1.6;">
                <div>📞 <strong>WhatsApp:</strong> <a href="https://wa.me/6281234567890" style="color: #0f172a; text-decoration: none; font-weight: 600;">{phone}</a></div>
                <div>✉️ <strong>Email:</strong> <a href="mailto:{email}" style="color: #696cff; text-decoration: none;">{email}</a></div>
                <div>🌐 <strong>Web:</strong> <a href="https://www.reftech.id" target="_blank" style="color: #696cff; text-decoration: none;">www.reftech.id</a></div>
                <div>📍 <strong>Alamat:</strong> Grand Galaxy City, Bekasi Selatan</div>
            </div>

            <div style="margin-top: 10px; padding: 4px 10px; background: rgba(105, 108, 255, 0.08); border-radius: 4px; font-size: 11px; color: #4338ca; font-weight: 600; display: inline-block;">
                🚀 Penyedia Solusi Server, Workstation & PC Refurbished Terpercaya
            </div>
        </td>
    </tr>
</table>
`.trim();

    let activeEmailId = null;
    let activeFolder = 'inbox';

    document.addEventListener('DOMContentLoaded', function() {
        if (!document.getElementById('customHtmlCodeInput').value) {
            document.getElementById('customHtmlCodeInput').value = customHtmlCode;
        }
        renderEmailList(activeFolder);
        refreshStudioLivePreview();
        renderComposeSignaturePreview();
        checkAndRestoreStoredDraft();

        // Background Auto-Sync on page load & periodic every 60s
        setTimeout(() => {
            syncMailbox(true);
        }, 2000);
        setInterval(() => {
            syncMailbox(true);
        }, 60000);
    });

    // Helper CSRF Header for Fetch
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '{{ csrf_token() }}';
    }

    // AUTO-SAVE DRAFT SYSTEM
    function triggerAutoSave() {
        clearTimeout(autoSaveTimer);
        const badge = document.getElementById('autoSaveStatusBadge');
        badge.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...';
        badge.className = 'badge bg-warning text-dark rounded-pill px-2 py-0';

        autoSaveTimer = setTimeout(() => {
            const draftData = {
                recipient: document.getElementById('composeRecipientEmail').value,
                subject: document.getElementById('composeSubject').value,
                body: document.getElementById('composeBody').value,
                cc: document.getElementById('composeCc').value,
                bcc: document.getElementById('composeBcc').value,
                template: document.getElementById('composeTemplateSelect').value,
                updatedAt: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
            };

            localStorage.setItem(DRAFT_STORAGE_KEY, JSON.stringify(draftData));

            badge.innerHTML = `<i class="mdi mdi-cloud-check-outline me-1"></i> Draft Tersimpan (${draftData.updatedAt})`;
            badge.className = 'badge bg-white text-primary rounded-pill px-2 py-0';

            document.getElementById('dockSubjectText').innerText = draftData.subject ? `Draft: ${draftData.subject}` : 'Draft: (Tanpa Subjek)';
            document.getElementById('dockRecipientText').innerText = draftData.recipient ? `Kepada: ${draftData.recipient}` : 'Kepada: -';
        }, 600);
    }

    function checkAndRestoreStoredDraft() {
        try {
            const raw = localStorage.getItem(DRAFT_STORAGE_KEY);
            if (raw) {
                const data = JSON.parse(raw);
                if (data.body && data.body.trim() && data.body !== `Halo Bapak/Ibu,\n\n[Tulis isi pesan Anda di sini]`) {
                    document.getElementById('floatingComposeDock').classList.remove('d-none');
                    document.getElementById('dockSubjectText').innerText = data.subject ? `Draft: ${data.subject}` : 'Draft: (Tersimpan)';
                    document.getElementById('dockRecipientText').innerText = data.recipient ? `Kepada: ${data.recipient}` : 'Klik untuk buka draft';
                }
            }
        } catch (e) {
            console.error('Draft restore error', e);
        }
    }

    function minimizeComposer() {
        triggerAutoSave();
        bootstrap.Modal.getInstance(document.getElementById('modalComposeEmail')).hide();
        document.getElementById('floatingComposeDock').classList.remove('d-none');
    }

    function restoreComposerFromDock() {
        document.getElementById('floatingComposeDock').classList.add('d-none');
        openComposeModal();
    }

    function confirmCloseComposer() {
        const body = document.getElementById('composeBody').value.trim();
        const subject = document.getElementById('composeSubject').value.trim();
        const recipient = document.getElementById('composeRecipientEmail').value.trim();

        if (body || subject || recipient) {
            triggerAutoSave();
            bootstrap.Modal.getInstance(document.getElementById('modalComposeEmail')).hide();
            document.getElementById('floatingComposeDock').classList.remove('d-none');
        } else {
            bootstrap.Modal.getInstance(document.getElementById('modalComposeEmail')).hide();
        }
    }

    function discardDraftAndReset() {
        if (confirm('Apakah Anda yakin ingin membuang draft ini dan mengosongkan form?')) {
            localStorage.removeItem(DRAFT_STORAGE_KEY);
            document.getElementById('composeRecipientEmail').value = '';
            document.getElementById('composeSubject').value = '';
            document.getElementById('composeBody').value = `Halo Bapak/Ibu,\n\n[Tulis isi pesan Anda di sini]`;
            document.getElementById('composeCc').value = '';
            document.getElementById('composeBcc').value = '';
            document.getElementById('composeTemplateSelect').value = 'blank';
            document.getElementById('floatingComposeDock').classList.add('d-none');

            const badge = document.getElementById('autoSaveStatusBadge');
            badge.innerHTML = '<i class="mdi mdi-trash-can-outline me-1"></i> Draft Dikosongkan';
            badge.className = 'badge bg-secondary text-white rounded-pill px-2 py-0';
        }
    }

    function saveDraftManual() {
        triggerAutoSave();
        alert('Draft email Anda telah tersimpan secara aman. Anda dapat melanjutkan menulis kapan saja!');
    }

    // Open Compose Modal
    function openComposeModal(prefillRecipient = '', prefillSubject = '', prefillBody = '') {
        const raw = localStorage.getItem(DRAFT_STORAGE_KEY);
        let restored = false;

        if (!prefillRecipient && !prefillSubject && !prefillBody && raw) {
            try {
                const data = JSON.parse(raw);
                if (data.body || data.subject || data.recipient) {
                    document.getElementById('composeRecipientEmail').value = data.recipient || '';
                    document.getElementById('composeSubject').value = data.subject || '';
                    document.getElementById('composeBody').value = data.body || `Halo Bapak/Ibu,\n\n[Tulis isi pesan Anda di sini]`;
                    document.getElementById('composeCc').value = data.cc || '';
                    document.getElementById('composeBcc').value = data.bcc || '';
                    document.getElementById('composeTemplateSelect').value = data.template || 'blank';
                    if (data.cc || data.bcc) {
                        document.getElementById('ccBccContainer').classList.remove('d-none');
                        document.getElementById('ccBccToggleText').innerText = 'Sembunyikan CC & BCC';
                    }
                    restored = true;
                }
            } catch (e) {
                console.error(e);
            }
        }

        if (!restored) {
            document.getElementById('composeRecipientEmail').value = prefillRecipient;
            document.getElementById('composeSubject').value = prefillSubject;
            document.getElementById('composeTemplateSelect').value = 'blank';
            document.getElementById('ccBccContainer').classList.add('d-none');
            document.getElementById('ccBccToggleText').innerText = 'Tampilkan CC & BCC';
            document.getElementById('toggleSignatureCheckbox').checked = true;

            if (prefillBody) {
                document.getElementById('composeBody').value = prefillBody;
            } else {
                document.getElementById('composeBody').value = `Halo Bapak/Ibu,\n\n[Tulis isi pesan Anda di sini]`;
            }
        }

        renderComposeSignaturePreview();
        document.getElementById('floatingComposeDock').classList.add('d-none');
        const modal = new bootstrap.Modal(document.getElementById('modalComposeEmail'));
        modal.show();
    }

    // Generate HTML Email Signature
    function generateHtmlSignature(layout, color, name, title, phone, email, tagline) {
        const companyName = "PT Reftech Indonesia";
        const website = "www.reftech.id";
        const address = "Grand Galaxy City, Bekasi Selatan, Jawa Barat";
        const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

        if (layout === 'sig_custom_html') {
            const raw = document.getElementById('customHtmlCodeInput') ? document.getElementById('customHtmlCodeInput').value : customHtmlCode;
            return raw
                .replace(/{name}/g, name)
                .replace(/{title}/g, title)
                .replace(/{phone}/g, phone)
                .replace(/{email}/g, email)
                .replace(/{company}/g, companyName)
                .replace(/{website}/g, website)
                .replace(/{address}/g, address);
        } else if (layout === 'sig_corporate') {
            return `
            <table cellpadding="0" cellspacing="0" border="0" style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #334155; line-height: 1.4; max-width: 540px; margin-top: 15px;">
                <tr>
                    <td style="border-left: 4px solid ${color}; padding-left: 14px; vertical-align: top;">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="vertical-align: middle; padding-right: 14px;">
                                    <div style="width: 52px; height: 52px; border-radius: 50%; background-color: ${color}; color: #ffffff; font-weight: bold; font-size: 18px; text-align: center; line-height: 52px; font-family: Arial, sans-serif;">
                                        ${initials}
                                    </div>
                                </td>
                                <td style="vertical-align: middle;">
                                    <div style="font-size: 16px; font-weight: 700; color: #0f172a; letter-spacing: -0.2px;">${name}</div>
                                    <div style="font-size: 13px; font-weight: 600; color: ${color}; margin-top: 2px;">${title} | ${companyName}</div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding-top: 10px;">
                                    <table cellpadding="0" cellspacing="0" border="0" style="font-size: 12px; color: #475569;">
                                        <tr>
                                            <td style="padding: 2px 0;">📞 <strong>Phone/WA:</strong> <a href="https://wa.me/${phone.replace(/[^0-9]/g, '')}" style="color: #0f172a; text-decoration: none; font-weight: 600;">${phone}</a></td>
                                            <td style="padding: 2px 14px;">✉️ <strong>Email:</strong> <a href="mailto:${email}" style="color: ${color}; text-decoration: none;">${email}</a></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 2px 0;">🌐 <strong>Web:</strong> <a href="https://${website}" target="_blank" style="color: ${color}; text-decoration: none;">${website}</a></td>
                                            <td style="padding: 2px 14px;">📍 <strong>Office:</strong> ${address}</td>
                                        </tr>
                                    </table>
                                    ${tagline ? `<div style="margin-top: 8px; padding-top: 6px; border-top: 1px dashed #cbd5e1; font-size: 11px; color: #64748b; font-style: italic;">🛡️ ${tagline}</div>` : ''}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            `;
        } else if (layout === 'sig_minimal') {
            return `
            <table cellpadding="0" cellspacing="0" border="0" style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #334155; line-height: 1.4; max-width: 500px; margin-top: 15px;">
                <tr>
                    <td style="padding-bottom: 6px;">
                        <span style="font-size: 15px; font-weight: 700; color: #0f172a;">${name}</span>
                        <span style="color: #94a3b8; margin: 0 6px;">|</span>
                        <span style="font-weight: 600; color: ${color};">${title}</span>
                    </td>
                </tr>
                <tr>
                    <td style="color: #475569; padding-bottom: 6px;">
                        <strong>${companyName}</strong> &bull; <span>${address}</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 11.5px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 6px;">
                        <span>📱 ${phone}</span>
                        <span style="color: #cbd5e1; margin: 0 8px;">&bull;</span>
                        <span>✉️ <a href="mailto:${email}" style="color: ${color}; text-decoration: none;">${email}</a></span>
                        <span style="color: #cbd5e1; margin: 0 8px;">&bull;</span>
                        <span>🌐 <a href="https://${website}" style="color: ${color}; text-decoration: none;">${website}</a></span>
                    </td>
                </tr>
            </table>
            `;
        } else if (layout === 'sig_executive') {
            return `
            <table cellpadding="0" cellspacing="0" border="0" style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 13px; color: #334155; line-height: 1.4; max-width: 550px; margin-top: 15px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px;">
                <tr>
                    <td style="vertical-align: top; padding-right: 14px; width: 60px;">
                        <div style="width: 56px; height: 56px; border-radius: 10px; background-color: ${color}; color: #ffffff; font-weight: 800; font-size: 20px; text-align: center; line-height: 56px; font-family: Arial, sans-serif; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                            ${initials}
                        </div>
                    </td>
                    <td style="vertical-align: top;">
                        <div style="font-size: 16px; font-weight: 800; color: #0f172a;">${name}</div>
                        <div style="font-size: 12px; font-weight: 600; color: ${color}; text-transform: uppercase; letter-spacing: 0.5px;">${title} &bull; ${companyName}</div>
                        <div style="margin-top: 6px; font-size: 12px; color: #475569;">
                            <div>📞 <a href="https://wa.me/${phone.replace(/[^0-9]/g, '')}" style="color: #0f172a; text-decoration: none; font-weight: 600;">${phone}</a> &nbsp;|&nbsp; ✉️ <a href="mailto:${email}" style="color: ${color}; text-decoration: none;">${email}</a></div>
                            <div style="margin-top: 2px;">🌐 <a href="https://${website}" style="color: ${color}; text-decoration: none;">${website}</a> &nbsp;|&nbsp; 📍 ${address}</div>
                        </div>
                        <div style="margin-top: 8px; display: inline-block; background: rgba(13, 148, 136, 0.1); color: #0d9488; font-size: 10.5px; font-weight: 700; padding: 3px 8px; border-radius: 20px;">
                            ✓ CERTIFIED IT HARDWARE & REFURBISHED PROVIDER
                        </div>
                    </td>
                </tr>
            </table>
            `;
        }
    }

    // Switch Folder
    function switchFolder(folder, el) {
        activeFolder = folder;
        document.querySelectorAll('.mailbox-nav-item').forEach(item => item.classList.remove('active'));
        if (el) el.classList.add('active');

        const listSection = document.getElementById('mailboxListSection');
        const detailSection = document.getElementById('mailDetailSection');
        const templatesSection = document.getElementById('templatesSection');

        if (folder === 'templates') {
            listSection.classList.add('d-none');
            detailSection.classList.add('d-none');
            templatesSection.classList.remove('d-none');
            return;
        } else {
            listSection.classList.remove('d-none');
            detailSection.classList.remove('d-none');
            templatesSection.classList.add('d-none');

            activeEmailId = null;
            const emptyEl = document.getElementById('detailEmptyState');
            const activeEl = document.getElementById('detailActiveContent');
            if (emptyEl) emptyEl.classList.remove('d-none');
            if (activeEl) activeEl.classList.add('d-none');
        }

        renderEmailList(folder);
    }

    // Render Email list
    function renderEmailList(folder, filterTag = null, searchQuery = '') {
        const container = document.getElementById('mailListContainer');
        container.innerHTML = '';

        const filtered = allEmails.filter(email => {
            let matchFolder = false;
            if (folder === 'all') matchFolder = true;
            else if (folder === 'starred') matchFolder = !!email.is_starred;
            else matchFolder = email.folder === folder;

            const matchTag = !filterTag || filterTag === 'all' || (email.tag && email.tag.toLowerCase().includes(filterTag.toLowerCase()));
            const query = searchQuery.toLowerCase();
            const matchSearch = !query || 
                (email.recipient_name && email.recipient_name.toLowerCase().includes(query)) ||
                (email.recipient_email && email.recipient_email.toLowerCase().includes(query)) ||
                (email.sender_name && email.sender_name.toLowerCase().includes(query)) ||
                (email.sender_email && email.sender_email.toLowerCase().includes(query)) ||
                (email.subject && email.subject.toLowerCase().includes(query)) ||
                (email.body && email.body.toLowerCase().includes(query));

            return matchFolder && matchTag && matchSearch;
        });

        if (filtered.length === 0) {
            container.innerHTML = `
                <div class="text-center p-5 text-muted">
                    <i class="mdi mdi-email-open-outline fs-1 mb-2 text-secondary"></i>
                    <p class="mb-0 fw-semibold">Tidak ada email di folder ini.</p>
                    <span class="small">Klik <strong>Sync Email</strong> untuk menarik email dari server.</span>
                </div>
            `;
            document.getElementById('detailSubject').innerText = 'Tidak Ada Pesan Dipilih';
            document.getElementById('detailPlainText').innerText = 'Silakan pilih pesan dari daftar di sebelah kiri untuk membaca isinya.';
            document.getElementById('detailHtmlFrame').srcdoc = '';
            document.getElementById('detailSender').innerText = '-';
            document.getElementById('detailRecipient').innerText = '-';
            document.getElementById('detailDate').innerText = '-';
            document.getElementById('detailAttachmentArea').classList.add('d-none');
            return;
        }

        filtered.forEach((mail) => {
            const isSelected = mail.id === activeEmailId;
            const isInbox = mail.folder === 'inbox';
            const displayName = isInbox ? (mail.sender_name || 'Pengirim') : (mail.recipient_name || 'Penerima');
            const displayEmail = isInbox ? mail.sender_email : mail.recipient_email;

            const div = document.createElement('div');
            div.className = `mail-item ${isSelected ? 'selected' : ''} ${!mail.is_read ? 'unread' : ''}`;
            div.dataset.id = mail.id;
            div.onclick = () => selectEmail(mail.id);

            div.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="d-flex align-items-center gap-1 text-truncate" style="max-width: 210px;">
                        <i class="mdi mdi-star ${mail.is_starred ? 'text-warning' : 'text-muted'} me-1" style="font-size: 0.95rem;"></i>
                        <span class="mail-title fw-semibold text-truncate">${displayName}</span>
                    </div>
                    <span class="small text-muted" style="font-size: 0.75rem;">${mail.date}</span>
                </div>
                <div class="small text-muted text-truncate mb-1" style="font-size: 0.8rem;">
                    ${isInbox ? 'Dari: ' : 'Kepada: '}${displayEmail}
                </div>
                <div class="mail-subject fw-medium text-dark text-truncate">${mail.subject}</div>
                <div class="mail-snippet text-muted text-truncate small">${mail.preview}</div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="badge bg-label-${mail.tag_color || 'primary'} small" style="font-size: 0.7rem;">${mail.tag}</span>
                    <span class="text-muted small" style="font-size: 0.72rem;">${mail.time || ''}</span>
                </div>
            `;
            container.appendChild(div);
        });

        if (activeEmailId !== null) {
            const currentSelected = filtered.find(e => parseInt(e.id) === parseInt(activeEmailId));
            if (!currentSelected) {
                activeEmailId = null;
                const emptyEl = document.getElementById('detailEmptyState');
                const activeEl = document.getElementById('detailActiveContent');
                if (emptyEl) emptyEl.classList.remove('d-none');
                if (activeEl) activeEl.classList.add('d-none');
            }
        } else {
            const emptyEl = document.getElementById('detailEmptyState');
            const activeEl = document.getElementById('detailActiveContent');
            if (emptyEl) emptyEl.classList.remove('d-none');
            if (activeEl) activeEl.classList.add('d-none');
        }
    }

    let currentDetailViewMode = 'html';

    function switchDetailViewMode(mode) {
        currentDetailViewMode = mode;
        const htmlCont = document.getElementById('detailHtmlContainer');
        const textCont = document.getElementById('detailPlainText');
        const btnHtml = document.getElementById('btnViewHtml');
        const btnPlain = document.getElementById('btnViewPlain');

        if (!htmlCont || !textCont) return;

        if (mode === 'html') {
            htmlCont.classList.remove('d-none');
            textCont.classList.add('d-none');
            if (btnHtml && btnPlain) {
                btnHtml.className = 'btn btn-xs btn-primary active';
                btnPlain.className = 'btn btn-xs btn-outline-secondary';
            }
            const iframe = document.getElementById('detailHtmlFrame');
            if (iframe) autoResizeIframe(iframe);
        } else {
            htmlCont.classList.add('d-none');
            textCont.classList.remove('d-none');
            if (btnHtml && btnPlain) {
                btnHtml.className = 'btn btn-xs btn-outline-secondary';
                btnPlain.className = 'btn btn-xs btn-primary active';
            }
        }
    }

    function autoResizeIframe(iframe) {
        try {
            if (iframe && iframe.contentWindow && iframe.contentWindow.document && iframe.contentWindow.document.body) {
                const doc = iframe.contentWindow.document;
                const height = Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight, 450);
                iframe.style.height = (height + 30) + 'px';
            }
        } catch(e) {}
    }

    // Select and display single email in detail pane
    function selectEmail(id) {
        if (!id) return;
        activeEmailId = parseInt(id);
        document.querySelectorAll('.mail-item').forEach(el => {
            el.classList.toggle('selected', parseInt(el.dataset.id) === activeEmailId);
        });

        const mail = allEmails.find(e => parseInt(e.id) === activeEmailId);
        if (!mail) {
            console.warn('Mail not found for id:', id);
            return;
        }

        const emptyEl = document.getElementById('detailEmptyState');
        const activeEl = document.getElementById('detailActiveContent');
        if (emptyEl) emptyEl.classList.add('d-none');
        if (activeEl) activeEl.classList.remove('d-none');

        const isInbox = mail.folder === 'inbox';
        if (!mail.is_read) {
            mail.is_read = true;
            const itemEl = document.querySelector(`.mail-item[data-id="${id}"]`);
            if (itemEl) itemEl.classList.remove('unread');

            const unreadCount = allEmails.filter(e => e.folder === 'inbox' && !e.is_read).length;
            const totalInbox = allEmails.filter(e => e.folder === 'inbox').length;
            if (document.getElementById('badgeInboxCount')) {
                document.getElementById('badgeInboxCount').innerText = unreadCount > 0 ? unreadCount : totalInbox;
            }
            if (document.getElementById('statInboxLabel')) {
                document.getElementById('statInboxLabel').innerText = `${totalInbox} Pesan (${unreadCount} Baru)`;
            }

            fetch('{{ route("sales.mailbox.mark-read") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ id: id })
            }).catch(e => console.error(e));
        }
        const senderText = isInbox ? `${mail.sender_name} <${mail.sender_email}>` : `${currentUserName} <${currentUserEmail}>`;
        const recipientText = isInbox ? `${currentUserName} <${currentUserEmail}>` : `${mail.recipient_name} <${mail.recipient_email}>`;

        document.getElementById('detailSubject').innerText = mail.subject;
        document.getElementById('detailAvatar').innerText = (isInbox ? (mail.sender_name || 'P') : (mail.recipient_name || 'K')).substring(0, 2).toUpperCase();
        document.getElementById('detailSender').innerText = senderText;
        document.getElementById('detailRecipient').innerText = recipientText;
        document.getElementById('detailDate').innerText = `${mail.date} (${mail.time || 'WIB'})`;
        document.getElementById('detailTag').innerText = mail.tag || 'Sales';
        document.getElementById('detailTag').className = `badge bg-label-${mail.tag_color || 'primary'} mt-1`;
        document.getElementById('detailStatusBadge').innerText = mail.status;
        document.getElementById('detailStatusBadge').className = `badge ${mail.status_badge}`;

        // Render Message Body (Rich HTML Template vs Plain text)
        const textElem = document.getElementById('detailPlainText');
        const frameElem = document.getElementById('detailHtmlFrame');
        const viewModeBar = document.getElementById('viewModeBar');

        const rawBodyText = mail.body || (mail.body_html ? mail.body_html.replace(/<[^>]*>?/gm, '') : '');
        textElem.innerText = rawBodyText;

        const hasRichHtml = mail.body_html && mail.body_html.trim().length > 0 && (mail.body_html.includes('<') || mail.body_html.includes('&'));

        if (hasRichHtml) {
            viewModeBar.classList.remove('d-none');
            let fullHtml = mail.body_html;
            const metaReferrer = '<meta name="referrer" content="no-referrer">';
            if (fullHtml.includes('<head>') || fullHtml.includes('<head ')) {
                fullHtml = fullHtml.replace(/<head([^>]*)>/i, '<head$1>' + metaReferrer);
            } else if (!fullHtml.includes('<html') && !fullHtml.includes('<body')) {
                fullHtml = `<!DOCTYPE html><html><head><meta charset="utf-8">${metaReferrer}<meta name="viewport" content="width=device-width, initial-scale=1.0"><style>body{font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #1e293b; padding: 16px; margin: 0; word-break: break-word; background: #ffffff;} img{max-width: 100%; height: auto;} table{max-width: 100% !important;}</style></head><body>${fullHtml}</body></html>`;
            }
            frameElem.srcdoc = fullHtml;
            switchDetailViewMode('html');
        } else {
            viewModeBar.classList.add('d-none');
            frameElem.srcdoc = '';
            switchDetailViewMode('plain');
        }

        // Star button
        const starBtn = document.getElementById('detailStarBtn');
        starBtn.classList.toggle('starred', !!mail.is_starred);

        // Attachments
        const attachArea = document.getElementById('detailAttachmentArea');
        const attachList = document.getElementById('attachmentItemsList');
        if (mail.has_attachment && mail.attachments && mail.attachments.length > 0) {
            attachArea.classList.remove('d-none');
            attachList.innerHTML = '';
            mail.attachments.forEach(att => {
                const iconClass = att.ext === 'xlsx' || att.ext === 'xls' ? 'mdi-file-excel-box text-success' : 'mdi-file-pdf-box text-danger';
                const div = document.createElement('div');
                div.className = 'd-inline-flex align-items-center p-2 bg-white rounded border shadow-sm';
                div.innerHTML = `
                    <i class="mdi ${iconClass} fs-3 me-2"></i>
                    <div class="me-3">
                        <span class="d-block fw-semibold small text-dark">${att.name}</span>
                        <span class="text-muted small" style="font-size: 0.75rem;">${att.size}</span>
                    </div>
                    <a href="${att.url || 'javascript:void(0);'}" target="_blank" class="btn btn-xs btn-outline-primary">
                        <i class="mdi mdi-download"></i> Unduh
                    </a>
                `;
                attachList.appendChild(div);
            });
        } else {
            attachArea.classList.add('d-none');
        }
    }

    // Toggle star via AJAX
    function toggleDetailStar() {
        const mail = allEmails.find(e => e.id === activeEmailId);
        if (!mail) return;

        fetch('{{ route("sales.mailbox.toggle-star") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({ id: mail.id })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                mail.is_starred = res.is_starred;
                selectEmail(activeEmailId);
                renderEmailList(activeFolder);
            }
        })
        .catch(err => console.error(err));
    }

    function filterByTag(tag) {
        renderEmailList(activeFolder, tag);
    }

    function searchEmails() {
        const q = document.getElementById('mailSearchInput').value;
        renderEmailList(activeFolder, null, q);
    }

    function refreshList() {
        document.getElementById('mailSearchInput').value = '';
        renderEmailList(activeFolder);
    }

    // Sync incoming & outgoing emails from IMAP server
    function syncMailbox(isSilent = false) {
        const btn = document.getElementById('btnSyncMailbox');
        if (!isSilent) {
            btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyinkronkan Server...';
            btn.disabled = true;
        }

        fetch('{{ route("sales.mailbox.sync") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!isSilent) {
                btn.innerHTML = '<i class="mdi mdi-sync me-1"></i> Sync Email';
                btn.disabled = false;
            }

            if (data.success) {
                const statusBadge = document.getElementById('smtpStatusBadge');
                if (statusBadge) {
                    statusBadge.className = 'badge bg-label-success rounded-pill d-inline-flex align-items-center';
                    statusBadge.innerHTML = `<i class="mdi mdi-check-circle-outline text-success me-1"></i> SMTP & IMAP Aktif (${currentUserEmail})`;
                }

                if (data.emails && data.emails.length > 0) {
                    allEmails = data.emails;
                    if (document.getElementById('badgeInboxCount')) {
                        document.getElementById('badgeInboxCount').innerText = data.unread_inbox > 0 ? data.unread_inbox : data.total_inbox;
                    }
                    if (document.getElementById('statInboxLabel')) {
                        document.getElementById('statInboxLabel').innerText = `${data.total_inbox} Pesan (${data.unread_inbox} Baru)`;
                    }
                    if (document.getElementById('badgeSentCount')) {
                        document.getElementById('badgeSentCount').innerText = data.total_sent;
                    }
                    if (document.getElementById('statSentLabel')) {
                        document.getElementById('statSentLabel').innerText = `${data.total_sent} Email`;
                    }
                    if (document.getElementById('badgeStarredCount')) {
                        document.getElementById('badgeStarredCount').innerText = data.total_starred;
                    }
                    if (document.getElementById('statStarredLabel')) {
                        document.getElementById('statStarredLabel').innerText = `${data.total_starred} Pesan`;
                    }
                    if (document.getElementById('badgeTrashCount')) {
                        document.getElementById('badgeTrashCount').innerText = data.total_trash;
                    }
                    renderEmailList(activeFolder);
                }

                if (!isSilent) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sinkronisasi Server Sukses!',
                            text: data.message,
                            timer: 2500,
                            showConfirmButton: false
                        });
                    } else {
                        alert(data.message);
                    }
                }
            } else {
                const statusBadge = document.getElementById('smtpStatusBadge');
                if (statusBadge) {
                    statusBadge.className = 'badge bg-label-danger rounded-pill d-inline-flex align-items-center cursor-pointer';
                    statusBadge.innerHTML = `<i class="mdi mdi-alert-circle-outline text-danger me-1"></i> Gagal Terkoneksi ke Server`;
                }

                if (!isSilent) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian Sinkronisasi Server',
                            text: data.message
                        });
                    } else {
                        alert(data.message);
                    }
                }
            }
        })
        .catch(err => {
            const statusBadge = document.getElementById('smtpStatusBadge');
            if (statusBadge) {
                statusBadge.className = 'badge bg-label-danger rounded-pill d-inline-flex align-items-center cursor-pointer';
                statusBadge.innerHTML = `<i class="mdi mdi-alert-circle-outline text-danger me-1"></i> Gagal Terkoneksi ke Server`;
            }

            if (!isSilent) {
                btn.innerHTML = '<i class="mdi mdi-sync me-1"></i> Sync Email';
                btn.disabled = false;
            }
            console.error(err);
        });
    }

    function toggleCcBcc() {
        const el = document.getElementById('ccBccContainer');
        el.classList.toggle('d-none');
        const isHidden = el.classList.contains('d-none');
        document.getElementById('ccBccToggleText').innerText = isHidden ? 'Tampilkan CC & BCC' : 'Sembunyikan CC & BCC';
    }

    function toggleComposeSignaturePreview() {
        const isChecked = document.getElementById('toggleSignatureCheckbox').checked;
        const box = document.getElementById('composeSignaturePreviewBox');
        box.classList.toggle('d-none', !isChecked);
    }

    function renderComposeSignaturePreview() {
        const sigHtml = generateHtmlSignature(selectedSigLayout, selectedSigColor, currentUserName, currentUserTitle, currentUserPhone, currentUserEmail, currentTagline);
        document.getElementById('composeSigHtmlContainer').innerHTML = sigHtml;

        const layoutLabels = {
            'sig_corporate': 'Corporate Modern',
            'sig_minimal': 'Clean Minimalist',
            'sig_executive': 'Executive Card',
            'sig_custom_html': 'Custom Raw HTML'
        };
        document.getElementById('composeSigModelName').innerText = layoutLabels[selectedSigLayout] || 'Custom Signature';
    }

    function insertTagIntoBody(tag) {
        const textarea = document.getElementById('composeBody');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + tag + text.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + tag.length;
        triggerAutoSave();
    }

    function pickContact(email, name, company) {
        document.getElementById('composeRecipientEmail').value = email;
        onComposeTemplateChanged(name, company);
        triggerAutoSave();
    }

    function onComposeTemplateChanged(overrideName = '', overrideCompany = '') {
        const tplId = document.getElementById('composeTemplateSelect').value;
        if (!tplId) return;

        const tpl = allTemplates.find(t => t.id === tplId);
        if (!tpl) return;

        const recipientInput = document.getElementById('composeRecipientEmail').value;
        const clientName = overrideName || (recipientInput ? recipientInput.split('@')[0] : 'Bapak/Ibu Pimpinan');
        const companyName = overrideCompany || 'Perusahaan Mitra';

        let subject = tpl.subject
            .replace(/{company_name}/g, companyName)
            .replace(/{client_name}/g, clientName);

        let body = tpl.body
            .replace(/{client_name}/g, clientName)
            .replace(/{company_name}/g, companyName)
            .replace(/{sales_name}/g, currentUserName);

        document.getElementById('composeSubject').value = subject;
        document.getElementById('composeBody').value = body;
        triggerAutoSave();
    }

    function useTemplate(tplId) {
        document.getElementById('composeTemplateSelect').value = tplId;
        onComposeTemplateChanged();
        const modal = new bootstrap.Modal(document.getElementById('modalComposeEmail'));
        modal.show();
    }

    function openCreateTemplateModal() {
        alert('Fitur tambah master template kustom akan tersedia di modul template management.');
    }

    // Send Real Email via Backend AJAX
    function sendUniversalEmail() {
        const recipient = document.getElementById('composeRecipientEmail').value.trim();
        const subject = document.getElementById('composeSubject').value.trim();
        const body = document.getElementById('composeBody').value.trim();
        const cc = document.getElementById('composeCc').value.trim();
        const bcc = document.getElementById('composeBcc').value.trim();
        const includeSignature = document.getElementById('toggleSignatureCheckbox').checked;

        if (!recipient || !subject) {
            alert('Mohon isi Alamat Email Tujuan dan Subjek email!');
            return;
        }

        const btn = document.getElementById('btnSendEmailSubmit');
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Mengirim Email...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('recipient_email', recipient);
        formData.append('subject', subject);
        formData.append('body', body);
        formData.append('cc', cc);
        formData.append('bcc', bcc);
        formData.append('include_signature', includeSignature ? 1 : 0);
        formData.append('signature_html', generateHtmlSignature(selectedSigLayout, selectedSigColor, currentUserName, currentUserTitle, currentUserPhone, currentUserEmail, currentTagline));

        const fileInput = document.getElementById('composeFile');
        if (fileInput.files.length > 0) {
            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append('attachments[]', fileInput.files[i]);
            }
        }

        fetch('{{ route("sales.mailbox.send") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = '<i class="mdi mdi-send me-1"></i> Kirim Pesan Sekarang';
            btn.disabled = false;

            if (data.success) {
                // Clear local draft
                localStorage.removeItem(DRAFT_STORAGE_KEY);
                document.getElementById('floatingComposeDock').classList.add('d-none');
                bootstrap.Modal.getInstance(document.getElementById('modalComposeEmail')).hide();

                if (data.data) {
                    allEmails.unshift({
                        id: data.data.id,
                        folder: 'sent',
                        sender_name: data.data.sender_name,
                        sender_email: data.data.sender_email,
                        recipient_name: data.data.recipient_name,
                        recipient_email: data.data.recipient_email,
                        subject: data.data.subject,
                        preview: data.data.preview,
                        body: data.data.body_text,
                        body_html: data.data.body_html,
                        tag: data.data.tag,
                        tag_color: data.data.tag_color,
                        date: 'Baru saja',
                        time: 'Baru saja',
                        status: 'Delivered',
                        status_badge: 'bg-success',
                        has_attachment: data.data.has_attachment,
                        attachments: data.data.attachments || [],
                        is_read: true,
                        is_starred: false
                    });
                }

                switchFolder('sent');
                if (data.data) selectEmail(data.data.id);

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Email Berhasil Dikirim!',
                        text: `Pesan sukses terkirim ke ${recipient} dengan Signature HTML Sales Anda.`,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    alert(`Email berhasil terkirim ke: ${recipient}!`);
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Pengiriman Gagal',
                        text: data.message,
                    });
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(err => {
            btn.innerHTML = '<i class="mdi mdi-send me-1"></i> Kirim Pesan Sekarang';
            btn.disabled = false;
            alert('Terjadi kesalahan jaringan saat mengirim email.');
            console.error(err);
        });
    }

    function sendQuickReply() {
        const replyText = document.getElementById('quickReplyText').value.trim();
        if (!replyText) {
            alert('Tuliskan isi balasan terlebih dahulu.');
            return;
        }

        const mail = allEmails.find(e => e.id === activeEmailId);
        const targetEmail = mail.folder === 'inbox' ? mail.sender_email : mail.recipient_email;

        openComposeModal(targetEmail, 'Re: ' + mail.subject.replace(/^Re:\s*/i, ''), replyText);
    }

    function replyEmail() {
        const mail = allEmails.find(e => e.id === activeEmailId);
        if (!mail) return;

        const targetEmail = mail.folder === 'inbox' ? mail.sender_email : mail.recipient_email;
        const replySubject = 'Re: ' + mail.subject.replace(/^Re:\s*/i, '');
        const replyBody = `Halo ${mail.sender_name},\n\nTerima kasih atas pesan Anda.\n\n\n\n--- Pada ${mail.date}, ${mail.sender_name} menulis: ---\n${mail.body}`;

        openComposeModal(targetEmail, replySubject, replyBody);
    }

    function forwardEmail() {
        const mail = allEmails.find(e => e.id === activeEmailId);
        if (!mail) return;

        const fwdSubject = 'Fwd: ' + mail.subject.replace(/^Fwd:\s*/i, '');
        const fwdBody = `\n\n\n---------- Pesan yang Diteruskan ----------\nDari: ${mail.sender_name} <${mail.sender_email}>\nTanggal: ${mail.date}\nSubjek: ${mail.subject}\n\n${mail.body}`;

        openComposeModal('', fwdSubject, fwdBody);
    }

    function deleteEmail() {
        if (confirm('Pindahkan email ini ke folder Sampah?')) {
            const mail = allEmails.find(e => e.id === activeEmailId);
            if (!mail) return;

            fetch('{{ route("sales.mailbox.delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ id: mail.id })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    mail.folder = 'trash';
                    mail.status = 'Trash';
                    mail.status_badge = 'bg-danger';
                    renderEmailList(activeFolder);
                }
            })
            .catch(err => console.error(err));
        }
    }

    // SMTP & IMAP Quick Presets
    function applySmtpPreset(type) {
        if (type === 'gmail') {
            document.getElementById('smtpHostInput').value = 'smtp.gmail.com';
            document.getElementById('smtpPortInput').value = '587';
            document.getElementById('smtpEncryptionInput').value = 'tls';
            document.getElementById('imapHostInput').value = 'imap.gmail.com';
            document.getElementById('imapPortInput').value = '993';
            document.getElementById('imapEncryptionInput').value = 'ssl';
        } else if (type === 'office365') {
            document.getElementById('smtpHostInput').value = 'smtp.office365.com';
            document.getElementById('smtpPortInput').value = '587';
            document.getElementById('smtpEncryptionInput').value = 'tls';
            document.getElementById('imapHostInput').value = 'outlook.office365.com';
            document.getElementById('imapPortInput').value = '993';
            document.getElementById('imapEncryptionInput').value = 'ssl';
        } else if (type === 'cpanel') {
            const email = document.getElementById('smtpFromAddress').value;
            const domain = email.includes('@') ? email.split('@')[1] : 'domain.com';
            document.getElementById('smtpHostInput').value = 'mail.' + domain;
            document.getElementById('smtpPortInput').value = '465';
            document.getElementById('smtpEncryptionInput').value = 'ssl';
            document.getElementById('imapHostInput').value = 'mail.' + domain;
            document.getElementById('imapPortInput').value = '993';
            document.getElementById('imapEncryptionInput').value = 'ssl';
        }
    }

    // Test SMTP Connection Handshake
    function testSmtpConnection() {
        const host = document.getElementById('smtpHostInput').value;
        const port = document.getElementById('smtpPortInput').value;
        const encryption = document.getElementById('smtpEncryptionInput').value;
        const username = document.getElementById('smtpUsernameInput').value;
        const password = document.getElementById('smtpPasswordInput').value;

        const btn = document.getElementById('btnTestSmtpConnection');
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Menghubungkan ke Server...';
        btn.disabled = true;

        fetch('{{ route("sales.mailbox.test-connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                smtp_host: host,
                smtp_port: port,
                smtp_encryption: encryption,
                smtp_username: username,
                smtp_password: password
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = '<i class="mdi mdi-lan-connect me-1"></i> Uji Koneksi (Test Handshake)';
            btn.disabled = false;

            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Koneksi Berhasil!',
                        text: data.message
                    });
                } else {
                    alert(data.message);
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Koneksi Gagal',
                        text: data.message
                    });
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(err => {
            btn.innerHTML = '<i class="mdi mdi-lan-connect me-1"></i> Uji Koneksi (Test Handshake)';
            btn.disabled = false;
            alert('Gagal menghubungi server untuk pengujian koneksi.');
            console.error(err);
        });
    }

    // Save SMTP & IMAP Configuration via AJAX
    function saveSmtpConfiguration() {
        const fromName = document.getElementById('smtpFromName').value;
        const fromAddress = document.getElementById('smtpFromAddress').value;
        const host = document.getElementById('smtpHostInput').value;
        const port = document.getElementById('smtpPortInput').value;
        const encryption = document.getElementById('smtpEncryptionInput').value;
        const imapHost = document.getElementById('imapHostInput').value;
        const imapPort = document.getElementById('imapPortInput').value;
        const imapEncryption = document.getElementById('imapEncryptionInput').value;
        const username = document.getElementById('smtpUsernameInput').value;
        const password = document.getElementById('smtpPasswordInput').value;

        const btn = document.getElementById('btnSaveSmtpSetting');
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...';
        btn.disabled = true;

        fetch('{{ route("sales.mailbox.settings") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                from_name: fromName,
                from_address: fromAddress,
                smtp_host: host,
                smtp_port: port,
                smtp_encryption: encryption,
                imap_host: imapHost,
                imap_port: imapPort,
                imap_encryption: imapEncryption,
                smtp_username: username,
                imap_username: username,
                smtp_password: password,
                imap_password: password,
                signature_layout: selectedSigLayout,
                signature_color: selectedSigColor,
                signature_html: generateHtmlSignature(selectedSigLayout, selectedSigColor, currentUserName, currentUserTitle, currentUserPhone, currentUserEmail, currentTagline)
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = '<i class="mdi mdi-content-save-check me-1"></i> Simpan Konfigurasi';
            btn.disabled = false;

            if (data.success) {
                currentUserName = fromName;
                currentUserEmail = fromAddress;
                document.getElementById('sidebarUserName').innerText = fromName;
                document.getElementById('activeMailHostLabel').innerText = host;

                bootstrap.Modal.getInstance(document.getElementById('modalSmtpConfig')).hide();

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Pengaturan Disimpan!',
                        text: data.message,
                        timer: 2500,
                        showConfirmButton: false
                    });
                } else {
                    alert(data.message);
                }

                // Auto trigger sync after save
                setTimeout(() => {
                    syncMailbox();
                }, 500);
            }
        })
        .catch(err => {
            btn.innerHTML = '<i class="mdi mdi-content-save-check me-1"></i> Simpan Konfigurasi';
            btn.disabled = false;
            console.error(err);
        });
    }

    // Signature Studio Functions
    function selectSigLayout(layoutId, el) {
        selectedSigLayout = layoutId;
        document.querySelectorAll('.sig-template-radio-card').forEach(c => c.classList.remove('active'));
        if (el) el.classList.add('active');

        const stdControls = document.getElementById('standardTemplateControls');
        const customControls = document.getElementById('customHtmlControls');

        if (layoutId === 'sig_custom_html') {
            stdControls.classList.add('d-none');
            customControls.classList.remove('d-none');
        } else {
            stdControls.classList.remove('d-none');
            customControls.classList.add('d-none');
        }

        refreshStudioLivePreview();
    }

    function selectSigColor(colorHex, el) {
        selectedSigColor = colorHex;
        document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
        if (el) el.classList.add('active');
        refreshStudioLivePreview();
    }

    function onCustomHtmlCodeChanged() {
        refreshStudioLivePreview();
    }

    function loadSampleHtmlBoilerplate() {
        document.getElementById('customHtmlCodeInput').value = customHtmlCode;
        refreshStudioLivePreview();
    }

    function insertTagIntoCustomHtml(tag) {
        const textarea = document.getElementById('customHtmlCodeInput');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + tag + text.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + tag.length;
        refreshStudioLivePreview();
    }

    function refreshStudioLivePreview() {
        const name = document.getElementById('studioNameInput').value || currentUserName;
        const title = document.getElementById('studioTitleInput').value || currentUserTitle;
        const phone = document.getElementById('studioPhoneInput').value || currentUserPhone;
        const email = document.getElementById('studioEmailInput').value || currentUserEmail;
        const tagline = document.getElementById('studioTaglineInput').value || '';

        const previewHtml = generateHtmlSignature(selectedSigLayout, selectedSigColor, name, title, phone, email, tagline);
        document.getElementById('studioLivePreviewContainer').innerHTML = previewHtml;
    }

    function copySignatureHtml() {
        const name = document.getElementById('studioNameInput').value || currentUserName;
        const title = document.getElementById('studioTitleInput').value || currentUserTitle;
        const phone = document.getElementById('studioPhoneInput').value || currentUserPhone;
        const email = document.getElementById('studioEmailInput').value || currentUserEmail;
        const tagline = document.getElementById('studioTaglineInput').value || '';

        const code = generateHtmlSignature(selectedSigLayout, selectedSigColor, name, title, phone, email, tagline);
        navigator.clipboard.writeText(code).then(() => {
            alert('Kode HTML Signature berhasil disalin ke clipboard!');
        });
    }

    function applySignatureToMyAccount() {
        currentUserName = document.getElementById('studioNameInput').value || currentUserName;
        currentUserTitle = document.getElementById('studioTitleInput').value || currentUserTitle;
        currentUserPhone = document.getElementById('studioPhoneInput').value || currentUserPhone;
        currentTagline = document.getElementById('studioTaglineInput').value || '';

        document.getElementById('sidebarUserName').innerText = currentUserName;
        document.getElementById('sidebarUserTitle').innerText = currentUserTitle;

        const layoutLabels = {
            'sig_corporate': 'Corporate Modern',
            'sig_minimal': 'Clean Minimalist',
            'sig_executive': 'Executive Card',
            'sig_custom_html': 'Custom Raw HTML'
        };
        document.getElementById('composeSigModelName').innerText = layoutLabels[selectedSigLayout] || 'Custom Signature';

        const btn = document.getElementById('btnApplySigSubmit');
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...';
        btn.disabled = true;

        const sigHtml = generateHtmlSignature(selectedSigLayout, selectedSigColor, currentUserName, currentUserTitle, currentUserPhone, currentUserEmail, currentTagline);

        fetch('{{ route("sales.mailbox.settings") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                from_name: currentUserName,
                signature_layout: selectedSigLayout,
                signature_color: selectedSigColor,
                signature_html: sigHtml
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = '<i class="mdi mdi-check-bold me-1"></i> Terapkan & Simpan Signature';
            btn.disabled = false;

            renderComposeSignaturePreview();
            selectEmail(activeEmailId);

            bootstrap.Modal.getInstance(document.getElementById('modalSignatureStudio')).hide();

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Signature Berhasil Diterapkan & Disimpan!',
                    text: 'Desain signature HTML baru Anda telah aktif di database dan otomatis disematkan pada setiap pengiriman email.',
                    timer: 2500,
                    showConfirmButton: false
                });
            } else {
                alert('Desain Signature HTML baru berhasil disimpan dan diterapkan!');
            }
        })
        .catch(err => {
            btn.innerHTML = '<i class="mdi mdi-check-bold me-1"></i> Terapkan & Simpan Signature';
            btn.disabled = false;
            console.error(err);
        });
    }

    // Auto-Sync background scheduler every 60 seconds (1 minute)
    setInterval(() => {
        syncMailbox(true);
    }, 60000);
</script>
@endpush
