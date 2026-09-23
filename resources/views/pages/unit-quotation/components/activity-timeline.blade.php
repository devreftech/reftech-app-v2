{{-- Activity & Discussion Timeline Component (Modern Chat Style) --}}
@php
    $rootId = $quote->root_id ?? $quote->id;
    $allRevisions = \App\Models\UnitQuotation::where(function ($q) use ($rootId) {
        $q->where('id', $rootId)->orWhere('root_id', $rootId);
    })->where('revision_number', '>', 0)->get();

    $revisionItems = $allRevisions->map(function ($rev) {
        return ['type' => 'revision', 'data' => $rev, 'created_at' => $rev->created_at];
    });

    $statusItems = $quote->statusHistory->map(function ($hist) {
        return ['type' => 'status', 'data' => $hist, 'created_at' => $hist->created_at];
    });

    $commentItems = $quote->comments->map(function ($comment) {
        return ['type' => 'comment', 'data' => $comment, 'created_at' => $comment->created_at];
    });

    // Chronological order (oldest to newest) for natural chat reading flow
    $feed = $statusItems->concat($commentItems)->concat($revisionItems)->sortBy('created_at')->values();
@endphp

<div class="card border-0 shadow-sm mb-4" id="activity-feed">
    {{-- Header --}}
    <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="mdi mdi-forum-outline text-primary fs-5"></i> Diskusi Tim &amp; Catatan Follow-Up
            </h6>
            <span class="badge bg-label-primary rounded-pill px-3 py-1 fw-semibold">
                {{ $quote->comments->count() }} Diskusi / {{ $feed->count() }} Total
            </span>
        </div>
        {{-- Quick Filter Pills --}}
        <div class="nav nav-pills gap-1.5 mt-2" id="timeline-filter-pills" role="tablist">
            <button type="button" class="btn btn-xs btn-primary rounded-pill px-3 active filter-pill" data-filter="all">
                <i class="mdi mdi-view-list me-1"></i> Semua ({{ $feed->count() }})
            </button>
            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-3 filter-pill" data-filter="comment">
                <i class="mdi mdi-comment-multiple-outline me-1"></i> Diskusi ({{ $quote->comments->count() }})
            </button>
            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-3 filter-pill" data-filter="status">
                <i class="mdi mdi-update me-1"></i> Status Log ({{ $quote->statusHistory->count() }})
            </button>
            @if ($allRevisions->count() > 0)
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-3 filter-pill" data-filter="revision">
                    <i class="mdi mdi-file-replace-outline me-1"></i> Revisi ({{ $allRevisions->count() }})
                </button>
            @endif
        </div>
    </div>

    <div class="card-body p-4">
        @php
            $hstMap = [
                'draft'        => ['label' => 'Quotation Dibuat',   'color' => 'secondary', 'icon' => 'mdi-file-document-outline'],
                'sent'         => ['label' => 'Terkirim ke Client', 'color' => 'info',      'icon' => 'mdi-send-outline'],
                'negotiation'  => ['label' => 'Negosiasi',          'color' => 'warning',   'icon' => 'mdi-handshake-outline'],
                'hot_prospect' => ['label' => 'Hot Prospect',       'color' => 'warning',   'icon' => 'mdi-fire'],
                'revision'     => ['label' => 'Revisi',             'color' => 'primary',   'icon' => 'mdi-file-replace-outline'],
                'po_received'  => ['label' => 'PO Diterima',        'color' => 'success',   'icon' => 'mdi-clipboard-check-outline'],
                'done'         => ['label' => 'Done',               'color' => 'success',   'icon' => 'mdi-check-circle-outline'],
                'loss'         => ['label' => 'Loss',               'color' => 'danger',    'icon' => 'mdi-close-circle-outline'],
            ];
        @endphp

        {{-- Chat & Timeline Stream Container --}}
        <div class="discussion-stream-box discussion-list mb-3" id="quotationDiscussionStream" style="max-height: 460px; overflow-y: auto;">
            @if ($feed->count() === 0)
                {{-- Empty State --}}
                <div class="text-center text-muted py-5">
                    <div class="avatar avatar-lg mx-auto mb-3 bg-label-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 50%;">
                        <i class="mdi mdi-forum-outline fs-3"></i>
                    </div>
                    <p class="mb-0 fw-semibold text-dark" style="font-size: 13.5px;">Belum ada catatan atau diskusi.</p>
                    <small class="text-muted" style="font-size: 11.5px;">Mulai percakapan tim dengan mengetik pesan di bawah.</small>
                </div>
            @else
                @foreach ($feed as $item)
                    @if ($item['type'] === 'status')
                        @php
                            $hist = $item['data'];
                            $hst = $hstMap[$hist->status] ?? ['label' => ucfirst(str_replace('_',' ',$hist->status)), 'color' => 'secondary', 'icon' => 'mdi-circle-outline'];
                        @endphp
                        {{-- Status Change Milestone Pill --}}
                        <div class="timeline-feed-item item-status my-3 text-center">
                            <span class="badge bg-label-{{ $hst['color'] }} rounded-pill px-3 py-1.5 shadow-xs d-inline-flex align-items-center gap-1.5 border border-{{ $hst['color'] }}-subtle" style="font-size: 11px; max-width: 92%; white-space: normal; text-align: left;">
                                <i class="mdi {{ $hst['icon'] }} fs-6"></i>
                                <strong>{{ $hst['label'] }}</strong>
                                @if ($hist->note)
                                    <span class="text-muted ms-1">— {{ $hist->note }}</span>
                                @endif
                                <span class="text-muted ms-auto ps-2" style="font-size: 10px;" title="{{ $hist->created_at->format('d M Y H:i') }}">
                                    • {{ $hist->created_at->diffInHours(now()) > 24 ? $hist->created_at->format('d M Y, H:i') : $hist->created_at->diffForHumans() }}
                                </span>
                            </span>
                        </div>

                    @elseif ($item['type'] === 'revision')
                        @php $rev = $item['data']; @endphp
                        {{-- Revision Published Milestone Banner --}}
                        <div class="timeline-feed-item item-revision my-3 p-2.5 rounded-3 bg-primary-subtle text-primary border border-primary-subtle d-flex align-items-center justify-content-between flex-wrap gap-2 shadow-xs" data-revision-id="{{ $rev->id }}">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary rounded-pill px-2.5 py-1 fw-semibold">
                                    <i class="mdi mdi-file-replace-outline me-1"></i>Revisi R{{ $rev->revision_number }}
                                </span>
                                <span style="font-size: 12px;">Penawaran direvisi ke versi <strong>#{{ $rev->no_quote }}</strong> (Total: Rp {{ number_format($rev->total, 0, ',', '.') }})</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <small class="text-muted" style="font-size: 11px;" title="{{ $rev->created_at->format('d M Y H:i') }}">
                                    {{ $rev->created_at->diffInHours(now()) > 24 ? $rev->created_at->format('d M Y, H:i') : $rev->created_at->diffForHumans() }}
                                </small>
                                @if ($rev->id !== $quote->id)
                                    <a href="{{ route('unit-quotation.show', $rev->id) }}" class="btn btn-xs btn-primary rounded-pill px-2.5 py-1">
                                        Buka R{{ $rev->revision_number }} <i class="mdi mdi-arrow-right ms-1"></i>
                                    </a>
                                @else
                                    <span class="badge bg-label-primary rounded-pill">Versi Ini</span>
                                @endif
                            </div>
                        </div>

                    @else
                        @php
                            $qcomment = $item['data'];
                            $isMe = $qcomment->user_id == Auth::id();
                            $userInitial = strtoupper(substr($qcomment->user->name ?? 'U', 0, 1));
                            $userRole = $qcomment->user->role ?? 'Team';
                        @endphp
                        {{-- Modern Chat Message Bubble --}}
                        <div class="timeline-feed-item item-comment d-flex gap-2.5 mb-3 {{ $isMe ? 'flex-row-reverse' : '' }}" data-comment-id="{{ $qcomment->id }}">
                            {{-- User Avatar --}}
                            <div class="flex-shrink-0">
                                @if ($qcomment->user && $qcomment->user->image)
                                    <img src="{{ url('') . '/' . $qcomment->user->image }}"
                                        class="rounded-circle border border-2 border-white shadow-xs"
                                        style="width: 36px; height: 36px; object-fit: cover;"
                                        alt="{{ $qcomment->user->name }}"
                                        onerror="this.outerHTML='<span class=\'avatar-initial rounded-circle bg-label-{{ $isMe ? 'primary' : 'info' }} fw-bold d-flex align-items-center justify-content-center shadow-xs\' style=\'width:36px;height:36px;font-size:13px;\'>{{ $userInitial }}</span>'">
                                @else
                                    <span class="avatar-initial rounded-circle bg-label-{{ $isMe ? 'primary' : 'info' }} fw-bold d-flex align-items-center justify-content-center shadow-xs" style="width: 36px; height: 36px; font-size: 13px;">
                                        {{ $userInitial }}
                                    </span>
                                @endif
                            </div>

                            {{-- Chat Bubble & Meta --}}
                            <div style="max-width: 78%;" class="{{ $isMe ? 'text-end' : '' }}">
                                <div class="d-flex align-items-center gap-1.5 mb-1 {{ $isMe ? 'flex-row-reverse' : '' }}">
                                    <span class="fw-bold text-dark" style="font-size: 12.5px;">{{ $qcomment->user->name ?? 'User' }}</span>
                                    @if ($userRole)
                                        <span class="badge bg-label-{{ $isMe ? 'primary' : 'secondary' }} rounded-pill px-1.5 py-0.5" style="font-size: 9.5px;">
                                            {{ $userRole }}
                                        </span>
                                    @endif
                                    <span class="text-muted" style="font-size: 10.5px;" title="{{ $qcomment->created_at->format('d M Y H:i') }}">
                                        <i class="mdi mdi-clock-outline me-0.5"></i>
                                        {{ $qcomment->created_at->diffInHours(now()) > 24 ? $qcomment->created_at->format('d M Y H:i') : $qcomment->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <div class="p-3 rounded-3 shadow-xs text-start {{ $isMe ? 'chat-bubble-me' : 'chat-bubble-other' }}"
                                    style="word-break: break-word; font-size: 13px; line-height: 1.5;">
                                    @php
                                        $msgFormatted = e($qcomment->comment);
                                        if ($qcomment->relationLoaded('mentions') && $qcomment->mentions->isNotEmpty()) {
                                            foreach ($qcomment->mentions as $m) {
                                                $mName = $m->name ?? '';
                                                if ($mName) {
                                                    $msgFormatted = str_replace(
                                                        '@' . e($mName),
                                                        '<span class="badge bg-label-primary px-2 py-0.5 rounded-pill fw-bold" style="font-size: 11.5px;">@' . e($mName) . '</span>',
                                                        $msgFormatted
                                                    );
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="comment-text">{!! nl2br($msgFormatted) !!}</div>

                                    @if ($isMe)
                                        <div class="comment-actions mt-2 pt-1.5 border-top d-flex align-items-center justify-content-end gap-2" style="border-top-color: rgba(105, 108, 255, 0.2) !important;">
                                            <a href="javascript:void(0);" class="btn-edit-comment text-primary text-decoration-none d-inline-flex align-items-center gap-0.5" style="font-size: 11px;">
                                                <i class="mdi mdi-pencil-outline"></i> Edit
                                            </a>
                                            <span class="text-muted" style="font-size: 9px;">•</span>
                                            <a href="javascript:void(0);" class="btn-delete-comment text-danger text-decoration-none d-inline-flex align-items-center gap-0.5" style="font-size: 11px;">
                                                <i class="mdi mdi-trash-can-outline"></i> Hapus
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        {{-- Form Kirim Pesan Chat (Sama Persis dengan Prospect & Purchase Request) --}}
        <form id="form-add-comment" class="mt-3">
            <div class="position-relative mention-textarea-wrapper">
                {{-- Mention dropdown popup (opens upward above input) --}}
                <div id="mentionDropdown" class="mention-dropdown-menu" style="display:none;"></div>

                <textarea
                    name="comment"
                    id="new-comment-text"
                    class="form-control shadow-none border"
                    rows="3"
                    placeholder="Tulis pesan atau catatan diskusi internal... ketik @ untuk mention rekan tim"
                    style="padding-right: 120px; resize:none; border-radius: 10px; font-size: 13px;"
                    required></textarea>

                {{-- Hidden inputs untuk mention --}}
                <div id="mentionInputs"></div>

                <button type="submit" class="btn btn-primary position-absolute d-flex align-items-center shadow-xs"
                    id="btn-submit-comment"
                    style="bottom: 12px; right: 12px; padding: 6px 16px; font-size: 13px; border-radius: 8px; font-weight: 600;">
                    <i class="mdi mdi-send me-1"></i> Kirim
                </button>
            </div>

            {{-- Tag mention yang dipilih --}}
            <div id="mentionTags" class="d-flex flex-wrap gap-1 mt-2"></div>
        </form>
    </div>
</div>
