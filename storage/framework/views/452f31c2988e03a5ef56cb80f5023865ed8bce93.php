<form action="<?php echo e(route('notulen.store')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal-onboarding modal modal-xl fade animate__animated" id="formNotulen" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Create Notulen</h4>
                        <form>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <div class="select2-primary">
                                            <select id="select2Primary" class="select2 form-select" name="mention[]"
                                                multiple>
                                                <?php $__currentLoopData = $user; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $users): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="title" class="form-control" name="title"
                                            placeholder="Put Title Notulen Here.....">
                                        <label for="title">Title</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <textarea class="form-control h-50" name="desc" id="desc" cols="30" rows="10"></textarea>
                                        <label for="desc">Description</label>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/notulen/form.blade.php ENDPATH**/ ?>