
<?php $__env->startSection('title', 'Product In'); ?>
<?php $__env->startSection('content'); ?>
    <div class="row invoice-preview">
        
        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4">
            <div class="card invoice-preview-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                        <div class="mb-xl-0 pb-1">
                            <div class="d-flex svg-illustration align-items-center gap-2 mb-4">
                                <span class="app-brand-logo demo">
                                    <span style="color: var(--bs-primary)">
                                        <img class="text-md" src="<?php echo e(asset('/asset')); ?>/logo/Reftech-Log.png" alt=""
                                            srcset="" width="60%">
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold">Barang Masuk</h3>
                            <div>
                                <span class="fw-bolder">#<?php echo e($product->invoice); ?></span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted"><?php echo e(Carbon\Carbon::parse($product->date)->format('d-m-Y')); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="row">
                        <div class="col-4 col-lg-2 fw-medium">
                            <p class="mb-1">Supplier </p>
                            <p class="mb-1">Note</p>
                        </div>
                        <div class="col-8">
                            <p class="mb-1">: <?php echo e($product->supplier ?? optional($product->supp)->supplier ?? '-'); ?></p>
                            <p class="mb-1">: <?php echo e($product->note); ?></p>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table m-0 mb-4">
                        <thead class="table-light border-top">
                            <tr>
                                <th>No.</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Modal</th>
                                <th>Discount</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 0;
                            ?>
                            <?php $__currentLoopData = $detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $products): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                    <?php if(Auth::user()->role == 'Logistic'): ?>
                                        <td class="align-top">RP <?php echo e(str_repeat('*', strlen((string) $products->modal))); ?>

                                        </td>
                                        <td class="align-top">RP <?php echo e(str_repeat('*', strlen((string) $products->disc))); ?>

                                        </td>
                                        <td class="align-top">RP <?php echo e(str_repeat('*', strlen((string) $products->amount))); ?>

                                        </td>
                                    <?php else: ?>
                                        <td class="align-top">RP <?php echo e(number_format($products->modal, 0, '', '.')); ?></td>
                                        <td class="align-top">RP <?php echo e(number_format($products->disc, 0, '', '.')); ?></td>
                                        <td class="align-top">RP <?php echo e(number_format($products->amount, 0, '', '.')); ?></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <tr style="font-size: 13px">
                                <td colspan="4" style="border:none;"></td>
                                <td>Subtotal</td>
                                <?php if(Auth::user()->role == 'Logistic'): ?>
                                    <td>: RP <?php echo e(str_repeat('*', strlen((string) $product->subtotal))); ?></td>
                                <?php else: ?>
                                    <td>: RP <?php echo e(number_format($product->subtotal, 0, '', '.')); ?></td>
                                <?php endif; ?>
                            </tr>
                            <tr style="font-size: 13px">
                                <td colspan="4" style="border:none;"></td>
                                <td>Tax <?php echo e($product->tax == '11' ? '11%' : ''); ?></td>
                                <?php if(Auth::user()->role == 'Logistic'): ?>
                                    <td>: RP <?php echo e(str_repeat('*', strlen((string) $tax))); ?></td>
                                <?php else: ?>
                                    <td>: RP <?php echo e(number_format($tax, 0, '', '.')); ?></td>
                                <?php endif; ?>
                            </tr>
                            <tr style="font-size: 13px;">
                                <td colspan="4" style="border:none;"></td>
                                <td>Shipping</td>
                                <td>: RP <?php echo e(number_format($product->shipping, 0, '', '.')); ?></td>
                            </tr>
                            <tr style="font-size: 13px">
                                <td colspan="4" style="border:none;"></td>
                                <td style="border:none;">Total</td>
                                <?php if(Auth::user()->role == 'Logistic'): ?>
                                    <td style="border:none;">: RP <?php echo e(str_repeat('*', strlen((string) $product->total))); ?>

                                    </td>
                                <?php else: ?>
                                    <td style="border:none;">: RP <?php echo e(number_format($product->total, 0, '', '.')); ?></td>
                                <?php endif; ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
        
        
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <?php if($product->accept == '0'): ?>
                <div class="card mb-3">
                    <div class="card-body">
                        <a href="#" class="btn btn-success d-grid w-100 waves-effect accept-product"
                            data-id="<?php echo e($product->id); ?>">Accept</a>
                    </div>
                </div>
            <?php endif; ?>
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="<?php echo e(route('productIn.print', $product->id)); ?>">
                        Download
                    </a>
                    
                    <?php if(Auth::user()->role == 'Admin' || Auth::user()->role == 'Accounting'): ?>
                        <a href="<?php echo e(route('product-in.edit', $product->id)); ?>"
                            class="btn btn-secondary d-grid w-100 waves-effect mb-3">
                            Edit Price
                        </a>
                    <?php endif; ?>
                    <a href="#" class="btn btn-danger d-grid w-100 waves-effect delete-invoice"
                        data-id="<?php echo e($product->id); ?>">Delete</a>
                </div>
            </div>
        </div>
        
    </div>

<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        $(document).on('click', '.delete-invoice', function() {
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
                        'url': '<?php echo e(url('product-in')); ?>/' + id,
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
                                    window.location.href = '/product-in';
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
                                window.setTimeout(function() {
                                    window.location.href = '/product-in/' + id;
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/warehouse/product-in/detail.blade.php ENDPATH**/ ?>