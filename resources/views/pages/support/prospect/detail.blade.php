@extends('layouts.sales.app')
@section('title', 'Detail Prospect - ' . ($client->company ?? 'Prospect'))

@section('content')
    {{-- Top Header / Breadcrumb Bar --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 mb-4 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('prospect.index') }}" class="text-muted fw-normal text-decoration-none">
                    <i class="mdi mdi-arrow-left me-1"></i>Prospects
                </a>
                <span class="text-muted">/</span>
                <span class="text-primary fw-semibold">Detail #{{ $prospect->id }}</span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <h4 class="fw-bold mb-0 text-heading">{{ $client->company ?? 'Prospect Detail' }}</h4>
                <span class="badge bg-label-primary rounded-pill px-3 py-1 fw-semibold">
                    <i class="mdi mdi-tag-outline me-1"></i>{{ $prospect->category ?? 'General' }}
                </span>
                @if ($prospect->level == '9')
                    <span class="badge bg-label-info rounded-pill px-3 py-1 fw-semibold">
                        <i class="mdi mdi-progress-clock me-1"></i>Follow Up In Progress
                    </span>
                @elseif ($prospect->id_quotation)
                    <span class="badge bg-label-success rounded-pill px-3 py-1 fw-semibold">
                        <i class="mdi mdi-file-check-outline me-1"></i>Quotation Created
                    </span>
                @else
                    <span class="badge bg-label-warning rounded-pill px-3 py-1 fw-semibold">
                        <i class="mdi mdi-star-outline me-1"></i>New Prospect
                    </span>
                @endif
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-label-primary waves-effect" data-bs-toggle="modal" data-bs-target="#editProspectModal">
                <i class="mdi mdi-pencil-outline me-1"></i>Edit Prospect
            </button>
            <a href="{{ route('prospect.index') }}" class="btn btn-label-secondary waves-effect">
                <i class="mdi mdi-arrow-left me-1"></i>Kembali
            </a>
            @if (Auth::user()->role == 'Sales')
                <button type="button" class="btn btn-primary with-quote waves-effect waves-light shadow-sm" data-id="{{ $prospect->id }}">
                    <i class="mdi mdi-lightning-bolt me-1"></i> Smart Quote
                </button>
            @endif
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Main Column --}}
        <div class="col-xl-8 col-lg-7 col-12">
            {{-- Card 1: Prospect Requirement & Category --}}
            <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%); border-left: 5px solid #696cff !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar avatar-sm bg-label-primary rounded p-1">
                                <i class="mdi mdi-text-box-search-outline fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-heading">Kebutuhan / Detail Prospek</h6>
                                <small class="text-muted">Informasi inquiry dan spesifikasi kebutuhan klien</small>
                            </div>
                        </div>
                        <span class="text-muted small">
                            <i class="mdi mdi-calendar-blank-outline me-1"></i>
                            {{ $prospect->date ? \Carbon\Carbon::parse($prospect->date)->format('d M Y') : '—' }}
                        </span>
                    </div>

                    <div class="bg-white rounded-3 p-3 border shadow-xs mb-3">
                        <div class="text-muted small text-uppercase fw-semibold mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">Deskripsi Kebutuhan:</div>
                        <div class="text-dark" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;">{{ $prospect->kebutuhan ?: 'Tidak ada rincian kebutuhan spesifik.' }}</div>
                    </div>

                    <div class="row g-2">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center p-2 rounded bg-light">
                                <i class="mdi mdi-shape-outline text-primary me-2 fs-5"></i>
                                <div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">Kategori</div>
                                    <div class="fw-semibold text-heading small">{{ $prospect->category ?: 'General' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center p-2 rounded bg-light">
                                <i class="mdi mdi-bullhorn-outline text-info me-2 fs-5"></i>
                                <div>
                                    <div class="text-muted small" style="font-size: 0.75rem;">Sumber Prospek (Source)</div>
                                    <div class="fw-semibold text-heading small">{{ $client->source ?: 'Direct / Marketing' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Company & PIC Profile --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-domain me-2 text-primary fs-5"></i> Informasi Perusahaan & Kontak PIC
                    </h6>
                </div>
                <div class="card-body pt-4">
                    <div class="row g-4">
                        {{-- Company Info --}}
                        <div class="col-md-6 border-end-md">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3" style="letter-spacing: 0.5px;">
                                <i class="mdi mdi-office-building-outline me-1"></i> Profil Perusahaan
                            </h6>
                            <div class="d-flex flex-column gap-2">
                                <div>
                                    <div class="text-muted small">Nama Perusahaan</div>
                                    <div class="fw-bold text-heading fs-6">{{ $client->company ?? '—' }}</div>
                                </div>
                                <div>
                                    <div class="text-muted small">Alamat Utama</div>
                                    <div class="text-dark small">{{ $client->address ?: '—' }}</div>
                                </div>
                                @if ($client->subAddress)
                                    <div>
                                        <div class="text-muted small">Sub Alamat / Plant</div>
                                        <div class="text-dark small">{{ $client->subAddress }}</div>
                                    </div>
                                @endif
                                <div class="row g-2 pt-1">
                                    <div class="col-6">
                                        <div class="text-muted small">Area / Kota</div>
                                        <div class="fw-semibold text-heading small">
                                            <i class="mdi mdi-map-marker-outline text-danger me-1"></i>{{ $client->area ?: '—' }}
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small">Tipe Klien (R/U)</div>
                                        <div class="fw-semibold text-heading small">{{ $client->ru ?: '—' }}</div>
                                    </div>
                                </div>
                                <div class="row g-2 pt-1">
                                    <div class="col-6">
                                        <div class="text-muted small">Telepon Kantor</div>
                                        <div class="text-dark small">{{ $client->phone ?: '—' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-muted small">Email Kantor</div>
                                        <div class="text-dark small text-truncate">{{ $client->email ?: '—' }}</div>
                                    </div>
                                </div>
                                @if ($client->unit)
                                    <div class="pt-1">
                                        <div class="text-muted small">Existing Machine / Unit</div>
                                        <div class="badge bg-label-secondary text-dark fw-normal text-wrap text-start mt-1">
                                            <i class="mdi mdi-cogs me-1 text-primary"></i>{{ $client->unit }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- PIC Info --}}
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3" style="letter-spacing: 0.5px;">
                                <i class="mdi mdi-account-tie-outline me-1"></i> Person In Charge (PIC)
                            </h6>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light">
                                    <div class="avatar avatar-md bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5 shadow-xs">
                                        {{ strtoupper(substr($pic->name_pic ?? 'P', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-heading fs-6">{{ $pic->name_pic ?? '—' }}</div>
                                        <span class="badge bg-label-primary small">{{ $pic->position ?? 'PIC Klien' }}</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="text-muted small mb-1">Nomor WhatsApp / HP</div>
                                    @if ($pic->phone_pic)
                                        @php
                                            $phoneClean = preg_replace('/[^0-9]/', '', $pic->phone_pic);
                                            if (substr($phoneClean, 0, 1) == '0') {
                                                $phoneClean = '62' . substr($phoneClean, 1);
                                            }
                                        @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-bold text-heading">{{ $pic->phone_pic }}</span>
                                            <a href="https://wa.me/{{ $phoneClean }}" target="_blank" class="btn btn-sm btn-success waves-effect py-1 px-2 shadow-xs">
                                                <i class="mdi mdi-whatsapp me-1"></i> Chat WA
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </div>

                                <div>
                                    <div class="text-muted small mb-1">Email PIC</div>
                                    @if ($pic->email_pic)
                                        <a href="mailto:{{ $pic->email_pic }}" class="text-primary fw-medium small">
                                            <i class="mdi mdi-email-outline me-1"></i>{{ $pic->email_pic }}
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </div>

                                <div class="pt-2 border-top">
                                    <div class="text-muted small">Sales In Charge</div>
                                    <div class="fw-semibold text-heading small mt-1">
                                        <i class="mdi mdi-account-check-outline text-success me-1"></i>
                                        {{ $client->sales->name ?? ($prospect->sales->name ?? 'Belum Ditugaskan') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Quotation Information --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-file-document-outline me-2 text-primary fs-5"></i> Dokumen Quotation / Penawaran
                    </h6>
                    @if (Auth::user()->role == 'Sales' && !@$quotation)
                        <button type="button" class="btn btn-sm btn-primary with-quote waves-effect py-1 px-3" data-id="{{ $prospect->id }}">
                            <i class="mdi mdi-lightning-bolt me-1"></i> Buat Smart Quote
                        </button>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if (@$quotation)
                        @php
                            $isSmartQuote = $quotationIsSmart ?? false;

                            // Status quotation lama = kode angka; Smart Quote = string.
                            $statusMap = [
                                '100' => ['label' => 'Done PO', 'color' => 'success', 'icon' => 'mdi-cart-check'],
                                '80'  => ['label' => 'Hot Prospect', 'color' => 'warning', 'icon' => 'mdi-fire'],
                                '60'  => ['label' => 'Negotiation', 'color' => 'primary', 'icon' => 'mdi-handshake'],
                                '40'  => ['label' => 'Progress FU', 'color' => 'info', 'icon' => 'mdi-progress-clock'],
                                '30'  => ['label' => 'Inquiry Accepted', 'color' => 'dark', 'icon' => 'mdi-check-all'],
                                '20'  => ['label' => 'Send WA/Email', 'color' => 'secondary', 'icon' => 'mdi-email-outline'],
                                '0'   => ['label' => 'Loss', 'color' => 'danger', 'icon' => 'mdi-close-circle-outline'],
                            ];
                            $smartStatusMap = [
                                'draft'        => ['label' => 'Draft', 'color' => 'secondary', 'icon' => 'mdi-file-outline'],
                                'sent'         => ['label' => 'Sent', 'color' => 'info', 'icon' => 'mdi-email-outline'],
                                'negotiation'  => ['label' => 'Negotiation', 'color' => 'warning', 'icon' => 'mdi-handshake'],
                                'revision'     => ['label' => 'Revisi', 'color' => 'primary', 'icon' => 'mdi-file-document-edit-outline'],
                                'hot_prospect' => ['label' => 'Hot Prospect', 'color' => 'danger', 'icon' => 'mdi-fire'],
                                'po_received'  => ['label' => 'PO Received', 'color' => 'success', 'icon' => 'mdi-cart-check'],
                                'loss'         => ['label' => 'Loss', 'color' => 'dark', 'icon' => 'mdi-close-circle-outline'],
                                'cancel'       => ['label' => 'Cancel', 'color' => 'dark', 'icon' => 'mdi-cancel'],
                            ];
                            $activeMap = $isSmartQuote ? $smartStatusMap : $statusMap;
                            $st = $activeMap[$quotation->status] ?? ['label' => 'Status: ' . $quotation->status, 'color' => 'primary', 'icon' => 'mdi-file-outline'];

                            $quoteUrl = $isSmartQuote
                                ? route('unit-quotation.show', $quotation->id)
                                : route('quotation.show', $quotation->id);
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No. Quotation</th>
                                        <th>Status Penawaran</th>
                                        <th class="text-end">Nilai Nett (Rp)</th>
                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-primary fs-6">{{ $quotation->no_quote }}</div>
                                            <small class="text-muted">{{ $quotation->title ?: 'Smart Quotation' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-label-{{ $st['color'] }} px-3 py-2 rounded-pill fw-semibold">
                                                <i class="mdi {{ $st['icon'] }} me-1"></i>{{ $st['label'] }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="fw-bold text-success fs-6">
                                                Rp {{ number_format($quotation->nett ?? ($quotation->total ?? 0), 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ $quoteUrl }}" class="btn btn-sm btn-outline-primary waves-effect rounded-pill px-3">
                                                <i class="mdi mdi-eye-outline me-1"></i> Lihat
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 px-3">
                            <div class="avatar avatar-md bg-label-secondary rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                <i class="mdi mdi-file-document-plus-outline fs-3 text-muted"></i>
                            </div>
                            <h6 class="fw-bold text-heading mb-1">Belum Ada Quotation</h6>
                            <p class="text-muted small mb-3">Prospek ini belum dikonversi ke dokumen penawaran harga resmi.</p>
                            @if (Auth::user()->role == 'Sales')
                                <button type="button" class="btn btn-primary with-quote waves-effect py-2 px-4 shadow-sm" data-id="{{ $prospect->id }}">
                                    <i class="mdi mdi-lightning-bolt me-1"></i> Buat Smart Quote Sekarang
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card 4: Discussion & Timeline Comments (Matched to Purchase Request Format) --}}
            <div class="card border-0 shadow-sm mb-4" id="viewComment">
                <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-forum-outline me-2 text-primary fs-5"></i> Diskusi & Catatan Follow-Up
                    </h6>
                    <span class="badge bg-label-primary rounded-pill">{{ count($prospectComments ?? []) }} Catatan</span>
                </div>
                <div class="card-body p-4">
                    {{-- Daftar pesan (Scrollable discussion list) --}}
                    <div class="discussion-list mb-4 p-3 rounded border" style="max-height: 420px; overflow-y: auto; background-color: #fcfcfd;">
                        @forelse($prospectComments as $disc)
                            @php
                                $isMe = $disc->id_user == Auth::id();
                                $userInitial = strtoupper(substr($disc->user->name ?? 'U', 0, 1));
                            @endphp
                            <div class="d-flex gap-3 mb-4 {{ $isMe ? 'flex-row-reverse' : '' }}">
                                <div class="flex-shrink-0">
                                    @if ($disc->user && $disc->user->image)
                                        <img src="{{ url('') . '/' . $disc->user->image }}"
                                            class="rounded-circle border border-2 border-white shadow-xs"
                                            style="width:38px;height:38px;object-fit:cover;"
                                            alt="{{ $disc->user->name }}">
                                    @else
                                        <span class="avatar-initial rounded-circle bg-label-{{ $isMe ? 'primary' : 'info' }} fw-bold d-flex align-items-center justify-content-center shadow-xs" style="width:38px;height:38px;font-size:14px;">
                                            {{ $userInitial }}
                                        </span>
                                    @endif
                                </div>
                                <div style="max-width: 75%;">
                                    <div class="d-flex align-items-center gap-2 mb-1 {{ $isMe ? 'flex-row-reverse' : '' }}">
                                        <span class="fw-semibold text-dark small" style="font-size: 13px;">{{ $disc->user->name ?? 'User' }}</span>
                                        <span class="text-muted" style="font-size: 11px;">
                                            {{ $disc->date ? (\Carbon\Carbon::parse($disc->date)->diffInHours(\Carbon\Carbon::now()) > 24 ? \Carbon\Carbon::parse($disc->date)->format('d M Y H:i') : \Carbon\Carbon::parse($disc->date)->diffForHumans()) : '' }}
                                        </span>
                                    </div>
                                    <div class="p-3 rounded-3 shadow-xs {{ $isMe ? 'chat-bubble-me' : 'chat-bubble-other' }}"
                                        style="word-break: break-word; font-size: 13.5px; line-height: 1.5;">
                                        @php
                                            $msg = e($disc->comment);
                                            if ($disc->mention) {
                                                foreach ($disc->mention as $m) {
                                                    if ($m->mention && $m->mention->name) {
                                                        $msg = str_replace(
                                                            '@' . $m->mention->name,
                                                            '<span class="fw-bold ' . ($isMe ? 'text-primary' : 'text-primary') . '">@' . e($m->mention->name) . '</span>',
                                                            $msg
                                                        );
                                                    }
                                                }
                                            }
                                        @endphp
                                        {!! nl2br($msg) !!}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <div class="avatar avatar-lg mx-auto mb-3 bg-label-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; border-radius: 50%;">
                                    <i class="mdi mdi-forum-outline fs-3"></i>
                                </div>
                                <p class="mb-0 fw-medium">Belum ada diskusi.</p>
                                <small class="text-muted">Mulai percakapan sekarang dengan mengetik pesan di bawah.</small>
                            </div>
                        @endforelse
                    </div>

                    {{-- Form kirim pesan (Sama persis dengan Purchase Request) --}}
                    <form action="{{ route('add_comment.prospect', $prospect->id) }}" method="POST" id="discussionForm">
                        @csrf
                        <div class="position-relative">
                            <textarea
                                name="comment"
                                id="discussionMessage"
                                class="form-control shadow-none"
                                rows="3"
                                placeholder="Tulis pesan... ketik @ untuk mention rekan tim"
                                style="padding-right: 120px; resize:none; border-radius: 8px; font-size: 13.5px;"
                                required></textarea>

                            {{-- Hidden inputs untuk mention --}}
                            <div id="mentionInputs"></div>

                            <button type="submit" class="btn btn-primary position-absolute d-flex align-items-center shadow-xs"
                                style="bottom:12px; right:12px; padding: 6px 14px; font-size: 13px; border-radius: 6px;">
                                <i class="mdi mdi-send me-1"></i> Kirim
                            </button>
                        </div>

                        {{-- Mention dropdown popup --}}
                        <ul id="mentionDropdown"
                            class="list-group shadow border-0"
                            style="display:none; position:absolute; z-index:999; min-width:240px; max-height:200px; overflow-y:auto; border-radius: 8px;">
                        </ul>

                        {{-- Tag mention yang dipilih --}}
                        <div id="mentionTags" class="d-flex flex-wrap gap-1 mt-2"></div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Sidebar Actions Column --}}
        <div class="col-xl-4 col-lg-5 col-12">
            {{-- Action Box --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h6 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                        <i class="mdi mdi-lightning-bolt-circle text-primary fs-5 me-2"></i> Tindakan Sales
                    </h6>
                </div>
                <div class="card-body p-4">
                    @if (in_array(Auth::user()->role, ['Admin', 'Support']))
                        {{-- Admin / Support Provide & Assign Form --}}
                        <form action="{{ route('add_sales.prospect', $prospect->id) }}" method="POST">
                            @csrf
                            <label class="form-label fw-bold text-dark small mb-2 text-uppercase" style="letter-spacing: 0.5px;">Status Penugasan Prospek</label>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="form-check custom-option custom-option-icon {{ @$prospect->provide == '1' ? 'checked' : '' }} {{ @$prospect->quotation ? 'disabled' : '' }} p-2 rounded border">
                                        <label class="form-check-label custom-option-content" for="provideCheck1">
                                            <span class="custom-option-body text-center">
                                                <i class="mdi mdi-file-check-outline fs-4 text-success mb-1"></i>
                                                <span class="custom-option-title d-block small fw-bold">Provided</span>
                                            </span>
                                            <input name="provideCheck" class="form-check-input check-provide" type="radio" value="1" id="provideCheck1" {{ @$prospect->provide == '1' ? 'checked' : '' }} {{ @$prospect->quotation ? 'disabled' : '' }}>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-check custom-option custom-option-icon {{ @$prospect->provide == '0' ? 'checked' : '' }} {{ @$prospect->quotation ? 'disabled' : '' }} p-2 rounded border">
                                        <label class="form-check-label custom-option-content" for="provideCheck2">
                                            <span class="custom-option-body text-center">
                                                <i class="mdi mdi-file-alert-outline fs-4 text-danger mb-1"></i>
                                                <span class="custom-option-title d-block small fw-bold">No Provide</span>
                                            </span>
                                            <input name="provideCheck" class="form-check-input check-no-provide" type="radio" value="0" id="provideCheck2" {{ @$prospect->provide == '0' ? 'checked' : '' }} {{ @$prospect->quotation ? 'disabled' : '' }}>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-floating form-floating-outline form-sales mb-3" {{ @$prospect->provide == '1' ? '' : 'hidden' }}>
                                <select class="form-select" id="selectSales" name="sales" {{ @$prospect->quotation ? 'disabled' : '' }}>
                                    <option value="" disabled>-- Pilih Sales --</option>
                                    @foreach ($sales as $user)
                                        <option value="{{ $user->id }}" {{ @$prospect->id_sales == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <label for="selectSales">Tugaskan ke Sales</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 waves-effect waves-light shadow-sm">
                                <i class="mdi mdi-content-save-outline me-1"></i> Simpan Penugasan
                            </button>
                        </form>

                        @if (Auth::user()->role == 'Support')
                            <div class="pt-3 mt-3 border-top">
                                <button type="button" class="btn btn-outline-danger w-100 waves-effect delete-prospect" data-id="{{ $prospect->id }}">
                                    <i class="mdi mdi-trash-can-outline me-1"></i> Hapus Prospek
                                </button>
                            </div>
                        @endif
                    @elseif (Auth::user()->role == 'Sales')
                        {{-- Sales Actions --}}
                        <div class="d-flex flex-column gap-2">
                            <button type="button" class="btn btn-primary btn-lg with-quote waves-effect waves-light shadow-sm text-nowrap fw-bold py-2 mb-1" data-id="{{ $prospect->id }}">
                                <i class="mdi mdi-lightning-bolt me-1 fs-5"></i> Smart Quote
                            </button>

                            @if ($prospect->level == null)
                                <button type="button" class="btn btn-outline-success fu-wa waves-effect text-start py-2" data-id="{{ $prospect->id }}">
                                    <i class="mdi mdi-whatsapp me-2 text-success fs-5"></i> On Process Follow-Up WA
                                </button>
                                <button type="button" class="btn btn-outline-warning no-respond waves-effect text-start py-2" data-id="{{ $prospect->id }}">
                                    <i class="mdi mdi-phone-missed me-2 text-warning fs-5"></i> No Respond
                                </button>
                                <button type="button" class="btn btn-outline-danger without-quote waves-effect text-start py-2" data-id="{{ $prospect->id }}">
                                    <i class="mdi mdi-close-circle-outline me-2 text-danger fs-5"></i> No Quote (Loss)
                                </button>
                            @elseif ($prospect->level == 9)
                                <div class="my-2 text-center text-muted small position-relative">
                                    <span class="bg-white px-2 z-1 position-relative">atau hubungkan quotation</span>
                                    <hr class="position-absolute top-50 start-0 end-0 my-0 z-0">
                                </div>

                                <form action="{{ route('choose_quotation.prospect', $prospect->id) }}" method="POST" class="mb-2">
                                    @csrf
                                    <div class="form-floating form-floating-outline mb-2">
                                        <select class="form-select select2" id="Type" name="id_quotation">
                                            @forelse ($allQuotation as $item)
                                                <option value="{{ $item->id }}">
                                                    {{ $item->no_quote }} - {{ $item->title }}
                                                </option>
                                            @empty
                                                <option value="" disabled>Belum ada quotation untuk PIC ini</option>
                                            @endforelse
                                        </select>
                                        <label for="Type">Pilih Quotation Yang Ada</label>
                                    </div>
                                    <button type="submit" class="btn btn-outline-primary w-100 waves-effect py-2">
                                        <i class="mdi mdi-link-variant me-1"></i> Tautkan Quotation
                                    </button>
                                </form>

                                <div class="pt-2 border-top d-flex flex-column gap-2">
                                    <button type="button" class="btn btn-outline-warning no-respond waves-effect text-start py-2" data-id="{{ $prospect->id }}">
                                        <i class="mdi mdi-phone-missed me-2 text-warning fs-5"></i> No Respond
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary no-provide waves-effect text-start py-2" data-id="{{ $prospect->id }}">
                                        <i class="mdi mdi-cancel me-2 text-muted fs-5"></i> No Provide
                                    </button>
                                    <button type="button" class="btn btn-outline-danger without-quote waves-effect text-start py-2" data-id="{{ $prospect->id }}">
                                        <i class="mdi mdi-close-circle-outline me-2 text-danger fs-5"></i> No Quote (Loss)
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Support Marketing PIC Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar avatar-md">
                            @if ($prospect->support && $prospect->support->image)
                                <img src="/{{ $prospect->support->image }}" alt="{{ $prospect->support->name }}" class="rounded-circle shadow-xs" style="object-fit: cover;">
                            @else
                                <span class="avatar-initial rounded-circle bg-label-info fw-bold fs-5">
                                    {{ strtoupper(substr($prospect->support->name ?? 'M', 0, 1)) }}
                                </span>
                            @endif
                        </div>
                        <div>
                            <div class="text-muted small" style="font-size: 0.75rem;">Marketing Support PIC:</div>
                            <div class="fw-bold text-heading fs-6">{{ $prospect->support->name ?? 'Marketing Team' }}</div>
                            <small class="text-muted">{{ $prospect->support->email ?? 'marketing@reftech.id' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    @php
        $categoryOptions = [
            'Service Compressor',
            'Rental Compressor',
            'Sparepart Compressor',
            'Instalasi Piping',
            'Air Audit',
            'Fire System',
            'HVAC System',
            'Unit Baru/Second',
        ];
        $sourceOptions = [
            'IG' => 'Instagram',
            'WhatsApp' => 'WhatsApp',
            'LinkedIn' => 'LinkedIn',
            'Website' => 'Website',
            'Indotrading' => 'Indotrading',
            'Tokopedia' => 'Tokopedia',
            'OLX' => 'OLX',
            'Google' => 'Google',
            'Google Ads' => 'Google Ads',
            'Meta Ads' => 'Meta Ads',
            'Facebook' => 'Facebook',
            'Other' => 'Other',
        ];
        $currentCategory = old('category', $prospect->category);
        $currentSource = old('source', $client->source);
        $currentDate = old('date', $prospect->date ? \Carbon\Carbon::parse($prospect->date)->format('Y-m-d') : '');
    @endphp

    <div class="modal fade" id="editProspectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="{{ route('prospect.update', $prospect->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            <i class="mdi mdi-pencil-outline me-1 text-primary"></i> Edit Detail Prospek #{{ $prospect->id }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Kebutuhan / Detail Prospek --}}
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="mdi mdi-text-box-search-outline me-1"></i> Kebutuhan / Detail Prospek
                        </h6>
                        <div class="row g-3 mb-2">
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" name="kebutuhan" id="editKebutuhan"
                                        placeholder="Rincian kebutuhan klien">{{ old('kebutuhan', $prospect->kebutuhan) }}</textarea>
                                    <label for="editKebutuhan">Deskripsi Kebutuhan</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="category" id="editCategory">
                                        <option value="">-- Pilih Kategori --</option>
                                        @if ($currentCategory && !in_array($currentCategory, $categoryOptions))
                                            <option value="{{ $currentCategory }}" selected>{{ $currentCategory }}</option>
                                        @endif
                                        @foreach ($categoryOptions as $opt)
                                            <option value="{{ $opt }}" {{ $currentCategory == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    <label for="editCategory">Kategori</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="source" id="editSource">
                                        <option value="">-- Pilih Source --</option>
                                        @if ($currentSource && !array_key_exists($currentSource, $sourceOptions))
                                            <option value="{{ $currentSource }}" selected>{{ $currentSource }}</option>
                                        @endif
                                        @foreach ($sourceOptions as $val => $label)
                                            <option value="{{ $val }}" {{ $currentSource == $val ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <label for="editSource">Sumber Prospek (Source)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" class="form-control" name="date" id="editDate" value="{{ $currentDate }}">
                                    <label for="editDate">Tanggal Prospek</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="source_detail" id="editSourceDetail"
                                        maxlength="100" placeholder="example.com"
                                        value="{{ old('source_detail', $client->source_detail) }}">
                                    <label for="editSourceDetail">Website Domain (opsional)</label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        {{-- Informasi Perusahaan & Kontak PIC --}}
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="mdi mdi-domain me-1"></i> Informasi Perusahaan & Kontak PIC
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="company" id="editCompany"
                                        placeholder="PT xxxxxxx" value="{{ old('company', $client->company) }}" required>
                                    <label for="editCompany">Nama Perusahaan</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" name="address" id="editAddress"
                                        placeholder="Alamat utama">{{ old('address', $client->address) }}</textarea>
                                    <label for="editAddress">Alamat Utama</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <textarea class="form-control h-px-100" name="subAddress" id="editSubAddress"
                                        placeholder="Sub alamat / plant">{{ old('subAddress', $client->subAddress) }}</textarea>
                                    <label for="editSubAddress">Sub Alamat / Plant</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="area" id="editArea"
                                        placeholder="Area / Kota" value="{{ old('area', $client->area) }}">
                                    <label for="editArea">Area / Kota</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select class="form-select" name="ru" id="editRu">
                                        <option value="">-- Pilih --</option>
                                        <option value="User" {{ old('ru', $client->ru) == 'User' ? 'selected' : '' }}>User</option>
                                        <option value="Reseller" {{ old('ru', $client->ru) == 'Reseller' ? 'selected' : '' }}>Reseller</option>
                                    </select>
                                    <label for="editRu">Tipe Klien (R/U)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="unit" id="editUnit"
                                        placeholder="Contoh: KAESER SK 21" value="{{ old('unit', $client->unit) }}">
                                    <label for="editUnit">Existing Machine / Unit</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="phone" id="editPhone"
                                        placeholder="021xxxxxxx" value="{{ old('phone', $client->phone) }}">
                                    <label for="editPhone">Telepon Kantor</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="email" id="editEmail"
                                        placeholder="company@email.com" value="{{ old('email', $client->email) }}">
                                    <label for="editEmail">Email Kantor</label>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="text-muted small text-uppercase fw-semibold mt-2">Person In Charge (PIC)</div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="namePic" id="editNamePic"
                                        placeholder="Nama PIC" value="{{ old('namePic', $pic->name_pic) }}">
                                    <label for="editNamePic">Nama PIC</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="position" id="editPosition"
                                        placeholder="Contoh: Purchasing" value="{{ old('position', $pic->position) }}">
                                    <label for="editPosition">Jabatan PIC</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="phonePic" id="editPhonePic"
                                        placeholder="08xxxxxxxxxx" value="{{ old('phonePic', $pic->phone_pic) }}">
                                    <label for="editPhonePic">Nomor WhatsApp / HP</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" name="emailPic" id="editEmailPic"
                                        placeholder="pic@email.com" value="{{ old('emailPic', $pic->email_pic) }}">
                                    <label for="editEmailPic">Email PIC</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-content-save-outline me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpush

@push('after-style')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <style>
        .custom-option { cursor: pointer; transition: all 0.2s ease; }
        .custom-option:hover { border-color: #696cff !important; background: #f8f9ff; }
        .custom-option.checked { border-color: #696cff !important; background: #f0f2ff; }

        /* Discussion Chat Bubbles (Same as Purchase Request) */
        .chat-bubble-me {
            background-color: #ECEAFE;
            border-radius: 12px 12px 2px 12px !important;
            color: #2F3349;
            border: 1px solid #d5d0fa;
        }

        .chat-bubble-other {
            background-color: #ffffff;
            border-radius: 12px 12px 12px 2px !important;
            color: #2F3349;
            border: 1px solid rgba(24, 28, 33, 0.08);
        }

        .discussion-list::-webkit-scrollbar {
            width: 5px;
        }
        .discussion-list::-webkit-scrollbar-track {
            background: transparent;
        }
        .discussion-list::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 10px;
        }

        #mentionDropdown .list-group-item { cursor: pointer; padding: 6px 12px; }
        #mentionDropdown .list-group-item:hover { background: #f0f0f0; }
        #mentionDropdown .list-group-item img { width: 28px; height: 28px; object-fit: cover; }
        .mention-tag { background: #e7f1ff; color: #696cff; border: 1px solid #bfdbfe; border-radius: 999px; padding: 2px 10px; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; }
        .mention-tag .remove-mention { cursor: pointer; font-weight: bold; color: #6b7280; }
        .mention-tag .remove-mention:hover { color: #ef4444; }

        @media (min-width: 768px) {
            .border-end-md {
                border-right: 1px solid rgba(24, 28, 33, 0.08) !important;
            }
        }
    </style>
@endpush

@push('after-script')
    <script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
@endpush

@push('page-script')
    <script>
        $(document).ready(function() {
            if ($('.select2').length) {
                $('.select2').select2();
            }

            // Scroll diskusi ke pesan terbaru
            var list = document.querySelector('.discussion-list');
            if (list) list.scrollTop = list.scrollHeight;

            // Buka kembali modal edit bila validasi gagal
            @if ($errors->any())
                var editModalEl = document.getElementById('editProspectModal');
                if (editModalEl) bootstrap.Modal.getOrCreateInstance(editModalEl).show();
            @endif
        });

        // ── @mention logic (Identical to Purchase Request) ───────────────────
        var allUsers = @json($allUsers ?? []);
        var selectedMentions = {}; // id => name
        var mentionStartIndex = -1;

        var textarea = document.getElementById('discussionMessage');
        var dropdown = document.getElementById('mentionDropdown');
        var tagsEl = document.getElementById('mentionTags');
        var inputsEl = document.getElementById('mentionInputs');

        function renderDropdown(query) {
            if (!dropdown || !textarea) return;
            var filtered = allUsers.filter(function (u) {
                return u.name.toLowerCase().indexOf(query.toLowerCase()) !== -1 && !selectedMentions[u.id];
            }).slice(0, 8);

            dropdown.innerHTML = '';
            if (!filtered.length) { dropdown.style.display = 'none'; return; }

            filtered.forEach(function (u) {
                var li = document.createElement('li');
                li.className = 'list-group-item d-flex align-items-center gap-2';
                var img = u.image ? '/' + u.image : 'assets/img/avatars/1.png';
                li.innerHTML = '<img src="' + img + '" class="rounded-circle shadow-xs" width="28" height="28" style="object-fit:cover;">' +
                    '<span class="fw-semibold text-heading small">' + u.name + '</span>' +
                    '<small class="text-muted ms-auto small">' + (u.role || '') + '</small>';
                li.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    selectMention(u);
                });
                dropdown.appendChild(li);
            });

            dropdown.style.display = 'block';
            dropdown.style.top = (textarea.offsetTop + textarea.offsetHeight) + 'px';
            dropdown.style.left = textarea.offsetLeft + 'px';
        }

        function selectMention(user) {
            if (!textarea) return;
            var val = textarea.value;
            var before = val.substring(0, mentionStartIndex);
            var after = val.substring(textarea.selectionStart);
            textarea.value = before + '@' + user.name + ' ' + after;
            textarea.focus();

            selectedMentions[user.id] = user.name;
            if (dropdown) dropdown.style.display = 'none';
            mentionStartIndex = -1;
            renderTags();
        }

        function renderTags() {
            if (!tagsEl || !inputsEl) return;
            tagsEl.innerHTML = '';
            inputsEl.innerHTML = '';
            Object.keys(selectedMentions).forEach(function (id) {
                var span = document.createElement('span');
                span.className = 'mention-tag';
                span.innerHTML = '@' + selectedMentions[id] +
                    ' <span class="remove-mention ms-1" data-id="' + id + '">&times;</span>';
                tagsEl.appendChild(span);

                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'mention[]';
                inp.value = id;
                inputsEl.appendChild(inp);
            });

            tagsEl.querySelectorAll('.remove-mention').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    delete selectedMentions[this.dataset.id];
                    renderTags();
                });
            });
        }

        if (textarea) {
            textarea.addEventListener('input', function () {
                var val = this.value;
                var pos = this.selectionStart;

                var atPos = -1;
                for (var i = pos - 1; i >= 0; i--) {
                    if (val[i] === '@') { atPos = i; break; }
                    if (val[i] === ' ' || val[i] === '\n') break;
                }

                if (atPos !== -1) {
                    mentionStartIndex = atPos;
                    var query = val.substring(atPos + 1, pos);
                    renderDropdown(query);
                } else {
                    if (dropdown) dropdown.style.display = 'none';
                    mentionStartIndex = -1;
                }
            });

            textarea.addEventListener('blur', function () {
                setTimeout(function () {
                    if (dropdown) dropdown.style.display = 'none';
                }, 200);
            });
        }

        // Provide toggle logic
        $(document).on('change', '.check-provide', function() {
            if ($(this).is(':checked')) {
                $('.form-sales').removeAttr('hidden');
            } else {
                $('.form-sales').attr('hidden', 'hidden');
            }
        });

        $(document).on('change', '.check-no-provide', function() {
            if ($(this).is(':checked')) {
                $('.form-sales').attr('hidden', 'hidden');
            } else {
                $('.form-sales').removeAttr('hidden');
            }
        });

        // Smart Quote Action
        $(document).on('click', '.with-quote', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Create Smart Quote?",
                text: "Konversi prospek ini menjadi Smart Quotation resmi?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Buat Smart Quote!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('prospect') }}/' + 'with_quotation/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil!",
                                    text: "Prospek berhasil dialihkan ke Smart Quote.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                                window.setTimeout(function() {
                                    window.location.href = '/smart-quote/create?prospect_id=' + id;
                                }, 1000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Gagal membuat Smart Quote!'
                                });
                            }
                        }
                    });
                }
            });
        });

        // Without Quote Action
        $(document).on('click', '.without-quote', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Tandai No Quote (Loss)?",
                text: "Status prospek ini akan diubah menjadi tanpa penawaran.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Simpan!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-danger me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('prospect') }}/' + 'without_quotation/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Tersimpan!",
                                    text: "Status prospek berhasil diperbarui.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                                window.setTimeout(function() {
                                    window.location.href = '/prospect';
                                }, 1000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Gagal memperbarui status!'
                                });
                            }
                        }
                    });
                }
            });
        });

        // Follow Up WhatsApp
        $(document).on('click', '.fu-wa', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Proses Follow Up WA?",
                text: "Prospek ini akan dipindahkan ke daftar Follow-Up In Progress.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Pindahkan ke Follow Up!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-success me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('prospect') }}/' + 'onProcessFU/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil!",
                                    text: "Prospek dipindahkan ke status Follow-Up.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                                window.setTimeout(function() {
                                    window.location.href = '/prospect';
                                }, 1000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Gagal memproses follow-up!'
                                });
                            }
                        }
                    });
                }
            });
        });

        // No Respond Action
        $(document).on('click', '.no-respond', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Tandai No Respond?",
                text: "Prospek ini akan ditandai tidak ada respon dari klien.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Tandai No Respond!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-warning me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('prospect') }}/' + 'no_respond/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Tersimpan!",
                                    text: "Status prospek berhasil diperbarui.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                                window.setTimeout(function() {
                                    window.location.href = '/prospect';
                                }, 1000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Gagal memperbarui status!'
                                });
                            }
                        }
                    });
                }
            });
        });

        // No Provide Action
        $(document).on('click', '.no-provide', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Tandai No Provide?",
                text: "Status prospek ini akan diubah menjadi No Provide.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, No Provide!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-secondary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        url: '{{ url('prospect') }}/' + 'no_provide/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'POST',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Tersimpan!",
                                    text: "Status prospek berhasil diperbarui.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                                window.setTimeout(function() {
                                    window.location.href = '/prospect';
                                }, 1000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Gagal memperbarui status!'
                                });
                            }
                        }
                    });
                }
            });
        });

        // Delete Prospect (Support)
        $(document).on('click', '.delete-prospect', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Hapus Prospek Ini?",
                text: "Data prospek yang dihapus tidak dapat dikembalikan!",
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
                    $.ajax({
                        url: '{{ url('prospect') }}/' + id,
                        type: 'POST',
                        data: {
                            '_method': 'DELETE',
                            '_token': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Terhapus!",
                                    text: "Prospek berhasil dihapus.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                    showConfirmButton: false,
                                    timer: 1000
                                });
                                window.setTimeout(function() {
                                    window.location.href = '/prospect';
                                }, 1000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Gagal menghapus prospek!'
                                });
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
