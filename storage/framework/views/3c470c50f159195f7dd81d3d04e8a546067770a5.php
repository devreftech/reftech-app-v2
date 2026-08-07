<?php $__env->startSection('title', 'Management Tools per Teknisi'); ?>
<?php $__env->startSection('content'); ?>
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center py-3 mb-1">
        <h4 class="fw-bold mb-0">
            Management Tools per Teknisi
        </h4>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTechnicianModal">
            <i class="mdi mdi-plus"></i> Add Technician
        </button>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Teknisi</th>
                                <th>Code</th>
                                <th>Total Tools Aktif</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $technicians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $technician): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($technician->name); ?></td>
                                    <td><?php echo e($technician->code ?? '-'); ?></td>
                                    <td><?php echo e($technician->tools_assigned_count); ?></td>
                                    <td class="text-end">
                                        <a href="<?php echo e(route('tool-assignment.show', $technician->id)); ?>"
                                            class="btn btn-sm btn-primary">Kelola Tools</a>
                                        <form action="<?php echo e(route('tool-assignment.remove-technician', $technician->id)); ?>"
                                            method="post" class="d-inline"
                                            onsubmit="return confirm('Hapus <?php echo e($technician->name); ?> dari daftar Tool Assignment? Tools yang sudah di-assign tidak akan terhapus.');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('delete'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada teknisi yang ditambahkan. Klik "Add
                                        Technician" untuk mulai.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    
    <form action="<?php echo e(route('tool-assignment.add-technician')); ?>" method="post">
        <?php echo csrf_field(); ?>
        <div class="modal fade" id="addTechnicianModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Technician</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pilih User</label>
                            <select class="form-select" name="user_id" required>
                                <option value="" disabled selected>-- Pilih User --</option>
                                <?php $__currentLoopData = $availableUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?> (<?php echo e($user->role); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <small class="text-muted">Semua user bisa ditambahkan, tidak terbatas role
                                Technician.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Tambahkan</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/technician/tool-assignment/index.blade.php ENDPATH**/ ?>