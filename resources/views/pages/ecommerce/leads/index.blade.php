@extends('layouts.sales.app')
@section('title', 'Leads Online - Multi Channel')

@push('after-style')
<style>
    .lead-stat-card {
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .lead-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.08);
    }
    .badge-channel-tokopedia {
        background-color: #e8fadf;
        color: #1b5e20;
        border: 1px solid #a5d6a7;
    }
    .badge-channel-shopee {
        background-color: #fff3e0;
        color: #e65100;
        border: 1px solid #ffcc80;
    }
    .badge-channel-wa {
        background-color: #e0f2f1;
        color: #00796b;
        border: 1px solid #80cbc4;
    }
    .badge-channel-indotrading {
        background-color: #e3f2fd;
        color: #1565c0;
        border: 1px solid #90caf9;
    }
</style>
@endpush

@section('content')
<div class="container-fluid flex-grow-1 container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="mdi mdi-chat-processing-outline me-2 text-primary"></i>Leads Online
            </h4>
            <p class="text-muted mb-0">Pencatatan cepat chat masuk dari Tokopedia, Shopee, WhatsApp & Indotrading dengan klasifikasi User vs Reseller</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create-lead">
                <i class="mdi mdi-chat-plus-outline me-1"></i>+ Catat Chat / Lead Baru
            </button>
        </div>
    </div>

    {{-- Flash Alerts --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- 4 Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card lead-stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block mb-1">Total Chat / Leads</span>
                            <h3 class="card-title mb-0 fw-bold text-primary">{{ number_format($totalLeads) }}</h3>
                            <small class="text-muted">Inbound Inquiry</small>
                        </div>
                        <div class="avatar avatar-md bg-light-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-forum-outline fs-3 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card lead-stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block mb-1">User vs Reseller</span>
                            <h3 class="card-title mb-0 fw-bold text-dark">
                                <span class="text-purple">{{ $userCount }}</span> <small class="text-muted fs-6">User</small> /
                                <span class="text-warning">{{ $resellerCount }}</span> <small class="text-muted fs-6">Reseller</small>
                            </h3>
                            <small class="text-muted">Segmentasi Calon Buyer</small>
                        </div>
                        <div class="avatar avatar-md bg-light-warning rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-account-switch-outline fs-3 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card lead-stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block mb-1">Closing / Quoted</span>
                            <h3 class="card-title mb-0 fw-bold text-success">{{ number_format($convertedCount) }}</h3>
                            <small class="text-muted">Berhasil jadi Deal/Quote</small>
                        </div>
                        <div class="avatar avatar-md bg-light-success rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-check-decagram-outline fs-3 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card lead-stat-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted d-block mb-1">Conversion Rate</span>
                            <h3 class="card-title mb-0 fw-bold text-info">{{ $conversionRate }}%</h3>
                            <div class="progress mt-1" style="height: 6px; width: 100px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, $conversionRate) }}%"></div>
                            </div>
                        </div>
                        <div class="avatar avatar-md bg-light-info rounded-circle d-flex align-items-center justify-content-center">
                            <i class="mdi mdi-trending-up fs-3 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Channel Breakdown Pills --}}
    @if (!empty($channelStats) && $channelStats->isNotEmpty())
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <span class="text-muted small fw-semibold me-2"><i class="mdi mdi-filter-variant me-1"></i>Distribusi Channel:</span>
                @foreach ($channelStats as $chName => $st)
                    @php
                        $badgeClass = 'bg-secondary';
                        if (str_contains($chName, 'Tokopedia')) $badgeClass = 'badge-channel-tokopedia';
                        elseif (str_contains($chName, 'Shopee')) $badgeClass = 'badge-channel-shopee';
                        elseif (str_contains($chName, 'WhatsApp')) $badgeClass = 'badge-channel-wa';
                        elseif (str_contains($chName, 'Indotrading')) $badgeClass = 'badge-channel-indotrading';
                    @endphp
                    <a href="{{ route('online-leads.index', array_merge(request()->all(), ['channel' => $chName])) }}" 
                       class="badge {{ $badgeClass }} text-decoration-none py-2 px-3 {{ request('channel') == $chName ? 'border border-dark shadow-xs' : '' }}">
                        {{ $chName }}: <strong class="ms-1">{{ $st['count'] }}</strong>
                        @if($st['deals'] > 0)
                            <span class="badge bg-success rounded-pill ms-1 text-white">{{ $st['deals'] }} Deal</span>
                        @endif
                    </a>
                @endforeach
                @if (request('channel'))
                    <a href="{{ route('online-leads.index', request()->except('channel')) }}" class="btn btn-sm btn-link text-danger py-0 px-2">
                        <i class="mdi mdi-close"></i> Reset Channel
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Filter & Search Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('online-leads.index') }}" method="GET" class="row g-3">
                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted">Cari Pembeli / PT / No Telp / Produk</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Ketik kata kunci..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small text-muted">Channel</label>
                    <select name="channel" class="form-select">
                        <option value="">-- Semua Channel --</option>
                        @foreach ($channels as $key => $meta)
                            <option value="{{ $key }}" {{ request('channel') == $key ? 'selected' : '' }}>{{ $key }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small text-muted">Tipe Prospek</label>
                    <select name="customer_type" class="form-select">
                        <option value="">-- Semua Tipe --</option>
                        <option value="User" {{ request('customer_type') == 'User' ? 'selected' : '' }}>User (End-User)</option>
                        <option value="Reseller" {{ request('customer_type') == 'Reseller' ? 'selected' : '' }}>Reseller (Toko/Partner)</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small text-muted">Status Funnel</label>
                    <select name="status" class="form-select">
                        <option value="">-- Semua Status --</option>
                        @foreach ($statuses as $stKey => $stMeta)
                            <option value="{{ $stKey }}" {{ request('status') == $stKey ? 'selected' : '' }}>{{ $stMeta['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                @if ($isManagerOrAdmin && $salesList->isNotEmpty())
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small text-muted">Sales Online</label>
                    <select name="id_sales" class="form-select">
                        <option value="">-- Semua Sales --</option>
                        @foreach ($salesList as $s)
                            <option value="{{ $s->id }}" {{ request('id_sales') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-12 col-md-1 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100" title="Terapkan Filter">
                        <i class="mdi mdi-filter"></i>
                    </button>
                    @if (request()->hasAny(['search', 'channel', 'customer_type', 'status', 'id_sales', 'month', 'year']))
                    <a href="{{ route('online-leads.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="mdi mdi-refresh"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Main Leads Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-semibold">
                <i class="mdi mdi-format-list-bulleted me-2 text-primary"></i>Daftar Chat & Leads Masuk
            </h5>
            <span class="badge bg-label-primary">{{ $leads->total() }} Data Ditemukan</span>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Calon Pembeli</th>
                        <th>Tanggal</th>
                        <th>Channel Sumber</th>
                        <th>Kebutuhan / Produk</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($leads as $index => $lead)
                        @php
                            $channelClass = 'badge-secondary';
                            if (str_contains($lead->channel, 'Tokopedia')) $channelClass = 'badge-channel-tokopedia';
                            elseif (str_contains($lead->channel, 'Shopee')) $channelClass = 'badge-channel-shopee';
                            elseif (str_contains($lead->channel, 'WhatsApp')) $channelClass = 'badge-channel-wa';
                            elseif (str_contains($lead->channel, 'Indotrading')) $channelClass = 'badge-channel-indotrading';

                            $statusMeta = $lead->status_meta;
                        @endphp
                        <tr>
                            <td>{{ $leads->firstItem() + $index }}</td>
                            <td>
                                @if ($lead->company)
                                    <div class="fw-bold text-dark"><i class="mdi mdi-domain me-1 text-primary"></i>{{ $lead->company }}</div>
                                    <div class="text-muted mt-1">{{ $lead->name }}</div>
                                @else
                                    <div class="fw-bold text-dark">{{ $lead->name }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($lead->date)->format('d M Y') }}</div>
                                <small class="text-muted">{{ $lead->created_at->format('H:i') }} WIB</small>
                            </td>
                            <td>
                                <span class="badge {{ $channelClass }} px-2 py-1">
                                    <i class="mdi {{ $lead->channel_meta['icon'] ?? 'mdi-chat' }} me-1"></i>{{ $lead->channel }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $kebutuhanCount = $lead->followUps->sum(fn($fu) => $fu->items->count());
                                @endphp
                                @if ($kebutuhanCount > 0)
                                    <button type="button" class="btn btn-sm btn-label-primary d-inline-flex align-items-center gap-1"
                                        data-bs-toggle="offcanvas" data-bs-target="#offcanvas-followup-{{ $lead->id }}"
                                        aria-controls="offcanvas-followup-{{ $lead->id }}" title="Klik untuk lihat & catat kebutuhan">
                                        <i class="mdi mdi-package-variant-closed"></i>
                                        <span class="fw-semibold">{{ $kebutuhanCount }} Kebutuhan</span>
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusMeta['badge'] }} px-2 py-1">
                                    {{ $statusMeta['label'] }}
                                </span>
                                @if ($lead->id_client)
                                    <div class="mt-1">
                                        <span class="badge bg-label-success" title="Sudah di-convert ke Client master">
                                            <i class="mdi mdi-link-variant"></i> Client #{{ $lead->id_client }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle lead-aksi-toggle" data-bs-toggle="dropdown" aria-expanded="false" title="Aksi">
                                        <i class="mdi mdi-dots-vertical"></i> Aksi
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        {{-- Quick WA Action --}}
                                        @if ($lead->wa_url)
                                            <li>
                                                <a href="{{ $lead->wa_url }}" target="_blank" class="dropdown-item">
                                                    <i class="mdi mdi-whatsapp me-2 text-success"></i>Kirim Pesan WhatsApp
                                                </a>
                                            </li>
                                        @endif

                                        {{-- Convert to Client Button --}}
                                        @if (!$lead->id_client)
                                            <li>
                                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-convert-{{ $lead->id }}">
                                                    <i class="mdi mdi-account-arrow-right-outline me-2 text-primary"></i>Convert ke Client
                                                </button>
                                            </li>
                                        @endif

                                        {{-- Follow Up & Kebutuhan Button --}}
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-followup-{{ $lead->id }}" aria-controls="offcanvas-followup-{{ $lead->id }}">
                                                <i class="mdi mdi-clipboard-text-clock-outline me-2 text-warning"></i>Follow Up &amp; Kebutuhan
                                                @if ($lead->followUps->isNotEmpty())
                                                    <span class="badge bg-label-warning rounded-pill ms-1">{{ $lead->followUps->count() }}</span>
                                                @endif
                                            </button>
                                        </li>

                                        {{-- Edit Button --}}
                                        <li>
                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modal-edit-{{ $lead->id }}">
                                                <i class="mdi mdi-pencil-outline me-2 text-secondary"></i>Edit Data Lead
                                            </button>
                                        </li>

                                        <li><hr class="dropdown-divider"></li>

                                        {{-- Delete Button --}}
                                        <li>
                                            <form action="{{ route('online-leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lead ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="mdi mdi-trash-can-outline me-2"></i>Hapus Lead
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <div class="text-muted mb-2">
                                    <i class="mdi mdi-chat-remove-outline fs-1"></i>
                                </div>
                                <h6 class="fw-semibold text-muted">Belum ada data chat / lead yang tercatat</h6>
                                <p class="text-muted small mb-3">Gunakan tombol di bawah untuk mencatat chat masuk dari marketplace, WhatsApp, atau Indotrading</p>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create-lead">
                                    <i class="mdi mdi-chat-plus-outline me-1"></i>+ Catat Chat Pertama
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($leads->hasPages())
        <div class="card-footer bg-white py-3 border-top">
            <div class="d-flex justify-content-end">
                {{ $leads->links() }}
            </div>
        </div>
        @endif
    </div>

</div>

{{--
    Modal Edit / Offcanvas Follow Up / Modal Convert — SENGAJA dirender di luar
    <table> (bukan di dalam @forelse baris tabel di atas). HTML gak boleh naruh
    <div>/<form> nyasar langsung di dalam <tbody> — browser bakal "benerin" sendiri
    lewat foster parenting, dan itu kadang bikin <form> putus dari tombol submit-nya
    (button-nya ke-detect ada di DOM, tapi form.closest() gak nemu form-nya lagi),
    jadinya klik Simpan/Submit gak ngapa-ngapain sama sekali tanpa error apapun.
    Makanya modal/offcanvas-nya dipisah ke loop sendiri di sini, di luar tabel.
--}}
@foreach ($leads as $lead)
    @php
        $channels = $channels ?? [];
        $statuses = $statuses ?? [];
    @endphp
    {{-- Modal Edit Lead --}}
    <div class="modal fade" id="modal-edit-{{ $lead->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('online-leads.update', $lead->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="mdi mdi-pencil-outline me-1 text-primary"></i>Edit Lead #{{ $lead->id }} - {{ $lead->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Channel Sumber <span class="text-danger">*</span></label>
                                <select name="channel" class="form-select select-channel-edit" required>
                                    @foreach ($channels as $key => $meta)
                                        <option value="{{ $key }}" {{ $lead->channel == $key ? 'selected' : '' }}>{{ $key }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Tipe Prospek <span class="text-danger">*</span></label>
                                <select name="customer_type" class="form-select" required>
                                    <option value="User" {{ $lead->customer_type == 'User' ? 'selected' : '' }}>User (End-User)</option>
                                    <option value="Reseller" {{ $lead->customer_type == 'Reseller' ? 'selected' : '' }}>Reseller (Toko/Partner)</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Nama Calon Pembeli <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ $lead->name }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold label-phone-edit">No. HP / WhatsApp <span class="text-danger phone-required-mark-edit">*</span></label>
                                <input type="text" name="phone" class="form-control input-phone-edit" value="{{ $lead->phone }}" required>
                                <small class="text-muted d-none phone-optional-hint-edit">Opsional — chat marketplace kadang belum nampilin no. HP pembeli.</small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Nama Perusahaan (PT)</label>
                                <input type="text" name="company" class="form-control" value="{{ $lead->company }}" placeholder="Kosongkan jika personal">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Status Funnel <span class="text-danger">*</span></label>
                                <select name="status" class="form-select" required>
                                    @foreach ($statuses as $stKey => $stMeta)
                                        <option value="{{ $stKey }}" {{ $lead->status == $stKey ? 'selected' : '' }}>{{ $stMeta['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">Tanggal Chat Masuk <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control" value="{{ \Carbon\Carbon::parse($lead->date)->format('Y-m-d') }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Kebutuhan / Produk Ditanyakan <small class="text-muted fw-normal">(Opsional)</small></label>
                                <textarea name="product_interest" class="form-control" rows="2" placeholder="Tuliskan spesifikasi, part number, atau daftar produk yang ditanyakan...">{{ $lead->product_interest }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan Tambahan</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Catatan diskusi atau spesifikasi...">{{ $lead->notes }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Offcanvas Follow Up & Kebutuhan (slide-over dari kanan, pola sama kaya drawer item di halaman PR) --}}
    @php
        // Kalau form follow-up lead ini gagal validasi, redirect-back bawa
        // $errors + old('lead_id_check') — dipakai buat nampilin pesan error
        // di sini DAN auto-buka lagi offcanvas-nya (lihat script di bawah),
        // supaya kelihatan jelas kenapa gagal (sebelumnya diam-diam gak tersimpan).
        $followupHasError = $errors->any() && (int) old('lead_id_check') === $lead->id;
    @endphp
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvas-followup-{{ $lead->id }}" aria-labelledby="offcanvas-followup-label-{{ $lead->id }}" style="width: 460px;" @if ($followupHasError) data-autoshow="1" @endif>
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title fw-bold" id="offcanvas-followup-label-{{ $lead->id }}">
                <i class="mdi mdi-clipboard-text-clock-outline me-1 text-warning"></i>Follow Up &amp; Kebutuhan
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <p class="text-muted small mb-3">{{ $lead->name }}</p>

            @if ($followupHasError)
                <div class="alert alert-danger py-2 small mb-3">
                    <strong class="d-block mb-1"><i class="mdi mdi-alert-circle-outline me-1"></i>Follow up gagal disimpan:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Timeline histori kebutuhan, tiap item ditampilin sebagai badge --}}
            <h6 class="fw-bold small text-uppercase text-muted mb-2">Histori Kebutuhan</h6>
            @if ($lead->followUps->isEmpty())
                <p class="text-muted small mb-4">Belum ada kebutuhan tercatat untuk lead ini.</p>
            @else
                <div class="followup-timeline mb-4">
                    @foreach ($lead->followUps as $followUp)
                        <div class="border rounded-3 p-3 mb-2 bg-light-subtle">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <small class="fw-semibold text-dark">
                                    <i class="mdi mdi-calendar-outline me-1"></i>{{ \Carbon\Carbon::parse($followUp->date)->format('d M Y') }}
                                </small>
                                <small class="text-muted">{{ $followUp->user->name ?? 'System' }}</small>
                            </div>
                            @if ($followUp->note)
                                <p class="small mb-2">{{ $followUp->note }}</p>
                            @endif
                            @if ($followUp->items->isNotEmpty())
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach ($followUp->items as $item)
                                        <div class="d-inline-flex align-items-center gap-1 badge bg-{{ $item->status_meta['badge'] }}-subtle border border-{{ $item->status_meta['badge'] }} text-{{ $item->status_meta['badge'] }} fw-semibold py-1 px-2">
                                            <span>{{ $item->item_name }}{{ $item->qty ? ' ('.$item->qty.')' : '' }}</span>
                                            <select class="followup-item-status-select border-0 bg-transparent fw-semibold text-{{ $item->status_meta['badge'] }}"
                                                style="width: auto; font-size: 10.5px; padding: 0;"
                                                data-item-id="{{ $item->id }}">
                                                @foreach (\App\Models\OnlineLeadFollowUpItem::STATUSES as $stKey => $stMeta)
                                                    <option value="{{ $stKey }}" {{ $item->status === $stKey ? 'selected' : '' }}>{{ $stMeta['label'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <hr>

            {{-- Form catat kebutuhan baru --}}
            <h6 class="fw-bold small text-uppercase text-muted mb-2">Catat Follow Up Baru</h6>
            <form action="{{ route('online-leads.follow-ups.store', $lead->id) }}" method="POST" class="form-followup">
                @csrf
                <input type="hidden" name="lead_id_check" value="{{ $lead->id }}">
                <div class="row g-2 mb-2">
                    <div class="col-12">
                        <label class="form-label small fw-semibold mb-1">Tanggal</label>
                        <input type="date" name="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold mb-1">Catatan Follow Up <small class="text-muted fw-normal">(Opsional)</small></label>
                        <input type="text" name="note" class="form-control form-control-sm" placeholder="mis. Customer nanya ulang harga & ketersediaan...">
                    </div>
                </div>

                <label class="form-label small fw-semibold mb-1">Item Kebutuhan <span class="text-danger">*</span></label>
                <div class="followup-items-container mb-2"></div>
                <button type="button" class="btn btn-sm btn-outline-primary btn-add-followup-item mb-3">
                    <i class="mdi mdi-plus me-1"></i>Tambah Item
                </button>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-sm btn-warning">
                        <i class="mdi mdi-content-save-outline me-1"></i>Simpan Follow Up
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Convert to Client --}}
    @if (!$lead->id_client)
    <div class="modal fade" id="modal-convert-{{ $lead->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('online-leads.convert', $lead->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-success">
                            <i class="mdi mdi-account-check-outline me-1"></i>Convert ke Master Client & Quotation
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-3">Lead ini akan otomatis didaftarkan ke dalam database <strong>Master Client Reftech</strong> sebagai data resmi:</p>
                        <ul class="list-group list-group-flush border rounded mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Nama Pembeli / PIC:</span>
                                <strong>{{ $lead->name }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">No. Telepon / WA:</span>
                                <strong>{{ $lead->phone }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Perusahaan (Client):</span>
                                <strong>{{ $lead->company ?: 'Personal - ' . $lead->name }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Tipe R/U:</span>
                                <span class="badge {{ $lead->customer_type == 'Reseller' ? 'bg-warning' : 'bg-primary' }}">{{ $lead->customer_type }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span class="text-muted">Sumber Channel:</span>
                                <span>{{ $lead->channel }}</span>
                            </li>
                        </ul>
                        <div class="alert alert-info py-2 small mb-0">
                            <i class="mdi mdi-information me-1"></i>Setelah di-convert, status lead ini akan berubah menjadi <strong>Deal</strong> dan langsung terhubung ke Master Client.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="mdi mdi-check me-1"></i>Ya, Convert Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

{{-- Template baris item Follow Up (di-clone via JS, dipakai di semua offcanvas-followup-*) --}}
<template id="tmpl-followup-item-row">
    <div class="row g-2 align-items-center mb-2 followup-item-row">
        <div class="col-5">
            <input type="text" name="items[__IDX__][item_name]" class="form-control form-control-sm" placeholder="Nama item / kebutuhan..." required>
        </div>
        <div class="col-2">
            <input type="text" name="items[__IDX__][qty]" class="form-control form-control-sm" placeholder="Qty">
        </div>
        <div class="col-4">
            <select name="items[__IDX__][status]" class="form-select form-select-sm">
                @foreach (\App\Models\OnlineLeadFollowUpItem::STATUSES as $stKey => $stMeta)
                    <option value="{{ $stKey }}" {{ $stKey === 'pending' ? 'selected' : '' }}>{{ $stMeta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-1">
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-followup-item"><i class="mdi mdi-close"></i></button>
        </div>
    </div>
</template>

{{-- Modal Create New Lead (10-Second Quick Input) --}}
<div class="modal fade" id="modal-create-lead" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('online-leads.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="mdi mdi-chat-plus-outline me-1"></i>Catat Chat / Lead Baru (< 10 Detik)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Channel Selection --}}
                        <div class="col-12">
                            <label class="form-label fw-bold">Channel Sumber Chat <span class="text-danger">*</span></label>
                            <select name="channel" id="select-channel-create" class="form-select form-select-lg" required autofocus>
                                <optgroup label="🛍️ Marketplace">
                                    <option value="Tokopedia - Airend Center">Tokopedia - Airend Center (Reftech)</option>
                                    <option value="Tokopedia - Part Compressor">Tokopedia - Part Compressor (Kojisha)</option>
                                    <option value="Tokopedia - Kojisha Filter">Tokopedia - Kojisha Filter (Kojisha)</option>
                                    <option value="Shopee - Kojisha Filter">Shopee - Kojisha Filter (Kojisha)</option>
                                </optgroup>
                                <optgroup label="💬 WhatsApp (Direct)">
                                    <option value="WhatsApp - Airend Center">WhatsApp - Airend Center (Reftech)</option>
                                    <option value="WhatsApp - Part Compressor">WhatsApp - Part Compressor (Kojisha)</option>
                                    <option value="WhatsApp - Kojisha">WhatsApp - Kojisha (Kojisha)</option>
                                </optgroup>
                                <optgroup label="🌐 B2B Directory">
                                    <option value="Indotrading">Indotrading</option>
                                </optgroup>
                            </select>
                        </div>

                        {{-- RU Segment (User vs Reseller) --}}
                        <div class="col-12">
                            <label class="form-label fw-bold">Tipe Calon Pembeli <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check custom-option custom-option-basic flex-grow-1 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="customer_type" id="type_user" value="User" checked>
                                    <label class="form-check-label ms-2" for="type_user">
                                        <strong class="d-block text-dark"><i class="mdi mdi-account-outline text-purple me-1"></i>User (End-User)</strong>
                                        <small class="text-muted">Pabrik / Pemakai Langsung</small>
                                    </label>
                                </div>
                                <div class="form-check custom-option custom-option-basic flex-grow-1 p-3 border rounded">
                                    <input class="form-check-input" type="radio" name="customer_type" id="type_reseller" value="Reseller">
                                    <label class="form-check-label ms-2" for="type_reseller">
                                        <strong class="d-block text-dark"><i class="mdi mdi-storefront-outline text-warning me-1"></i>Reseller</strong>
                                        <small class="text-muted">Toko Teknik / Bengkel / Partner</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Name & Phone --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Nama Calon Pembeli <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama kontak / buyer..." required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold" id="label-phone-create">No. Telp / WhatsApp <span class="text-danger" id="phone-required-mark-create">*</span></label>
                            <input type="text" name="phone" id="input-phone-create" class="form-control" placeholder="08xxxxxxxxxx..." required>
                            <small class="text-muted d-none" id="phone-optional-hint-create">Opsional — chat marketplace kadang belum nampilin no. HP pembeli.</small>
                        </div>

                        {{-- Company & Date --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Nama PT / Perusahaan <small class="text-muted fw-normal">(Opsional)</small></label>
                            <input type="text" name="company" class="form-control" placeholder="PT / CV / Bengkel...">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-bold">Tanggal Chat</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>

                        {{-- Product Interest Textarea --}}
                        <div class="col-12">
                            <label class="form-label fw-bold">Kebutuhan / Produk Ditanyakan <small class="text-muted fw-normal">(Opsional)</small></label>
                            <textarea name="product_interest" class="form-control" rows="2" placeholder="Tuliskan spesifikasi, part number, atau daftar produk yang ditanyakan..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Catatan Singkat <small class="text-muted fw-normal">(Opsional)</small></label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Catatan respon, spek, atau janji follow-up..."></textarea>
                        </div>

                        {{-- If Manager/Admin can assign --}}
                        @if ($isManagerOrAdmin && $salesList->isNotEmpty())
                        <div class="col-12">
                            <label class="form-label fw-bold">Sales PIC</label>
                            <select name="id_sales" class="form-select">
                                @foreach ($salesList as $s)
                                    <option value="{{ $s->id }}" {{ Auth::id() == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="mdi mdi-content-save me-1"></i>Simpan Lead
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-script')
<script>
    (function() {
        // Dropdown "Aksi" di kolom paling kanan kepotong/ketutup card, karena
        // tabel dibungkus .table-responsive (overflow-x:auto) yang clip elemen
        // absolute di dalamnya. Fix: pakai Popper strategy "fixed" biar dropdown-menu
        // posisinya dihitung relatif ke viewport, bukan ke container yang di-clip.
        if (typeof bootstrap !== 'undefined' && bootstrap.Dropdown) {
            document.querySelectorAll('.lead-aksi-toggle').forEach(function(toggleEl) {
                new bootstrap.Dropdown(toggleEl, {
                    popperConfig: function(defaultConfig) {
                        return Object.assign({}, defaultConfig, { strategy: 'fixed' });
                    }
                });
            });
        }

        // No. Telp gak wajib kalau channel-nya Marketplace (Tokopedia/Shopee) —
        // chat marketplace sering belum nampilin no. HP pembeli sebelum di-follow-up.
        var MARKETPLACE_KEYWORDS = ['Tokopedia', 'Shopee'];

        function isMarketplaceChannel(channel) {
            if (!channel) return false;
            return MARKETPLACE_KEYWORDS.some(function(kw) { return channel.indexOf(kw) !== -1; });
        }

        function applyPhoneRequirement(channelValue, $input, $requiredMark, $hint) {
            var marketplace = isMarketplaceChannel(channelValue);
            if (marketplace) {
                $input.removeAttribute('required');
                if ($requiredMark) $requiredMark.classList.add('d-none');
                if ($hint) $hint.classList.remove('d-none');
            } else {
                $input.setAttribute('required', 'required');
                if ($requiredMark) $requiredMark.classList.remove('d-none');
                if ($hint) $hint.classList.add('d-none');
            }
        }

        // Modal Create (elemen tunggal, pakai id)
        var selectCreate = document.getElementById('select-channel-create');
        var inputPhoneCreate = document.getElementById('input-phone-create');
        var markCreate = document.getElementById('phone-required-mark-create');
        var hintCreate = document.getElementById('phone-optional-hint-create');
        if (selectCreate && inputPhoneCreate) {
            selectCreate.addEventListener('change', function() {
                applyPhoneRequirement(selectCreate.value, inputPhoneCreate, markCreate, hintCreate);
            });
            applyPhoneRequirement(selectCreate.value, inputPhoneCreate, markCreate, hintCreate);
        }

        // Modal Edit (banyak instance, satu per lead — delegasikan lewat class)
        document.addEventListener('change', function(e) {
            if (!e.target.classList.contains('select-channel-edit')) return;
            var $form = e.target.closest('form');
            if (!$form) return;
            var $input = $form.querySelector('.input-phone-edit');
            var $mark = $form.querySelector('.phone-required-mark-edit');
            var $hint = $form.querySelector('.phone-optional-hint-edit');
            if ($input) applyPhoneRequirement(e.target.value, $input, $mark, $hint);
        });

        document.querySelectorAll('.select-channel-edit').forEach(function(sel) {
            var $form = sel.closest('form');
            if (!$form) return;
            var $input = $form.querySelector('.input-phone-edit');
            var $mark = $form.querySelector('.phone-required-mark-edit');
            var $hint = $form.querySelector('.phone-optional-hint-edit');
            if ($input) applyPhoneRequirement(sel.value, $input, $mark, $hint);
        });

        // ── Follow Up & Kebutuhan: tambah/hapus baris item, seed 1 baris kosong
        // pas offcanvas dibuka, dan update status per-item lewat AJAX tanpa reload ──
        var followupItemIdx = {};

        function addFollowupItemRow(container) {
            var offcanvasEl = container.closest('.offcanvas');
            var offcanvasId = offcanvasEl ? offcanvasEl.id : 'default';
            if (!followupItemIdx[offcanvasId]) followupItemIdx[offcanvasId] = 0;
            var idx = followupItemIdx[offcanvasId]++;

            var tmpl = document.getElementById('tmpl-followup-item-row');
            if (!tmpl) return;
            var html = tmpl.innerHTML.replace(/__IDX__/g, idx);
            var wrapper = document.createElement('div');
            wrapper.innerHTML = html.trim();
            container.appendChild(wrapper.firstElementChild);
        }

        document.addEventListener('click', function(e) {
            var addBtn = e.target.closest('.btn-add-followup-item');
            if (addBtn) {
                var container = addBtn.closest('.offcanvas-body').querySelector('.followup-items-container');
                if (container) addFollowupItemRow(container);
                return;
            }
            var removeBtn = e.target.closest('.btn-remove-followup-item');
            if (removeBtn) {
                var row = removeBtn.closest('.followup-item-row');
                var container2 = removeBtn.closest('.followup-items-container');
                if (row && container2 && container2.children.length > 1) {
                    row.remove();
                }
            }
        });

        // Seed 1 baris kosong ke SEMUA offcanvas Follow Up begitu script ini jalan (bukan
        // nunggu event buka offcanvas) — halaman ini muat banyak script lain duluan (notif
        // navbar dll), jadi kalau nunggu event "show.bs.offcanvas", ada race condition:
        // user bisa keburu buka & submit sebelum listener-nya sempat ke-register, jadinya
        // baris item gak pernah muncul otomatis padahal offcanvas-nya udah kebuka.
        document.querySelectorAll('.followup-items-container').forEach(function(container) {
            if (container.children.length === 0) {
                addFollowupItemRow(container);
            }
        });
        // Safety net tambahan kalau ada offcanvas yang somehow masih kosong pas dibuka
        // (mis. race condition submit yang ngosongin ulang container-nya).
        document.addEventListener('show.bs.offcanvas', function(e) {
            if (!e.target.id || e.target.id.indexOf('offcanvas-followup-') !== 0) return;
            var container = e.target.querySelector('.followup-items-container');
            if (container && container.children.length === 0) {
                addFollowupItemRow(container);
            }
        });

        // Kalau submit sebelumnya gagal validasi (mis. semua baris item kosong),
        // buka lagi otomatis offcanvas lead yang bersangkutan biar pesan errornya kelihatan.
        document.querySelectorAll('.offcanvas[data-autoshow="1"]').forEach(function(el) {
            if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
                bootstrap.Offcanvas.getOrCreateInstance(el).show();
            }
        });

        // Sebelum submit: buang baris item yang nama-nya kosong (mis. nambah baris tapi gak
        // jadi diisi) — sebelumnya baris kosong ini bikin validasi backend gagal diam-diam
        // (gak ada pesan error tampil), jadi kelihatan kaya "gak kesimpan".
        document.addEventListener('submit', function(e) {
            var form = e.target.closest('.form-followup');
            if (!form) return;

            var rows = form.querySelectorAll('.followup-item-row');
            var filledCount = 0;
            rows.forEach(function(row) {
                var nameInput = row.querySelector('input[name*="[item_name]"]');
                if (nameInput && nameInput.value.trim() === '') {
                    row.remove();
                } else {
                    filledCount++;
                }
            });

            if (filledCount === 0) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'warning', title: 'Item Kebutuhan Kosong', text: 'Isi minimal 1 nama item kebutuhan sebelum menyimpan follow up.' });
                } else {
                    alert('Isi minimal 1 nama item kebutuhan sebelum menyimpan follow up.');
                }
            }
        });

        // Update status item (Pending/Provided/Not Provided) langsung dari timeline
        document.addEventListener('change', function(e) {
            if (!e.target.classList.contains('followup-item-status-select')) return;
            var select = e.target;
            var itemId = select.dataset.itemId;
            var newStatus = select.value;
            var prevClasses = ['bg-secondary-subtle', 'bg-success-subtle', 'bg-danger-subtle', 'border-secondary', 'border-success', 'border-danger', 'text-secondary', 'text-success', 'text-danger'];

            select.disabled = true;
            fetch("{{ url('/online-leads/follow-up-items') }}/" + itemId + "/status", {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (!data.success) return;
                    select.classList.remove.apply(select.classList, prevClasses);
                    select.classList.add('bg-' + data.status_meta.badge + '-subtle', 'border-' + data.status_meta.badge, 'text-' + data.status_meta.badge);
                })
                .finally(function() {
                    select.disabled = false;
                });
        });
    })();
</script>
@endpush
