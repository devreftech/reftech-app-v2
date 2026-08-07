<?php $__env->startSection('title', 'Summary Audit Tools'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        Summary Audit Tools
    </h4>

    <form method="get" class="mb-4" style="max-width: 320px;">
        <div class="form-floating form-floating-outline">
            <select class="form-select" name="period_id" onchange="this.form.submit()">
                <?php $__empty_1 = true; $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <option value="<?php echo e($p->id); ?>" <?php echo e($period && $period->id == $p->id ? 'selected' : ''); ?>>
                        <?php echo e($p->tahun); ?> - Semester <?php echo e($p->semester); ?>

                        (<?php echo e(\Carbon\Carbon::parse($p->tanggal_mulai)->format('d M')); ?> -
                        <?php echo e(\Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y')); ?>)
                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <option value="">Belum ada periode audit</option>
                <?php endif; ?>
            </select>
            <label>Periode</label>
        </div>
    </form>

    <?php if(!$period): ?>
        <div class="alert alert-secondary">Belum ada periode audit yang pernah digenerate.</div>
    <?php else: ?>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold"><?php echo e($summary['total_teknisi']); ?></div>
                        <div class="small text-muted">Teknisi</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-secondary"><?php echo e($summary['draft']); ?></div>
                        <div class="small text-muted">Draft</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-warning"><?php echo e($summary['submitted']); ?></div>
                        <div class="small text-muted">Menunggu Verifikasi</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-success"><?php echo e($summary['verified']); ?></div>
                        <div class="small text-muted">Verified</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold text-danger"><?php echo e($summary['rejected']); ?></div>
                        <div class="small text-muted">Ditolak</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card text-center">
                    <div class="card-body py-3">
                        <div class="fs-4 fw-bold"><?php echo e($summary['total_tools']); ?></div>
                        <div class="small text-muted">Total Tools</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-4">
                <div class="card text-center border-success">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-success"><?php echo e($summary['total_ada']); ?></div>
                        <div class="small text-muted">Ada</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center border-warning">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-warning"><?php echo e($summary['total_rusak']); ?></div>
                        <div class="small text-muted">Rusak</div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card text-center border-danger">
                    <div class="card-body py-3">
                        <div class="fs-3 fw-bold text-danger"><?php echo e($summary['total_hilang']); ?></div>
                        <div class="small text-muted">Hilang</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Teknisi</th>
                            <th>No Audit</th>
                            <th>Status</th>
                            <th>Total Tools</th>
                            <th>Ada</th>
                            <th>Rusak</th>
                            <th>Hilang</th>
                            <th>Disubmit</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $audits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $badge = [
                                    'Draft' => 'bg-label-secondary',
                                    'Submitted' => 'bg-label-warning',
                                    'Verified' => 'bg-label-success',
                                    'Rejected' => 'bg-label-danger',
                                ][$audit->status_submit] ?? 'bg-label-secondary';
                            ?>
                            <tr>
                                <td><?php echo e($audit->technician->name ?? '-'); ?></td>
                                <td><?php echo e($audit->no_audit); ?></td>
                                <td><span class="badge <?php echo e($badge); ?>"><?php echo e($audit->status_submit); ?></span></td>
                                <td><?php echo e($audit->total_tools); ?></td>
                                <td><?php echo e($audit->total_ada); ?></td>
                                <td><?php echo e($audit->total_rusak); ?></td>
                                <td><?php echo e($audit->total_hilang); ?></td>
                                <td><?php echo e($audit->submitted_at ? \Carbon\Carbon::parse($audit->submitted_at)->format('d M Y H:i') : '-'); ?></td>
                                <td>
                                    <a href="<?php echo e(route('tool-audit-verification.show', $audit->id)); ?>" class="btn btn-sm btn-outline-primary">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9" class="text-center">Belum ada teknisi dengan tools aktif di periode ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/admin/tool-audit-summary/index.blade.php ENDPATH**/ ?>