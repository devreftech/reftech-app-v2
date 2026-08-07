
<?php $__env->startSection('title', 'expense'); ?>
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
                            <h3 class="fw-bold">Transaction Journal</h3>
                            <div>
                                <span class="badge bg-label-primary fs-6 fw-bold"><?php echo e($expense->no_expense ?? '-'); ?></span>
                            </div>
                            <div class="mt-1">
                                <span class="fw-semibold text-dark"><?php echo e($expense->no_invoice ?? '-'); ?></span>
                            </div>
                            <div class="mt-1">
                                <span class="text-muted"><?php echo e(Carbon\Carbon::parse($expense->date)->format('d-m-Y')); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-body mb-3">
                    <div class="d-flex justify-content-between flex-xl-row flex-md-column flex-sm-row flex-column">
                        <div class="row">
                            <h4><?php echo e($expense->memo); ?></h4>
                            <div class="col-4 fw-medium">
                                <p class="mb-1">No Cheque.</p>
                                
                            </div>
                            <div class="col-8">
                                <p class="mb-1">: <?php echo e($expense->no_cheque); ?></p>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered m-0 mb-4">
                        <thead class="table-light border-top">
                            <tr>
                                <th>No.</th>
                                <th>Code</th>
                                <th>Account</th>
                                <th>Memo</th>
                                <th>Debit</th>
                                <th>Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 0;
                            ?>
                            <?php $__currentLoopData = $detailExpense; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $no++;
                                ?>
                                <tr style="font-size: 13px">
                                    <td class="align-top"><?php echo e($no); ?></td>
                                    <td class="align-top">
                                        <p class="mb-0 fw-semibold">
                                            <?php echo e($detail->account->code); ?>

                                        </p>
                                    </td>
                                    <td class="align-top">
                                        <p>
                                            <?php echo e($detail->account->name); ?>

                                        </p>
                                    </td>
                                    <td class="align-top">
                                        <?php echo e($detail->memo); ?>

                                    </td>
                                    <td class="align-top">RP <?php echo e(number_format($detail->amount, 2, '', '.')); ?></td>
                                    <td class="align-top">RP 0,00</td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if(@$expense->id_bank): ?>
                                <tr style="font-size: 13px">
                                    <td class="align-top"><?php echo e($no + 1); ?></td>
                                    <td class="align-top">
                                        <p class="mb-0 fw-semibold">
                                            1102-003
                                        </p>
                                    </td>
                                    <td class="align-top">BCA IDR</td>
                                    <td class="align-top">Kas/Bank</td>
                                    <td class="align-top"> RP 0,00</td>
                                    <td> RP <?php echo e(number_format($expense->amount, 0, '', '.')); ?></td>
                                </tr>
                            <?php endif; ?>
                            <tr style="font-size: 13px">
                                <td colspan="3" style="border:none;"></td>
                                <td>Total</td>
                                <td class="align-top"> RP <?php echo e(number_format($expense->amount, 0, '', '.')); ?></td>
                                <td class="align-top"> RP <?php echo e($expense->id_bank ? number_format($expense->amount, 0, '', '.') : '0,00 '); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="fs-5 fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:100%;"> Say
                        amount: #
                        <?php echo e($terbilang); ?> Rupiah</p>
                </div>
            </div>
        </div>
        
        
        <div class="col-xl-3 col-md-4 col-12 invoice-actions">
            <div class="card">
                <div class="card-body">
                    <a class="btn btn-primary btn-outline-secondary d-grid w-100 mb-3 waves-effect" target="_blank"
                        href="<?php echo e(route('expense.print', $expense->id)); ?>">
                        Download
                    </a>
                    
                    <a href="#" class="btn btn-danger d-grid w-100 waves-effect delete-expense mb-3"
                        data-id="<?php echo e($expense->id); ?>">Delete</a>
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

        $(document).on('click', '.delete-expense', function() {
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
                        'url': '<?php echo e(url('expense')); ?>/' + id,
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
                                    window.location.href = '/expense';
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

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/expense/detail.blade.php ENDPATH**/ ?>