<?php $__env->startSection('title', 'BAST - ' . $bast->no_bast); ?>

<?php
    $isReftech = $bast->entity === 'Reftech';
    $entityFullName = $isReftech ? 'PT. Reftech Jaya Optima' : 'PT. Kojisha Innotiv Indonesia';
?>

<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">
        
        <div class="d-flex justify-content-between align-items-start flex-xl-row flex-md-column flex-sm-row flex-column pb-3 mb-3"
            style="border-bottom: 2px solid #dee2e6;">
            <?php if($isReftech): ?>
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-3">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img class="text-md"
                                    src="<?php echo e(url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png')); ?>"
                                    alt="" srcset="" width="55%">
                            </span>
                        </span>
                    </div>
                    <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                    <div class="text-muted" style="font-size: 10px">
                        <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                        <p class="mb-1">Bandung – Jawa Barat 40218</p>
                        <p class="mb-1">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                            54417653<?php echo e('  |  '); ?><i
                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="mb-xl-0 pb-1">
                    <div class="d-flex svg-illustration align-items-center gap-2 mb-2">
                        <span class="app-brand-logo demo">
                            <span style="color: var(--bs-primary)">
                                <img class="text-md" src="<?php echo e(asset('/asset')); ?>/logo/Kojisha-Log.png" alt=""
                                    srcset="" width="55%">
                            </span>
                        </span>
                    </div>
                    <p class="mb-1 fw-bolder">PT Kojisha Innotiv Indonesia</p>
                    <div class="text-muted" style="font-size: 10px">
                        <p class="mb-1">Jl. Nancep No. 45A, Setu</p>
                        <p class="mb-1">Cibitung - Kab. Bekasi 17320</p>
                        <p class="mb-1">
                            <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>+62 812-1000-0997
                            <?php echo e('   '); ?><i
                                class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@kojisha.com
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            <div class="text-end">
                <div class="mt-1">
                    <span class="text-muted"><?php echo e($bast->work_date->format('d-m-Y')); ?></span>
                </div>
            </div>
        </div>

        
        <div class="text-center mb-4">
            <h4 class="fw-bold mb-1 text-uppercase">Berita Acara Serah Terima Pekerjaan</h4>
            <div class="fw-bold"><?php echo e($bast->no_bast); ?></div>
        </div>

        <p class="mb-3">
            Bersama dengan ini kami <?php echo e($entityFullName); ?>, telah menyelesaikan pekerjaan hingga
            <strong>SELESAI</strong> untuk pekerjaan sbb :
        </p>

        <div class="border rounded p-3 text-center fw-bold text-uppercase mb-3" style="font-size: 16px;">
            <?php echo e($bast->work_title); ?>

        </div>

        <table class="mb-3" style="font-size: 14px;">
            <tr>
                <td style="width: 220px;">Tanggal Pekerjaan</td>
                <td style="width: 20px;">:</td>
                <td><?php echo e($bast->work_date->format('d-m-Y')); ?></td>
            </tr>
            <tr>
                <td>Sesuai PO/ kontrak no.</td>
                <td>:</td>
                <td><?php echo e($bast->po_number ?: '-'); ?></td>
            </tr>
            <tr>
                <td>Terhadap unit-unit sebagai berikut</td>
                <td>:</td>
                <td></td>
            </tr>
        </table>

        <table class="table table-bordered mb-3">
            <thead>
                <tr>
                    <th style="width: 8%;">No.</th>
                    <th>Unit</th>
                    <th>Serial No.</th>
                    <th style="width: 15%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $bast->units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($index + 1); ?></td>
                        <td><?php echo e($unit->unit_name); ?></td>
                        <td><?php echo e($unit->serial_no ?: '-'); ?></td>
                        <td><?php echo e($unit->qty); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <p class="mb-1">Hasil pengecekan pada saat test running :</p>
        <div class="border rounded p-3 mb-4" style="min-height: 90px; white-space: pre-wrap; font-size: 14px;"><?php echo e($bast->test_running_result); ?></div>

        <p class="mb-2">
            Demikian <strong>BERITA ACARA SERAH TERIMA PEKERJAAN</strong> ini di tandatangani oleh kedua belah pihak :
        </p>
        <ul class="mb-3">
            <li>Pelaksana pekerjaan&nbsp; : <strong><?php echo e($entityFullName); ?></strong></li>
            <li>Pemberi pekerjaan&nbsp; : <strong><?php echo e($bast->customer_name); ?></strong></li>
        </ul>
        <p class="mb-5">
            Dengan ini segala hal yang berhubungan dengan pekerjaan tersebut di atas dinyatakan
            <strong>SELESAI</strong>
        </p>

        
        <div class="row mt-5 pt-3">
            <div class="col-6 text-center">
                <p class="fw-bold mb-5"><?php echo e($entityFullName); ?></p>
                <div style="border-top: 1px solid #333; width: 60%; margin: 0 auto;"></div>
            </div>
            <div class="col-6 text-center">
                <p class="fw-bold mb-5"><?php echo e($bast->customer_name); ?></p>
                <div style="border-top: 1px solid #333; width: 60%; margin: 0 auto;"></div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice-print.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/accounting/bast/print.blade.php ENDPATH**/ ?>