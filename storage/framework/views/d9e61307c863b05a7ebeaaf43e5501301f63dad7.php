
<?php $__env->startSection('title', 'Existing'); ?>
<?php $__env->startSection('content'); ?>
    
    <?php
        if (Auth::user()->role == 'Admin') {
            $datatable = 'datatable-crm-admin';
        } else {
            if (Auth::user()->id == '1' || Auth::user()->id == '16' || Auth::user()->id == '23') {
                $datatable = 'datatable-crm-info';
            } else {
                $datatable = 'datatable-crm';
            }
        }

    ?>
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="<?php echo e($datatable); ?> table table-striped" id="dataTableCrm">
                <thead>
                    <tr>
                        <th></th>
                        <th></th>
                        <th>ID</th>
                        <th>Company</th>
                        <?php if(Auth::user()->role == 'Sales'): ?>
                            <th>R/U</th>
                        <?php endif; ?>
                        <th>Status</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Note</th>
                        <th>Last Contact</th>
                        <th>Next Follow Up</th>
                        <?php if(Auth::user()->role == 'Admin'): ?>
                            <th>Assigned</th>
                        <?php endif; ?>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/forms-selects.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-crm.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-crm-info.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-crm-admin.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        $(document).ready(function() {
            $('#dataTableCrm').on('change', '.status-dropdown', function() {
                var selectedValue = $(this).val();
                var rowId = $(this).data('id');
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                console.log('id = ' + rowId);


                $.ajax({
                    type: 'POST',
                    url: '/existing/update-status/' + rowId,
                    data: {
                        status: selectedValue,
                        _token: csrfToken
                    },
                    success: function(response) {
                        console.log('Perubahan status berhasil dikirim ke server');
                        // Handle response jika perlu
                    },
                    error: function(error) {
                        console.error('Gagal mengirim permintaan ke server:', error);
                        // Handle error jika perlu
                    }
                });
            });
        });
        // $(document).ready(function() {
        //     // Inisialisasi Select2
        //     $('#options').select2({
        //         templateResult: formatState
        //     });
        // });

        // // Fungsi untuk merender opsi dengan badge
        // function formatState(state) {
        //     if (!state.id) {
        //         return state.text;
        //     }
        //     var badgeClass = $(state.element).data('badge');
        //     return $(
        //         '<span class="' + badgeClass + '">' + state.text + '</span>'
        //     );
        // }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/existing/index.blade.php ENDPATH**/ ?>