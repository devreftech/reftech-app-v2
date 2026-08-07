<form action="<?php echo e(route('accept.contract', $contract->id)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <div class="modal-onboarding modal fade animate__animated" id="acceptContract<?php echo e($contract->id); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Accept Contract of <?php echo e($contract->no_contract); ?></h4>
                        <div class="onboarding-info mb-3">
                            <?php echo e($contract->unitQuotation?->client?->company ?? '-'); ?>

                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" class="form-control" id="no_contract_<?php echo e($contract->id); ?>"
                                        name="no_contract" placeholder="No Contract"
                                        value="<?php echo e($result); ?>/<?php echo e($contract->unitQuotation?->tax ? 'P' : 'NP'); ?>/SELLCTX/RJO/<?php echo e($thisYear); ?>">
                                    <label for="no_contract_<?php echo e($contract->id); ?>">No Contract</label>
                                </div>
                            </div>
                        </div>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/accounting/accept-contract-unit.blade.php ENDPATH**/ ?>