
<?php $__env->startSection('title', 'Penawaran Unit'); ?>
<?php $__env->startSection('content'); ?>
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Sales /</span> Penawaran Unit
        </h4>
        <a href="<?php echo e(route('unit-quotation.create')); ?>">
            <button class="btn btn-primary btn-sm">
                <i class="mdi mdi-plus me-1"></i> Buat Penawaran
            </button>
        </a>
    </div>

    <div class="card">
        <div class="card-datatable table-responsive">
            <table class="datatable-unit-quotation table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">No. Quotation</th>
                        <th>Client</th>
                        <th>Description</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-unit-quotation.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/unit-quotation/index.blade.php ENDPATH**/ ?>