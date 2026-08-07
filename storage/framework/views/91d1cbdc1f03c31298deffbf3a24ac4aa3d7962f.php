
<?php $__env->startSection('title', 'Purchase Invoice AP'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4"> <span class="text-muted">Account Payable / Purchase Invoice/</span> Invoice #123123 </h4>
    <div class="row mb-3">
        <div class="col-8">
            <div class="card">
                <div class="card-header">
                    <h4>Information</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body py-1">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="menu-icon tf-icons mdi mdi-file-document-outline m-0 fs-1"></i>
                                        </div>
                                        <div class="col">
                                            <p class="text-muted mb-0"> No Invoice</p>
                                            <a href="<?php echo e(route('product-in.show', $product->id)); ?>"
                                                class="text-black fs-5 fw-medium" target="_blank">
                                                <?php echo e($product->invoice); ?>

                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body py-1">
                                    <div class="row">
                                        <div class="col-2">
                                            <i class="menu-icon tf-icons mdi mdi-file-document-edit-outline m-0 fs-1"></i>
                                        </div>
                                        <div class="col">
                                            <p class="text-muted mb-0">Invoice Date</p>
                                            <h5><?php echo e(Carbon\Carbon::parse($product->date)->format('d-m-Y')); ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <p class="text-muted">Supplier</p>
                                    <h5 class="mb-0"><?php echo e($product->supp->supplier ?? $product->supplier); ?></h5>
                                    <p class="mb-0"><?php echo e($product->supp->info); ?></p>
                                    <h6 class="mb-0"><?php echo e($product->supp->npwp ?? ''); ?></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card h-100">
                <div class="card-body">
                    <h4>Summarry</h4>
                    <div class="row">
                        <div class="col-6">
                            <p>Total Invoice</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp <?php echo e(number_format($product->total, 0, ',', '.')); ?></p>
                        </div>
                        <div class="col-6">
                            <p>Advance Payment</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp
                                <?php echo e($product->accept == '1' ? number_format($product->accept, 0, ',', '.') : '0'); ?></p>
                        </div>
                        <div class="col-6">
                            <p>Subtotal</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp <?php echo e(number_format($product->accept, 0, ',', '.')); ?></p>
                        </div>
                        
                        <hr>
                        <div class="col-6">
                            <p class="fw-bolder">Grand Total</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp <?php echo e(number_format($product->accept, 0, ',', '.')); ?></p>
                        </div>
                        <div class="col-6">
                            <p>Outstanding</p>
                        </div>
                        <div class="col-6">
                            <p class="text-end fw-bolder">Rp
                                <?php echo e($product->accept == '1' ? number_format($product->accept, 0, ',', '.') : '0'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <h4>Detail Product In</h4>
            <div class="table-responsive">
                <table class="table m-0">
                    <thead class="">
                        <tr>
                            <th>Item</th>
                            <th>Desc</th>
                            <th>Qty</th>
                            <th>Price</th>
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
                                <td class="align-top"><?php echo e($products->qty); ?> <?php echo e($products->detailProduct->product->unit); ?>

                                </td>
                                <td class="align-top">RP <?php echo e(number_format($products->modal, 0, '', '.')); ?></td>
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
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="mb-3">
                    Retur Barang
                </h5>
                <?php if($product->accept == 0): ?>
                    <a type="button" data-bs-toggle="modal" data-bs-target="#productReturn">
                        <button type="button" class="btn btn-primary d-grid waves-effect float-end">
                            Retur Barang
                        </button>
                    </a>
                <?php endif; ?>
            </div>

            <div class="table-responsive">
                <table class="table m-0">
                    <thead class="">
                        <tr>
                            <th>Item</th>
                            <th>Desc</th>
                            <th>Qty</th>
                            <th>Note</th>
                            <th style="width: 20%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 0;
                        ?>
                        <?php $__empty_1 = true; $__currentLoopData = $return; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $retur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $no++;
                            ?>
                            <tr style="font-size: 13px">
                                <td class="align-top"><?php echo e($no); ?></td>
                                <td class="text-nowrap align-top">
                                    <p class="mb-0 fw-semibold" style="font-size: 12px">
                                        <?php echo e($retur->replacement->replacement); ?>

                                    </p>
                                    <pre class="mb-0"
                                        style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($retur->replacement->product->description); ?></pre>
                                </td>
                                <td class="align-top"><?php echo e($retur->qty); ?>

                                    <?php echo e($retur->replacement->product->unit); ?>

                                </td>
                                <td class="align-top"><?php echo e($retur->note); ?></td>
                                <td class="align-top">
                                    <?php if($retur->status == 0): ?>
                                        <a href="#" class="btn btn-primary d-grid w-100 waves-effect clear-return"
                                            data-id="<?php echo e($retur->id); ?>">Clear Return</a>
                                    <?php else: ?>
                                        <p class="text-success">Sudah Clear</p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada return di invoice ini</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php echo $__env->make('components.modal.payable.return', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
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
        $(document).on('click', '.clear-return', function() {
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
                        'url': '<?php echo e(url('product-in')); ?>/clear-return/' + id,
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
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/payable/detail-invoice.blade.php ENDPATH**/ ?>