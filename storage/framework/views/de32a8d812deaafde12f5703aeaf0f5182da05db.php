
<?php $__env->startSection('title', 'Aging Report AP'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4"> <span class="text-muted">Account Payable / Aging Report/</span> Invoice #123123 </h4>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-6 mb-3">
                    <h4 class="mb-3">Purchase Aging</h4>
                    <div class="row">
                        <div class="col-6 mb-3">
                            Invoice Number
                        </div>
                        <div class="col-6 mb-3">
                            : <a class="text-black"
                                href="<?php echo e(route('invoice.show', $product->id)); ?>"><?php echo e($product->invoice); ?></a>
                        </div>
                        <div class="col-6 mb-3">
                            Invoice Date
                        </div>
                        <div class="col-6 mb-3">
                            : <?php echo e($product->date); ?>

                        </div>
                        <div class="col-6 mb-3">
                            Supplier
                        </div>
                        <div class="col-6 mb-3">
                            : <?php echo e($product->supp->supplier ?? $product->supplier); ?>

                        </div>
                        <div class="col-6 mb-3">
                            Info
                        </div>
                        <div class="col-6 mb-3">
                            : <?php echo e($product->info); ?>

                        </div>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    <div class="info text-end">
                        <p class="badge bg-label-danger text-danger rounded">Overdue</p>
                        <p>Days Past Due : <?php echo e($diffDue < 0 ? abs($diffDue) : 0); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer border">
            <div class="row mt-3">
                <div class="col-4">
                    <div class="card">
                        <div class="card-body bg-label-secondary">
                            <p>product Total</p>
                            <h5>Rp <?php echo e(number_format($product->total, 0, ',', '.')); ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-body bg-label-secondary">
                            <p>Paid to Date</p>
                            <h5>-</h5>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card">
                        <div class="card-body bg-label-secondary">
                            <p>Outstanding</p>
                            <h5 class="text-danger">Rp <?php echo e(number_format($product->total, 0, ',', '.')); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-sales-product-ar.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/payable/detail-aging.blade.php ENDPATH**/ ?>