<form action="<?php echo e(route('invoice.update', $invoice->id)); ?>" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PATCH'); ?>
    <div class="modal-onboarding modal fade animate__animated" id="acceptInvoice-<?php echo e($quote->id); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Accept Invoice <?php echo e($quote->no_quote); ?></h4>
                        <div class="onboarding-info mb-3">
                            <?php echo e($quote->pic->client->company); ?>

                        </div>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <?php
                                        if ($quote->pic->client->info == 'Reftech') {
                                            $code = 'RJO';
                                        } else {
                                            $code = 'KII';
                                        }

                                        if ($quote->pic->client->info == 'Reftech') {
                                            $nextCode = 'RJO';
                                        } else {
                                            $code = 'KII';
                                        }

                                        if ($quote->tax == '11' && $invoice->flag == 'Reftech') {
                                            $nextCode = $nextCodePR;
                                        } elseif ($quote->tax == '0' && $invoice->flag == 'Reftech') {
                                            $nextCode = $nextCodeNPR;
                                        } elseif ($quote->tax == '11' && $invoice->flag == 'Kojisha') {
                                            $nextCode = $nextCodePK;
                                        } elseif ($quote->tax == '0' && $invoice->flag == 'Kojisha') {
                                            $nextCode = $nextCodeNPK;
                                        }

                                    ?>
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            value="<?php echo e($quote->tax != 0 ? $nextCode . '/SJ-P/' . $code . '/' . $monthCode . '/' . $year : $nextCode . '/SJ-NP/' . $code . '/' . $monthCode . '/' . $year); ?>"
                                            placeholder="Put No Invoice Here ...." id="invoice" name="invoice">
                                        <label for="invoice">No Invoice</label>
                                    </div>

                                    <p class="text-danger text-start">
                                        Last No :
                                        <?php if($quote->tax == '11' && $invoice->flag == 'Reftech'): ?>
                                            <?php echo e($displayLastPR ?? '-'); ?>

                                        <?php elseif($quote->tax == '0' && $invoice->flag == 'Reftech'): ?>
                                            <?php echo e($displayLastNPR ?? '-'); ?>

                                        <?php elseif($quote->tax == '11' && $invoice->flag == 'Kojisha'): ?>
                                            <?php echo e($displayLastPK ?? '-'); ?>

                                        <?php elseif($quote->tax == '0' && $invoice->flag == 'Kojisha'): ?>
                                            <?php echo e($displayLastNPK ?? '-'); ?>

                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="text"
                                            placeholder="Put Terms & Payments Here ...." id="payment" name="payment"
                                            value="">
                                        <label for="payment">Terms & Payments</label>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-label-secondary waves-effect" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/invoice/accept.blade.php ENDPATH**/ ?>