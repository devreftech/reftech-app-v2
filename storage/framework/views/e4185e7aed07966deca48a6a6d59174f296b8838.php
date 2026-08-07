
<?php $__env->startSection('title', 'Monitoring machine'); ?>
<?php $__env->startSection('content'); ?>
    
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    

                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Location</th>
                                    <th>Tag</th>
                                    <th>Brand</th>
                                    <th>Condition</th>
                                    <th>Voltage</th>
                                    <th>Amphere</th>
                                    <th>Drain</th>
                                    <th>Pre</th>
                                    <th>After</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $mesinDryer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($item->location ?? '-'); ?></td>
                                        <td><?php echo e($item->tag ?? '-'); ?></td>
                                        <td><?php echo e($item->brand_type ?? '-'); ?></td>
                                        <td><?php echo e($item->condition ?? '-'); ?></td>
                                        <td><?php echo e($item->voltage ?? '-'); ?></td>
                                        <td><?php echo e($item->ampere ?? '-'); ?></td>
                                        
                                        <td>
                                            <?php echo e($item->drain ?? $item->drain == 'OK' ? '✅' : '❌'); ?></td>
                                        <td>
                                            <?php echo e($item->pre ?? $item->pre == 'OK' ? '✅' : '❌'); ?>

                                        </td>
                                        <td>
                                            <?php echo e($item->after ?? $item->after == 'OK' ? '✅' : '❌'); ?>

                                        </td>
                                        
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        

                        <div class="table-responsive text-nowrap">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Location</th>
                                        <th>Tag</th>
                                        <th>Brand</th>
                                        <th style="width: 60%;">Issue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $mesinDryer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(@$item->issue): ?>
                                            <tr>
                                                <td><?php echo e($item->location ?? '-'); ?></td>
                                                <td><?php echo e($item->tag ?? '-'); ?></td>
                                                <td><?php echo e($item->brand_type ?? '-'); ?></td>
                                                <td><?php echo e($item->issue ?? '-'); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            
        </div>
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('after-style'); ?>
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
        <link rel="stylesheet"
            href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
        <link rel="stylesheet"
            href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
        <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('after-script'); ?>
        <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('page-script'); ?>
        <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/includes/table-recap-compressor.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/includes/table-recap-dryer-week.js"></script>
        <script src="<?php echo e(asset('assets')); ?>/includes/table-recap-dryer-issue-week.js"></script>
    <?php $__env->stopPush(); ?>

    <?php $__env->startPush('script'); ?>
        <script>
            var recapRoute = "<?php echo e(route('service-manager.recap', [':month', ':year'])); ?>";
        </script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/recapWeek.blade.php ENDPATH**/ ?>