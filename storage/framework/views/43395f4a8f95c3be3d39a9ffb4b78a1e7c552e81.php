
<?php $__env->startSection('title', 'Data Product'); ?>
<?php $__env->startSection('content'); ?>


<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 py-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
            <i class="mdi mdi-package-variant-closed text-primary"></i> <?php echo e($product->commodity); ?>

        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 13px;">
                <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo e(url('product')); ?>">Products</a></li>
                <li class="breadcrumb-item active"><?php echo e($product->commodity); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="#" data-bs-toggle="modal" data-bs-target="#updateStock-<?php echo e($product->id); ?>"
            class="btn btn-sm btn-label-success rounded-pill px-3">
            <i class="mdi mdi-tray-arrow-down me-1"></i> Edit Stock
        </a>
        <a href="#" data-bs-toggle="modal" data-bs-target="#updateProduct-<?php echo e($product->id); ?>"
            class="btn btn-sm btn-label-primary rounded-pill px-3">
            <i class="mdi mdi-pencil-outline me-1"></i> Edit
        </a>
        <?php if(Auth::user()->role == 'Admin'): ?>
            <a href="#" data-id="<?php echo e($product->id); ?>" class="btn btn-sm btn-label-danger rounded-pill px-3 delete-product">
                <i class="mdi mdi-delete-outline me-1"></i> Delete
            </a>
        <?php endif; ?>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="mdi mdi-warehouse fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Warehouse Stock</small>
                    <span class="fw-bold text-dark" style="font-size: 15px;"><?php echo e($product->warehouse_stock); ?> <?php echo e($product->unit); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-info"><i class="mdi mdi-office-building-outline fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Office Stock</small>
                    <span class="fw-bold text-dark" style="font-size: 15px;"><?php echo e($product->stock); ?> <?php echo e($product->unit); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-warning"><i class="mdi mdi-timer-sand fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">Pending Stock</small>
                    <span class="fw-bold text-dark" style="font-size: 15px;"><?php echo e($product->pending_stock); ?> <?php echo e($product->unit); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div class="avatar avatar-md flex-shrink-0">
                    <span class="avatar-initial rounded-circle bg-label-success"><i class="mdi mdi-cube-outline fs-4"></i></span>
                </div>
                <div>
                    <small class="text-muted fw-semibold d-block" style="font-size: 11px;">All Stock</small>
                    <span class="fw-bold text-success" style="font-size: 15px;"><?php echo e($allStock); ?> <?php echo e($product->unit); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    
    <div class="col-xl-8">
        
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-information-outline text-primary"></i> Informasi Produk
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3" style="font-size: 13px;">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Comodity</small>
                        <span class="fw-semibold text-dark"><?php echo e($product->commodity); ?></span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Short Description</small>
                        <span class="fw-semibold text-dark"><?php echo e($product->detail_desc ?: '-'); ?></span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Genuine / OEM</small>
                        <span class="fw-semibold text-dark"><?php echo e($product->go ?: '-'); ?></span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Category</small>
                        <span class="badge bg-label-secondary rounded-pill px-3 py-1"><?php echo e($product->category ?: '-'); ?></span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Dimension</small>
                        <span class="fw-semibold text-dark"><?php echo e($product->dimension ?: '-'); ?></span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Weight</small>
                        <span class="fw-semibold text-dark"><?php echo e($product->weight); ?> Gram</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Stock Awal</small>
                        <span class="fw-semibold text-dark"><?php echo e($product->first_stock); ?> <?php echo e($product->unit); ?> (<?php echo e($product->date); ?>)</span>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row g-3" style="font-size: 13px;">
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Note</small>
                        <p class="mb-0 text-dark" style="white-space: pre-wrap; max-width: 100%; overflow-x: auto;"><?php echo e($product->note ?: '-'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Description</small>
                        <p class="mb-0 text-dark" style="white-space: pre-wrap; max-width: 100%; overflow-x: auto;"><?php echo e($product->description ?: '-'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        
        <?php if($partInquiries->isNotEmpty()): ?>
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-magnify text-primary"></i> Part Inquiry
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Part Number</th>
                                <th>Harga Jual</th>
                                <th>Jumlah Vendor</th>
                                <th>Last Inquiry</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $partInquiries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($pi->brand); ?></td>
                                    <td><?php echo e($pi->pn); ?></td>
                                    <td>Rp <?php echo e(number_format($pi->price, 0, ',', '.')); ?></td>
                                    <td><?php echo e($pi->sparePartVendorPrices->count()); ?> vendor</td>
                                    <td><?php echo e($pi->sparePartVendorPrices->max('date') ? \Carbon\Carbon::parse($pi->sparePartVendorPrices->max('date'))->format('d M Y') : '-'); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('part-inquiry.show', $pi->id)); ?>" class="btn btn-sm btn-label-primary">
                                            <i class="mdi mdi-eye-outline"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-arrow-down-bold-box-outline text-primary"></i> Product In
                </h6>
            </div>
            <div class="card-body">
                <table class="datatable-product-in-detail table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>invoice</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-arrow-up-bold-box-outline text-primary"></i> Product Out
                </h6>
            </div>
            <div class="card-body">
                <table class="datatable-product-out-detail table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>invoice</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-file-document-multiple-outline text-primary"></i> Quotation
                </h6>
            </div>
            <div class="card-body">
                <table class="datatable-product-quotation table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>no quote</th>
                            <th>equivalent</th>
                            <th>Qty</th>
                            <th>price</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-shuffle-variant text-primary"></i> Equivalent
                </h6>
                <button type="button" class="btn btn-xs btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createEquivalent-<?php echo e($product->id); ?>">
                    <i class="mdi mdi-plus me-1"></i> New
                </button>
            </div>
            <div class="card-body">
                <table class="datatable-product-equivalent<?php echo e(Auth::user()->role == 'Logistic' ? '-logistik' : ''); ?> table table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Brand</th>
                            <th>PN</th>
                            <th>Price</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    
    <div class="col-xl-4">
        
        <div class="card border-0 shadow-sm mb-3 overflow-hidden">
            <div class="card-header bg-body-tertiary border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="mdi mdi-swap-horizontal text-primary"></i> Replacement
                </h6>
                <button type="button" class="btn btn-xs btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#createReplacement-<?php echo e($product->id); ?>">
                    <i class="mdi mdi-plus me-1"></i> New
                </button>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-striped mb-0">
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
                        <?php $__empty_1 = true; $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $allRep = $detail->stock + $detail->warehouse_stock;
                            ?>
                            <tr>
                                <td>
                                    <?php echo e($detail->replacement); ?>

                                </td>
                                <td>
                                    <?php echo e($allRep); ?> <?php echo e($detail->product->unit); ?>

                                </td>
                                <?php if(Auth::user()->role == 'Admin'): ?>
                                    <td>
                                        Rp.<?php echo e(number_format($detail->modal, 0, '', '.')); ?>

                                    </td>
                                <?php endif; ?>
                                <td>
                                    <?php if(Auth::user()->role == 'Admin'): ?>
                                        <a href="#" data-id="<?php echo e($detail->id); ?>"
                                            class="btn btn-sm btn-label-danger delete-replacement">
                                            <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline m-0"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a type="button" data-bs-toggle="modal"
                                        data-bs-target="#editReplacement-<?php echo e($detail->id); ?>">
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
    <?php echo $__env->make('components.modal.warehouse.product.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.warehouse.product.stock', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.warehouse.replacement.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.warehouse.equivalent.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__currentLoopData = $serials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $serial): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.warehouse.equivalent.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.warehouse.replacement.form-price', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/moment/moment.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/flatpickr/flatpickr.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/FormValidation.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/Bootstrap5.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/formvalidation/dist/js/plugins/AutoFocus.min.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('page-script'); ?>
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/warehouse/product/detail.blade.php ENDPATH**/ ?>