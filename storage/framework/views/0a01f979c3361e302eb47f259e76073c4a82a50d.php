<form action="<?php echo e(route('quotation.choose-machine')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal-onboarding modal modal-l fade animate__animated" id="chooseMachine" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Choose Machine</h4>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <div class="form-floating form-floating-outline">
                                            <select id="select2Basic" class="select2 form-select form-select-lg"
                                                data-allow-clear="true" name="machine">
                                                <?php $__currentLoopData = $machine; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $machines): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($machines->id); ?>">
                                                        <?php echo e($machines->brand); ?> <?php echo e($machines->sku); ?>

                                                    </option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                        <label for="select2Basic">Choose Machine</label>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/quotation/overhaul/form.blade.php ENDPATH**/ ?>