
<?php $__env->startSection('title', 'Payment Recieve AR'); ?>
<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-6 mb-3">
                    <h4 class="mb-3">Supplier Info</h4>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="card bg-label-secondary">
                                <div class="card-body">
                                    No Invoice
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    : <a href="<?php echo e(route('invoice.show', $product->id)); ?>" class="text-black"
                                        target="_blank">
                                        <?php echo e($product->invoice); ?>

                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-label-secondary">
                                <div class="card-body">
                                    Company
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    : <?php echo e($product->supp->supplier ?? $product->supplier); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-label-secondary">
                                <div class="card-body">
                                    NPWP
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    : <?php echo e($product->supp->npwp ?? '-'); ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card bg-label-secondary h-100">
                                <div class="card-body">
                                    Info
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    : <?php echo e($product->info); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 mb-3">
                    
                    <div class="mb-xl-0 pb-1">
                        <div class="svg-illustration align-items-center gap-2 mb-4">
                            <span class="app-brand-logo demo">
                                <span style="color: var(--bs-primary)">
                                    <img src="<?php echo e(asset('/asset')); ?>/logo/Reftech-Log.png" alt="Logo" width="60%"
                                        class="d-block ms-auto">
                                </span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="info text-end pt-5 px-3">
                        <h6>Payment Receipt No.</h6>
                        <h3><?php echo e($receipt); ?></h3>
                        <p><?php echo e(Carbon\Carbon::parse($product->date_payment)->format('d-m-Y')); ?></p>
                        <?php
                            if ($product->accept == 0) {
                                $warna = 'text-danger';
                                $text = 'UNPAID';
                            } else {
                                $warna = 'text-success';
                                $text = 'PAID';
                            }

                        ?>
                        <h4 class=" <?php echo e($warna); ?>">
                            <?php echo e($text); ?></h4>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <h5 class="mb-3">
                    Product Info
                </h5>
                <?php if($product->accept == 0): ?>
                    <div class="functional d-flex justify-content-between">
                        <a class="mx-2" type="button" data-bs-toggle="modal" data-bs-target="#editDate">
                            <button type="button" class="btn btn-warning">
                                Edit Date
                            </button>
                        </a>
                        <a class="mx-2" type="button" data-bs-toggle="modal" data-bs-target="#addPPH">
                            <button type="button" class="btn btn-primary">
                                <?php echo e($product->pph > 0 ? 'Edit' : 'Add'); ?> PPH
                            </button>
                        </a>
                        <a href="#" class="btn btn-success d-grid waves-effect accept-product"
                            data-id="<?php echo e($product->id); ?>">Confirm Purchase</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table class="table m-0">
                    <thead class="">
                        <tr>
                            <th>Item</th>
                            <th>Desc</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <?php if($product->pph > 0): ?>
                                <th>PPH</th>
                            <?php endif; ?>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 0;
                        ?>
                        <?php $__currentLoopData = $detProduct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $products): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $no++;
                            ?>
                            <tr style="font-size: 13px">
                                <td class="align-top"><?php echo e($no); ?></td>
                                <td class="text-nowrap align-top">
                                    <p class="mb-0 fw-semibold" style="font-size: 12px">
                                        <?php echo e($products->detailProduct->replacement); ?>

                                    </p>
                                    <pre class="mb-0"
                                        style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($products->detailProduct->product->description); ?></pre>
                                </td>
                                <td class="align-top"><?php echo e($products->qty); ?>

                                    <?php echo e($products->detailProduct->product->unit); ?>

                                </td>
                                <td class="align-top">RP <?php echo e(number_format($products->modal, 0, '', '.')); ?></td>
                                <?php if($product->pph > 0): ?>
                                    <td class="align-top">RP <?php echo e(number_format($product->pph, 0, '', '.')); ?></td>
                                <?php endif; ?>
                                <td class="align-top">RP <?php echo e(number_format($products->amount, 0, '', '.')); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr class="table-light">
                            <td colspan="4" class="text-end">
                                <p class="mb-0">Total:</p>
                            </td>
                            <td colspan="2">
                                <p class="fw-semibold mb-0 text-end">RP
                                    <?php echo e(number_format($product->total, 0, '', '.')); ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php echo $__env->make('components.modal.payable.pph', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.payable.date', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-sales-invoice-ar.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        $(".invoice-item-pph-label").on('keyup', function() {
            var input = $(this)
            var input_val = input.val();

            // original length
            var original_len = input_val.length;

            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // send updated string to input
            input.val(input_val);
            var nomorInt = parseFloat(input_val.replace(/[.,]/g, ''));
            console.log(nomorInt);
            $(`#pph`).val(nomorInt);
        });

        $(document).on('click', '.accept-product', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Accept it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('product-in')); ?>/accept/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Accepted!",
                                    text: "Your file has been Accepted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                setTimeout(function() {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Accept!'
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
        $(document).on('click', '.confirm-payment', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to Confirm this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Confirm it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('product-in')); ?>/accept/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Confirmed!",
                                    text: "Your file has been Confirmed.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/payable/receipt/' +
                                        id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
                        icon: "error",
                        customClass: {
                            confirmButton: "btn btn-success waves-effect",
                        },
                    });
                }
            });
        });
        $(document).on('click', '.unconfirm-payment', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: "Are you sure to UnConfirm this?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, UnConfirm it!",
                customClass: {
                    confirmButton: "btn btn-primary me-3 waves-effect waves-light",
                    cancelButton: "btn btn-label-secondary waves-effect",
                },
                buttonsStyling: false,
            }).then(function(result) {
                if (result.value) {
                    $.ajax({
                        'url': '<?php echo e(url('unconfirm-payment')); ?>/payment/' + id,
                        'type': 'POST',
                        'data': {
                            '_method': 'POST',
                            '_token': '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response == 1) {
                                Swal.fire({
                                    icon: "success",
                                    title: "UnConfirmed!",
                                    text: "Your file has been UnConfirmed.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/payment-detail/payment/' +
                                        id;
                                }, 2000);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Data Failed to Convert!'
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire({
                        title: "Cancelled",
                        text: "Your Convert is cancelled :)",
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/payable/detail-receipt.blade.php ENDPATH**/ ?>