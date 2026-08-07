
<?php $__env->startSection('title', $product->invoice); ?>
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
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
                        <?php echo e('  |  '); ?><i
                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                    </p>
                    <p class="mb-1">
                    </p>
                </div>
            </div>
            <div class="text-end">
                <h3 class="fw-bold">Barang Masuk</h3>
                <div>
                    <span class="fw-bolder">#<?php echo e($product->invoice); ?></span>
                </div>
                <div class="mt-1">
                    <span class="text-muted"><?php echo e(Carbon\Carbon::parse($product->date)->format('d-m-Y')); ?></span>
                </div>
            </div>
        </div>

        <hr>
        <div class="card-body mb-3">
            <div class="row">
                <div class="col-4 col-lg-2 fw-medium">
                    <p class="mb-1">Supplier </p>
                    <p class="mb-1">Note</p>
                </div>
                <div class="col-8">
                    <p class="mb-1">: <?php echo e($product->supplier); ?></p>
                    <p class="mb-1">: <?php echo e($product->note); ?></p>
                </div>
            </div>
        </div>
        <div class="mb-2">
            <table class="table table-borderless m-0" style="width: 100%">

                <thead class="table-light border-top">
                    <tr>
                        <th>No.</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Modal</th>
                        <th>Discount</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 0;
                    ?>
                    <?php $__currentLoopData = $detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $products): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $no++;
                        ?>
                        <tr style="font-size: 13px">
                            <td class="align-top"><?php echo e($no); ?></td>
                            <td class="text-nowrap align-top">
                                <p class="mb-0 fw-semibold" style="font-size: 12px">
                                    <?php echo e($products->detailProduct->replacement); ?>

                                </p>
                                <pre class="mb-0"
                                    style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($products->detailProduct->product->description); ?></pre>
                            </td>
                            <td class="align-top"><?php echo e($products->qty); ?> <?php echo e($products->detailProduct->product->unit); ?>

                            </td>
                            <?php if(Auth::user()->role == 'Logistic'): ?>
                                <td class="align-top">RP <?php echo e(str_repeat('*', strlen((string) $products->modal))); ?>

                                </td>
                                <td class="align-top">RP <?php echo e(str_repeat('*', strlen((string) $products->disc))); ?>

                                </td>
                                <td class="align-top">RP <?php echo e(str_repeat('*', strlen((string) $products->amount))); ?>

                                </td>
                            <?php else: ?>
                                <td class="align-top">RP <?php echo e(number_format($products->modal, 0, '', '.')); ?></td>
                                <td class="align-top">RP <?php echo e(number_format($products->disc, 0, '', '.')); ?></td>
                                <td class="align-top">RP <?php echo e(number_format($products->amount, 0, '', '.')); ?></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr style="font-size: 13px">
                        <td colspan="4" style="border:none;"></td>
                        <td>Subtotal</td>
                        <?php if(Auth::user()->role == 'Logistic'): ?>
                            <td>: RP <?php echo e(str_repeat('*', strlen((string) $product->subtotal))); ?></td>
                        <?php else: ?>
                            <td>: RP <?php echo e(number_format($product->subtotal, 0, '', '.')); ?></td>
                        <?php endif; ?>
                    </tr>
                    <tr style="font-size: 13px">
                        <td colspan="4" style="border:none;"></td>
                        <td>Tax <?php echo e($product->tax == '11' ? '11%' : ''); ?></td>
                        <?php if(Auth::user()->role == 'Logistic'): ?>
                            <td>: RP <?php echo e(str_repeat('*', strlen((string) $tax))); ?></td>
                        <?php else: ?>
                            <td>: RP <?php echo e(number_format($tax, 0, '', '.')); ?></td>
                        <?php endif; ?>
                    </tr>
                    <tr style="font-size: 13px;">
                        <td colspan="4" style="border:none;"></td>
                        <td>Shipping</td>
                        <td>: RP <?php echo e(number_format($product->shipping, 0, '', '.')); ?></td>
                    </tr>
                    <tr style="font-size: 13px">
                        <td colspan="4" style="border:none;"></td>
                        <td style="border:none;" class="total">Total</td>
                        <?php if(Auth::user()->role == 'Logistic'): ?>
                            <td style="border:none;" class="total">: RP
                                <?php echo e(str_repeat('*', strlen((string) $product->total))); ?></td>
                        <?php else: ?>
                            <td style="border:none;" class="total">: RP
                                <?php echo e(number_format($product->total, 0, '', '.')); ?></td>
                        <?php endif; ?>
                    </tr>
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/warehouse/product-in/detail-print.blade.php ENDPATH**/ ?>