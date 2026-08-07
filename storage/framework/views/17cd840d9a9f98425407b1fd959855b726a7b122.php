
<?php $__env->startSection('title', $suo->no_invoice_booking); ?>
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">

        <div class="table-responsive mb-5">
            <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                <tbody>
                    
                    <tr>
                        <td colspan="3" class="py-1">
                            <div class="row">
                                <div class="col-8">
                                    <h5 class="fw-bold mb-0">Delivery Order</h5>
                                </div>
                                <div class="col-4">
                                    <p class="mb-0"><span class="fw-bold">D.O. No :</span>
                                        <?php echo e($suo->no_invoice_booking); ?></p>
                                </div>
                            </div>
                        </td>
                    </tr>

                    
                    <tr>
                        <td colspan="3" class="py-0">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                        <div class="mb-xl-0 pb-1">
                                            <div class="d-flex svg-illustration align-items-center gap-2">
                                                <span class="app-brand-logo demo">
                                                    <span style="color: var(--bs-primary)">
                                                        <img class="text-md"
                                                            src="<?php echo e(asset('/asset')); ?>/logo/Reftech-Log.png"
                                                            alt="" srcset="" width="60%">
                                                    </span>
                                                </span>
                                            </div>
                                            <p class="mb-1 mx-2 fw-bolder">PT Reftech Jaya Optima</p>
                                            <div class="mx-2" style="font-size: 10px">
                                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                                <p class="mb-1">
                                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                                    <?php echo e('   '); ?><i class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                                </p>
                                                <p class="mb-1"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <?php
                                        if ($client) {
                                            $address = $delivery->destination == '1'
                                                ? $client->address
                                                : $client->subAddress;
                                        } else {
                                            $address = $suo->address;
                                        }
                                    ?>
                                    <div class="row mt-3" style="font-size: 13px">
                                        <div class="col-4 text-end">
                                            <p class="mb-1">Date</p>
                                            <p class="mb-1">Order No</p>
                                            <p class="mb-1">Customer</p>
                                            <p class="mb-1">Delivery To</p>
                                        </div>
                                        <div class="col-8">
                                            <p class="mb-1">: <?php echo e(\Carbon\Carbon::parse($delivery->date)->format('d-m-Y')); ?></p>
                                            <p class="mb-1">: <?php echo e($suo->no_suo); ?></p>
                                            <p class="mb-1">: <?php echo e($suo->company); ?></p>
                                            <p class="mb-1">: <?php echo e($address); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    
                    <tr>
                        <th class="text-center">No</th>
                        <th class="text-center">Qty</th>
                        <th class="text-center" style="width: 80%">Description</th>
                    </tr>

                    
                    <tr style="font-size: 13px">
                        <td class="text-nowrap align-top">
                            <?php $__currentLoopData = $suo->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="mb-0 fw-semibold"><?php echo e($i + 1); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td class="text-nowrap align-top">
                            <?php $__currentLoopData = $suo->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="mb-0 fw-semibold"><?php echo e($item->qty); ?> <?php echo e($item->unit); ?></p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td class="text-nowrap align-top">
                            <?php $__currentLoopData = $suo->detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <p class="mb-0 fw-semibold">
                                    <?php echo e($item->item_name); ?><?php echo e($item->notes ? ' — ' . $item->notes : ''); ?>

                                </p>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                    </tr>

                    
                    <tr>
                        <td colspan="3">
                            <div class="row mb-3">
                                <div class="col-4 mt-5 text-center">
                                    <div class="pb-5"></div>
                                    <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black">Shipper</p>
                                </div>
                                <div class="col-4"></div>
                                <div class="col-4 mt-5 text-center">
                                    <div class="pb-5"></div>
                                    <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black">Recieved</p>
                                </div>
                            </div>
                            <p class="mb-0">Distribusi : Putih dan Pink → Pelanggan, <span class="fw-bold">Kuning → Accounting PT. Reftech</span></p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>
<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice-print-do.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/suo/sj-print.blade.php ENDPATH**/ ?>