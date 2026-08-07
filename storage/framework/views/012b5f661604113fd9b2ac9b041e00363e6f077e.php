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

<body>
    <!--  Layout wrapper  -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <div class="container-fluid flex-grow-1 container-p-y">
                <div class="container">
                    <div class="card mb-4">
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
                                    <h3 class="fw-bold">PIC - <?php echo e($client->sales->name); ?></h3>
                                    
                                </div>
                            </div>
                            <br>
                            <div class="daily mb-4">
                                <h4>Purchase Order</h4>
                                <div class="table-responsive text-nowrap mt-4">
                                    <table class="table table-bordered">
                                        <thead class="table-light" align="center">
                                            <th style="vertical-align: middle;">Date</th>
                                            <th style="vertical-align: middle;">No Quote</th>
                                            <th style="vertical-align: middle;">Total Price</th>
                                        </thead>
                                        <tbody>
                                            <?php $__currentLoopData = $quotePO; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr align="center">
                                                    <td><?php echo e(\Carbon\Carbon::parse($item->po_date)->format('d-m-Y')); ?></td>
                                                    <td><?php echo e($item->no_quote); ?></td>
                                                    
                                                    <td><?php echo e($item->nett); ?></td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/existing/yearly.blade.php ENDPATH**/ ?>