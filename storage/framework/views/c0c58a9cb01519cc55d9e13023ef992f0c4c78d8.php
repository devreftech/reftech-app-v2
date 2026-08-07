<form action="<?php echo e(route('monitoring.fajarPaper-addMainlog', $monitoring->id)); ?>" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal animate__animated animate__fadeIn" id="plusMainlog" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Link Maintenance Log
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="row g-2 mb-3">
                        <div class="col-12 mb-3">
                            <div class="form-floating form-floating-outline mb-2">
                                <select class="select2 form-select" data-allow-clear="true" name="mainlog"
                                    data-id="1">
                                    <option> ---- Choose Mainlog Here ---- </option>
                                    <?php $__currentLoopData = $maintenance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mainlog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($mainlog->id); ?>">
                                            <?php echo e($mainlog->desc); ?> ( <?php echo e(\Carbon\Carbon::parse($mainlog->date)->format('d-m-Y')); ?> ) - <?php echo e($mainlog->teknisi->name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <label for="Unit" class="mb-2">Unit</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary waves-effect"
                        data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/monitoring/client/mainlog.blade.php ENDPATH**/ ?>