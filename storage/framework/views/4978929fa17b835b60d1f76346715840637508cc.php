<form action="<?php echo e(route('add_mention.quotation', $quote->id)); ?>" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal-onboarding modal modal-xl fade animate__animated" id="addMention" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body"> Add Mention of <?php echo e($quote->no_quote); ?>

                        </h4>
                        <div class="onboarding-info mb-3">
                            <?php echo e($quote->pic->client->company); ?>

                        </div>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <select class="select2 form-select" id="selectAddress"
                                            aria-label="Default select example" name="admin" data-allow-clear="true">
                                            <option disabled selected>----- Choose Admin -------</option>
                                            <?php $__currentLoopData = $admin; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $admins): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($admins->id); ?>">
                                                    <?php echo e($admins->name); ?>

                                                </option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <label for="selectAddress">Choose Admin</label>
                                    </div>
                                </div>
                                <div class="col-12  mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="textarea" name="comment" id="comment">
                                        <label for="html5-date-input">Comment</label>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/quotation/mentions.blade.php ENDPATH**/ ?>