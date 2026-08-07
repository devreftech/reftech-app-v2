<div class="modal animate__animated animate__fadeIn" id="addMainLog" tabindex="-1" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-center" id="exampleModalLabel5"> Maintenance Log
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('store.daily-mainlog-service', $machine->id ?? '0')); ?>" method="post"
                    enctype="multipart/form-data" id="myForm">
                    <?php echo csrf_field(); ?>
                    <div class="row mb-3">
                        <div class="col-12 col-lg-6">
                            <div class="form-floating form-floating-outline mb-3">
                                <input type="date" class="form-control" name="date" id="formDate"
                                    value="<?php echo e(\Carbon\Carbon::today()->format('Y-m-d')); ?>">
                                <label for="formDate">Date</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <div class="form-floating form-floating-outline mb-4">
                                <select class="form-select" id="exampleFormControlSelect1"
                                    aria-label="Default select example" name="teknisi">
                                    <?php $__currentLoopData = $teknisi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pagawe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($pagawe->role == 'Technician' || $pagawe->id == '9' || $pagawe->id == '27'): ?>
                                            <option value="<?php echo e($pagawe->id); ?>"><?php echo e($pagawe->name); ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <label for="exampleFormControlSelect1">Vendor</label>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label for="defaultInput" class="form-label">Maintenance Description</label>
                            <div class="input-group input-group-merge">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="main_desc"></textarea>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label for="defaultInput" class="form-label">Next Maintenance Planned</label>
                            <div class="input-group input-group-merge">
                                <textarea class="form-control h-px-100" id="exampleFormControlTextarea1" name="main_next"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="float-end">
                        <a href="<?php echo e(route('index.daily-monitoring', $machine->id)); ?>" type="button"
                            class="btn btn-lg btn-outline-secondary">
                            Back
                        </a>
                        <button :disabled="focused" type="submit" class="btn btn-lg btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/monitoring/mainlog-create-service.blade.php ENDPATH**/ ?>