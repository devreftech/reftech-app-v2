<?php $__env->startSection('title', 'Verifikasi Audit Tools'); ?>
<?php $__env->startSection('content'); ?>
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <h4 class="fw-bold py-3 mb-4">
        Verifikasi Audit Tools
    </h4>

    <ul class="nav nav-pills mb-3">
        <?php $__currentLoopData = ['Submitted' => 'Menunggu Verifikasi', 'Verified' => 'Sudah Diverifikasi', 'Rejected' => 'Ditolak']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="nav-item">
                <a class="nav-link <?php echo e($status == $key ? 'active' : ''); ?>"
                    href="<?php echo e(route('tool-audit-verification.index', ['status' => $key])); ?>"><?php echo e($label); ?></a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    <div class="card mb-3">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>No Audit</th>
                        <th>Teknisi</th>
                        <th>Periode</th>
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
                        <tr>
                            <td><?php echo e($audit->no_audit); ?></td>
                            <td><?php echo e($audit->technician->name ?? '-'); ?></td>
                            <td><?php echo e($audit->period->tahun); ?> - Semester <?php echo e($audit->period->semester); ?></td>
                            <td><?php echo e($audit->total_tools); ?></td>
                            <td><?php echo e($audit->total_ada); ?></td>
                            <td><?php echo e($audit->total_rusak); ?></td>
                            <td><?php echo e($audit->total_hilang); ?></td>
                            <td><?php echo e($audit->submitted_at ? \Carbon\Carbon::parse($audit->submitted_at)->format('d M Y H:i') : '-'); ?></td>
                            <td>
                                <a href="<?php echo e(route('tool-audit-verification.show', $audit->id)); ?>" class="btn btn-sm btn-primary">
                                    <?php echo e($status == 'Submitted' ? 'Verifikasi' : 'Lihat'); ?>

                                </a>
                                <?php if(Auth::user()->role == 'Admin'): ?>
                                    <a href="<?php echo e(route('tool-audit-verification.edit', $audit->id)); ?>" class="btn btn-sm btn-outline-warning">
                                        <i class="mdi mdi-pencil"></i> Edit
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/admin/tool-audit-verification/index.blade.php ENDPATH**/ ?>