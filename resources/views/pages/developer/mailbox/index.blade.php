@extends('layouts.sales.app')
@section('title', 'Developer — Mailbox Central Management')

@push('after-style')
<style>
    .dev-mailbox-card {
        border-radius: 12px;
        transition: all 0.2s ease-in-out;
    }
    .dev-mailbox-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(105, 108, 255, 0.08) !important;
    }
    .status-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .status-pulse-dot.active {
        background-color: #71dd37;
        box-shadow: 0 0 0 3px rgba(113, 221, 55, 0.25);
    }
    .status-pulse-dot.inactive {
        background-color: #ffab00;
        box-shadow: 0 0 0 3px rgba(255, 171, 0, 0.25);
    }
    .user-avatar-initial {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 15px;
    }
    .table-mailbox td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center py-3 mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold mb-1 d-flex align-items-center">
                <i class="mdi mdi-email-sync-outline text-primary me-2 fs-3"></i>
                <span class="text-muted fw-light">Developer /</span> Mailbox Central Management
            </h4>
            <div class="text-muted small">
                Pusat konfigurasi kredensial SMTP & IMAP, pengujian koneksi handshake server, dan monitoring sinkronisasi email seluruh tim Sales.
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-label-success rounded-pill px-3 py-2 fs-6">
                <i class="mdi mdi-server-network me-1"></i> Server: {{ $stats['server_host'] }} (SSL: 465/993)
            </span>
            <button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="openConfigureModal()">
                <i class="mdi mdi-plus-circle-outline me-1"></i> Konfigurasi Mailbox User
            </button>
        </div>
    </div>

    <!-- Quick Metrics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm dev-mailbox-card bg-label-primary p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded-circle bg-primary text-white">
                            <i class="mdi mdi-email-check-outline fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <span class="d-block text-muted small">Akun Mailbox Ditambahkan</span>
                        <h4 class="mb-0 fw-bold text-primary">{{ $stats['total_configured'] }} Akun</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm dev-mailbox-card bg-label-success p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded-circle bg-success text-white">
                            <i class="mdi mdi-check-decagram-outline fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <span class="d-block text-muted small">Mailbox Aktif</span>
                        <h4 class="mb-0 fw-bold text-success">{{ $stats['active_configured'] }} Akun</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm dev-mailbox-card bg-label-warning p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded-circle bg-warning text-white">
                            <i class="mdi mdi-pause-circle-outline fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <span class="d-block text-muted small">Mailbox Nonaktif</span>
                        <h4 class="mb-0 fw-bold text-warning">{{ $stats['inactive_configured'] }} Akun</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm dev-mailbox-card bg-label-info p-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-md me-3">
                        <span class="avatar-initial rounded-circle bg-info text-white">
                            <i class="mdi mdi-email-multiple-outline fs-4"></i>
                        </span>
                    </div>
                    <div>
                        <span class="d-block text-muted small">Pesan di Database</span>
                        <h4 class="mb-0 fw-bold text-info">{{ $stats['total_messages_db'] }} Email</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main DataTable Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-table-account text-primary fs-4"></i>
                <div>
                    <h5 class="mb-0 fw-bold">Daftar Akun Mailbox yang Dikonfigurasi</h5>
                    <span class="text-muted small">Hanya menampilkan akun yang telah disiapkan konfigurasi emailnya oleh Developer.</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm" style="max-width: 240px;">
                    <span class="input-group-text bg-transparent border-end-0"><i class="mdi mdi-magnify"></i></span>
                    <input type="text" id="devTableSearch" class="form-control border-start-0" placeholder="Cari nama / email..." onkeyup="filterDeveloperTable()">
                </div>
                <select class="form-select form-select-sm" id="devStatusFilter" onchange="filterDeveloperTable()" style="width: 140px;">
                    <option value="all">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="openConfigureModal()">
                    <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Akun
                </button>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-mailbox mb-0" id="devMailboxTable">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Pengguna / Sales</th>
                        <th>Role & NIP</th>
                        <th>Webmail & From Name</th>
                        <th>Host & Port Server</th>
                        <th>Status Koneksi</th>
                        <th>Email di DB</th>
                        <th>Terakhir Sinkron</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($configuredUsers as $u)
                        <tr class="dev-user-row" data-name="{{ strtolower($u['name']) }}" data-email="{{ strtolower($u['email']) }}" data-webmail="{{ strtolower($u['from_address']) }}" data-status="{{ $u['is_active'] ? 'active' : 'inactive' }}">
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    @if ($u['image'])
                                        <img src="{{ $u['image'] }}" alt="{{ $u['name'] }}" class="rounded-circle me-3" width="38" height="38" style="object-fit: cover;">
                                    @else
                                        <div class="user-avatar-initial bg-label-primary text-primary me-3">
                                            {{ strtoupper(substr($u['name'], 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <strong class="d-block text-dark">{{ $u['name'] }}</strong>
                                        <span class="text-muted small" style="font-size: 0.78rem;">{{ $u['email'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-label-{{ $u['role'] === 'Sales' ? 'success' : ($u['role'] === 'Admin' ? 'primary' : 'warning') }} rounded-pill mb-1">
                                    {{ $u['role'] }}
                                </span>
                                <div class="text-muted small" style="font-size: 0.72rem;">NIP: {{ $u['nip'] ?: '-' }}</div>
                            </td>
                            <td>
                                @if ($u['is_configured'])
                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 220px;" title="{{ $u['from_address'] }}">
                                        <i class="mdi mdi-email-check-outline text-success me-1"></i> {{ $u['from_address'] }}
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">From: <strong>{{ $u['from_name'] }}</strong></div>
                                @else
                                    <span class="text-muted fst-italic small">Belum di-setting</span>
                                @endif
                            </td>
                            <td>
                                @if ($u['is_configured'])
                                    <div class="small text-dark font-monospace" style="font-size: 0.78rem;">
                                        SMTP: {{ $u['smtp_host'] }}:{{ $u['smtp_port'] }} ({{ strtoupper($u['smtp_encryption']) }})
                                    </div>
                                    <div class="small text-muted font-monospace" style="font-size: 0.75rem;">
                                        IMAP: {{ $u['imap_host'] }}:{{ $u['imap_port'] }} ({{ strtoupper($u['imap_encryption']) }})
                                    </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-label-success rounded-pill d-inline-flex align-items-center">
                                    <span class="status-pulse-dot active me-1"></span> Terkonfigurasi
                                </span>
                            </td>
                            <td>
                                @if ($u['is_configured'])
                                    <span class="badge bg-label-primary fw-bold" title="Inbox: {{ $u['inbox_count'] }} ({{ $u['unread_count'] }} Baru), Sent: {{ $u['sent_count'] }}">
                                        {{ $u['total_messages'] }} Email ({{ $u['unread_count'] }} Baru)
                                    </span>
                                @else
                                    <span class="text-muted small">0</span>
                                @endif
                            </td>
                            <td>
                                @if ($u['last_synced_at'])
                                    <span class="small text-dark d-block fw-semibold">{{ $u['last_synced_at'] }}</span>
                                @else
                                    <span class="text-muted small fst-italic">Belum pernah</span>
                                @endif
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-inline-flex gap-1">
                                    @if ($u['is_configured'])
                                        <button type="button" class="btn btn-xs btn-outline-success" onclick="syncUserMailbox({{ $u['id'] }}, '{{ addslashes($u['name']) }}')" title="Tarik Email Server Sekarang">
                                            <i class="mdi mdi-sync"></i> Sync
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-xs btn-outline-primary" onclick="editUserMailbox({{ $u['id'] }})" title="Konfigurasi Akun SMTP/IMAP">
                                        <i class="mdi mdi-cog-outline"></i> Setting
                                    </button>
                                    @if ($u['is_configured'])
                                        <button type="button" class="btn btn-xs btn-outline-danger" onclick="resetUserMailbox({{ $u['id'] }}, '{{ addslashes($u['name']) }}')" title="Hapus / Reset Akun Mailbox Ini">
                                            <i class="mdi mdi-trash-can-outline"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="avatar avatar-xl bg-label-primary mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border-radius: 50%;">
                                    <i class="mdi mdi-email-plus-outline text-primary fs-2"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Belum Ada Akun Mailbox yang Dikonfigurasi</h6>
                                <p class="small text-muted mb-3" style="max-width: 440px; margin: 0 auto;">
                                    Hanya akun yang Anda tambahkan yang akan muncul di daftar ini dan memiliki akses ke modul Mailbox.
                                </p>
                                <button type="button" class="btn btn-primary btn-sm shadow-sm" onclick="openConfigureModal()">
                                    <i class="mdi mdi-plus-circle-outline me-1"></i> Tambah Akun Mailbox Pertama
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: KONFIGURASI MAILBOX USER OLEH DEVELOPER -->
<div class="modal fade" id="modalDevMailboxConfig" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-2 bg-white rounded text-primary d-flex align-items-center justify-content-center">
                        <i class="mdi mdi-server-network fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title text-white mb-0 fw-bold" id="modalDevMailboxTitle">Konfigurasi Mailbox Pengguna</h5>
                        <span class="small text-white text-opacity-75">Kelola host cPanel, port SSL, dan kredensial akun email tim Sales</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- User Selector -->
                <div class="mb-3 p-3 bg-label-secondary rounded border">
                    <label class="form-label small fw-bold text-dark mb-1">Pilih Akun Sales / Pengguna yang Akan Dikonfigurasi: <span class="text-danger">*</span></label>
                    <select class="form-select form-select-sm" id="devConfigUserId" onchange="onDevUserSelected(this.value)">
                        <option value="">-- Pilih User / Sales --</option>
                        @foreach ($availableUsers as $u)
                            <option value="{{ $u['id'] }}" data-name="{{ $u['name'] }}" data-email="{{ $u['email'] }}">
                                {{ $u['name'] }} ({{ $u['email'] }}) &mdash; {{ $u['role'] }} {{ $u['is_already_configured'] ? '✓ [Terkonfigurasi]' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Webmail cPanel Guide Banner -->
                <div class="mb-3 p-3 bg-label-primary rounded border border-primary border-opacity-25 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-web fs-4 text-primary me-2"></i>
                        <div>
                            <strong class="d-block text-primary small">Standar Server Webmail Hosting Perusahaan</strong>
                            <span class="text-muted" style="font-size: 0.75rem;">SMTP Port 465 (SSL) &bull; IMAP Port 993 (SSL) &bull; Host: <code>srv162.niagahoster.com</code></span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-xs btn-primary shadow-sm" onclick="autoFillDevCpanelPreset()">
                        <i class="mdi mdi-auto-fix me-1"></i> Auto-Fill Host cPanel
                    </button>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Nama Pengirim (From Name): <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="devFromName" placeholder="Nama Lengkap Sales">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Alamat Webmail Resmi: <span class="text-danger">*</span></label>
                        <input type="email" class="form-control form-control-sm" id="devFromAddress" placeholder="support@reftech.id / nama@reftech.id">
                    </div>

                    <!-- SMTP (Kirim) Section -->
                    <div class="col-12"><div class="border-top my-1"></div><strong class="text-primary small d-flex align-items-center"><i class="mdi mdi-send me-1"></i> Pengaturan SMTP (Server Kirim Email Keluar)</strong></div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">SMTP Host: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="devSmtpHost" value="srv162.niagahoster.com" placeholder="srv162.niagahoster.com atau mail.reftech.id">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">SMTP Port: <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" id="devSmtpPort" value="465" placeholder="465">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Enkripsi SMTP:</label>
                        <select class="form-select form-select-sm" id="devSmtpEncryption">
                            <option value="ssl" selected>SSL (Port 465 Rekomendasi)</option>
                            <option value="tls">TLS (Port 587)</option>
                        </select>
                    </div>

                    <!-- IMAP (Terima) Section -->
                    <div class="col-12"><div class="border-top my-1"></div><strong class="text-success small d-flex align-items-center"><i class="mdi mdi-inbox-arrow-down me-1"></i> Pengaturan IMAP (Server Terima / Tarik Email Masuk)</strong></div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">IMAP Host: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="devImapHost" value="srv162.niagahoster.com" placeholder="srv162.niagahoster.com atau mail.reftech.id">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">IMAP Port: <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" id="devImapPort" value="993" placeholder="993">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold">Enkripsi IMAP:</label>
                        <select class="form-select form-select-sm" id="devImapEncryption">
                            <option value="ssl" selected>SSL (Port 993 Rekomendasi)</option>
                            <option value="tls">TLS</option>
                        </select>
                    </div>

                    <!-- Credentials -->
                    <div class="col-12"><div class="border-top my-1"></div><strong class="text-dark small d-flex align-items-center"><i class="mdi mdi-key-outline me-1"></i> Kredensial Login Akun Webmail cPanel</strong></div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Username Login Webmail: <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-control-sm" id="devSmtpUsername" placeholder="username@reftech.id">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Password Akun Webmail: <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <input type="password" class="form-control" id="devSmtpPassword" placeholder="Masukkan password email server">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('devSmtpPassword')">
                                <i class="mdi mdi-eye-outline" id="devSmtpPasswordIcon"></i>
                            </button>
                        </div>
                        <span class="text-muted small" style="font-size: 0.72rem;">Password disimpan dalam bentuk terenkripsi aman (AES-256).</span>
                    </div>

                    <!-- Default Signature Layout -->
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Template Signature Bawaan:</label>
                        <select class="form-select form-select-sm" id="devSignatureLayout">
                            <option value="sig_corporate" selected>Corporate Modern</option>
                            <option value="sig_minimal">Clean Minimalist</option>
                            <option value="sig_executive">Executive Card</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Warna Aksen Signature:</label>
                        <select class="form-select form-select-sm" id="devSignatureColor">
                            <option value="#696cff" selected>Primary Indigo (#696cff)</option>
                            <option value="#0d9488">Teal Enterprise (#0d9488)</option>
                            <option value="#0284c7">Sky Blue (#0284c7)</option>
                            <option value="#ea580c">Warm Amber (#ea580c)</option>
                        </select>
                    </div>
                </div>

                <!-- Test Connection Feedback Box -->
                <div id="devTestConnAlert" class="mt-3 p-3 rounded border small d-none">
                </div>
            </div>
            <div class="modal-footer bg-light py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-outline-info shadow-sm" id="btnDevTestConnection" onclick="testDevMailboxConnection()">
                    <i class="mdi mdi-lightning-bolt me-1"></i> Test Koneksi Handshake (Live)
                </button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-primary shadow-sm" id="btnDevSaveSetting" onclick="saveDevMailboxSetting()">
                        <i class="mdi mdi-content-save-check me-1"></i> Simpan & Terapkan Akun
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('after-script')
<script>
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '{{ csrf_token() }}';
    }

    // Filter Developer DataTable
    function filterDeveloperTable() {
        const q = document.getElementById('devTableSearch').value.toLowerCase();
        const status = document.getElementById('devStatusFilter').value;
        const rows = document.querySelectorAll('.dev-user-row');

        rows.forEach(row => {
            const name = row.dataset.name || '';
            const email = row.dataset.email || '';
            const webmail = row.dataset.webmail || '';
            const rowStatus = row.dataset.status || '';

            const matchQuery = !q || name.includes(q) || email.includes(q) || webmail.includes(q);
            const matchStatus = status === 'all' || rowStatus === status;

            row.style.display = matchQuery && matchStatus ? '' : 'none';
        });
    }

    function togglePasswordVisibility(inputId) {
        const el = document.getElementById(inputId);
        const icon = document.getElementById(inputId + 'Icon');
        if (el.type === 'password') {
            el.type = 'text';
            icon.className = 'mdi mdi-eye-off-outline';
        } else {
            el.type = 'password';
            icon.className = 'mdi mdi-eye-outline';
        }
    }

    function autoFillDevCpanelPreset() {
        const email = document.getElementById('devFromAddress').value.trim() || document.getElementById('devSmtpUsername').value.trim();
        let domain = 'reftech.id';
        if (email.includes('@')) {
            domain = email.split('@')[1];
        }

        document.getElementById('devSmtpHost').value = 'srv162.niagahoster.com';
        document.getElementById('devSmtpPort').value = '465';
        document.getElementById('devSmtpEncryption').value = 'ssl';

        document.getElementById('devImapHost').value = 'srv162.niagahoster.com';
        document.getElementById('devImapPort').value = '993';
        document.getElementById('devImapEncryption').value = 'ssl';
    }

    function onDevUserSelected(userId) {
        if (!userId) return;
        editUserMailbox(userId);
    }

    function openConfigureModal() {
        document.getElementById('modalDevMailboxTitle').innerText = 'Konfigurasi Mailbox Pengguna';
        document.getElementById('devConfigUserId').value = '';
        document.getElementById('devFromName').value = '';
        document.getElementById('devFromAddress').value = '';
        document.getElementById('devSmtpHost').value = 'srv162.niagahoster.com';
        document.getElementById('devSmtpPort').value = '465';
        document.getElementById('devSmtpEncryption').value = 'ssl';
        document.getElementById('devImapHost').value = 'srv162.niagahoster.com';
        document.getElementById('devImapPort').value = '993';
        document.getElementById('devImapEncryption').value = 'ssl';
        document.getElementById('devSmtpUsername').value = '';
        document.getElementById('devSmtpPassword').value = '';
        document.getElementById('devTestConnAlert').classList.add('d-none');

        const modal = new bootstrap.Modal(document.getElementById('modalDevMailboxConfig'));
        modal.show();
    }

    function editUserMailbox(userId) {
        document.getElementById('devTestConnAlert').classList.add('d-none');
        document.getElementById('devConfigUserId').value = userId;

        fetch(`{{ url('/developer/mailbox-management/user') }}/${userId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const u = data.user;
                    const s = data.setting;
                    document.getElementById('modalDevMailboxTitle').innerText = `Konfigurasi Mailbox: ${u.name}`;
                    document.getElementById('devFromName').value = s.from_name || u.name;
                    document.getElementById('devFromAddress').value = s.from_address || u.email;
                    document.getElementById('devSmtpHost').value = s.smtp_host || 'srv162.niagahoster.com';
                    document.getElementById('devSmtpPort').value = s.smtp_port || '465';
                    document.getElementById('devSmtpEncryption').value = s.smtp_encryption || 'ssl';
                    document.getElementById('devImapHost').value = s.imap_host || 'srv162.niagahoster.com';
                    document.getElementById('devImapPort').value = s.imap_port || '993';
                    document.getElementById('devImapEncryption').value = s.imap_encryption || 'ssl';
                    document.getElementById('devSmtpUsername').value = s.smtp_username || u.email;
                    document.getElementById('devSmtpPassword').value = s.smtp_password || '';
                    document.getElementById('devSignatureLayout').value = s.signature_layout || 'sig_corporate';
                    document.getElementById('devSignatureColor').value = s.signature_color || '#696cff';

                    const modal = new bootstrap.Modal(document.getElementById('modalDevMailboxConfig'));
                    modal.show();
                } else {
                    alert(data.message);
                }
            })
            .catch(err => console.error(err));
    }

    function testDevMailboxConnection() {
        const alertBox = document.getElementById('devTestConnAlert');
        const btn = document.getElementById('btnDevTestConnection');

        const payload = {
            smtp_host: document.getElementById('devSmtpHost').value,
            smtp_port: document.getElementById('devSmtpPort').value,
            smtp_encryption: document.getElementById('devSmtpEncryption').value,
            smtp_username: document.getElementById('devSmtpUsername').value,
            smtp_password: document.getElementById('devSmtpPassword').value,
            imap_host: document.getElementById('devImapHost').value,
            imap_port: document.getElementById('devImapPort').value,
            imap_encryption: document.getElementById('devImapEncryption').value,
            imap_username: document.getElementById('devSmtpUsername').value,
        };

        if (!payload.smtp_username || !payload.smtp_password) {
            alert('Harap isi Username dan Password terlebih dahulu untuk melakukan test.');
            return;
        }

        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Menguji Handshake...';
        btn.disabled = true;
        alertBox.className = 'mt-3 p-3 rounded border small bg-light d-flex align-items-center';
        alertBox.innerHTML = '<i class="mdi mdi-loading mdi-spin text-primary me-2 fs-5"></i> Menghubungi port server SMTP & IMAP...';
        alertBox.classList.remove('d-none');

        fetch('{{ route("developer.mailbox.test_connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = '<i class="mdi mdi-lightning-bolt me-1"></i> Test Koneksi Handshake (Live)';
            btn.disabled = false;

            if (data.success) {
                alertBox.className = 'mt-3 p-3 rounded border small bg-label-success text-success';
                alertBox.innerHTML = `<i class="mdi mdi-check-circle-outline me-1 fs-5"></i> <strong>SUKSES:</strong> ${data.message}`;
            } else {
                alertBox.className = 'mt-3 p-3 rounded border small bg-label-danger text-danger';
                alertBox.innerHTML = `<i class="mdi mdi-close-circle-outline me-1 fs-5"></i> <strong>GAGAL:</strong> ${data.message}`;
            }
        })
        .catch(err => {
            btn.innerHTML = '<i class="mdi mdi-lightning-bolt me-1"></i> Test Koneksi Handshake (Live)';
            btn.disabled = false;
            alertBox.className = 'mt-3 p-3 rounded border small bg-label-danger text-danger';
            alertBox.innerHTML = `<i class="mdi mdi-alert-circle-outline me-1 fs-5"></i> Gagal terhubung ke server (Network error).`;
            console.error(err);
        });
    }

    function saveDevMailboxSetting() {
        const userId = document.getElementById('devConfigUserId').value;
        if (!userId) {
            alert('Pilih pengguna terlebih dahulu.');
            return;
        }

        const btn = document.getElementById('btnDevSaveSetting');
        btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Menyimpan...';
        btn.disabled = true;

        const payload = {
            user_id: userId,
            from_name: document.getElementById('devFromName').value,
            from_address: document.getElementById('devFromAddress').value,
            smtp_host: document.getElementById('devSmtpHost').value,
            smtp_port: document.getElementById('devSmtpPort').value,
            smtp_encryption: document.getElementById('devSmtpEncryption').value,
            smtp_username: document.getElementById('devSmtpUsername').value,
            smtp_password: document.getElementById('devSmtpPassword').value,
            imap_host: document.getElementById('devImapHost').value,
            imap_port: document.getElementById('devImapPort').value,
            imap_encryption: document.getElementById('devImapEncryption').value,
            imap_username: document.getElementById('devSmtpUsername').value,
            signature_layout: document.getElementById('devSignatureLayout').value,
            signature_color: document.getElementById('devSignatureColor').value,
        };

        fetch('{{ route("developer.mailbox.save") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = '<i class="mdi mdi-content-save-check me-1"></i> Simpan & Terapkan Akun';
            btn.disabled = false;

            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('modalDevMailboxConfig')).hide();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.message);
                    window.location.reload();
                }
            } else {
                alert(data.message || 'Terjadi kesalahan saat menyimpan.');
            }
        })
        .catch(err => {
            btn.innerHTML = '<i class="mdi mdi-content-save-check me-1"></i> Simpan & Terapkan Akun';
            btn.disabled = false;
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        });
    }

    function syncUserMailbox(userId, userName) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: `Sinkronisasi Mailbox ${userName}...`,
                text: 'Sedang menarik kotak masuk & terkirim dari server email hosting.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        fetch(`{{ url('/developer/mailbox-management/sync') }}/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sinkronisasi Selesai!',
                        text: data.message,
                        timer: 2500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    alert(data.message);
                    window.location.reload();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian Sinkronisasi',
                        text: data.message
                    });
                } else {
                    alert(data.message);
                }
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        });
    }

    function resetUserMailbox(userId, userName) {
        if (confirm(`Apakah Anda yakin ingin me-reset konfigurasi mailbox untuk ${userName}?`)) {
            fetch(`{{ url('/developer/mailbox-management/delete') }}/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(err => console.error(err));
        }
    }

    function toggleUserMailbox(userId, userName, activate) {
        const actionText = activate ? 'mengaktifkan' : 'menonaktifkan';
        if (confirm(`Apakah Anda yakin ingin ${actionText} akses menu Mailbox untuk ${userName}?`)) {
            fetch(`{{ url('/developer/mailbox-management/toggle-active') }}/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Diperbarui!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert(data.message);
                        window.location.reload();
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            });
        }
    }
</script>
@endpush
