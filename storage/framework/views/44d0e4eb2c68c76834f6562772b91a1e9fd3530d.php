
<?php $__env->startSection('title', 'Monitoring machine'); ?>
<?php $__env->startSection('content'); ?>
    <div class="card mb-3">
        <div class="card-body">
            <form action="<?php echo e(route('store.monthly-monitoring', $machine->id)); ?>" method="post" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <h5 class="text-center">MONTHLY CHECK AIR DRYER <?php echo e($machine->unit->brand); ?>

                    <?php echo e($machine->unit->unit->sku); ?></h5>
                <div class="daily-compressor">
                    <div class="row">
                        <div class="col-12 col-lg-8">
                            <div class="row mb-3">
                                <div class="col-4 col-lg-2">
                                    Location
                                </div>
                                <div class="col-8 col-lg-10">
                                    : <?php echo e($machine->location); ?>

                                </div>
                                <div class="col-4 col-lg-2">
                                    Tag Number
                                </div>
                                <div class="col-8 col-lg-10">
                                    : <?php echo e($machine->tag); ?>

                                </div>
                                <div class="col-4 col-lg-2">
                                    Unit
                                </div>
                                <div class="col-8 col-lg-10">
                                    : <?php echo e($machine->unit->brand); ?> <?php echo e($machine->unit->unit->sku); ?>

                                </div>
                                <div class="col-4 col-lg-2">
                                    Date
                                </div>
                                <div class="col-8 col-lg-10">
                                    : <?php echo e(\Carbon\Carbon::now()->format('d-m-Y')); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12 col-md-6 mb-3">
                            <label for="defaultSelect" class="form-label">Condition</label>
                            <select id="conditionSelect" name="condition" class="form-select">
                                <option value="Running">Running</option>
                                <option value="Stand By">Stand By</option>
                                <option value="Off">Off</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label for="defaultInput" class="form-label">Input HP (High Pressure)</label>
                            <input id="numberInput" class="form-control offDisable" name="hp" type="text"
                                placeholder="Input HP">
                        </div>
                        <div class="col-6 col-md-3 mb-3">
                            <label for="defaultInput" class="form-label">Input LP (Low Pressure)</label>
                            <input id="numberInput" class="form-control offDisable" name="lp" type="text"
                                placeholder="Input LP">
                        </div>
                        <div class="col-12 mb-3">
                            <label for="defaultInput" class="form-label">Pengecekan dan bersihkan strainer pada timer drain
                                di dryer, inline filter dan receiver tank</label>
                            <div class="input-group input-group-merge">
                                <select name="strainer" class="form-select offDisable">
                                    <option value="Oke">Oke</option>
                                    <option value="Not Oke">Not Oke</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="float-end">
                    <a href="<?php echo e(route('index.daily-monitoring', $machine->id)); ?>" type="button"
                        class="btn btn-lg btn-outline-secondary">
                        Back
                    </a>
                    <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
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
    <script src="<?php echo e(asset('assets')); ?>/includes/table-monitoring-machine.js"></script>
    <script>
        $('#conditionSelect').on('change', function() {
            var condition = $(this).val();
            var disable = $('.offDisable');
            var number = $('#numberInput');

            if (condition == 'Off') {
                disable.prop('disabled', true);
                // number.prop('disabled', true);
            } else {
                // number.prop('disabled', false);
                disable.prop('disabled', false);
            }
            console.log(number);

            console.log(condition);
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/monitoring/form-monthly.blade.php ENDPATH**/ ?>