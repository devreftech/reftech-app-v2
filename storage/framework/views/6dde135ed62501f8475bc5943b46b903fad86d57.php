<form action="<?php echo e(@$libs ? route('library.update', $libs->id) : route('library.store')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php if(@$libs): ?>
        <?php echo method_field('PATCH'); ?>
    <?php endif; ?>
    <div class="modal-onboarding modal fade animate__animated" id="formLibrary<?php echo e(@$libs->id ?? ''); ?>" tabindex="-1"
        style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="onboarding-content mb-0">
                        <h4 class="onboarding-title text-body">Create <?php echo e($type); ?></h4>
                        <form>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="Name" class="form-control" name="name"
                                            placeholder="Put Name Here....." value="<?php echo e(@$libs->name); ?>">
                                        <label for="Name">Name</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="Link" class="form-control" name="link"
                                            placeholder="Put Link Google Drive Here....." value="<?php echo e(@$libs->link); ?>">
                                        <label for="Link">Link</label>
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="Model" class="form-control" name="model"
                                            placeholder="Put Model Here....." value="<?php echo e(@$libs->models); ?>">
                                        <label for="Model">Model</label>
                                    </div>
                                </div>
                                <input type="hidden" name="type" value="<?php echo e($type); ?>">
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/library/form.blade.php ENDPATH**/ ?>