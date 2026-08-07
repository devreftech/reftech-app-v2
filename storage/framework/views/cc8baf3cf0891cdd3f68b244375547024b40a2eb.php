
<?php $__env->startSection('title', 'Delivery Order'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row invoice-preview">
        
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
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
                                                            <div
                                                                class="d-flex svg-illustration align-items-center gap-2 mb-2">
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
                                                        <p class="mb-1">: <?php echo e($invoice->dateDo); ?></p>
                                                        <p class="mb-1">: <?php echo e($invoice->no_po); ?></p>
                                                        <p class="mb-1">: <?php echo e($quote->pic->client->company); ?></p>
                                                        <?php if($invoice->doTo == '1'): ?>
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
                                <tr style="font-size: 13px">
                                    <td class="text-nowrap align-top">
                                        <?php $__currentLoopData = $dQuote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php
                                                $no++;
                                            ?>
                                            <p class="mb-0 fw-semibold">
                                                <?php echo e($no); ?>

                                            </p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td class="text-nowrap align-top">
                                        <?php $__currentLoopData = $dQuote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p class="mb-0 fw-semibold">
                                                <?php echo e($product->qty); ?> <?php echo e($product->info_qty); ?>

                                            </p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                    <td class="text-nowrap align-top">
                                        <?php $__currentLoopData = $dQuote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p class="mb-0 fw-semibold">
                                                <?php echo e($product->equivalent->brand); ?> <?php echo e($product->equivalent->pn); ?>

                                                <?php echo e($product->detail_product); ?>

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
                                                class="fw-bold">Kuning → Accounting PT. Reftech</span></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <a class="btn btn-primary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="<?php echo e(route('invoice.print_teknisi', $invoice->id)); ?>">
                        Download
                    </a>
                    <a href="#" class="btn btn-outline-danger d-grid w-100 waves-effect delete-invoice mb-3"
                        data-id="<?php echo e($quote->id); ?>">Delete</a>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <a type="button" data-bs-toggle="modal" data-bs-target="#changeDate"
                        class="d-grid w-100 waves-effect mb-3">
                        <button type="button" class="btn btn-secondary">
                            Change Date And Address
                        </button>
                    </a>
                </div>
            </div>
        </div>
        <?php echo $__env->make('components.modal.accounting.delivery.form-teknisi', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__env->stopSection(); ?>
    <?php $__env->startPush('after-style'); ?>
        <!-- Page CSS -->
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice.css" />
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('after-script'); ?>
        <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('page-script'); ?>
        <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('script'); ?>
        <script>
            $('#backButton').click(function() {
                window.history.back();
            });

            const dateInput = document.getElementById('dateInput');
            const resetCheckbox = document.getElementById('checkDate');

            // Saat checkbox di-check
            resetCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    dateInput.value = ''; // Hapus nilai date
                }
            });

            // Saat input tanggal diisi
            dateInput.addEventListener('input', function() {
                if (this.value) {
                    resetCheckbox.checked = false; // Uncheck checkbox
                }
            });
        </script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/delivery/teknisi.blade.php ENDPATH**/ ?>