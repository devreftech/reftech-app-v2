
<?php $__env->startSection('title', 'Detail Return'); ?>
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
                                        <img class="text-md"
                                            src="<?php echo e(url('https://reftech.id/wp-content/uploads/2021/10/Reftech-Logo-Hitam.png')); ?>"
                                            alt="" srcset="" width="60%">
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <h3 class="fw-bold">Return Product</h3>
                            <div>
                                <span class="fw-bolder">#<?php echo e($return->no_return); ?></span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted"><?php echo e(Carbon\Carbon::parse($return->date)->format('d-m-Y')); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="row">
                        <div class="col-4 col-lg-2 fw-medium">
                            <p class="mb-1">Client </p>
                            <p class="mb-1">Sales </p>
                        </div>
                        <div class="col-8">
                            <p class="mb-1">: <?php echo e($quote->pic->client->company); ?> - <?php echo e($quote->pic->name_pic); ?></p>
                            <p class="mb-1">: <?php echo e($quote->sales->name); ?></p>
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
                                <th>note</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 0;
                            ?>
                            <?php $__currentLoopData = $dReturn; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $no++;
                                ?>
                                <tr style="font-size: 13px">
                                    <td class="align-top"><?php echo e($no); ?></td>
                                    <td class="text-nowrap align-top">
                                        <p class="mb-0 fw-semibold" style="font-size: 12px">
                                            <?php echo e($product->replacement->replacement); ?>

                                        </p>
                                        <pre class="mb-0"
                                            style="font-size: 10px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; max-width: 100%; overflow-x: auto; white-space: pre-wrap;"><?php echo e($product->replacement->product->description); ?></pre>
                                    </td>
                                    <td class="align-top"><?php echo e($product->qty); ?>

                                    </td>
                                    <td class="align-top"><?php echo e($product->note); ?>

                                    </td>
                                    <td class="align-top">
                                        <?php if($return->status == 0): ?>
                                            <?php if($product->status == 0): ?>
                                                <a href="#"
                                                    class="btn btn-primary d-grid w-100 waves-effect accept-return mb-3"
                                                    data-id="<?php echo e($product->id); ?>" data-return="<?php echo e($return->id); ?>">
                                                    Accept
                                                </a>
                                            <?php else: ?>
                                                Accepted
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <?php if($product->status == 0): ?>
                                                Not Accepted
                                            <?php else: ?>
                                                Done
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body">
                    <?php if($return->status == 0): ?>
                        <a class="btn btn-primary d-grid w-100 mb-3 waves-effect"
                            href="<?php echo e(route('product-in.return', $return->id)); ?>">
                            Cetak Product In
                        </a>
                        <a href="#" class="btn btn-outline-danger d-grid w-100 mb-3 waves-effect delete-invoice"
                            data-id="<?php echo e($return->id); ?>">Delete</a>
                    <?php endif; ?>
                    <button class="btn btn-outline-secondary d-grid w-100 mb-3 waves-effect" id="backButton">
                        Back
                    </button>
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
        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
        }

        $('#backButton').click(function() {
            window.history.back();
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
            // console.log(id);
            $(`#modal-${id}`).val(nomorInt);
            var modal = $(`#modal-${id}`).val();
            console.log(modal);
        });

        $(document).on('click', '.accept-return', function() {
            var id = $(this).data('id');
            var idreturn = $(this).data('return');
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
                        'url': '<?php echo e(url('accept')); ?>/return/' + id,
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
                                    text: "Your file has been accepted.",
                                    customClass: {
                                        confirmButton: "btn btn-success waves-effect",
                                    },
                                })
                                window.setTimeout(function() {
                                    window.location.href = '/return/' + idreturn;
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/warehouse/return/detail.blade.php ENDPATH**/ ?>