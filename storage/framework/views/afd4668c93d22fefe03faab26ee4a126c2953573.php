
<?php $__env->startSection('title', 'Monitoring machine'); ?>
<?php $__env->startSection('content'); ?>
    <?php if(Auth::user()->role != 'Technician' || Auth::user()->id == 13): ?>
        <div class="row mb-4">
            <div class="col-12 col-md-3 mb-2">
                <div class="card bg-label-info h-100">
                    <div class="card-body">
                        <h5>Machine On All Plant</h5>
                    </div>
                    <div class="card-footer">
                        <p class="text-black float-end fs-5"><?php echo e($allPlantMonitoring); ?>/ <span
                                class="text-muted fs-6"><?php echo e($allPlant); ?></span></p>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="row">
                    <div class="col-6 col-md-4 mb-2">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant GT 3 / BOILER</h5>
                                <p class="float-end fs-5"><?php echo e($GT3Monitoring); ?>/ <span
                                        class="text-muted fs-6"><?php echo e($GT3); ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mb-2">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant GT 1-2</h5>
                                <p class="float-end fs-5"><?php echo e($GTMonitoring); ?>/ <span
                                        class="text-muted fs-6"><?php echo e($GT); ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4 mb-2">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant INC</h5>
                                <p class="float-end fs-5"><?php echo e($INCMonitoring); ?>/ <span
                                        class="text-muted fs-6"><?php echo e($INC); ?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant PM 1-2</h5>
                                <p class="float-end fs-5"><?php echo e($PM12Monitoring); ?>/ <span
                                        class="text-muted fs-6"><?php echo e($PM12); ?></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant PM 3-5</h5>
                                <p class="float-end fs-5"><?php echo e($PM35Monitoring); ?>/ <span
                                        class="text-muted fs-6"><?php echo e($PM35); ?></span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Plant PM 7-8</h5>
                                <p class="float-end fs-5"><?php echo e($PM78Monitoring); ?>/ <span
                                        class="text-muted fs-6"><?php echo e($PM78); ?></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5> Machine </h5>
                        <div class="card-datatable table-responsive pt-0">
                            <table class="datatable-compressor-client table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>ID</th>
                                        <th>Brand</th>
                                        <th>Unit</th>
                                        <th>Tag</th>
                                        <th>Location</th>
                                        <th>PIC</th>
                                        <th>Info</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        <div class="row">
            
            
        </div>
    <?php else: ?>
        <h3>Rekap Issue & Maintenance Log <?php echo e(\Carbon\Carbon::createFromFormat('m', $month)->format('F')); ?> ,
            <?php echo e($year); ?>

        </h3>
        <?php $__currentLoopData = $result; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h5><?php echo e($item['machine']); ?></h5>
                        <a href="<?php echo e(route('visitor.daily-monitoring', $item['id'])); ?>">
                            <button type="button" class="btn btn-primary">
                                Details Maintenance
                            </button>
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6">
                            <h5 class="badge rounded-pill bg-label-primary fs-big">Issue Recommendation</h5>
                            <div class="table-responsive text-nowrap mb-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:10%;">Date</th>
                                            <th>Issue</th>
                                            <th style="width:25%;">Pic</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $item['log']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <!-- Menampilkan tanggal log jika ada -->
                                                <td><?php echo e($log['date'] ?? 'N/A'); ?></td>
                                                <!-- Jika tanggal ada, tampilkan, jika tidak tampilkan 'N/A' -->
                                                <td>
                                                    <pre class="mb-1"
                                                        style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;"><?php echo e($log['log']); ?></pre>
                                                </td>
                                                <td><?php echo e($log['pic']); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <h5 class="badge rounded-pill bg-label-success fs-big">Maintenance Log</h5>
                            <div class="table-responsive text-nowrap mb-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:10%;">Date</th>
                                            <th>Maintenance</th>
                                            <th style="width:25%;">Pic</th>
                                            <th style="width:10%;">Button</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $item['mainlog']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mainlog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <!-- Menampilkan tanggal mainlog jika ada -->
                                                <td><?php echo e($mainlog['date'] ?? 'N/A'); ?></td>
                                                <!-- Jika tanggal ada, tampilkan, jika tidak tampilkan 'N/A' -->
                                                <td>
                                                    <pre class="mb-1"
                                                        style="font-size: 15px; font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto;"><?php echo e($mainlog['log']); ?></pre>
                                                </td>
                                                <td><?php echo e($mainlog['technician']); ?></td>
                                                <?php if($mainlog['id_service'] != null): ?>
                                                    <td>
                                                        <a class="btn btn-warning waves-effect"
                                                            href="<?php echo e(route('service-reports.show', $mainlog['id_service'])); ?>">
                                                            <i class="menu-icon tf-icons mdi mdi-eye-outline"></i>
                                                        </a>
                                                    </td>
                                                <?php elseif($mainlog['id_service'] == null && $mainlog['id_pic'] == Auth::user()->id): ?>
                                                    <td>
                                                        <a class="btn btn-primary waves-effect"
                                                            href="<?php echo e(route('create.daily-monitoring-reports', [$mainlog['id'], $mainlog['id_machine']])); ?>">
                                                            <i class="menu-icon tf-icons mdi mdi-file-plus-outline"></i>
                                                        </a>
                                                    </td>
                                                <?php else: ?>
                                                    <td>
                                                        Has No Reports
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
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
    <script src="<?php echo e(asset('assets')); ?>/includes/table-coordinator-compressor.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-coordinator-dryer.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-recap-month.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-issue-month.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-reports-fp.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        var recapRoute = "<?php echo e(route('service-manager.recap', [':month', ':year'])); ?>";
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/service-index.blade.php ENDPATH**/ ?>