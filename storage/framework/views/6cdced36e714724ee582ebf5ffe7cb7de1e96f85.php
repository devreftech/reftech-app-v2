<form
    action="<?php echo e(request()->is('leads/detail/*') ? route('visit.leads', $leads->id) : route('visit.leads', $existing->id)); ?>"
    method="post" enctype="multipart/form-data">
    
    <?php echo csrf_field(); ?>
    <div class="modal animate__animated animate__fadeIn" id="createActionVisit" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Create Daily Call</h4>
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
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="bs-datepicker-date" placeholder="MM/DD/YYYY"
                                    class="form-control" name="date"
                                    value="<?php echo e(\Carbon\Carbon::today()->format('d/m/Y')); ?>" disabled>
                                <label for="bs-datepicker-date">Date</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input class="form-control" type="date" id="follow_up" name="follow_up">
                                <label for="follow_up">Next Follow Up</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectAction" aria-label="Default select example"
                                    name="action" disabled>
                                    <option disabled>----- Choose Action -----</option>
                                    <option value="Phone Office">Phone Office</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                    <option value="Visit" selected>Visit</option>
                                </select>
                                <label for="selectAction">Action</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="scheduleAnimation" class="form-control" name="note"
                                    placeholder="Put Your Note Here...." value="-">
                                <label for="scheduleAnimation">Note</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectStatus" aria-label="Default select example"
                                    name="status">
                                    <option disabled>----- Choose Status -----</option>
                                    <option value="Responded">Responded</option>
                                    <option value="Not Respon">Not Responded</option>
                                </select>
                                <label for="selectStatus">Status</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="form-select" id="selectStatus" aria-label="Default select example"
                                    name="issues">
                                    <?php $__currentLoopData = $issue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $issues): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(request()->is('leads/detail/*')): ?>
                                            <option value="<?php echo e($issues->id); ?>"
                                                <?php echo e($issues->id == $leads->id_issues ? 'selected' : ''); ?>>
                                                <?php echo e($issues->issue); ?></option>
                                        <?php else: ?>
                                            <option value="<?php echo e($issues->id); ?>"
                                                <?php echo e($issues->id == $existing->id_issues ? 'selected' : ''); ?>>
                                                <?php echo e($issues->issue); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <label for="selectStatus">Status</label>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/pages/sales/activities/form-visit.blade.php ENDPATH**/ ?>