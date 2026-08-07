
<?php $__env->startSection('title', 'Detail Customers'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Clients / Customers /</span> Details <?php echo e($customers->company); ?>

    </h4>
    <div class="row mb-4">
        <div class="col-md-6">
            <h5 class="fw-bold pb-1 mb-3">
                Details
            </h5>
            <div class="card">
                <div class="card-header pb-0">
                    <div class="text-end text-muted">
                        <a type="button" data-bs-toggle="modal" data-bs-target="#updateCustomers-<?php echo e($customers->id); ?>">
                            <button type="button" class="btn btn-sm btn-label-primary">Edit</button>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <p class="card-text">
                    <div class="row mb-1">
                        <div class="col-3">
                            Adress
                        </div>
                        <div class="col-9">
                            : <?php echo e($customers->address); ?>

                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Phone
                        </div>
                        <div class="col-9">
                            : <?php echo e($customers->phone); ?>

                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Email
                        </div>
                        <div class="col-9">
                            : <?php echo e($customers->email); ?>

                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Mobile
                        </div>
                        <div class="col-9">
                            : <?php echo e($customers->mobile); ?>

                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            R/U
                        </div>
                        <div class="col-9">
                            : <?php echo e($customers->ru); ?>

                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Source
                        </div>
                        <div class="col-9">
                            : <?php echo e($customers->source); ?>

                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-3">
                            Machine
                        </div>
                        <div class="col-9">
                            : <?php echo e($customers->machine); ?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            Assigned
                        </div>
                        <div class="col-9">
                            : <?php echo e($customers->sales->name); ?>

                        </div>
                    </div>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold pb-1 mb-3">
                    PIC
                </h5>
                <a type="button" data-bs-toggle="modal" data-bs-target="#createPic">
                    <button type="button" class="btn btn-primary">
                        + Create New PIC
                    </button>
                </a>
            </div>
            <?php $__currentLoopData = $charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card mb-2">
                    <div class="card-header pb-0">
                        <div class="text-end text-muted">
                            <a type="button" data-bs-toggle="modal" data-bs-target="#updatePic-<?php echo e($pic->id); ?>">
                                <button type="button" class="btn btn-sm btn-label-primary">
                                    <i class="menu-icon tf-icons mdi mdi-14px mdi-account-edit-outline"></i>Edit
                                </button>
                            </a>
                            <button type="button" class="btn btn-sm btn-label-danger">
                                <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline"></i>Delete
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text">
                        <div class="row mb-1">
                            <div class="col-3">
                                Name
                            </div>
                            <div class="col-9">
                                : <?php echo e($pic->name_pic); ?>

                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-3">
                                Phone
                            </div>
                            <div class="col-9">
                                : <?php echo e($pic->phone_pic); ?>

                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-3">
                                Email
                            </div>
                            <div class="col-9">
                                : <?php echo e($pic->email_pic); ?>

                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-3">
                                Position
                            </div>
                            <div class="col-9">
                                : <?php echo e($pic->position); ?>

                            </div>
                        </div>
                        </p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="col-md-12">
            <div class="d-flex justify-content-between mb-2">
                <h5 class="fw-bold pb-1 mb-3">
                    Machine
                </h5>
                <a type="button" data-bs-toggle="modal" data-bs-target="#createMachine">
                    <button type="button" class="btn btn-primary">
                        + Create New machine
                    </button>
                </a>
            </div>
            <div class="row">
                <?php $__currentLoopData = $machines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="card mb-2 col-6">
                        <div class="card-header pb-0">
                            <div class="text-end text-muted">
                                <button type="button" class="btn btn-sm btn-label-danger">
                                    <i class="menu-icon tf-icons mdi mdi-14px mdi-delete-outline"></i>Delete
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                            <div class="row mb-1">
                                <div class="col-3">
                                    Brand
                                </div>
                                <div class="col-9">
                                    : <?php echo e($machine->brand); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Phone
                                </div>
                                <div class="col-9">
                                    : <?php echo e($machine->type); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Email
                                </div>
                                <div class="col-9">
                                    : <?php echo e($machine->serial_number); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Position
                                </div>
                                <div class="col-9">
                                    : <?php echo e($machine->bar); ?>

                                </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-3">
                                    Position
                                </div>
                                <div class="col-9">
                                    : <?php echo e($machine->running); ?>

                                </div>
                            </div>
                            </p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 my-3">
            <h5 class="fw-bold pb-1 mb-2">
                Daily Call History
            </h5>
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Action</th>
                                <th>Status</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php $__empty_1 = true; $__currentLoopData = $callhis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $callhistory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <?php echo e(\Carbon\Carbon::parse($callhistory->date)->format('d-m-Y')); ?>

                                    </td>
                                    <td>
                                        <?php echo e($callhistory->action); ?>

                                    </td>
                                    <td>
                                        <?php echo e($callhistory->status); ?>

                                    </td>
                                    <td>
                                        <?php echo e($callhistory->clients->area); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Kamu belum punya Call History.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6 my-3">
            <h5 class="fw-bold pb-1 mb-2">
                Quotation
            </h5>
            <div class="card">
                <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Number Quote</th>
                                <th>Status</th>
                                <th>Total Price</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php $__empty_1 = true; $__currentLoopData = $quote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quotation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <?php echo e(\Carbon\Carbon::parse($quotation->estimated_date)->format('d-m-Y')); ?>

                                    </td>
                                    <td>
                                        <?php echo e($quotation->no_quote); ?>

                                    </td>
                                    <td><span
                                            class="badge bg-label-<?php echo e($quotation->status == '25' ? 'info' : ($quotation->status == '50' ? 'warning' : ($quotation->status == '75' ? 'primary' : ($quotation->status == '100' ? 'success' : ($quotation->status == '0' ? 'danger' : ''))))); ?>"><?php echo e($quotation->status); ?>%</span>
                                    </td>
                                    <td>
                                        RP <?php echo e(number_format($quotation->harga_total, 0, '', '.')); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center">
                                        Kamu belum punya Quotation.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('components.modal.client.customers.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.pic.customers.form-create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php $__currentLoopData = $charge; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo $__env->make('components.modal.pic.customers.form-update', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
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
        $(document).on('click', '.delete-pic', function() {
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
                        'url': '<?php echo e(url('pic')); ?>/' + id,
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
            // Swal.fire({
            //     title: "Are you sure?",
            //     text: "You won't be able to revert this!",
            //     icon: "warning",
            //     showCancelButton: true,
            //     confirmButtonColor: "#3085d6",
            //     cancelButtonColor: "#d33",
            //     confirmButtonText: "Yes, delete it!"
            // }).then((result) => {
            //     if (result.isConfirmed) {
            //         $.ajax({
            //             'url': '<?php echo e(url('leads')); ?>/' + id,
            //             'type': 'POST',
            //             'data': {
            //                 '_method': 'DELETE',
            //                 '_token': '<?php echo e(csrf_token()); ?>'
            //             },
            //             success: function(response) {
            //                 if (response == 1) {
            //                     Swal.fire({
            //                         title: "Deleted!",
            //                         text: "Your file has been deleted.",
            //                         icon: "success"
            //                     })
            //                     window.setTimeout(function() {
            //                         location.reload();
            //                     }, 2000);
            //                 } else {
            //                     Swal.fire({
            //                         icon: 'error',
            //                         title: 'Oops...',
            //                         text: 'Data Failed to Delete!'
            //                     });
            //                 }
            //             }
            //         });
            //     }
            // });
        });
    </script>
<?php $__env->stopPush(); ?>


<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/clients/customers/detail.blade.php ENDPATH**/ ?>