
<?php $__env->startSection('title', 'My Service Reports'); ?>
<?php $__env->startSection('content'); ?>
    <?php if(Auth::user()->role == 'Technician' || Auth::user()->role == 'Coordinator'): ?>
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Service Departement /</span> Service Reports
        </h4>
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-reports table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>No Service</th>
                            <th>Company</th>
                            <th>Job Desc</th>
                            <th>Unit Type</th>
                            <th>Serial / Tag</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    <?php elseif(Auth::user()->role == 'Admin'): ?>
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Service Department /</span> Service Reports
        </h4>
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-reports-admin table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">No Service</th>
                            <th class="text-center">Company</th>
                            <th class="text-center">Job Desc</th>
                            <th class="text-center">Brand Type</th>
                            <th class="text-center">Serial / Tag</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Sales</th>
                            <th class="text-center">Technician</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    <?php else: ?>
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light">Service Department /</span> Service Reports
        </h4>
        <div class="card mb-3">
            <div class="card-datatable table-responsive pt-0">
                <table class="datatable-reports-sales table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>No Service</th>
                            <th>Company</th>
                            <th>Job Desc</th>
                            <th>Unit Type</th>
                            <th>Serial / Tag</th>
                            <th>Date</th>
                            <th>Technician</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
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
    <script src="<?php echo e(asset('assets')); ?>/includes/table-reports.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-reports-admin.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-reports-sales.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        $(document).on('click', '.view-report', function() {
            let id = $(this).data('id');

            $.ajax({
                url: "/service-reports-viewed",
                type: "POST",
                data: {
                    id: id,
                    _token: "<?php echo e(csrf_token()); ?>"
                },
                success: function() {
                    window.location.href = "/service-reports/" + id;
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/technician/service-reports/index.blade.php ENDPATH**/ ?>