<?php $__env->startSection('title', 'Audit Tools'); ?>
<?php $__env->startSection('content'); ?>
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <h4 class="fw-bold py-3 mb-4">
        Audit Tools
    </h4>
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No Audit</th>
                                <th>Periode</th>
                                <th>Window</th>
                                <th>Total Tools</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $audits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($audit->no_audit); ?></td>
                                    <td><?php echo e($audit->period->tahun); ?> - Semester <?php echo e($audit->period->semester); ?></td>
                                    <td>
                                        <?php echo e(\Carbon\Carbon::parse($audit->period->tanggal_mulai)->format('d M')); ?> -
                                        <?php echo e(\Carbon\Carbon::parse($audit->period->tanggal_selesai)->format('d M Y')); ?>

                                    </td>
                                    <td><?php echo e($audit->total_tools); ?></td>
                                    <td>
                                        <?php
                                            $badge = [
                                                'Draft' => 'bg-label-secondary',
                                                'Submitted' => 'bg-label-warning',
                                                'Verified' => 'bg-label-success',
                                                'Rejected' => 'bg-label-danger',
                                            ][$audit->status_submit] ?? 'bg-label-secondary';
                                        ?>
                                        <span class="badge <?php echo e($badge); ?>"><?php echo e($audit->status_submit); ?></span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('tool-audit.show', $audit->id)); ?>" class="btn btn-sm btn-primary">
                                            <?php echo e(in_array($audit->status_submit, ['Draft', 'Rejected']) ? 'Isi Audit' : 'Lihat'); ?>

                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="6" class="text-center">
                                        Belum ada periode audit yang aktif untuk kamu. Audit tools dibuka otomatis di 10 hari terakhir bulan Juni & Desember.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/technician/tool-audit/index.blade.php ENDPATH**/ ?>