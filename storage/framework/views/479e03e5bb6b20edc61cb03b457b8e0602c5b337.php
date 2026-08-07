
<?php $__env->startSection('title', 'Req Purchase'); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluid p-0" style="width: calc(100% - 10px); margin-right:5px;margin-left:5px;">
        <h4 class="fw-bold py-3 mb-4"> <span class="text-muted fw-normal">Request Purchase</h4>

        <div class="card mb-4">
            
            <div class="card-header p-0 border-bottom">
                <ul class="nav nav-tabs px-3 pt-2" id="purchaseRequestTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active px-3 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-new" aria-controls="navs-pills-top-new"
                            aria-selected="true">
                            <i class="mdi mdi-file-plus-outline me-1"></i>New Purchase
                            <?php if(@$newCount >= 1): ?>
                                <span class="badge bg-danger rounded-pill ms-1"><?php echo e($newCount); ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link px-3 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-acc" aria-controls="navs-pills-top-acc"
                            aria-selected="false">
                            <i class="mdi mdi-clipboard-check-outline me-1"></i>Approved
                            <?php if(@$accCount >= 1): ?>
                                <span class="badge bg-warning rounded-pill ms-1"><?php echo e($accCount); ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link px-3 py-3" role="tab" data-bs-toggle="tab"
                            data-bs-target="#navs-pills-top-delivery" aria-controls="navs-pills-top-delivery"
                            aria-selected="false">
                            <i class="mdi mdi-truck-delivery-outline me-1"></i>On Delivery
                            <?php if(@$deliveryCount >= 1): ?>
                                <span class="badge bg-info rounded-pill ms-1"><?php echo e($deliveryCount); ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button"
                            class="nav-link px-3 py-3 <?php echo e(auth::user()->role == 'ServiceM' ? 'active' : ''); ?>"
                            role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-done"
                            aria-controls="navs-pills-top-done" aria-selected="false">
                            <i class="mdi mdi-check-all me-1"></i>Done Purchase
                            <?php if(@$doneCount >= 1): ?>
                                <span class="badge bg-success rounded-pill ms-1"><?php echo e($doneCount); ?></span>
                            <?php endif; ?>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade active show p-3" id="navs-pills-top-new" role="tabpanel">
                    <div class="table-responsive">
                        <table class="datatable-purchase-request-new table table-bordered">
                            <thead>
                                <tr>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th class="text-center">Sign</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade p-3" id="navs-pills-top-acc" role="tabpanel">
                    <div class="table-responsive">
                        <table class="datatable-purchase-request-acc table table-bordered">
                            <thead>
                                <tr>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th class="text-center">Sign</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade p-3" id="navs-pills-top-delivery" role="tabpanel">
                    <div class="table-responsive">
                        <table class="datatable-purchase-request-delivery table table-bordered">
                            <thead>
                                <tr>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th class="text-center">Sign</th>
                                    <th>Tipe Pembelian</th>
                                    <th>Cargo</th>
                                    <th>No Resi</th>
                                    <th>Tgl Pembelian</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade p-3" id="navs-pills-top-done" role="tabpanel">
                    <div class="table-responsive">
                        <table class="datatable-purchase-request-done table table-bordered">
                            <thead>
                                <tr>
                                    <th>No PR</th>
                                    <th>No PO</th>
                                    <th>No SO</th>
                                    <th>Customer</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th class="text-center">Sign</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
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
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-advanced.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-purchase-request.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        // Initialize Bootstrap tooltips using jQuery
        $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        $(document).on('click', '.delete-payable', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('payable-acount')); ?>/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'DELETE',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Deleted!",
                                    text: "Your file has been deleted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/payable-acount';
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Delete!'
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/warehouse/purchase/index.blade.php ENDPATH**/ ?>