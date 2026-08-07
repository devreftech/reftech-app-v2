<form action="<?php echo e(route('store.machine-technician')); ?>" method="post" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="modal animate__animated animate__fadeIn" id="createMachine" tabindex="-1" style="display: none;"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel5">Create New Machine
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
                    <div class="divider divider-dark mx-3">
                        <div class="divider-text"><span class="fw-semibold">Machine</span></div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <select class="select2 form-select form-select-lg invoice-item-client"
                                    data-allow-clear="true" name="id_client" id="selectclient">
                                    <option selected>----- Select Client -----</option>
                                    <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($client->id); ?>">
                                            <?php echo e($client->company); ?> ||
                                            <?php echo e($client->sales->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <label for="select2Basic">Client</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="brand" class="form-control" name="brand"
                                    placeholder="Example: Kaeser" value="<?php echo e(old('brand')); ?>">
                                <label for="brand">Brand</label>
                            </div>
                        </div>
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="typeAnimation" class="form-control" name="type"
                                    placeholder="Example: CEO" value="<?php echo e(old('type')); ?>">
                                <label for="typeAnimation">Type</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="text" id="serialNumberAnimation" class="form-control"
                                    name="serial_number" placeholder="exampe: CSD-27"
                                    value="<?php echo e(old('serial_number')); ?>">
                                <label for="serialNumberAnimation">Serial Number</label>
                            </div>
                        </div>
                        <div class="col-3 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="barAnimation" class="form-control" name="bar"
                                    placeholder="Example : 100" value="<?php echo e(old('bar')); ?>">
                                <label for="barAnimation">Bar</label>
                            </div>
                        </div>
                        <div class="col-3 mb-2">
                            <div class="form-floating form-floating-outline">
                                <input type="phone" id="runningAnimation" class="form-control" name="running"
                                    placeholder="Example : 100" value="<?php echo e(old('running')); ?>">
                                <label for="runningAnimation">Running Hour</label>
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
<?php /**PATH /home/u877155683/domains/reftech.my.id/reftech.my.id/resources/views/components/modal/machine/form-technician.blade.php ENDPATH**/ ?>