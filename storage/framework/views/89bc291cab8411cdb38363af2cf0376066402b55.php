
<?php $__env->startSection('title', 'Monthly Reports'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row invoice-preview">
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-row flex-column">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md"
                                            src="<?php echo e(url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png')); ?>"
                                            alt="" srcset="" width="60%">
                                    </span>
                                </span>
                            </div>
                            <p class="mb-1 fw-bolder">PT Reftech Jaya Optima</p>
                            <div style="font-size: 10px">
                                <p class="mb-1">Taman Kopo Indah V, Ruko Sommerville No. 31</p>
                                <p class="mb-1">Bandung – Jawa Barat 40218</p>
                                <p class="mb-1">
                                    <i class="mdi mdi-phone-outline scaleX-n1-rtl me-1 mdi-14px"></i>022
                                    54417653<?php echo e('  |  '); ?><i
                                        class="mdi mdi-email-outline scaleX-n1-rtl me-1 mdi-14px"></i>admin@reftech.id
                                </p>
                            </div>
                        </div>
                        <div>
                            <h3 class="fw-bold">MONTHLY MONITORING</h3>
                            <h3 class="fw-bold mt-0">AIR DRYER</h3>
                            <div class="mt-1">
                                <span class="text-muted">Month <?php echo e($thisMonth); ?></span>
                            </div>
                            
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="table-responsive text-nowrap mt-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <th>Machine</th>
                                <th>Condition</th>
                                <th>HP (High Pressure)</th>
                                <th>LP (Low Pressure)</th>
                                <th>Strainer</th>
                                <th>PIC</th>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $monitoringDRYER; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="<?php echo e($item->idM == $machine->id ? 'bg-label-warning' : ''); ?>">
                                        <td><?php echo e($item->unit->brand); ?> <?php echo e($item->unit->unit->sku); ?> || <?php echo e($item->tag); ?>

                                            - <?php echo e($item->location); ?>

                                        </td>
                                        <td><?php echo e($item->condition ?? '-'); ?></td>
                                        <td><?php echo e($item->hp ?? '-'); ?></td>
                                        <td><?php echo e($item->lp ?? '-'); ?></td>
                                        <td><?php echo e($item->strainer ?? '-'); ?></td>
                                        <td><?php echo e($item->name ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-5">
                        <div class="col-4 mt-5 text-center">
                            <p>PT Reftech Jaya Optima</p>
                            <div class="">
                                <img src="<?php echo e(url('') . '/asset/sign/ttdAngel.jpg'); ?>" alt="" srcset=""
                                    height="100">
                            </div>
                            <p>Angel Irene</p>
                        </div>
                        <div class="col-4"></div>
                        <div class="col-4 mt-5 text-center">
                            <p>PT Fajar Surya Wisesa</p>
                            <div class="pb-5"></div>
                            <p class="pt-3">..........................................</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card mb-3">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="<?php echo e(route('service-manager-monthly.print', [request()->route('id'), request()->route('month')])); ?>">
                        Download
                    </a>
                    <button id="buttonShare" data-id="1"
                        class="btn btn-success d-grid w-100 waves-effect mb-3">Bagikan</button>
                </div>
            </div>
        </div>
        
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice.css" />
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
        $(() => {
            $('#buttonShare').on('click', function() {
                const id = $(this).data('id')
                if (navigator.share) {
                    navigator.share({
                        title: 'Service Reports',
                        text: 'Check out this link!',
                        url: '<?php echo e(route('service-reports.show', ':id')); ?>'.replace(':id', id)
                    }).then(() => {
                        console.log('Thanks for sharing!');
                    }).catch(err => {
                        console.error('Error sharing:', err);
                    });
                } else {
                    alert('Sharing not supported in this browser.');
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/service-visitor-monthly.blade.php ENDPATH**/ ?>