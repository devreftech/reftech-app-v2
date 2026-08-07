
<?php $__env->startSection('title', 'Delivery Order'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row invoice-preview">
        
        <?php if($invoice->flag == 'Reftech'): ?>
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body" style="margin-left: 20mm">
                        <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column mb-2 text-black"
                            style="margin-left: 20mm">
                            <div class="mb-xl-0 pb-1" style="border-bottom: 1px solid black; width:70%">
                                <div class="d-flex svg-illustration align-items-center gap-2">
                                    <span class="app-brand-logo demo">
                                        <span style="color: var(--bs-primary)">
                                            <img class="text-md" src="<?php echo e(asset('/asset')); ?>/logo/Reftech-Log.png"
                                                alt="" srcset="" width="60%">
                                        </span>
                                    </span>
                                </div>
                                <p class="mb-1 mx-2 fw-bolder text-black">PT Reftech Jaya Optima</p>
                                <p class="mb-1 mx-2 fw-bolder fs-tiny"><span class="text-danger">Compressor</span> | <span
                                        class="text-success">Sparepart</span> | <span class="text-grey">Rental</span> |
                                    <span class="text-info">Service</span>
                                </p>
                                <p class="mb-1 mx-2 fw-bolder fs-tiny"
                                    style="border-bottom: 1px solid black; width:fit-content;">
                                    Office :</p>
                                <div class="mx-2" style="font-size: 10px">
                                    <p class="mb-1 text-black">Taman Kopo Indah V, Ruko Sommerville No.
                                        31</p>
                                    <p class="mb-1 text-black">Bandung – Jawa Barat 40218</p>
                                    <p class="mb-1 text-black">
                                        <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                        54417653
                                        <?php echo e('   '); ?><i
                                            class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                    </p>
                                    <p class="mb-1">
                                    </p>
                                </div>
                            </div>
                            <div class="text-end">
                                <h1 class="fw-bold text-black m-2 p-2" style="border: 2px solid black;">Dokumen</h1>
                            </div>
                        </div>
                        <div class="from p-1 text-black"
                            style="border:2px solid black; border-radius:5px; width:25%; margin-left: 20mm">
                            <div class="row">
                                <div class="col-4 pr-0">
                                    <p class="text-black">From :</p>
                                </div>
                                <div class="col-8 px-0">
                                    <p class="mb-0">Rayi</p>
                                    <p class="mb-0 fst-italic">Staff Accounting</p>
                                </div>
                            </div>
                        </div>
                        <div class="my-5"></div>
                        <div class="float-end text-black" id="info-cust"
                            style="border:3px solid black; border-radius:15px; width:40%; margin-top:150px">
                            <div class="row">
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0 pt-1">TO</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 fw-semibold pt-1">: <?php echo e($quote->pic->client->company); ?></p>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">ALAMAT</p>
                                </div>
                                <div class="col-8">
                                    <?php if($invoice->invoiceTo == '1'): ?>
                                        <p class="mb-0 ">: <?php echo e($quote->pic->client->address); ?></p>
                                    <?php else: ?>
                                        <p class="mb-0 ">: <?php echo e($quote->pic->client->subAddress); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">Attn.</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 ">: <?php echo e($quote->pic->name_pic); ?></p>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">Phone</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 ">: <?php echo e($quote->pic->phone_pic); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
                <div class="card invoice-preview-card">
                    <div class="card-body" style="margin-left: 20mm">
                        <div class="text-center">
                            <div class="row" style="border-bottom: 1px solid black">
                                <div class="col-2">
                                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                                        <span class="app-brand-logo demo">
                                            <span style="color: var(--bs-primary)">
                                                <img class="text-md" src="<?php echo e(asset('/asset')); ?>/logo/Logo-update-size.png"
                                                    alt="" srcset="" width="50%">
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="title">
                                        <p class="mb-1 fs-5 fw-bolder text-black">PT KOJISHA INNOTIV INDONESIA</p>
                                        <p class="mb-1 mx-2 fw-bolder fs-tiny"
                                            style="border-bottom: 1px solid black; display:inline-block">Office :</p>
                                        <div style="font-size: 10px">
                                            <p class="mb-1">Jl. Nancep, RT 01 RW 03 Kampung Cigebang Desa Cibening, Setu
                                            </p>
                                            <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                                            <p class="mb-1">
                                                <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62
                                                812-1000-0997
                                                <?php echo e(' | '); ?><i
                                                    class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2"></div>
                            </div>


                            
                        </div>
                        <div class="title mt-2" style="border:3px solid black; border-radius:15px; width:25%;">
                            <h1 class="fw-bold text-black text-center m-2">Dokumen</h1>
                        </div>
                        <div class="my-5"></div>
                        <div class="float-end" id="info-cust"
                            style="border:6px double black; width:40%; border-radius:15px; margin-top:200px">
                            <div class="row">
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0 pt-1">TO</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 fw-semibold pt-1">: <?php echo e($quote->pic->client->company); ?></p>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">ALAMAT</p>
                                </div>
                                <div class="col-8">
                                    <?php if($invoice->invoiceTo == '1'): ?>
                                        <p class="mb-0 ">: <?php echo e($quote->pic->client->address); ?></p>
                                    <?php else: ?>
                                        <p class="mb-0 ">: <?php echo e($quote->pic->client->subAddress); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">Attn.</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 ">: <?php echo e($quote->pic->name_pic); ?></p>
                                </div>
                                <div class="col-4 px-0">
                                    <p class="mb-0 fw-semibold p-4 py-0">Phone</p>
                                </div>
                                <div class="col-8">
                                    <p class="mb-0 ">: <?php echo e($quote->pic->phone_pic); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="btn-group w-100 mb-3">
                        <a class="btn btn-primary waves-effect" target="_blank"
                            href="<?php echo e(route('invoice.label_print', $invoice->id)); ?>">
                            Download
                        </a>
                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?php echo e(route('print.invoice', $invoice->id)); ?>" target="_blank">
                                    <i class="mdi mdi-file-document-outline me-1"></i> Invoice
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo e(route('invoice.label_print', $invoice->id)); ?>" target="_blank">
                                    <i class="mdi mdi-package-variant-closed me-1"></i> Sampul
                                </a>
                            </li>
                        </ul>
                    </div>
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
        <?php echo $__env->make('components.modal.accounting.delivery.change-date-label', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/label/detail.blade.php ENDPATH**/ ?>