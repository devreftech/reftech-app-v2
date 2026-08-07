<div class="modal-onboarding modal fade animate__animated" id="detailFee" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="onboarding-content mb-0">
                    <h4 class="onboarding-title text-body"> Insert Fee of <?php echo e($quote->no_quote); ?></h4>
                    <div class="onboarding-info mb-3">
                        <?php echo e($quote->pic->client->company); ?>

                    </div>
                    <form>
                        <div class="row mb-4">
                            <?php $__currentLoopData = $dquote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-6">
                                    <p class="text-start"> <?php echo e($detail->product); ?>'s fee</p>
                                </div>
                                <div class="col-6">
                                    : Rp <?php echo e(number_format($detail->fee, 0, ',', '.')); ?>

                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <hr>
                            <div class="col-6">
                                <h5 class="text-start"> Total Fee</h5>
                            </div>
                            <div class="col-6">
                                <h5>: Rp <?php echo e(number_format($quote->fee, 0, ',', '.')); ?></h5>
                            </div>
                            <div class="col-6">
                                <h5 class="text-start"> Nett Profit</h5>
                            </div>
                            <div class="col-6">
                                <h5>: Rp <?php echo e(number_format($quote->nett, 0, ',', '.')); ?></h5>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/quotation/detail-fee.blade.php ENDPATH**/ ?>