<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default"
    data-assets-path="../../assets/" data-template="vertical-menu-template">

<head>
    <?php echo $__env->make('includes.sales.meta', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__env->startSection('title', 'Monitoring Visit'); ?>
    <?php echo $__env->make('includes.sales.style', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />


    
    <script src="<?php echo e(asset('/assets')); ?>/vendor/js/helpers.js"></script>

    
    
    <script src="<?php echo e(asset('/assets')); ?>/vendor/js/template-customizer.js"></script>

    
    <script src="<?php echo e(asset('assets')); ?>/js/config.js"></script>
    <?php echo app('Tightenco\Ziggy\BladeRouteGenerator')->generate(); ?>
</head>

<body class="justify-content-center align-items-center">
    <!--  Layout wrapper  -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="container-xxl flex-grow-1 container-p-y">
                <div class="container">
                    <div class="card text-center">
                        <div class="card-header">Monitoring <?php echo e($machine->unit->brand); ?> <?php echo e($machine->unit->unit->sku); ?>

                        </div>
                        <div class="card-body">
                            <a href="<?php echo e(route('create.daily-monitoring', $machine->id)); ?>"
                                class="btn btn-primary waves-effect" <?php echo e($hasMonitoringToday ? 'disabled' : ''); ?>>Daily
                                Monitoring</a>
                            <a href="<?php echo e(route('create.weekly-monitoring', $machine->id)); ?>"
                                class="btn btn-warning waves-effect">Weekly Monitoring</a>
                            <a href="<?php echo e(route('create.monthly-monitoring', $machine->id)); ?>"
                                class="btn btn-warning waves-effect">Monthly Monitoring</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-backdrop fade"></div>
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    
    <?php echo $__env->yieldPushContent('before-script'); ?>

    <?php echo $__env->make('includes.sales.script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/main.js"></script>

    <script>
        $(document).on('click', '.view-quote', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan

            $.ajax({
                url: '<?php echo e(url('quotation')); ?>/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
        $(document).on('click', '.view-quotation', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan

            console.log(id);

            $.ajax({
                url: '<?php echo e(url('quotation')); ?>/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
        $(document).on('click', '.view-prospect', function(e) {
            e.preventDefault(); // Mencegah perubahan halaman segera

            var id = $(this).data('id');
            var idQ = $(this).data('quotation');
            var href = $(this).attr('href'); // Ambil URL tujuan    

            $.ajax({
                url: '<?php echo e(url('prospect')); ?>/' + id + '/view_comment',
                type: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>', // Token CSRF
                },
                success: function(response) {
                    console.log(response); // Lakukan apa yang perlu dilakukan setelah AJAX sukses

                    // Arahkan ke halaman baru setelah AJAX selesai
                    window.location.href = href;
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText); // Tangani error jika ada
                }
            });
        });
    </script>

    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-monitoring-machine-visit.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-monitoring-dryer-visit.js"></script>

    <?php echo $__env->yieldPushContent('script'); ?>
</body>

</html>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/monitoring/monitoring/button.blade.php ENDPATH**/ ?>