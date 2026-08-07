
<?php $__env->startSection('title', 'Completed: Sales Order'); ?>
<?php $__env->startSection('no-container'); ?> <?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <div class="card mb-3">
            <div class="card-datatable pt-0">
                <table
                    class="datatable-sales-completed-search<?php echo e(auth::user()->role == 'Sales' ? '' : '-admin'); ?> table table-bordered">
                    <thead>
                        <tr>
                            <th>PO Date</th>
                            <?php if(Auth::user()->role == 'Sales'): ?>
                                <th>PO No.</th>
                            <?php endif; ?>
                            <th>Customer</th>
                            <th>Part Desc</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Delivery</th>
                            <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Logistic' ): ?>
                                <th>Sales</th>
                                <th>Team</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    
<?php $__env->stopSection(); ?>

<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/bootstrap-select/bootstrap-select.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/tagify/tagify.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/bloodhound/bloodhound.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
    
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-advanced.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-search-sales-completed.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-search-sales-completed-admin.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-pending.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/forms-selects.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $(document).on('click', '.button-clear', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Clear it??",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Cleared it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('notulen')); ?>/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'PATCH',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Cleared!",
                                    text: "This Notulen has been Cleared.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/notulen';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Cleared!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your imaginary file is safe :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/pending/completed.blade.php ENDPATH**/ ?>