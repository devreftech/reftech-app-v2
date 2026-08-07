<form action="<?php echo e(route('change-po.quotation', $invoices->id)); ?>" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal-onboarding modal fade animate__animated" id="changePo<?php echo e($invo); ?>" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Change no PO <?php echo e($invo == 0 ? 'DP' : 'BP'); ?></h4>
                        <div class="onboarding-info mb-3">
                            <?php echo e($quote->pic->client->company); ?>

                        </div>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control form-control-sm" type="text"
                                            placeholder="Put No Purchase Order Here ...." id="po" name="po"
                                            value="<?php echo e($invoices->no_po); ?>">
                                        <label for="po">No Purchase Order</label>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/quotation/change-po.blade.php ENDPATH**/ ?>