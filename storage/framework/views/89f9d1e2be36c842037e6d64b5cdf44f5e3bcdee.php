<?php $__env->startSection('title', 'Verifikasi - ' . $audit->no_audit); ?>
<?php $__env->startSection('content'); ?>
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php
        $headerBadge = [
            'Submitted' => 'bg-label-warning',
            'Verified' => 'bg-label-success',
            'Rejected' => 'bg-label-danger',
        ][$audit->status_submit] ?? 'bg-label-secondary';
        $canDecide = $audit->status_submit == 'Submitted';
    ?>

    <h4 class="fw-bold py-3 mb-0">
        <span class="text-muted fw-light">Verifikasi Audit Tools /</span> <?php echo e($audit->no_audit); ?>

        <span class="badge <?php echo e($headerBadge); ?> align-middle"><?php echo e($audit->status_submit); ?></span>
    </h4>
    <p class="text-muted mb-4">
        Teknisi: <strong><?php echo e($audit->technician->name ?? '-'); ?></strong> —
        Periode <?php echo e($audit->period->tahun); ?> Semester <?php echo e($audit->period->semester); ?>

        <?php if($audit->submitted_at): ?>
            <br>Disubmit: <?php echo e(\Carbon\Carbon::parse($audit->submitted_at)->format('d M Y H:i')); ?>

        <?php endif; ?>
        <?php if($audit->verified_at): ?>
            <br>Diproses: <?php echo e(\Carbon\Carbon::parse($audit->verified_at)->format('d M Y H:i')); ?> oleh <?php echo e($audit->verifiedBy->name ?? '-'); ?>

        <?php endif; ?>
    </p>

    <a href="<?php echo e(route('tool-audit-verification.index', ['status' => $audit->status_submit])); ?>"
        class="btn btn-outline-secondary btn-sm mb-3">
        <i class="mdi mdi-arrow-left"></i> Kembali
    </a>

    <form action="<?php echo e(route('tool-audit-verification.decide', $audit->id)); ?>" method="post">
        <?php echo csrf_field(); ?>
        <?php $__currentLoopData = $audit->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $tool = $item->fixedAsset;
                $master = $tool->toolsMaster ?? null;
                $kondisiBadge = ['Ada' => 'bg-label-success', 'Rusak' => 'bg-label-warning', 'Hilang' => 'bg-label-danger'][$item->kondisi] ?? 'bg-label-secondary';
            ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-3 align-items-start">
                        <div class="col-md-3 text-center">
                            <?php if($tool && $tool->foto_awal): ?>
                                <img src="<?php echo e(asset($tool->foto_awal)); ?>" alt="foto awal"
                                    style="width:100%;max-width:140px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                            <?php endif; ?>
                            <div class="small text-muted mt-1">Foto Awal (baseline)</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <?php if($item->foto_audit): ?>
                                <img src="<?php echo e(asset($item->foto_audit)); ?>" alt="foto audit"
                                    style="width:100%;max-width:140px;aspect-ratio:1/1;object-fit:cover;border-radius:6px;">
                            <?php endif; ?>
                            <div class="small text-muted mt-1">Foto Audit (sekarang)</div>
                        </div>
                        <div class="col-md-3">
                            <h6 class="mb-1"><?php echo e($master->nama_tools ?? '-'); ?></h6>
                            <div class="text-muted small mb-2">
                                Qty terdaftar: <?php echo e($tool->qty ?? '-'); ?> — Qty sekarang: <?php echo e($item->qty_actual ?? '-'); ?>

                            </div>
                            <span class="badge <?php echo e($kondisiBadge); ?>"><?php echo e($item->kondisi ?? '-'); ?></span>
                            <?php if($item->alasan): ?>
                                <div class="small text-muted mt-1"><?php echo e($item->kondisi == 'Hilang' ? 'Catatan' : 'Alasan'); ?>: <?php echo e($item->alasan); ?></div>
                            <?php endif; ?>
                            <?php if($item->metode_ganti): ?>
                                <div class="small text-muted mt-1">Ganti: <?php echo e($item->metode_ganti); ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <?php if($canDecide): ?>
                                <div class="form-floating form-floating-outline mb-2">
                                    <select class="form-select form-select-sm" name="item_status[<?php echo e($item->id); ?>]">
                                        <option value="Pending" <?php echo e($item->status_verifikasi_item == 'Pending' ? 'selected' : ''); ?>>Pending</option>
                                        <option value="Sesuai" <?php echo e($item->status_verifikasi_item == 'Sesuai' ? 'selected' : ''); ?>>Sesuai</option>
                                        <option value="Tidak Sesuai" <?php echo e($item->status_verifikasi_item == 'Tidak Sesuai' ? 'selected' : ''); ?>>Tidak Sesuai</option>
                                    </select>
                                    <label>Status Item</label>
                                </div>
                                <input type="text" class="form-control form-control-sm" name="item_note[<?php echo e($item->id); ?>]"
                                    value="<?php echo e($item->catatan_admin_item); ?>" placeholder="Catatan (opsional)">
                            <?php else: ?>
                                <?php
                                    $itemBadge = ['Sesuai' => 'bg-label-success', 'Tidak Sesuai' => 'bg-label-danger'][$item->status_verifikasi_item] ?? 'bg-label-secondary';
                                ?>
                                <span class="badge <?php echo e($itemBadge); ?>"><?php echo e($item->status_verifikasi_item); ?></span>
                                <?php if($item->catatan_admin_item): ?>
                                    <div class="small text-muted mt-1"><?php echo e($item->catatan_admin_item); ?></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($canDecide): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="form-floating form-floating-outline mb-3">
                        <textarea class="form-control" name="catatan_admin" id="catatanAdmin" rows="2"
                            placeholder="Catatan buat teknisi..."></textarea>
                        <label for="catatanAdmin">Catatan Admin (wajib kalau Tolak)</label>
                    </div>
                    <button type="submit" name="action" value="verify" class="btn btn-success me-2">
                        <i class="mdi mdi-check"></i> Verifikasi Selesai
                    </button>
                    <button type="submit" name="action" value="reject" class="btn btn-outline-danger">
                        <i class="mdi mdi-close"></i> Tolak / Minta Perbaikan
                    </button>
                </div>
            </div>
        <?php elseif($audit->catatan_admin): ?>
            <div class="alert alert-secondary">
                <strong>Catatan Admin:</strong> <?php echo e($audit->catatan_admin); ?>

            </div>
        <?php endif; ?>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/admin/tool-audit-verification/show.blade.php ENDPATH**/ ?>