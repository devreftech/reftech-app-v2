
<?php $__env->startSection('title', 'My Pending PO'); ?>
<?php $__env->startSection('content'); ?>
    
<h4 class="fw-bold py-3 mb-4">
    Pending PO
</h4>
<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <table id="dataTablependingPo" class="datatables-basic table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Assigned</th>
                    <th>No PO</th>
                    <th>Customer</th>
                    <th>Barang</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>24-11-2023</td>
                    <td>Mrs Regita</td>
                    <td>487544e5</td>
                    <td>PT. Indospring Tbk,</td>
                    <td> <span class="badge bg-label-info me-1"> Detail </span></td>
                    <td>26-11-2023</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
<link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.css">
<link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <script>
        new DataTable('#dataTablePendingPo')
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-js'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/po/pending/index.blade.php ENDPATH**/ ?>