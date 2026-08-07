
<?php $__env->startSection('title', $invoice->no_invoice); ?>
<div class="invoice-print p-4">
    <?php if($delivery->type == 'ekspedisi'): ?>
        <div class="container-fluid flex-grow-1 container-p-y">

            <?php if($invoice->flag == 'Reftech'): ?>
                <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                    <div class="mb-xl-0 pb-1">
                        <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                            <span class="app-brand-logo demo">
                                <span style="color: var(--bs-primary)">
                                    <img class="text-md" src="<?php echo e(asset('/asset')); ?>/logo/Reftech-Log.png" alt=""
                                        srcset="" width="60%">
                                </span>
                            </span>
                        </div>
                        <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                        <div style="font-size: 10px">
                            <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                            <p class="mb-1">Bandung – Jawa Barat 40218</p>
                            <p class="mb-1">
                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022 54417653
                                <?php echo e('   '); ?><i
                                    class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                            </p>
                            <p class="mb-1">
                            </p>
                        </div>
                    </div>
                    <div class="text-end">
                        <h1 class="fw-bold title-invoice" style="color: blue;">Delivery Order</h1>
                        <div>
                            <span class="fw-bolder">#<?php echo e($invoice->no_invoice); ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                    <div class="mb-xl-0 pb-1">
                        <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                            <span class="app-brand-logo demo">
                                <span style="color: var(--bs-primary)">
                                    <img class="text-md" src="<?php echo e(asset('/asset')); ?>/logo/Logo-update-size.png"
                                        alt="" srcset="" width="60%">
                                </span>
                            </span>
                        </div>
                        <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                        <div style="font-size: 10px">
                            <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                            <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                            <p class="mb-1">
                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                                <?php echo e(' | '); ?><i
                                    class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                            </p>
                        </div>
                    </div>
                    <div class="text-end">
                        <h1 class="fw-bold title-invoice" style="color: blue;">Delivery Order</h1>
                        <div>
                            <span class="fw-bolder">#<?php echo e($invoice->no_invoice); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="mb-4">
                <?php
                    if ($delivery->destination == '1') {
                        $address = $invoice->quote->pic->client->address;
                    } else {
                        $address = $invoice->quote->pic->client->subAddress;
                    }
                ?>
                <div class="table-responsive text-nowrap">
                    <table class="table table-bordered" style="border: 1px solid black;">
                        <tr>
                            <td colspan="2" style="vertical-align: top; width: 50%;">
                                <div class="row">
                                    <div class="col-4 fw-medium">
                                        <p class="mb-1" style="font-size: 15px">Customers </p>
                                        <p class="mb-1">Adress</p>
                                    </div>
                                    <div class="col-8">
                                        <p class="mb-1" style="font-size: 15px">:
                                            <?php echo e($invoice->quote->pic->client->company); ?>

                                        </p>
                                        <?php if($invoice->invoiceTo == '1'): ?>
                                            <pre
                                                style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: <?php echo e($address); ?></pre>
                                        <?php else: ?>
                                            <pre
                                                style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: <?php echo e($address); ?></pre>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td style=" background-color: #F9F9F9;" class="text-center">
                                <p class="fs-5 text-black fw-medium m-0">Purchase Order :</p>
                            </td>
                            <td style=" background-color: #F9F9F9;" class="text-center">
                                <p class="fs-5 text-black fw-medium m-0">Shipment Date :</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <pre
                                    style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($invoice->no_po); ?></pre>
                            </td>
                            <td class="text-center">
                                <pre
                                    style="font-size: 13px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e(Carbon\Carbon::parse($delivery->date)->format('d-m-Y')); ?></pre>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mb-2">
                <table class="table table-bordered m-0" style="border: 1px solid rgb(60, 60, 60)">
                    <?php if($invoice->quote->type == 'Sparepart'): ?>
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Part Number</th>
                                <th style="width: 40%">Description</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 0;
                                $qty = 0;
                            ?>
                            <?php $__currentLoopData = $dDelivery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $no++;
                                ?>
                                <tr style="font-size: 13px">
                                    <td class="align-top"><?php echo e($no); ?></td>
                                    <td class="text-nowrap align-top">
                                        <p class="mb-0 fw-semibold" style="font-size: 12px">
                                            <?php echo e($product->product); ?>

                                        </p>
                                    </td>
                                    <td>
                                        <pre class="mb-0"
                                            style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($product->view == '0' ? $product->desc : ''); ?></pre>
                                    </td>
                                    <td class="align-top"><?php echo e($product->qty); ?> <?php echo e($product->info_qty); ?> </td>
                                </tr>
                                <?php
                                    $qty += $product->qty;
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td colspan="3"></td>
                                <td><?php echo e($qty); ?> <?php echo e($product->info_qty); ?> </td>
                            </tr>
                        </tbody>
                    <?php else: ?>
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Description</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 0;
                                $qty = 0;
                            ?>
                            <?php $__currentLoopData = $dDelivery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $no++;
                                ?>
                                <tr style="font-size: 13px">
                                    <td class="align-top"><?php echo e($no); ?></td>
                                    <td class="text-nowrap align-top">
                                        <p class="mb-0 fw-semibold" style="font-size: 12px">
                                            <?php echo e($product->product); ?>

                                        </p>
                                    </td>
                                    
                                    <td class="align-top"><?php echo e($product->qty); ?> <?php echo e($product->info_qty); ?> </td>
                                </tr>
                                <?php
                                    $qty += $product->qty;
                                ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td colspan="2"></td>
                                <td><?php echo e($qty); ?> <?php echo e($product->info_qty); ?> </td>
                            </tr>
                        </tbody>
                    <?php endif; ?>
                </table>
            </div>
            <div class="row">
                <div class="col-4 my-5 text-center">
                    <div class="pb-5"></div>
                    <?php if($delivery->invoice->flag == 'Reftech'): ?>
                        <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">PT. Reftech Jaya Optima
                        </p>
                    <?php else: ?>
                        <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">PT. Kojisha Innotiv
                            Indonesia
                        </p>
                    <?php endif; ?>
                    <p>Shipper</p>
                </div>
                <div class="col-4"></div>
                <div class="col-4 my-5 text-center">
                    <div class="pb-5"></div>
                    <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">
                        <?php echo e($quote->pic->client->company); ?></p>
                    <p>Recieved</p>
                </div>
            </div>
        </div>
    <?php else: ?>
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
                                            <?php echo e($invoice->no_invoice); ?></p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="py-0">
                                <div class="row">
                                    <div class="col-6">
                                        <?php if($invoice->flag == 'Reftech'): ?>
                                            <div
                                                class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
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
                                                        <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No.
                                                            31</p>
                                                        <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                                        <p class="mb-1">
                                                            <i
                                                                class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                                            54417653
                                                            <?php echo e('   '); ?><i
                                                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                                        </p>
                                                        <p class="mb-1">
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div
                                                class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                                                <div class="mb-xl-0 pb-1">
                                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                                        <span class="app-brand-logo demo">
                                                            <span style="color: var(--bs-primary)">
                                                                <img class="text-md"
                                                                    src="<?php echo e(asset('/asset')); ?>/logo/Logo-update-size.png"
                                                                    alt="" srcset="" width="60%">
                                                            </span>
                                                        </span>
                                                    </div>
                                                    <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                                                    <div style="font-size: 10px">
                                                        <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                                                        <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                                        <p class="mb-1">
                                                            <i
                                                                class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62
                                                            812-1000-0997
                                                            <?php echo e(' | '); ?><i
                                                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-6">
                                        <div class="row mt-3" style="font-size: 13px">
                                            <div class="col-4 text-end">
                                                <p class="mb-1">Date</p>
                                                <p class="mb-1">Order No</p>
                                                <p class="mb-1">Customer</p>
                                                <p class="mb-1">Delivery To</p>
                                            </div>
                                            <div class="col-8">
                                                <p class="mb-1">: <?php echo e($delivery->date); ?></p>
                                                <p class="mb-1">: <?php echo e($invoice->no_po); ?></p>
                                                <p class="mb-1">: <?php echo e($quote->pic->client->company); ?></p>
                                                <?php if($delivery->destination == '1'): ?>
                                                    <p class="mb-1">: <?php echo e($quote->pic->client->address); ?></p>
                                                <?php else: ?>
                                                    <p class="mb-1">: <?php echo e($quote->pic->client->subAddress); ?></p>
                                                <?php endif; ?>
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
                        <?php
                            $no = 0;
                        ?>
                        
                        <?php
                            $no = 0;
                        ?>
                        <tr style="font-size: 13px">
                            <td class="text-wrap align-top">
                                <?php $__currentLoopData = $dDelivery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $no++;
                                    ?>
                                    <p class="mb-0 fw-semibold">
                                        <?php echo e($no); ?>

                                    </p>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td class="text-wrap align-top">
                                <?php $__currentLoopData = $dDelivery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <p class="mb-0 fw-semibold">
                                        <?php echo e($product->qty); ?> <?php echo e($product->info_qty); ?>

                                    </p>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td class="text-wrap align-top">
                                <?php $__currentLoopData = $dDelivery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <p class="mb-0 fw-semibold">
                                        <?php echo e($product->product); ?>

                                        <?php echo e($product->desc ?? ''); ?>

                                    </p>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                        </tr>
                        
                        <tr>
                            <td colspan="3">
                                <div class="row mb-3">
                                    <div class="col-4 mt-5 text-center">
                                        <div class="pb-5"></div>
                                        <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">
                                            Shipper</p>
                                    </div>
                                    <div class="col-4"></div>
                                    <div class="col-4 mt-5 text-center">
                                        <div class="pb-5"></div>
                                        <p class="fw-bold mx-3 mb-0" style="border-top: 1px solid black ">
                                            Recieved</p>
                                    </div>
                                </div>
                                <p class="mb-0">Distribusi : Putih dan Pink → Pelanggan, <span
                                        class="fw-bold">Kuning → Accounting
                                        <?php echo e($delivery->invoice->flag == 'Reftech' ? 'PT. Reftech' : 'PT. Kojisha'); ?></span></span>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->startPush('after-style'); ?>
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice-print-do.css" />
    <link rel="stylesheet" href="style.css">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        $(document).ready(function() {
            // Ambil tinggi dari elemen <pre>
            var preHeight = $('#notePre').outerHeight();
            // Atur tinggi elemen <p> menjadi sama dengan tinggi elemen <pre>
            $('#noteParagraph').css('height', preHeight + 'px');
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/delivery/manual/detail-print.blade.php ENDPATH**/ ?>