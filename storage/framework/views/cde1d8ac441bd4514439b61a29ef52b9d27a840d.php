
<?php $__env->startSection('title', $expense->memo); ?>
<div class="invoice-print p-4">
    <div class="container-fluid flex-grow-1 container-p-y">

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
                        <span class="fw-bolder"><?php echo e($expense->no_invoice); ?></span>
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
        <div class="mb-2">
            <table class="table table-borderless m-0" style="width: 100%">

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
                        <td class="align-top"> RP
                            <?php echo e($expense->id_bank ? number_format($expense->amount, 0, '', '.') : '0,00 '); ?></td>
                    </tr>
                </tbody>
            </table>
            <p class="fs-5 fw-medium mt-2 p-2" style="background-color: rgb(248, 248, 248); width:100%;"> Say
                amount: #
                <?php echo e($terbilang); ?> Rupiah</p>
        </div>
    </div>
</div>
<?php $__env->startPush('after-style'); ?>
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('assets')); ?>/vendor/css/pages/app-invoice-print.css" />
    <link rel="stylesheet" href="style.css">
<?php $__env->stopPush(); ?>
<?php $__env->startPush('after-script'); ?>
    <script src="<?php echo e(asset('assets')); ?>/js/app-invoice-print.js"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.sales.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/finance/expense/detail-print.blade.php ENDPATH**/ ?>