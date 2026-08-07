
<?php
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

    $feed = $statusItems->concat($commentItems)->concat($revisionItems)->sortByDesc('created_at')->values();
?>

<div class="card border-0 shadow-sm overflow-hidden mb-4">
    
    <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="mdi mdi-forum-outline text-primary fs-5"></i> Activity &amp; Team Discussion
            </h6>
            <span class="badge bg-label-primary rounded-pill px-3 py-1 fw-semibold">
                <?php echo e($feed->count()); ?> Aktivitas
            </span>
        </div>
        
        <div class="nav nav-pills gap-1.5 mt-2" id="timeline-filter-pills" role="tablist">
            <button type="button" class="btn btn-xs btn-primary rounded-pill px-3 active filter-pill" data-filter="all">
                <i class="mdi mdi-view-list me-1"></i> Semua (<?php echo e($feed->count()); ?>)
            </button>
            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-3 filter-pill" data-filter="status">
                <i class="mdi mdi-update me-1"></i> Status (<?php echo e($quote->statusHistory->count()); ?>)
            </button>
            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-3 filter-pill" data-filter="comment">
                <i class="mdi mdi-comment-multiple-outline me-1"></i> Diskusi (<?php echo e($quote->comments->count()); ?>)
            </button>
            <?php if($allRevisions->count() > 0): ?>
                <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-3 filter-pill" data-filter="revision">
                    <i class="mdi mdi-file-replace-outline me-1"></i> Revisi (<?php echo e($allRevisions->count()); ?>)
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card-body p-4">
        <?php
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
        ?>

        
        <div class="card border bg-light-subtle shadow-none rounded-3 mb-4 overflow-hidden">
            <div class="card-header bg-white py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                <span class="fw-bold text-dark d-flex align-items-center gap-1.5" style="font-size: 12px;">
                    <i class="mdi mdi-pencil-box-outline text-primary fs-6"></i> Tulis Catatan / Komentar Internal
                </span>
                <span class="badge bg-label-secondary" style="font-size: 10px;">
                    <i class="mdi mdi-shield-lock-outline me-1"></i>Internal Team Only
                </span>
            </div>
            <div class="card-body p-3 bg-white">
                <form id="form-add-comment">
                    <div class="d-flex align-items-start gap-3">
                        <div class="avatar avatar-sm flex-shrink-0 mt-1">
                            <span class="avatar-initial rounded-circle bg-primary text-white fw-bold">
                                <?php echo e(strtoupper(substr(Auth::user()->name ?? 'U', 0, 1))); ?>

                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <textarea id="new-comment-text" class="form-control border p-2.5 text-dark" rows="2"
                                placeholder="Ketik perkembangan follow up, kesepakatan dengan client, atau instruksi internal..." style="font-size: 13px; border-radius: 8px; background:#fafafa;" required></textarea>
                            <div class="d-flex align-items-center justify-content-between mt-2 pt-2 border-top">
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="mdi mdi-information-outline me-1"></i>Komentar hanya dapat dilihat oleh tim internal
                                </small>
                                <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm fw-semibold" id="btn-submit-comment">
                                    <i class="mdi mdi-send me-1"></i> Kirim Komentar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if($feed->count() === 0): ?>
            
            <div class="text-center py-5">
                <div class="avatar avatar-md mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-secondary">
                        <i class="mdi mdi-clock-outline fs-4 text-secondary"></i>
                    </span>
                </div>
                <h6 class="fw-semibold text-muted mb-1" style="font-size: 13.5px;">Belum Ada Aktivitas</h6>
                <small class="text-muted" style="font-size: 11.5px;">Aktivitas pergerakan status, revisi, dan catatan tim akan tercatat secara kronologis di sini.</small>
            </div>
        <?php else: ?>
            
            <div class="timeline-stream-container">
                <?php $__currentLoopData = $feed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($item['type'] === 'status'): ?>
                        <?php
                            $hist = $item['data'];
                            $hst = $hstMap[$hist->status] ?? ['label' => ucfirst(str_replace('_',' ',$hist->status)), 'color' => 'secondary', 'icon' => 'mdi-circle-outline'];
                        ?>
                        <div class="timeline-feed-item item-status mb-3 p-3 rounded-3 border bg-white shadow-xs position-relative overflow-hidden">
                            <div class="position-absolute top-0 start-0 bottom-0 bg-<?php echo e($hst['color']); ?>" style="width: 4px;"></div>
                            <div class="ps-2">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-label-<?php echo e($hst['color']); ?> rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                            <i class="mdi <?php echo e($hst['icon']); ?>"></i> <?php echo e($hst['label']); ?>

                                        </span>
                                        <small class="text-muted fw-semibold" style="font-size: 11px;">System Status Log</small>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;" title="<?php echo e($hist->created_at->format('d M Y H:i')); ?>">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        <?php echo e($hist->created_at->diffInHours(now()) > 48
                                            ? $hist->created_at->format('d M Y, H:i')
                                            : $hist->created_at->diffForHumans()); ?>

                                    </small>
                                </div>
                                <?php if($hist->note): ?>
                                    <div class="mt-2 p-2.5 rounded-2 bg-light text-dark" style="font-size: 12px; border-left: 3px solid var(--bs-<?php echo e($hst['color']); ?>); background-color: #f8f9fa;">
                                        <i class="mdi mdi-information-outline text-<?php echo e($hst['color']); ?> me-1"></i> <?php echo e($hist->note); ?>

                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif($item['type'] === 'revision'): ?>
                        <?php $rev = $item['data']; ?>
                        <div class="timeline-feed-item item-revision mb-3 p-3 rounded-3 border bg-white shadow-xs position-relative overflow-hidden" data-revision-id="<?php echo e($rev->id); ?>">
                            <div class="position-absolute top-0 start-0 bottom-0 bg-primary" style="width: 4px;"></div>
                            <div class="ps-2">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary rounded-pill px-3 py-1 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                            <i class="mdi mdi-file-replace-outline"></i> Revisi R<?php echo e($rev->revision_number); ?> Diterbitkan
                                        </span>
                                        <small class="text-muted fw-semibold" style="font-size: 11px;">Revision Update</small>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;" title="<?php echo e($rev->created_at->format('d M Y H:i')); ?>">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        <?php echo e($rev->created_at->diffInHours(now()) > 48
                                            ? $rev->created_at->format('d M Y, H:i')
                                            : $rev->created_at->diffForHumans()); ?>

                                    </small>
                                </div>
                                <div class="mt-2 p-2.5 rounded-2 bg-primary-subtle text-primary border border-primary-subtle d-flex align-items-center justify-content-between flex-wrap gap-2" style="font-size: 12px;">
                                    <div>
                                        <i class="mdi mdi-link-variant me-1"></i> Penawaran direvisi menjadi versi <strong>#<?php echo e($rev->no_quote); ?></strong> (Total: Rp <?php echo e(number_format($rev->total, 0, ',', '.')); ?>)
                                    </div>
                                    <?php if($rev->id !== $quote->id): ?>
                                        <a href="<?php echo e(route('unit-quotation.show', $rev->id)); ?>" class="btn btn-xs btn-primary rounded-pill px-3 py-1 flex-shrink-0">
                                            Buka Versi R<?php echo e($rev->revision_number); ?> <i class="mdi mdi-arrow-right ms-1"></i>
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-primary rounded-pill flex-shrink-0">Versi Saat Ini</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php $qcomment = $item['data']; ?>
                        <div class="timeline-feed-item item-comment mb-3 p-3 rounded-3 border bg-white shadow-xs position-relative overflow-hidden" data-comment-id="<?php echo e($qcomment->id); ?>">
                            <div class="position-absolute top-0 start-0 bottom-0 bg-info" style="width: 4px;"></div>
                            <div class="ps-2">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-xs">
                                            <span class="avatar-initial rounded-circle bg-info text-white fw-bold" style="font-size: 10px;">
                                                <?php echo e(strtoupper(substr($qcomment->user->name ?? 'U', 0, 1))); ?>

                                            </span>
                                        </div>
                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 13px;"><?php echo e($qcomment->user->name ?? 'User'); ?></h6>
                                        <span class="badge bg-label-info rounded-pill px-2 py-0.5" style="font-size: 9.5px;">
                                            <?php echo e($qcomment->user->role ?? 'Team'); ?>

                                        </span>
                                    </div>
                                    <small class="text-muted" style="font-size: 11px;" title="<?php echo e($qcomment->created_at->format('d M Y H:i')); ?>">
                                        <i class="mdi mdi-clock-outline me-1"></i>
                                        <?php echo e($qcomment->created_at->diffInHours(now()) > 48
                                            ? $qcomment->created_at->format('d M Y, H:i')
                                            : $qcomment->created_at->diffForHumans()); ?>

                                    </small>
                                </div>
                                <p class="mb-2 comment-text text-dark" style="font-size: 13px; white-space: pre-line; line-height: 1.5;"><?php echo e($qcomment->comment); ?></p>
                                <?php if($qcomment->user_id === Auth::id()): ?>
                                    <div class="comment-actions pt-2 border-top d-flex align-items-center gap-2">
                                        <a href="javascript:void(0);" class="btn-edit-comment text-primary text-decoration-none d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                            <i class="mdi mdi-pencil-outline"></i> Edit
                                        </a>
                                        <span class="text-muted" style="font-size: 10px;">•</span>
                                        <a href="javascript:void(0);" class="btn-delete-comment text-danger text-decoration-none d-inline-flex align-items-center gap-1" style="font-size: 11px;">
                                            <i class="mdi mdi-trash-can-outline"></i> Hapus
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/unit-quotation/components/activity-timeline.blade.php ENDPATH**/ ?>