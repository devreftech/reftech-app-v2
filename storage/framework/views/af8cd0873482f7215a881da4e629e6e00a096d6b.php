<div class="modal-onboarding modal modal-xl fade animate__animated" id="addMention" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body mb-3">
                <div class="onboarding-content mb-0">
                    <h4 class="onboarding-title text-body">Add Mention Comment Prospect</h4>
                    <div class="form-floating form-floating-outline">
                        <div class="select2-primary">
                            <select id="select2Primary" class="select2 form-select" name="mention[]" multiple>
                                <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $users): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($users->id != Auth::id()): ?>
                                        <option value="<?php echo e($users->id); ?>"><?php echo e($users->name); ?>

                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <label for="select2Primary">Mention To</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/prospect/add-mention.blade.php ENDPATH**/ ?>