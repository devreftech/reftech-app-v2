
<?php $__env->startSection('title', 'Data Product'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Products Set /</span> <?php echo e($productSet->product->commodity); ?>

    </h4>
    <div class="row mb-3">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="text-end text-muted">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#updateStock-<?php echo e($product->id); ?>">
                            <button type="button" class="btn btn-sm btn-label-success">Edit Stock</button>
                        </a>
                        <a type="button" data-bs-toggle="modal" data-bs-target="#updateProduct-<?php echo e($product->id); ?>">
                            <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                        </a>
                        <?php if(Auth::user()->role == 'Admin'): ?>
                            <a href="#" data-id="<?php echo e($product->id); ?>"
                                class="btn btn-sm btn-label-danger delete-product">Delete
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text">
                    <div class="row mb-1">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-3">
                                    Comodity
                                </div>
                                <div class="col-9">
                                    : <?php echo e($product->commodity); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Short Description
                                </div>
                                <div class="col-9">
                                    : <?php echo e($product->detail_desc); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Genuine / OEM
                                </div>
                                <div class="col-9">
                                    : <?php echo e($product->go); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Category
                                </div>
                                <div class="col-9">
                                    : <?php echo e($product->category); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Dimension
                                </div>
                                <div class="col-9">
                                    : <?php echo e($product->dimension); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Weight
                                </div>
                                <div class="col-9">
                                    : <?php echo e($product->weight); ?> Gram
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Stock Awal
                                </div>
                                <div class="col-9">
                                    : <?php echo e($product->first_stock); ?> <?php echo e($product->unit); ?> (<?php echo e($product->date); ?>)
                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Warehouse Stock
                                </div>
                                <div class="col-9">
                                    : <?php echo e($product->warehouse_stock); ?> <?php echo e($product->unit); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Office Stock
                                </div>
                                <div class="col-9">
                                    : <?php echo e($product->stock); ?> <?php echo e($product->unit); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Pending Stock
                                </div>
                                <div class="col-9">
                                    : <?php echo e($product->pending_stock); ?> <?php echo e($product->unit); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    All Stock
                                </div>
                                <div class="col-9">
                                    : <?php echo e($allStock); ?> <?php echo e($product->unit); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Note
                                </div>
                                <div class="col-9">
                                    <pre class="mb-1"
                                        style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: <?php echo e($product->note); ?>

                                </pre>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="row mb-1">
                                    <div class="col-3">
                                        Description
                                    </div>
                                    <div class="col-9">
                                        <pre class="mb-1"
                                            style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;">: <?php echo e($product->description); ?>

                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col col-12 ">
                <div class="d-flex justify-content-between mb-2">
                    <h5 class="fw-bold pb-1 mb-2">
                        Item Product set
                    </h5>
                    <a type="button" data-bs-toggle="modal" data-bs-target="#createItemReplacement">
                        <button type="button" class="btn btn-primary">
                            + New Item
                        </button>
                    </a>
                </div>
                <div class="card">
                    <div class="table-responsive text-nowrap h-100">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Replacement</th>
                                    <th>Stock</th>
                                    <?php if(Auth::user()->role == 'Admin'): ?>
                                        <th>Modal</th>
                                    <?php endif; ?>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php $__empty_1 = true; $__currentLoopData = $itemProduct; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $allRep = $detail->replacement->stock + $detail->replacement->warehouse_stock;
                                    ?>
                                    <tr>
                                        <td>
                                            <?php echo e($detail->replacement->replacement); ?>

                                        </td>
                                        <td>
                                            <?php echo e($allRep); ?> <?php echo e($detail->replacement->product->unit); ?>

                                        </td>
                                        <?php if(Auth::user()->role == 'Admin'): ?>
                                            <td>
                                                Rp.<?php echo e(number_format($detail->replacement->modal, 0, '', '.')); ?>

                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <?php if(Auth::user()->role == 'Admin'): ?>
                                                <a href="#" data-id="<?php echo e($detail->replacement->id); ?>"
                                                    class="btn btn-sm btn-label-danger delete-replacement">
                                                    <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a type="button" data-bs-toggle="modal"
                                                data-bs-target="#editReplacement-<?php echo e($detail->replacement->id); ?>">
                                                <button type="button" class="btn btn-sm btn-label-primary">
                                                    <i
                                                        class="menu-icon tf-icons mdi mdi-14px mdi-note-edit-outline m-0"></i>
                                                </button>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            Kamu belum punya Replacement.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('components.modal.warehouse.product.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.product.set.item', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <?php echo $__env->make('components.modal.warehouse.replacement.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet"
        href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/select2/select2.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/forms-selects.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/tables-datatables-basic.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-equivalent.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-equivalent-logistik.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-product-in-detail.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-product-out-detail.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/includes/table-quotation-product.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script></script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('script'); ?>
    <script>
        $(document).on('click', '.delete-product', function() {
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
                        'url': '<?php echo e(url('product')); ?>/' + id,
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
                                    window.location.href = '/product';
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
        $(document).on('click', '.delete-replacement', function() {
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
                        'url': '<?php echo e(url('product')); ?>/replacement/' + id,
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
                                    location.reload();
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
        $(document).on('click', '.delete-equivalent', function() {
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
                        'url': '<?php echo e(url('product')); ?>/equivalent/' + id,
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
                                    location.reload();
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
        $(() => {

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }


            $(".invoice-item-price-label").on('keyup', function() {
                var input = $(this)
                var id = input.data('id');
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
                console.log(id);
                console.log(nomorInt);
                $(`#price-${id}`).val(nomorInt);
            });
            $(".invoice-item-modal-label").on('keyup', function() {
                var input = $(this)
                var id = input.data('id');
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
                console.log(id);
                console.log(nomorInt);
                $(`#modal-${id}`).val(nomorInt);
            });
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/warehouse/product-set/detail.blade.php ENDPATH**/ ?>