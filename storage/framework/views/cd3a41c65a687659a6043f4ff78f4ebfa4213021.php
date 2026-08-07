
<?php $__env->startSection('title', 'Detail Quotation'); ?>

<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
            <div class="mb-xl-0 pb-3">
                <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                    <span class="app-brand-logo demo">
                        <span style="color: var(--bs-primary)">
                            <img class="text-md" src="<?php echo e(asset('assets')); ?>/img/favicon/logo-reftech1.png" alt=""
                                srcset="">
                        </span>
                    </span>
                    <span class="h4 mb-0 app-brand-text fw-bold fs-2">PT REFTECH JAYA OPTIMA</span>
                </div>
                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 27</p>
                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                <p>
                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1"></i>022 54417653
                </p>
            </div>
            <div>
                <h3 class="fw-bold">AUDIT TOOLS</h3>
                <div>
                    <span class="fw-bolder fs-5">By <?php echo e(Auth::user()->name); ?></span>
                </div>
                <div class="mt-1">
                    <span class="fw-bolder">#<?php echo e($audit->no_audit); ?></span>
                </div>
                <div class="mt-1">
                    <span class="text-muted"><?php echo e($audit->status); ?></span>
                </div>
            </div>
        </div>

        <hr>

        <div class="mb-4">
            <div class="row">
                <div class="col-lg-6 col-md-6 mt-3">
                    <h6 class="pb-2 fw-semibold fs-4">Tools :</h6>
                </div>
            </div>
            <div class="row">
                <div class="col-2 fw-medium">
                    <p class="mb-1">Name </p>
                    <p class="mb-1">Code </p>
                    <p class="mb-1">Phone </p>
                </div>
                <div class="col-4">
                    <p class="mb-1">: <?php echo e($audit->technician->name); ?></p>
                    <p class="mb-1">: <?php echo e($audit->technician->code); ?></p>
                    <p class="mb-1">: <?php echo e($audit->technician->phone); ?></p>
                </div>
                <div class="col-2 fw-medium">
                    <p class="mb-1">Note </p>
                </div>
                <div class="col-4">
                    <p class="mb-1">: <?php echo e($audit->note); ?></p>
                </div>
            </div>
        </div>

        <div class="mb-2">
            <table class="table table-borderless m-0" style="width: 100%">
                <thead class="table-light border-top">
                    <tr>
                        <th>No.</th>
                        <th>Tools</th>
                        <th>Qty</th>
                        <th>Description</th>
                        <th>Assesment</th>
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 0;
                    ?>
                    <?php $__currentLoopData = $tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $no++;
                        ?>
                        <tr class="row-product">
                            <td><?php echo e($no); ?></td>
                            <td class="text-nowrap"><?php echo e($tool->tools); ?></td>
                            <td><?php echo e($tool->qty); ?></td>
                            <td><?php echo e($tool->desc); ?></td>
                            <td><?php echo e($tool->assesment); ?></td>
                            <td><?php echo e($tool->note); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->startPush('after-style'); ?>
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice-print.css" />
    <link rel="stylesheet" href="style.css">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/technician/audit-tools/detail-print.blade.php ENDPATH**/ ?>