
<?php $__env->startSection('title', 'Unit Acquisition'); ?>
<?php $__env->startSection('content'); ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-end">
                <a href="<?php echo e(route('fixed.create')); ?>" class="btn btn-primary waves-effect mb-3">
                    Unit Acquisition Baru
                </a>
            </div>
        </div>
        <div class="card-datatable table-responsive pt-0">
            <table class="datatable-unit-acquisition table table-striped">
                <thead>
                    <tr>
                        <th>Tgl Beli</th>
                        <th>Code</th>
                        <th>Unit</th>
                        <th>Kondisi</th>
                        <th>Supplier</th>
                        <th>Harga Pokok</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-advanced.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-unit-acquisition.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/warehouse/unit-acquisition/index.blade.php ENDPATH**/ ?>