
<?php $__env->startSection('title', 'Detail Employee'); ?>
<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Users /</span> <?php echo e($users->name); ?>

    </h4>
    <div class="row">
        <div class="col-6 col-md-3 mx-auto mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <img src="<?php echo e(url('') . '/' . $users->image); ?>" alt="" srcset="" class="h-100 w-100">
                </div>
            </div>
        </div>
        <div class="col-12 col-md-9 mb-3">
            <div class="card h-100">
                <div class="card-header pb-3">
                    <div class="text-end text-muted">
                        <?php if($users->role == 'Sales'): ?>
                            <a type="button" data-bs-toggle="modal" data-bs-target="#updateTarget-<?php echo e($users->id); ?>">
                                <button type="button" class="btn btn-sm btn-label-success">Edit Target</button>
                            </a>
                        <?php endif; ?>
                        <a type="button" data-bs-toggle="modal" data-bs-target="#updateUsers-<?php echo e($users->id); ?>">
                            <button type="button" class="btn btn-sm btn-label-primary">Edit Profile</button>
                        </a>
                        <a href="#" data-id="<?php echo e($users->id); ?>"
                            class="btn btn-sm btn-label-danger delete-user">Delete</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h5 class="text-muted">Profile</h5>
                            <div class="row">
                                <div class="col-4">
                                    <p class="mb-1">
                                        NIP
                                    </p>
                                </div>
                                <div class="col-8">: <?php echo e($users->nip); ?> </div>
                                <div class="col-4">
                                    <p class="mb-1">
                                        Name
                                    </p>
                                </div>
                                <div class="col-8">: <?php echo e($users->name); ?> </div>
                                <div class="col-4">
                                    <p class="mb-1">
                                        Phone
                                    </p>
                                </div>
                                <div class="col-8">: <?php echo e($users->phone); ?> </div>
                                <div class="col-4">
                                    <p class="mb-1">
                                        Birthday
                                    </p>
                                </div>
                                <div class="col-8">: <?php echo e($users->birthday); ?> </div>
                                <div class="col-4">
                                    <p>
                                        Address
                                    </p>
                                </div>
                                <div class="col-8">
                                    <pre class="mb-1"
                                        style="font-family: 'Inter', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap; font-size: 15px;">: <?php echo e($users->address); ?>

                                    </pre>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <h5 class="text-muted">Data Employee</h5>
                            <div class="row">
                                <div class="col-4">
                                    <p class="mb-1">
                                        Entry Date
                                    </p>
                                </div>
                                <div class="col-8">: <?php echo e($users->date_in); ?> </div>
                                <div class="col-4">
                                    <p class="mb-1">
                                        Position
                                    </p>
                                </div>
                                <div class="col-8">: <?php echo e($detail[0]->position); ?> </div>
                                <div class="col-4">
                                    <p class="mb-1">
                                        Role
                                    </p>
                                </div>
                                <div class="col-8">: <?php echo e($users->role); ?> </div>
                                <div class="col-4">
                                    <p class="mb-1">
                                        Area
                                    </p>
                                </div>
                                <div class="col-8">: <?php echo e($detail[0]->area); ?> </div>
                                <div class="col-4">
                                    <p class="mb-1">
                                        Code
                                    </p>
                                </div>
                                <div class="col-8">: <?php echo e($users->code); ?> </div>
                                <div class="col-4">
                                    <p class="mb-1">
                                        Status
                                    </p>
                                </div>
                                <div class="col-8">: <?php echo e($users->active == '1' ? 'Active' : 'Non Active'); ?> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="fw-bold">History Position</h5>
                    <a type="button" data-bs-toggle="modal" data-bs-target="#newPosition-<?php echo e($users->id); ?>">
                        <button type="button" class="btn btn-sm btn-label-primary waves-effect waves-light">Update
                            Position</button>
                    </a>
                </div>
                <div class="table-responsive text-nowrap mb-4">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Position</th>
                                <th>Role</th>
                                <th>Area</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php $__empty_1 = true; $__currentLoopData = $detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($details->date); ?></td>
                                    <td><?php echo e($details->position); ?></td>
                                    <td><?php echo e($details->roles); ?></td>
                                    <td><?php echo e($details->area); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4">Tidak ada History</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('components.modal.user.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.user.position', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('components.modal.user.target', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('after-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.css" />
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/vendor/libs/sweetalert2/sweetalert2.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('page-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/pages-account-settings-account.js"></script>
    <script src="<?php echo e(asset('assets')); ?>/js/extended-ui-sweetalert2.js"></script>
<?php $__env->stopPush(); ?>
<?php $__env->startPush('script'); ?>
    <script>
        $(document).ready(function() {
            $(".cursor-pointer").click(function() {
                $(this).children().toggleClass("mdi-eye-off-outline mdi-eye-outline");
                toggleInputType($('#password'));
            });

            function toggleInputType(inputElement) {
                var currentType = inputElement.attr("type");
                var newType = (currentType === "password") ? "text" : "password";
                inputElement.attr("type", newType);
            }
            $("#phone").on("input", function() {
                $(this).val($(this).val().replace(/[^0-9]/g, ''));
            });

            function formatNumber(n) {
                return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            }
            $(".total-label").on('keyup click change', function() {
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
                $(`#semuanya`).val(nomorInt);
                console.log('ini value semuanya :' + $('#semuanya').val());
            });
            $('#ddSales').on('change', function() {
                var role = $(this).val();
                console.log(role);
                if (role == 'Sales') {
                    $('#inputTarget').removeAttr('hidden');
                } else {
                    $('#inputTarget').attr('hidden', true);
                }
            });
        });
        $(document).on('click', '.delete-user', function() {
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
                        'url': '<?php echo e(url('employee')); ?>/' + id,
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
                                    window.location.href = '/employee';
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/admin/employee/user/detail.blade.php ENDPATH**/ ?>